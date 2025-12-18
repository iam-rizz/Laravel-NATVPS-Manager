<?php

namespace App\Services\Virtualizor;

use App\Models\Server;
use App\Services\Virtualizor\Contracts\VirtualizorServiceInterface;
use App\Services\Virtualizor\DTOs\ActionResult;
use App\Services\Virtualizor\DTOs\ConnectionResult;
use App\Services\Virtualizor\DTOs\VpsInfo;
use App\Services\Virtualizor\Exceptions\ConnectionException;
use Illuminate\Support\Facades\Log;
use Virtualizor_Enduser_API;

class VirtualizorService implements VirtualizorServiceInterface
{
    /**
     * Create a Virtualizor API client for a server.
     */
    protected function createClient(Server $server): Virtualizor_Enduser_API
    {
        return new Virtualizor_Enduser_API(
            $server->ip_address,
            $server->api_key,
            $server->api_pass,
            $server->port ?? 4083,
            true // is_admin = true for admin API access
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testConnection(Server $server): ConnectionResult
    {
        try {
            $client = $this->createClient($server);
            $result = $client->listvs();

            if ($result === false) {
                return ConnectionResult::failure('Failed to connect to server or invalid credentials');
            }

            // Check for authentication errors
            if (isset($result['error']) && !empty($result['error'])) {
                return ConnectionResult::failure(
                    'API error: ' . (is_array($result['error']) ? implode(', ', $result['error']) : $result['error']),
                    ['error' => $result['error']]
                );
            }

            return ConnectionResult::success('Connection successful', [
                'vps_count' => isset($result['vs']) ? count($result['vs']) : 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Virtualizor connection test failed', [
                'server_id' => $server->id,
                'error' => $e->getMessage(),
            ]);

            return ConnectionResult::failure('Connection failed: ' . $e->getMessage());
        }
    }


    /**
     * {@inheritdoc}
     */
    public function listVps(Server $server): array
    {
        try {
            $client = $this->createClient($server);
            $result = $client->listvs();

            if ($result === false || !isset($result['vs'])) {
                return [];
            }

            $vpsList = [];
            foreach ($result['vs'] as $vpsData) {
                $vpsList[(int) $vpsData['vpsid']] = VpsInfo::fromApiResponse($vpsData);
            }

            return $vpsList;
        } catch (\Exception $e) {
            Log::error('Failed to list VPS', [
                'server_id' => $server->id,
                'error' => $e->getMessage(),
            ]);

            throw new ConnectionException('Failed to list VPS: ' . $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVpsInfo(Server $server, int $vpsId): ?VpsInfo
    {
        try {
            $client = $this->createClient($server);
            $result = $client->vpsinfo($vpsId);

            if ($result === false || empty($result['vps'])) {
                return null;
            }

            return VpsInfo::fromApiResponse($result['vps']);
        } catch (\Exception $e) {
            Log::error('Failed to get VPS info', [
                'server_id' => $server->id,
                'vps_id' => $vpsId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function startVps(Server $server, int $vpsId): ActionResult
    {
        try {
            $client = $this->createClient($server);
            $result = $client->start($vpsId);

            if ($result === true) {
                return ActionResult::success('VPS started successfully');
            }

            return ActionResult::failure('Failed to start VPS');
        } catch (\Exception $e) {
            Log::error('Failed to start VPS', [
                'server_id' => $server->id,
                'vps_id' => $vpsId,
                'error' => $e->getMessage(),
            ]);

            return ActionResult::failure('Failed to start VPS: ' . $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stopVps(Server $server, int $vpsId): ActionResult
    {
        try {
            $client = $this->createClient($server);
            $result = $client->stop($vpsId);

            if ($result === true) {
                return ActionResult::success('VPS stopped successfully');
            }

            return ActionResult::failure('Failed to stop VPS');
        } catch (\Exception $e) {
            Log::error('Failed to stop VPS', [
                'server_id' => $server->id,
                'vps_id' => $vpsId,
                'error' => $e->getMessage(),
            ]);

            return ActionResult::failure('Failed to stop VPS: ' . $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function restartVps(Server $server, int $vpsId): ActionResult
    {
        try {
            $client = $this->createClient($server);
            $result = $client->restart($vpsId);

            if ($result === true) {
                return ActionResult::success('VPS restarted successfully');
            }

            return ActionResult::failure('Failed to restart VPS');
        } catch (\Exception $e) {
            Log::error('Failed to restart VPS', [
                'server_id' => $server->id,
                'vps_id' => $vpsId,
                'error' => $e->getMessage(),
            ]);

            return ActionResult::failure('Failed to restart VPS: ' . $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function poweroffVps(Server $server, int $vpsId): ActionResult
    {
        try {
            $client = $this->createClient($server);
            $result = $client->poweroff($vpsId);

            if ($result === true) {
                return ActionResult::success('VPS powered off successfully');
            }

            return ActionResult::failure('Failed to power off VPS');
        } catch (\Exception $e) {
            Log::error('Failed to power off VPS', [
                'server_id' => $server->id,
                'vps_id' => $vpsId,
                'error' => $e->getMessage(),
            ]);

            return ActionResult::failure('Failed to power off VPS: ' . $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVpsStatus(Server $server, int $vpsId): int
    {
        try {
            $client = $this->createClient($server);
            $result = $client->status($vpsId);

            return (int) ($result ?? 0);
        } catch (\Exception $e) {
            Log::error('Failed to get VPS status', [
                'server_id' => $server->id,
                'vps_id' => $vpsId,
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getDomainForwarding(Server $server, int $vpsId): array
    {
        try {
            $client = $this->createClient($server);
            $result = $client->vdf(['svs' => $vpsId]);

            if ($result === false) {
                return [];
            }

            // Return the VDF records if available
            return $result['vdf'] ?? $result['vdf_records'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to get domain forwarding', [
                'server_id' => $server->id,
                'vps_id' => $vpsId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createDomainForwarding(Server $server, int $vpsId, array $data): ActionResult
    {
        try {
            $client = $this->createClient($server);
            
            $post = [
                'svs' => $vpsId,
                'add_vdf' => 1,
                'src_hostname' => $data['domain'] ?? '',
                'protocol' => $data['protocol'] ?? 'http',
                'src_port' => $data['source_port'] ?? 80,
                'dest_port' => $data['destination_port'] ?? 80,
            ];

            $result = $client->vdf($post);

            if ($result === false) {
                return ActionResult::failure('Failed to create domain forwarding rule');
            }

            if (isset($result['error']) && !empty($result['error'])) {
                $errorMsg = is_array($result['error']) ? implode(', ', $result['error']) : $result['error'];
                return ActionResult::failure('API error: ' . $errorMsg, ['error' => $result['error']]);
            }

            if (isset($result['done']) && $result['done']) {
                return ActionResult::success('Domain forwarding rule created successfully', [
                    'record_id' => $result['vdf_id'] ?? null,
                ]);
            }

            return ActionResult::failure('Failed to create domain forwarding rule');
        } catch (\Exception $e) {
            Log::error('Failed to create domain forwarding', [
                'server_id' => $server->id,
                'vps_id' => $vpsId,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return ActionResult::failure('Failed to create domain forwarding: ' . $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDomainForwarding(Server $server, int $vpsId, int $recordId): ActionResult
    {
        try {
            $client = $this->createClient($server);
            
            $post = [
                'svs' => $vpsId,
                'delete' => $recordId,
            ];

            $result = $client->vdf($post);

            if ($result === false) {
                return ActionResult::failure('Failed to delete domain forwarding rule');
            }

            if (isset($result['error']) && !empty($result['error'])) {
                $errorMsg = is_array($result['error']) ? implode(', ', $result['error']) : $result['error'];
                return ActionResult::failure('API error: ' . $errorMsg, ['error' => $result['error']]);
            }

            if (isset($result['done']) && $result['done']) {
                return ActionResult::success('Domain forwarding rule deleted successfully');
            }

            return ActionResult::failure('Failed to delete domain forwarding rule');
        } catch (\Exception $e) {
            Log::error('Failed to delete domain forwarding', [
                'server_id' => $server->id,
                'vps_id' => $vpsId,
                'record_id' => $recordId,
                'error' => $e->getMessage(),
            ]);

            return ActionResult::failure('Failed to delete domain forwarding: ' . $e->getMessage());
        }
    }
}
