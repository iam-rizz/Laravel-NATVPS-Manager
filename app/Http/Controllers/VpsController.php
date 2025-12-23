<?php

namespace App\Http\Controllers;

use App\Models\NatVps;
use App\Models\Server;
use App\Models\User;
use App\Enums\UserRole;
use App\Services\AuditLogService;
use App\Services\GeoLocation\GeoLocationService;
use App\Services\MailService;
use App\Services\Virtualizor\Contracts\VirtualizorServiceInterface;
use App\Services\Virtualizor\DTOs\VpsInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Unified VPS Controller
 * 
 * Handles VPS operations for both admin and regular users:
 * - Admin: Full CRUD, assign/unassign, import
 * - User: View assigned VPS, power actions
 */
class VpsController extends Controller
{
    public function __construct(
        protected VirtualizorServiceInterface $virtualizorService,
        protected GeoLocationService $geoLocationService,
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Display a listing of VPS instances.
     * Admin sees all, user sees only assigned.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if ($user->isAdmin()) {
            $natVpsList = NatVps::with(['server', 'user'])
                ->orderBy('hostname')
                ->get();

            return view('vps.index', compact('natVpsList'));
        }

        // User view - fetch live data
        $vpsList = NatVps::with('server')
            ->where('user_id', $user->id)
            ->get();

        $vpsWithSpecs = [];
        $apiErrors = [];

        foreach ($vpsList as $natVps) {
            $vpsData = [
                'natVps' => $natVps,
                'liveInfo' => null,
                'apiOffline' => false,
            ];

            if ($natVps->server) {
                try {
                    $liveInfo = $this->virtualizorService->getVpsInfo(
                        $natVps->server,
                        $natVps->vps_id
                    );

                    if ($liveInfo) {
                        $vpsData['liveInfo'] = $liveInfo;
                        $this->updateCachedSpecs($natVps, $liveInfo);
                    } else {
                        $vpsData['apiOffline'] = true;
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch VPS info', [
                        'nat_vps_id' => $natVps->id,
                        'error' => $e->getMessage(),
                    ]);
                    $vpsData['apiOffline'] = true;
                    $apiErrors[] = $natVps->hostname;
                }
            }

            $vpsWithSpecs[] = $vpsData;
        }

        if (!empty($apiErrors)) {
            session()->flash('warning', 'API connection issues for: ' . implode(', ', $apiErrors) . '. Showing cached data where available.');
        }

