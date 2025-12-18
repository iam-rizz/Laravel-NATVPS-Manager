<?php

namespace App\Services\Virtualizor\Contracts;

use App\Models\Server;
use App\Services\Virtualizor\DTOs\ActionResult;
use App\Services\Virtualizor\DTOs\ConnectionResult;
use App\Services\Virtualizor\DTOs\VpsInfo;

interface VirtualizorServiceInterface
{
    /**
     * Test connection to a Virtualizor server.
     */
    public function testConnection(Server $server): ConnectionResult;

    /**
     * List all VPS instances on a server.
     *
     * @return array<int, VpsInfo>
     */
    public function listVps(Server $server): array;

    /**
     * Get detailed information about a specific VPS.
     */
    public function getVpsInfo(Server $server, int $vpsId): ?VpsInfo;

    /**
     * Start a VPS.
     */
    public function startVps(Server $server, int $vpsId): ActionResult;

    /**
     * Stop a VPS.
     */
    public function stopVps(Server $server, int $vpsId): ActionResult;

    /**
     * Restart a VPS.
     */
    public function restartVps(Server $server, int $vpsId): ActionResult;

    /**
     * Power off a VPS.
     */
    public function poweroffVps(Server $server, int $vpsId): ActionResult;

    /**
     * Get the current status of a VPS.
     *
     * @return int 1 if running, 0 if stopped
     */
    public function getVpsStatus(Server $server, int $vpsId): int;

    /**
     * Get domain forwarding rules for a VPS.
     */
    public function getDomainForwarding(Server $server, int $vpsId): array;

    /**
     * Create a domain forwarding rule.
     */
    public function createDomainForwarding(Server $server, int $vpsId, array $data): ActionResult;

    /**
     * Delete a domain forwarding rule.
     */
    public function deleteDomainForwarding(Server $server, int $vpsId, int $recordId): ActionResult;
}
