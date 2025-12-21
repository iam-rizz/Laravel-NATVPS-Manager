<?php

namespace App\Http\Controllers;

use App\Models\NatVps;
use App\Services\AuditLogService;
use App\Services\Virtualizor\VirtualizorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Unified Domain Forwarding Controller
 * 
 * Handles domain forwarding for both admin and regular users.
 * Access control is handled within each method.
 */
class DomainForwardingController extends Controller
{
    public function __construct(
        protected VirtualizorService $virtualizorService,
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Check if user can access this VPS.
     */
    protected function authorizeAccess(NatVps $natVps): void
    {
        $user = Auth::user();
        
        if (!$user->isAdmin() && $natVps->user_id !== $user->id) {
            abort(403);
        }
    }

    /**
     * Display domain forwarding for a NAT VPS.
     */
    public function index(NatVps $natVps)
    {
        $this->authorizeAccess($natVps);
        
        $natVps->load(['server', 'user']);
        
        $forwardings = [];
        $portConfig = [];
        $apiError = null;
        
        if ($natVps->server) {
            try {
                $data = $this->virtualizorService->getDomainForwardingWithConfig(
                    $natVps->server,
                    $natVps->vps_id
                );
                $forwardings = $data['forwardings'];
                $portConfig = $data['config'];
            } catch (\Exception $e) {
                $apiError = $e->getMessage();
            }
        }

        return view('vps.domain-forwarding', compact('natVps', 'forwardings', 'portConfig', 'apiError'));
    }

    /**
     * Store a new domain forwarding rule.
     */
    public function store(Request $request, NatVps $natVps)
    {
        $this->authorizeAccess($natVps);
        
        $validated = $request->validate([
            'domain' => ['nullable', 'string', 'max:255'],
            'protocol' => ['required', 'in:http,https,tcp'],
            'source_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'destination_port' => ['required', 'integer', 'min:1', 'max:65535'],
        ]);

        if (!$natVps->server) {
            return redirect()->back()->with('error', 'NAT VPS has no associated server.');
        }

        $result = $this->virtualizorService->createDomainForwarding(
            $natVps->server,
            $natVps->vps_id,
            $validated
        );

        if (!$result->success) {
            return redirect()->back()->with('error', $result->message);
        }

        $this->auditLogService->log(
            'domain_forwarding.created',
            $request->user(),
            $natVps,
            [
                'vps_id' => $natVps->id,
                'vps_hostname' => $natVps->hostname,
                'rule' => [
                    'domain' => $validated['domain'] ?? null,
                    'protocol' => $validated['protocol'],
                    'source_port' => $validated['source_port'],
                    'destination_port' => $validated['destination_port'],
                ],
                'metadata' => ['result' => 'success'],
            ]
        );

        return redirect()->back()->with('success', 'Domain forwarding rule created successfully.');
    }

    /**
     * Update a domain forwarding rule.
     */
    public function update(Request $request, NatVps $natVps, int $recordId)
    {
        $this->authorizeAccess($natVps);
        
        $validated = $request->validate([
            'domain' => ['nullable', 'string', 'max:255'],
            'protocol' => ['required', 'in:http,https,tcp'],
            'source_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'destination_port' => ['required', 'integer', 'min:1', 'max:65535'],
        ]);

        if (!$natVps->server) {
            return redirect()->back()->with('error', 'NAT VPS has no associated server.');
        }

        $result = $this->virtualizorService->updateDomainForwarding(
            $natVps->server,
            $natVps->vps_id,
            $recordId,
            $validated
        );

        if (!$result->success) {
            return redirect()->back()->with('error', $result->message);
        }

        return redirect()->back()->with('success', 'Domain forwarding rule updated successfully.');
    }

    /**
     * Delete a domain forwarding rule.
     */
    public function destroy(Request $request, NatVps $natVps, int $recordId)
    {
        $this->authorizeAccess($natVps);
        
        if (!$natVps->server) {
            return redirect()->back()->with('error', 'NAT VPS has no associated server.');
        }

        $ruleDetails = null;
        try {
            $forwardings = $this->virtualizorService->getDomainForwarding(
                $natVps->server,
                $natVps->vps_id
            );
            foreach ($forwardings as $forwarding) {
                if (isset($forwarding['id']) && $forwarding['id'] == $recordId) {
                    $ruleDetails = $forwarding;
                    break;
                }
            }
        } catch (\Exception $e) {
            // Continue with deletion
        }

        $result = $this->virtualizorService->deleteDomainForwarding(
            $natVps->server,
            $natVps->vps_id,
            $recordId
        );

        if (!$result->success) {
            return redirect()->back()->with('error', $result->message);
        }

        $this->auditLogService->log(
            'domain_forwarding.deleted',
            $request->user(),
            $natVps,
            [
                'vps_id' => $natVps->id,
                'vps_hostname' => $natVps->hostname,
                'record_id' => $recordId,
                'rule' => $ruleDetails,
                'metadata' => ['result' => 'success'],
            ]
        );

        return redirect()->back()->with('success', 'Domain forwarding rule deleted successfully.');
    }
}
