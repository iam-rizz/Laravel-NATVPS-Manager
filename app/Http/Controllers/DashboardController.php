<?php

namespace App\Http\Controllers;

use App\Models\NatVps;
use App\Models\Server;
use App\Models\User;
use App\Services\Virtualizor\Contracts\VirtualizorServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Unified Dashboard Controller
 * 
 * Shows different dashboard based on user role:
 * - Admin: Full statistics, server health, recent activity
 * - User: Assigned VPS summary and quick access
 */
class DashboardController extends Controller
{
    public function __construct(
        protected VirtualizorServiceInterface $virtualizorService
    ) {}

    /**
     * Display the dashboard based on user role.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        }

        return $this->userDashboard($request);
    }

    /**
     * Admin dashboard with full statistics.
     */
    protected function adminDashboard(): View
    {
        $totalServers = Server::count();
        $totalNatVps = NatVps::count();
        $totalUsers = User::count();
        
        $serversWithIssues = $this->getServersWithIssues();
        $recentActivity = $this->getRecentActivity();
        
        $assignedVpsCount = NatVps::whereNotNull('user_id')->count();
        $unassignedVpsCount = NatVps::whereNull('user_id')->count();
        $activeServers = Server::where('is_active', true)->count();
        $inactiveServers = Server::where('is_active', false)->count();

        return view('dashboard.admin', compact(
            'totalServers',
            'totalNatVps',
            'totalUsers',
            'serversWithIssues',
            'recentActivity',
            'assignedVpsCount',
            'unassignedVpsCount',
            'activeServers',
            'inactiveServers'
        ));
    }

    /**
     * User dashboard with VPS summary.
     */
    protected function userDashboard(Request $request): View
    {
        $user = $request->user();
        
        $assignedVpsCount = NatVps::where('user_id', $user->id)->count();
        
        $assignedVps = NatVps::with('server')
            ->where('user_id', $user->id)
            ->orderBy('hostname')
            ->get();

        return view('dashboard.user', compact(
            'assignedVpsCount',
            'assignedVps'
        ));
    }

    /**
     * Get servers that have connection issues.
     */
    protected function getServersWithIssues(): array
    {
        $issues = [];
        
        $staleServers = Server::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('last_checked')
                    ->orWhere('last_checked', '<', now()->subHours(24));
            })
            ->get();

        foreach ($staleServers as $server) {
            $issues[] = [
                'server' => $server,
                'issue' => $server->last_checked 
                    ? 'Not checked in over 24 hours' 
                    : 'Never checked',
                'severity' => 'warning',
            ];
        }

        $serversToCheck = Server::where('is_active', true)
            ->whereNotNull('last_checked')
            ->where('last_checked', '>=', now()->subHours(24))
            ->get();

        foreach ($serversToCheck as $server) {
            try {
                $result = $this->virtualizorService->testConnection($server);
                if (!$result->success) {
                    $issues[] = [
                        'server' => $server,
                        'issue' => $result->message ?? 'Connection failed',
                        'severity' => 'error',
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Dashboard server check failed', [
                    'server_id' => $server->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $issues;
    }

    /**
     * Get recent activity summary.
     */
    protected function getRecentActivity(): array
    {
        $activity = [];

        $recentVps = NatVps::with(['server', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentVps as $vps) {
            $activity[] = [
                'type' => 'vps_created',
                'message' => "NAT VPS '{$vps->hostname}' was added",
                'details' => $vps->server ? "on server {$vps->server->name}" : '',
                'timestamp' => $vps->created_at,
            ];
        }

        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentUsers as $user) {
            $activity[] = [
                'type' => 'user_created',
                'message' => "User '{$user->name}' was created",
                'details' => $user->email,
                'timestamp' => $user->created_at,
            ];
        }

        $recentServers = Server::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentServers as $server) {
            $activity[] = [
                'type' => 'server_created',
                'message' => "Server '{$server->name}' was added",
                'details' => $server->ip_address,
                'timestamp' => $server->created_at,
            ];
        }

        usort($activity, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);
        
        return array_slice($activity, 0, 10);
    }
}
