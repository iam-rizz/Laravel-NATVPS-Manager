<?php

namespace App\Http\Controllers;

use App\Models\NatVps;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\Console\ConsoleProxyHealthCheck;
use App\Services\Console\WebSocketUrlBuilder;
use App\Services\Virtualizor\Contracts\VirtualizorServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Controller for VPS console access (VNC and SSH).
 * 
 * Implements Requirements: 1.1, 1.2, 1.3, 2.1, 2.2, 3.1, 3.2, 3.3, 6.1, 6.2, 6.3
 */
class ConsoleController extends Controller
{
    protected WebSocketUrlBuilder $webSocketUrlBuilder;
    protected ConsoleProxyHealthCheck $healthCheck;

    public function __construct(
        protected VirtualizorServiceInterface $virtualizorService,
        protected AuditLogService $auditLogService
    ) {
        $this->webSocketUrlBuilder = new WebSocketUrlBuilder();
        $this->healthCheck = new ConsoleProxyHealthCheck();
    }

    /**
     * Get console proxy health status.
     */
    public function proxyHealth(): JsonResponse
    {
        $health = $this->healthCheck->check(useCache: false);
        
        return response()->json($health);
    }

    /**
     * Check if the current user can access the VPS console.
     * 
     * Requirements: 3.1, 3.3, 6.2
     * - Admin users can access any VPS console
     * - Regular users can only access VPS they own
     *
     * @param User $user The user requesting access
     * @param NatVps $natVps The VPS to access
     * @return bool True if access is allowed
     */
    public function canAccessConsole(User $user, NatVps $natVps): bool
    {
        // Admin can access any VPS console (Requirement 3.1)
        if ($user->isAdmin()) {
            return true;
        }

        // Regular users can only access their own VPS (Requirement 3.3)
        return $natVps->user_id === $user->id;
    }

