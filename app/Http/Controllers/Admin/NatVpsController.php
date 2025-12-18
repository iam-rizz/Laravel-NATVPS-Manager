<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NatVps;
use App\Models\Server;
use App\Models\User;
use App\Enums\UserRole;
use App\Services\Virtualizor\VirtualizorService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NatVpsController extends Controller
{
    /**
     * Display a listing of all NAT VPS instances.
     * Requirements: 3.4
     */
    public function index()
    {
        $natVpsList = NatVps::with(['server', 'user'])
            ->orderBy('hostname')
            ->get();

        return view('admin.nat-vps.index', compact('natVpsList'));
    }

    /**
     * Show the form for creating a new NAT VPS.
     */
    public function create()
    {
        $servers = Server::where('is_active', true)->orderBy('name')->get();
        $users = User::where('role', UserRole::User)->orderBy('name')->get();

        return view('admin.nat-vps.create', compact('servers', 'users'));
    }

    /**
     * Store a newly created NAT VPS in storage.
     * Requirements: 3.1, 3.5
     */
    public function store(Request $request)
    {
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

        return redirect()
            ->route('admin.nat-vps.index')
            ->with('success', "NAT VPS '{$natVps->hostname}' created successfully.");
    }

    /**
     * Display the specified NAT VPS.
     * Requirements: 3.4, 4.5
     */
    public function show(NatVps $natVps)
    {
        $natVps->load(['server', 'user', 'domainForwardings']);

        return view('admin.nat-vps.show', compact('natVps'));
    }

    /**
     * Show the form for editing the specified NAT VPS.
     */
    public function edit(NatVps $natVps)
    {
        $servers = Server::where('is_active', true)->orderBy('name')->get();
        $users = User::where('role', UserRole::User)->orderBy('name')->get();

        return view('admin.nat-vps.edit', compact('natVps', 'servers', 'users'));
    }

    /**
     * Update the specified NAT VPS in storage.
     * Requirements: 3.2, 3.5
     */
    public function update(Request $request, NatVps $natVps)
    {
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

        // Handle empty SSH credentials - keep existing if not provided
        if (empty($validated['ssh_username'])) {
            unset($validated['ssh_username']);
        }
        if (empty($validated['ssh_password'])) {
            unset($validated['ssh_password']);
        }

        $natVps->update($validated);

        return redirect()
            ->route('admin.nat-vps.index')
            ->with('success', "NAT VPS '{$natVps->hostname}' updated successfully.");
    }

    /**
     * Remove the specified NAT VPS from storage.
     * Requirements: 3.3
     */
    public function destroy(NatVps $natVps)
    {
        $hostname = $natVps->hostname;

        // Delete associated domain forwardings
        $natVps->domainForwardings()->delete();

        $natVps->delete();

        return redirect()
            ->route('admin.nat-vps.index')
            ->with('success', "NAT VPS '{$hostname}' deleted successfully.");
    }

    /**
     * Assign a user to the NAT VPS.
     * Requirements: 4.1
     */
    public function assign(Request $request, NatVps $natVps)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);

        $user = User::findOrFail($validated['user_id']);

        $natVps->update(['user_id' => $user->id]);

        return redirect()
            ->back()
            ->with('success', "NAT VPS '{$natVps->hostname}' assigned to {$user->name}.");
    }

    /**
     * Remove user assignment from the NAT VPS.
     * Requirements: 4.2
     */
    public function unassign(NatVps $natVps)
    {
        $natVps->update(['user_id' => null]);

        return redirect()
            ->back()
            ->with('success', "User assignment removed from NAT VPS '{$natVps->hostname}'.");
    }

    /**
     * Import NAT VPS instances from a Virtualizor server.
     */
    public function importFromServer(Request $request, Server $server, VirtualizorService $virtualizorService)
    {
        try {
            $vpsList = $virtualizorService->listVps($server);

            $imported = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($vpsList as $vpsId => $vpsInfo) {
                $existing = NatVps::where('server_id', $server->id)
                    ->where('vps_id', $vpsId)
                    ->first();

                if ($existing) {
                    // Update existing record with fresh data
                    $existing->update([
                        'hostname' => $vpsInfo->hostname ?? $existing->hostname,
                        'cached_specs' => $vpsInfo->toArray(),
                        'specs_cached_at' => now(),
                    ]);
                    $updated++;
                } else {
                    // Create new record
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
                        'skipped' => $skipped,
                        'total' => count($vpsList),
                    ],
                ]);
            }

            return redirect()
                ->route('admin.nat-vps.index')
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
     * Show import selection page.
     */
    public function showImport()
    {
        $servers = Server::where('is_active', true)->orderBy('name')->get();

        return view('admin.nat-vps.import', compact('servers'));
    }
}