        return view('vps.user-index', [
            'vpsWithSpecs' => $vpsWithSpecs,
            'apiErrors' => $apiErrors,
        ]);
    }

    /**
     * Show the form for creating a new VPS.
     * Admin only.
     */
    public function create(): View
    {
        $this->authorizeAdmin();

        $servers = Server::where('is_active', true)->orderBy('name')->get();
        $users = User::where('role', UserRole::User)->orderBy('name')->get();

        return view('vps.create', compact('servers', 'users'));
    }

    /**
     * Store a newly created VPS.
     * Admin only.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'server_id' => ['required', 'exists:servers,id'],
            'vps_id' => ['required', 'integer', 'min:1'],
            'hostname' => ['required', 'string', 'max:255'],
            'user_id' => ['nullable', 'exists:users,id'],
            'ssh_username' => ['nullable', 'string', 'max:255'],
            'ssh_password' => ['nullable', 'string', 'max:255'],
            'ssh_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
        ]);

        $validated['ssh_port'] = $validated['ssh_port'] ?? 22;

        $natVps = NatVps::create($validated);

        $this->auditLogService->log(
            'vps.created',
            $request->user(),
            $natVps,
            [
                'new' => [
                    'id' => $natVps->id,
                    'server_id' => $natVps->server_id,
                    'vps_id' => $natVps->vps_id,
                    'hostname' => $natVps->hostname,
                    'user_id' => $natVps->user_id,
                    'ssh_port' => $natVps->ssh_port,
                ],
            ]
        );

        return redirect()
            ->route('vps.index')
            ->with('success', "NAT VPS '{$natVps->hostname}' created successfully.");
    }

    /**
     * Display the specified VPS.
     */
    public function show(NatVps $natVps): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Check access for non-admin users
        if (!$user->isAdmin() && !$natVps->isOwnedBy($user)) {
            abort(403);
        }

        $natVps->load(['server', 'user']);

        $liveInfo = null;
        $apiOffline = false;
        $vdfCount = 0;

        if ($natVps->server) {
            try {
                $liveInfo = $this->virtualizorService->getVpsInfo($natVps->server, $natVps->vps_id);

                if ($liveInfo) {
                    $natVps->update([
                        'cached_specs' => $liveInfo->toArray(),
                        'specs_cached_at' => now(),
                    ]);

                    // Get VDF count for admin
                    if ($user->isAdmin()) {
                        $forwardings = $this->virtualizorService->getDomainForwarding($natVps->server, $natVps->vps_id);
                        $vdfCount = count($forwardings);
                    }
                } else {
                    $apiOffline = true;
                }
            } catch (\Exception $e) {
                $apiOffline = true;
            }
        } else {
            $apiOffline = true;
        }

        // Fetch server location data if not cached
        if ($natVps->server && !$natVps->server->location_data) {
            $this->geoLocationService->getLocationForServer($natVps->server);
            $natVps->load('server');
        }

        if ($apiOffline) {
            $message = 'API is currently unavailable. Showing cached data.';
            if ($natVps->specs_cached_at) {
                $message .= ' Last updated: ' . $natVps->specs_cached_at->diffForHumans();
            }
            session()->flash('warning', $message);
        }

        $viewName = $user->isAdmin() ? 'vps.show' : 'vps.user-show';

        return view($viewName, compact('natVps', 'vdfCount', 'liveInfo', 'apiOffline'));
    }

    /**
     * Show the form for editing the specified VPS.
     * Admin only.
     */
    public function edit(NatVps $natVps): View
    {
        $this->authorizeAdmin();

        $servers = Server::where('is_active', true)->orderBy('name')->get();
        $users = User::where('role', UserRole::User)->orderBy('name')->get();

        return view('vps.edit', compact('natVps', 'servers', 'users'));
    }

    /**
     * Update the specified VPS.
     * Admin only.
     */
    public function update(Request $request, NatVps $natVps): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'server_id' => ['required', 'exists:servers,id'],
            'vps_id' => ['required', 'integer', 'min:1'],
            'hostname' => ['required', 'string', 'max:255'],
            'user_id' => ['nullable', 'exists:users,id'],
            'ssh_username' => ['nullable', 'string', 'max:255'],
            'ssh_password' => ['nullable', 'string', 'max:255'],
            'ssh_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
        ]);

        $validated['ssh_port'] = $validated['ssh_port'] ?? 22;

        if (empty($validated['ssh_username'])) {
            unset($validated['ssh_username']);
        }
        if (empty($validated['ssh_password'])) {
            unset($validated['ssh_password']);
        }

        $oldValues = [
            'server_id' => $natVps->server_id,
            'vps_id' => $natVps->vps_id,
            'hostname' => $natVps->hostname,
            'user_id' => $natVps->user_id,
            'ssh_port' => $natVps->ssh_port,
        ];

        $natVps->update($validated);

        $newValues = [
            'server_id' => $natVps->server_id,
            'vps_id' => $natVps->vps_id,
            'hostname' => $natVps->hostname,
            'user_id' => $natVps->user_id,
            'ssh_port' => $natVps->ssh_port,
        ];

        $this->auditLogService->log(
            'vps.updated',
            $request->user(),
            $natVps,
            AuditLogService::makeUpdateProperties($oldValues, $newValues)
        );

        return redirect()
            ->route('vps.index')
            ->with('success', "NAT VPS '{$natVps->hostname}' updated successfully.");
    }

    /**
     * Remove the specified VPS.
     * Admin only.
     */
    public function destroy(Request $request, NatVps $natVps): RedirectResponse
    {
        $this->authorizeAdmin();

        $hostname = $natVps->hostname;

        $deletedVpsDetails = [
            'id' => $natVps->id,
            'server_id' => $natVps->server_id,
            'vps_id' => $natVps->vps_id,
            'hostname' => $natVps->hostname,
            'user_id' => $natVps->user_id,
            'ssh_port' => $natVps->ssh_port,
        ];

        $this->auditLogService->log(
            'vps.deleted',
            $request->user(),
            $natVps,
            ['deleted' => $deletedVpsDetails]
        );

        $natVps->delete();

        return redirect()
            ->route('vps.index')
            ->with('success', "NAT VPS '{$hostname}' deleted successfully.");
    }

    /**
     * Assign a user to the VPS.
     * Admin only.
     */
    public function assign(Request $request, NatVps $natVps): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($validated['user_id']);
        $natVps->update(['user_id' => $user->id]);

        $this->auditLogService->log(
            'vps.assigned',
            $request->user(),
            $natVps,
            [
                'vps_id' => $natVps->id,
                'vps_hostname' => $natVps->hostname,
                'assigned_user_id' => $user->id,
                'assigned_user_name' => $user->name,
                'assigned_user_email' => $user->email,
            ]
        );

        $mailService = app(MailService::class);
        $mailService->sendVpsAssigned($user, $natVps);

        return redirect()
            ->back()
            ->with('success', "NAT VPS '{$natVps->hostname}' assigned to {$user->name}.");
    }

    /**
     * Remove user assignment from the VPS.
     * Admin only.
     */
    public function unassign(Request $request, NatVps $natVps): RedirectResponse
    {
        $this->authorizeAdmin();

        $user = $natVps->user;
        
        $previousUserDetails = $user ? [
            'previous_user_id' => $user->id,
            'previous_user_name' => $user->name,
            'previous_user_email' => $user->email,
        ] : [];

        $natVps->update(['user_id' => null]);

        $this->auditLogService->log(
            'vps.unassigned',
            $request->user(),
            $natVps,
            array_merge([
                'vps_id' => $natVps->id,
                'vps_hostname' => $natVps->hostname,
            ], $previousUserDetails)
        );

        if ($user) {
            $mailService = app(MailService::class);
            $mailService->sendVpsUnassigned($user, $natVps);
        }

        return redirect()
            ->back()
            ->with('success', "User assignment removed from NAT VPS '{$natVps->hostname}'.");
    }

    /**
     * Show import selection page.
     * Admin only.
     */
    public function showImport(): View
    {
        $this->authorizeAdmin();

        $servers = Server::where('is_active', true)->orderBy('name')->get();

        return view('vps.import', compact('servers'));
    }

    /**
     * Import VPS instances from a Virtualizor server.
     * Admin only.
     */
    public function importFromServer(Request $request, Server $server)
    {
        $this->authorizeAdmin();

        try {
            $vpsList = $this->virtualizorService->listVps($server);

            $imported = 0;
            $updated = 0;

            foreach ($vpsList as $vpsId => $vpsInfo) {
                $existing = NatVps::where('server_id', $server->id)
                    ->where('vps_id', $vpsId)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'hostname' => $vpsInfo->hostname ?? $existing->hostname,
                        'cached_specs' => $vpsInfo->toArray(),
                        'specs_cached_at' => now(),
                    ]);
                    $updated++;
                } else {
                    NatVps::create([
                        'server_id' => $server->id,
                        'vps_id' => $vpsId,
                        'hostname' => $vpsInfo->hostname ?? "VPS-{$vpsId}",
                        'ssh_port' => 22,
                        'cached_specs' => $vpsInfo->toArray(),
                        'specs_cached_at' => now(),
                    ]);
                    $imported++;
                }
            }

            $message = "Import completed: {$imported} imported, {$updated} updated.";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'imported' => $imported,
                        'updated' => $updated,
                        'total' => count($vpsList),
                    ],
                ]);
            }

            return redirect()
                ->route('vps.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            $errorMessage = "Failed to import VPS: " . $e->getMessage();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Power actions - available to both admin and authorized users.
     */
    public function start(NatVps $natVps): RedirectResponse
    {
        return $this->performPowerAction($natVps, 'start');
    }

    public function stop(NatVps $natVps): RedirectResponse
    {
        return $this->performPowerAction($natVps, 'stop');
    }

    public function restart(NatVps $natVps): RedirectResponse
    {
        return $this->performPowerAction($natVps, 'restart');
    }

    public function poweroff(NatVps $natVps): RedirectResponse
    {
        return $this->performPowerAction($natVps, 'poweroff');
    }

    /**
     * Update SSH credentials for a VPS.
     * Available to both admin and authorized users.
     */
    public function updateSshCredentials(Request $request, NatVps $natVps): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check access for non-admin users
        if (!$user->isAdmin() && !$natVps->isOwnedBy($user)) {
            abort(403);
        }

        $validated = $request->validate([
            'ssh_username' => ['nullable', 'string', 'max:255'],
            'ssh_password' => ['nullable', 'string', 'max:255'],
            'ssh_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
        ]);

        $oldValues = [
            'ssh_username' => $natVps->ssh_username,
            'ssh_port' => $natVps->ssh_port,
        ];

        $natVps->update([
            'ssh_username' => $validated['ssh_username'] ?? $natVps->ssh_username,
            'ssh_password' => $validated['ssh_password'] ?? $natVps->ssh_password,
            'ssh_port' => $validated['ssh_port'] ?? $natVps->ssh_port ?? 22,
        ]);

        $newValues = [
            'ssh_username' => $natVps->ssh_username,
            'ssh_port' => $natVps->ssh_port,
        ];

        $this->auditLogService->log(
            'vps.ssh_updated',
            $user,
            $natVps,
            AuditLogService::makeUpdateProperties($oldValues, $newValues)
        );

        return redirect()
            ->route('vps.show', $natVps)
            ->with('success', __('app.ssh_credentials_updated'));
    }

    /**
     * Perform a power action on the VPS.
     */
    protected function performPowerAction(NatVps $natVps, string $action): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check access for non-admin users
        if (!$user->isAdmin() && !$natVps->isOwnedBy($user)) {
            abort(403);
        }

        if (!$natVps->server) {
            return redirect()
                ->route('vps.show', $natVps)
                ->with('error', __('app.vps_no_server'));
        }

        $actionLabels = [
            'start' => 'started',
            'stop' => 'stopped',
            'restart' => 'restarted',
            'poweroff' => 'powered off',
        ];

        try {
            $result = match ($action) {
                'start' => $this->virtualizorService->startVps($natVps->server, $natVps->vps_id),
                'stop' => $this->virtualizorService->stopVps($natVps->server, $natVps->vps_id),
                'restart' => $this->virtualizorService->restartVps($natVps->server, $natVps->vps_id),
                'poweroff' => $this->virtualizorService->poweroffVps($natVps->server, $natVps->vps_id),
                default => throw new \InvalidArgumentException("Unknown action: {$action}"),
            };

            $this->auditLogService->log(
                "vps.{$action}",
                $user,
                $natVps,
                AuditLogService::makeActionProperties(
                    $result->success ? 'success' : 'failure',
                    $result->success ? null : $result->message,
                    ['vps_id' => $natVps->vps_id, 'hostname' => $natVps->hostname]
                )
            );

            if ($result->success) {
                if ($natVps->user) {
                    $mailService = app(MailService::class);
                    $mailService->sendVpsPowerAction($natVps->user, $natVps, $action, $user->name);
                }

                return redirect()
                    ->route('vps.show', $natVps)
                    ->with('success', "VPS has been {$actionLabels[$action]} successfully.");
            }

            return redirect()
                ->route('vps.show', $natVps)
                ->with('error', $result->message ?? "Failed to {$action} VPS.");
        } catch (\Exception $e) {
            Log::error("Failed to {$action} VPS", [
                'nat_vps_id' => $natVps->id,
                'error' => $e->getMessage(),
            ]);

            $this->auditLogService->log(
                "vps.{$action}",
                $user,
                $natVps,
                AuditLogService::makeActionProperties(
                    'failure',
                    $e->getMessage(),
                    ['vps_id' => $natVps->vps_id, 'hostname' => $natVps->hostname]
                )
            );

            return redirect()
                ->route('vps.show', $natVps)
                ->with('error', "Failed to {$action} VPS: " . $e->getMessage());
        }
    }

    /**
     * Get resource usage data for a VPS (AJAX endpoint).
     */
    public function resourceUsage(NatVps $natVps): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdmin() && !$natVps->isOwnedBy($user)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$natVps->server) {
            return response()->json([
                'success' => false,
                'message' => 'VPS has no associated server.',
            ], 400);
        }

        try {
            $resourceUsage = $this->virtualizorService->getResourceUsage($natVps->server, $natVps->vps_id);

            if (!$resourceUsage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to fetch resource usage data.',
                ], 503);
            }

            return response()->json([
                'success' => true,
                'data' => $resourceUsage->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch resource usage: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update cached specs for a NAT VPS.
     */
    protected function updateCachedSpecs(NatVps $natVps, VpsInfo $vpsInfo): void
    {
        $natVps->update([
            'cached_specs' => $vpsInfo->toArray(),
            'specs_cached_at' => now(),
        ]);
    }

    /**
     * Authorize admin access.
     */
    protected function authorizeAdmin(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isAdmin()) {
            abort(403);
        }
    }
}
