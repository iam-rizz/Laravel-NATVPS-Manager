<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Services\AuditLogService;
use App\Services\Virtualizor\Contracts\VirtualizorServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ServerController extends Controller
{
    public function __construct(
        protected VirtualizorServiceInterface $virtualizorService,
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Display a listing of all servers.
     * Requirements: 2.4
     */
    public function index()
    {
        $servers = Server::withCount('natVps')
            ->orderBy('name')
            ->get();

        return view('admin.servers.index', compact('servers'));
    }

    /**
     * Show the form for creating a new server.
     */
    public function create()
    {
        return view('admin.servers.create');
    }

    /**
     * Store a newly created server in storage.
     * Requirements: 2.1, 5.1
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'ip_address' => ['required', 'string', 'max:255'],
            'api_key' => ['required', 'string'],
            'api_pass' => ['required', 'string'],
            'port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'is_active' => ['boolean'],
        ]);

        $validated['port'] = $validated['port'] ?? 4083;
        $validated['is_active'] = $request->boolean('is_active', true);

        $server = Server::create($validated);

        // Log server creation
        $this->auditLogService->log(
            'server.created',
            $request->user(),
            $server,
            [
                'new' => [
                    'id' => $server->id,
                    'name' => $server->name,
                    'ip_address' => $server->ip_address,
                    'port' => $server->port,
                    'is_active' => $server->is_active,
                ],
            ]
        );

        return redirect()
            ->route('servers.index')
            ->with('success', "Server '{$server->name}' created successfully.");
    }

    /**
     * Show the form for editing the specified server.
     */
    public function edit(Server $server)
    {
        return view('admin.servers.edit', compact('server'));
    }


    /**
     * Update the specified server in storage.
     * Requirements: 2.2, 5.2
     */
    public function update(Request $request, Server $server)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'ip_address' => ['required', 'string', 'max:255'],
            'api_key' => ['nullable', 'string'],
            'api_pass' => ['nullable', 'string'],
            'port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'is_active' => ['boolean'],
        ]);

        $validated['port'] = $validated['port'] ?? 4083;
        $validated['is_active'] = $request->boolean('is_active', true);

        // Capture old values before update
        $oldValues = [
            'name' => $server->name,
            'ip_address' => $server->ip_address,
            'port' => $server->port,
            'is_active' => $server->is_active,
        ];

        // Only update credentials if provided (allow keeping existing)
        if (empty($validated['api_key'])) {
            unset($validated['api_key']);
        }
        if (empty($validated['api_pass'])) {
            unset($validated['api_pass']);
        }

        $server->update($validated);

        // Capture new values after update
        $newValues = [
            'name' => $server->name,
            'ip_address' => $server->ip_address,
            'port' => $server->port,
            'is_active' => $server->is_active,
        ];

        // Log server update with old and new values
        $this->auditLogService->log(
            'server.updated',
            $request->user(),
            $server,
            AuditLogService::makeUpdateProperties($oldValues, $newValues)
        );

        return redirect()
            ->route('servers.index')
            ->with('success', "Server '{$server->name}' updated successfully.");
    }

    /**
     * Remove the specified server from storage.
     * Requirements: 2.3, 5.3
     */
    public function destroy(Request $request, Server $server)
    {
        $serverName = $server->name;
        
        // Capture server details before deletion for audit log
        $deletedServerDetails = [
            'id' => $server->id,
            'name' => $server->name,
            'ip_address' => $server->ip_address,
            'port' => $server->port,
            'is_active' => $server->is_active,
        ];

        // Log server deletion before actually deleting
        $this->auditLogService->log(
            'server.deleted',
            $request->user(),
            $server,
            [
                'deleted' => $deletedServerDetails,
            ]
        );

        // Disassociate NAT VPS instances before deleting
        $server->natVps()->update(['server_id' => null]);
        
        $server->delete();

        return redirect()
            ->route('servers.index')
            ->with('success', "Server '{$serverName}' deleted successfully.");
    }

    /**
     * Test connection to the specified server.
     * Requirements: 2.5, 5.4
     */
    public function testConnection(Request $request, Server $server)
    {
        try {
            $result = $this->virtualizorService->testConnection($server);
            
            // Update last_checked timestamp
            $server->update(['last_checked' => now()]);

            // Log connection test with result
            $this->auditLogService->log(
                'server.connection_test',
                $request->user(),
                $server,
                AuditLogService::makeActionProperties(
                    $result->success ? 'success' : 'failure',
                    $result->success ? null : $result->message,
                    [
                        'server_id' => $server->id,
                        'server_name' => $server->name,
                    ]
                )
            );

            if ($result->success) {
                return response()->json([
                    'success' => true,
                    'message' => $result->message,
                    'data' => $result->details,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result->message,
                'data' => $result->details,
            ], 422);
        } catch (\Exception $e) {
            Log::error('Server connection test failed', [
                'server_id' => $server->id,
                'error' => $e->getMessage(),
            ]);

            // Log failed connection test
            $this->auditLogService->log(
                'server.connection_test',
                $request->user(),
                $server,
                AuditLogService::makeActionProperties(
                    'failure',
                    $e->getMessage(),
                    [
                        'server_id' => $server->id,
                        'server_name' => $server->name,
                    ]
                )
            );

            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
