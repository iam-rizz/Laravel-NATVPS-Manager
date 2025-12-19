<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NatVps;
use App\Services\Virtualizor\Contracts\VirtualizorServiceInterface;
use App\Services\Virtualizor\DTOs\VpsInfo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Controller for user VPS viewing and power actions.
 * 
 * Implements Requirements: 5.1, 5.2, 5.3, 5.4, 6.1, 6.2, 6.3, 6.4, 6.5
 */
class VpsController extends Controller
{
    public function __construct(
        protected VirtualizorServiceInterface $virtualizorService
    ) {}

    /**
     * Display a listing of VPS instances assigned to the current user.
     * 
     * Requirements: 5.1 - Retrieve VPS details from Virtualizor API
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        
        // Get VPS instances assigned to the current user
        // Admin users see all VPS instances
        if ($user->isAdmin()) {
            $vpsList = NatVps::with('server', 'user')->get();
        } else {
            $vpsList = NatVps::with('server')
                ->where('user_id', $user->id)
                ->get();
        }

        // Fetch live data from API for each VPS
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
                        // Update cached specs
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

        return view('user.vps.index', [
            'vpsWithSpecs' => $vpsWithSpecs,
            'apiErrors' => $apiErrors,
        ]);
    }

    /**
     * Display the specified VPS with details from API.
     * 
     * Requirements: 5.2, 5.3, 5.4 - Show VPS specs and SSH credentials
     */
    public function show(NatVps $natVps): View
    {
        // Load server relation for SSH command display
        $natVps->load('server');
        
        $liveInfo = null;
        $apiOffline = false;

        if ($natVps->server) {
            try {
                $liveInfo = $this->virtualizorService->getVpsInfo(
                    $natVps->server,
                    $natVps->vps_id
                );

                if ($liveInfo) {
                    $this->updateCachedSpecs($natVps, $liveInfo);
                } else {
                    $apiOffline = true;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch VPS info for show', [
                    'nat_vps_id' => $natVps->id,
                    'error' => $e->getMessage(),
                ]);
                $apiOffline = true;
            }
        } else {
            $apiOffline = true;
        }

        return view('user.vps.show', [
            'natVps' => $natVps,
            'liveInfo' => $liveInfo,
            'apiOffline' => $apiOffline,
        ]);
    }

    /**
     * Start the specified VPS.
     * 
     * Requirements: 6.1 - Call Virtualizor API start method
     */
    public function start(NatVps $natVps): RedirectResponse
    {
        return $this->performPowerAction($natVps, 'start');
    }

    /**
     * Stop the specified VPS.
     * 
     * Requirements: 6.2 - Call Virtualizor API stop method
     */
    public function stop(NatVps $natVps): RedirectResponse
    {
        return $this->performPowerAction($natVps, 'stop');
    }

    /**
     * Restart the specified VPS.
     * 
     * Requirements: 6.3 - Call Virtualizor API restart method
     */
    public function restart(NatVps $natVps): RedirectResponse
    {
        return $this->performPowerAction($natVps, 'restart');
    }

    /**
     * Power off the specified VPS.
     * 
     * Requirements: 6.4 - Call Virtualizor API poweroff method
     */
    public function poweroff(NatVps $natVps): RedirectResponse
    {
        return $this->performPowerAction($natVps, 'poweroff');
    }

    /**
     * Perform a power action on the VPS.
     * 
     * Requirements: 6.5 - Display error message with details from API response
     */
    protected function performPowerAction(NatVps $natVps, string $action): RedirectResponse
    {
        if (!$natVps->server) {
            return redirect()
                ->route('user.vps.show', $natVps)
                ->with('error', 'Cannot perform action: VPS has no associated server.');
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

            if ($result->success) {
                return redirect()
                    ->route('user.vps.show', $natVps)
                    ->with('success', "VPS has been {$actionLabels[$action]} successfully.");
            }

            return redirect()
                ->route('user.vps.show', $natVps)
                ->with('error', $result->message ?? "Failed to {$action} VPS.");
        } catch (\Exception $e) {
            Log::error("Failed to {$action} VPS", [
                'nat_vps_id' => $natVps->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('user.vps.show', $natVps)
                ->with('error', "Failed to {$action} VPS: " . $e->getMessage());
        }
    }

    /**
     * Update cached specs for a NAT VPS.
     * 
     * Requirements: 5.3 - Cache data for offline display
     */
    protected function updateCachedSpecs(NatVps $natVps, VpsInfo $vpsInfo): void
    {
        $natVps->update([
            'cached_specs' => $vpsInfo->toArray(),
            'specs_cached_at' => now(),
        ]);
    }
}