    /**
     * Show the console index page with VPS list.
     * 
     * Requirements: 1.1
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Get VPS list based on user role
        if ($user->isAdmin()) {
            // Admin can see all VPS
            $vpsList = NatVps::with('server', 'user')
                ->orderBy('hostname')
                ->get();
        } else {
            // Regular users only see their own VPS
            $vpsList = NatVps::with('server')
                ->where('user_id', $user->id)
                ->orderBy('hostname')
                ->get();
        }

        return view('console.index', [
            'vpsList' => $vpsList,
        ]);
    }

    /**
     * Show the console page for a VPS.
     * 
     * Requirements: 1.1
     */
    public function show(NatVps $natVps): View|JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$this->canAccessConsole($user, $natVps)) {
            abort(403, __('app.console_access_denied'));
        }

        // Log admin console access (Requirement 3.2)
        if ($user->isAdmin() && $natVps->user_id !== $user->id) {
            $this->logAdminConsoleAccess($user, $natVps, 'view');
        }

        $natVps->load('server');

        return view('console.show', [
            'natVps' => $natVps,
        ]);
    }

    /**
     * Get VNC connection details for a VPS.
     * 
     * Requirements: 2.1, 2.2
     */
    public function getVncDetails(NatVps $natVps): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$this->canAccessConsole($user, $natVps)) {
            return response()->json([
                'success' => false,
                'error' => __('app.console_access_denied'),
            ], 403);
        }

        // Log admin console access (Requirement 3.2)
        if ($user->isAdmin() && $natVps->user_id !== $user->id) {
            $this->logAdminConsoleAccess($user, $natVps, 'vnc');
        }

        if (!$natVps->server) {
            return response()->json([
                'success' => false,
                'error' => __('app.vps_no_server'),
            ], 400);
        }

        try {
            $vncInfo = $this->virtualizorService->getVncInfo($natVps->server, $natVps->vps_id);

            if (!$vncInfo) {
                return response()->json([
                    'success' => false,
                    'error' => __('app.vnc_not_available'),
                ], 400);
            }

            // Build WebSocket URL (Requirement 6.1)
            $websocketUrl = $this->buildWebSocketUrl('vnc', $vncInfo['host'], $vncInfo['port']);

            return response()->json([
                'success' => true,
                'data' => [
                    'host' => $vncInfo['host'],
                    'port' => $vncInfo['port'],
                    'password' => $vncInfo['password'],
                    'websocket_url' => $websocketUrl,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get VNC details', [
                'nat_vps_id' => $natVps->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => __('app.console_temporarily_unavailable'),
            ], 503);
        }
    }

    /**
     * Get SSH connection details for a VPS.
     * 
     * Requirements: 1.3
     * 
     * Returns SSH credentials from database:
     * - host: Server IP address
     * - port: Custom SSH port from nat_vps table
     * - username: SSH username from nat_vps table
     * - password: SSH password from nat_vps table (encrypted)
     */
    public function getSshDetails(NatVps $natVps): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$this->canAccessConsole($user, $natVps)) {
            return response()->json([
                'success' => false,
                'error' => __('app.console_access_denied'),
            ], 403);
        }

        // Log admin console access (Requirement 3.2)
        if ($user->isAdmin() && $natVps->user_id !== $user->id) {
            $this->logAdminConsoleAccess($user, $natVps, 'ssh');
        }

        // Check if SSH credentials are configured in database
        if (empty($natVps->ssh_username) || empty($natVps->ssh_password)) {
            return response()->json([
                'success' => false,
                'error' => __('app.ssh_not_configured'),
            ], 400);
        }

        $natVps->load('server');

        // Get SSH host from server IP address
        $sshHost = $natVps->server?->ip_address ?? '';
        
        // Get custom SSH port from database (default to 22 if not set)
        $sshPort = $natVps->ssh_port ?? 22;

        if (empty($sshHost)) {
            return response()->json([
                'success' => false,
                'error' => __('app.vps_no_server'),
            ], 400);
        }

        // Build WebSocket URL for SSH proxy (Requirement 6.1)
        $websocketUrl = $this->buildWebSocketUrl('ssh', $sshHost, $sshPort);

        return response()->json([
            'success' => true,
            'data' => [
                'host' => $sshHost,                      // From server.ip_address
                'port' => (int) $sshPort,                // From nat_vps.ssh_port (custom port)
                'username' => $natVps->ssh_username,     // From nat_vps.ssh_username
                'password' => $natVps->ssh_password,     // From nat_vps.ssh_password (decrypted)
                'websocket_url' => $websocketUrl,
            ],
        ]);
    }

    /**
     * Generate a secure time-limited token for console access.
     * 
     * Requirements: 6.3
     */
    public function generateToken(NatVps $natVps): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$this->canAccessConsole($user, $natVps)) {
            return response()->json([
                'success' => false,
                'error' => __('app.console_access_denied'),
            ], 403);
        }

        // Generate secure token
        $token = Str::random(64);
        $ttlMinutes = config('services.console.security.token_ttl', 5);
        $expiresAt = now()->addMinutes($ttlMinutes);

        // Store token in cache with TTL (Requirement 6.3)
        $tokenData = [
            'vps_id' => $natVps->id,
            'user_id' => $user->id,
            'type' => 'console',
            'created_at' => now()->toIso8601String(),
            'expires_at' => $expiresAt->toIso8601String(),
        ];

        Cache::put("console_token:{$token}", $tokenData, $expiresAt);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'expires_at' => $expiresAt->toIso8601String(),
                'ttl_seconds' => $ttlMinutes * 60,
            ],
        ]);
    }

    /**
     * Build WebSocket URL based on configuration.
     * 
     * Requirements: 6.1 - Use WSS in production
     * 
     * @param string $type Connection type ('vnc' or 'ssh')
     * @param string $host Target host
     * @param int $port Target port
     * @param string|null $token Optional authentication token
     * @return string The WebSocket URL
     */
    public function buildWebSocketUrl(string $type, string $host, int $port, ?string $token = null): string
    {
        return $this->webSocketUrlBuilder->buildUrl($type, $host, $port, $token);
    }

    /**
     * Get the WebSocket URL builder instance.
     * 
     * @return WebSocketUrlBuilder
     */
    public function getWebSocketUrlBuilder(): WebSocketUrlBuilder
    {
        return $this->webSocketUrlBuilder;
    }

    /**
     * Log admin console access for audit purposes.
     * 
     * Requirements: 3.2
     */
    protected function logAdminConsoleAccess(User $admin, NatVps $natVps, string $accessType): void
    {
        $this->auditLogService->log(
            'console.access',
            $admin,
            $natVps,
            AuditLogService::makeActionProperties(
                'success',
                null,
                [
                    'access_type' => $accessType,
                    'vps_id' => $natVps->vps_id,
                    'hostname' => $natVps->hostname,
                    'owner_id' => $natVps->user_id,
                ]
            )
        );
    }
}
