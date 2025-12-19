<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NatVps;
use App\Services\Virtualizor\VirtualizorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller for managing domain forwarding (VDF) rules for users.
 */
class DomainForwardingController extends Controller
{
    public function __construct(
        protected VirtualizorService $virtualizorService
    ) {}

    /**
     * Display domain forwarding for a NAT VPS - data from Virtualizor API.
     */
    public function index(NatVps $natVps): View
    {
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

        return view('user.vps.domain-forwarding.index', compact('natVps', 'forwardings', 'portConfig', 'apiError'));
    }

    /**
     * Store a new domain forwarding rule.
     */
    public function store(Request $request, NatVps $natVps): RedirectResponse
    {
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

        return redirect()->back()->with('success', 'Domain forwarding rule created successfully.');
    }

    /**
     * Update a domain forwarding rule.
     */
    public function update(Request $request, NatVps $natVps, int $recordId): RedirectResponse
    {
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
     * Delete a domain forwarding rule by Virtualizor record ID.
     */
    public function destroy(NatVps $natVps, int $recordId): RedirectResponse
    {
        if (!$natVps->server) {
            return redirect()->back()->with('error', 'NAT VPS has no associated server.');
        }

        $result = $this->virtualizorService->deleteDomainForwarding(
            $natVps->server,
            $natVps->vps_id,
            $recordId
        );

        if (!$result->success) {
            return redirect()->back()->with('error', $result->message);
        }

        return redirect()->back()->with('success', 'Domain forwarding rule deleted successfully.');
    }
}
