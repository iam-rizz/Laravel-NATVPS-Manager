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
            false // is_admin = false for enduser API access
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

            // Log the full response for debugging
            // Log::debug('Virtualizor listvs response', ['result' => $result]);

            // Count VPS - check both 'vs' and 'vps' keys
            $vpsCount = 0;
            if (isset($result['vs']) && is_array($result['vs'])) {
                $vpsCount = count($result['vs']);
            } elseif (isset($result['vps']) && is_array($result['vps'])) {
                $vpsCount = count($result['vps']);
            }

            return ConnectionResult::success('Connection successful', [
                'vps_count' => $vpsCount,
                'raw_keys' => array_keys($result),
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

            if ($result === false) {
                return null;
            }

            // Check for 'info' key (enduser API response) or 'vps' key
            $vpsData = $result['info'] ?? $result['vps'] ?? null;
            
            if (empty($vpsData)) {
                return null;
            }

            return VpsInfo::fromApiResponse($vpsData);
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
        $data = $this->getDomainForwardingWithConfig($server, $vpsId);
        return $data['forwardings'] ?? [];
    }

    /**
     * Get domain forwarding with server config (port restrictions).
     */
    public function getDomainForwardingWithConfig(Server $server, int $vpsId): array
    {
        try {
            $client = $this->createClient($server);
            $result = $client->vdf(['svs' => $vpsId]);

            if ($result === false) {
                return ['forwardings' => [], 'config' => []];
            }

            // Get forwarding records
            $forwardings = [];
            if (isset($result['haproxydata']) && is_array($result['haproxydata'])) {
                $forwardings = array_values($result['haproxydata']);
            } elseif (isset($result['vdf']) && is_array($result['vdf'])) {
                $forwardings = array_values($result['vdf']);
            }

            // Get port configuration from server_haconfigs
            $config = [];
            $srcIps = $result['arr_haproxy_src_ips'] ?? [];
            if (isset($result['server_haconfigs']) && is_array($result['server_haconfigs'])) {
                $haConfig = $result['server_haconfigs'][0] ?? [];
                $config = [
                    'reserved_ports' => $haConfig['haproxy_reservedports'] ?? '',
                    'reserved_ports_http' => $haConfig['haproxy_reservedports_http'] ?? '',
                    'allowed_ports' => $haConfig['haproxy_allowedports'] ?? '',
                    'src_ips' => $srcIps,
                ];
            }

            // Get dest_ip from vpses data
            $destIp = '';
            if (isset($result['vpses']) && is_array($result['vpses'])) {
                foreach ($result['vpses'] as $vps) {
                    if (isset($vps['ips']) && is_array($vps['ips'])) {
                        foreach ($vps['ips'] as $ipData) {
                            $ip = $ipData['ip'] ?? $ipData;
                            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && strpos($ip, '10.') === 0) {
                                $destIp = $ip;
                                break 2;
                            }
                        }
                    }
                }
            }

            return [
                'forwardings' => $forwardings,
                'config' => $config,
                'src_ip' => $srcIps[0] ?? '',
                'dest_ip' => $destIp,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get domain forwarding', [
                'server_id' => $server->id,
                'vps_id' => $vpsId,
                'error' => $e->getMessage(),
            ]);

            return ['forwardings' => [], 'config' => []];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createDomainForwarding(Server $server, int $vpsId, array $data): ActionResult
    {
        try {
            $client = $this->createClient($server);
            
            $protocol = strtoupper($data['protocol'] ?? 'HTTP');
            
            // Based on Virtualizor API docs: https://www.virtualizor.com/docs/enduser-api/add-domain-forwarding/
            // First get VDF config to get src_ip and dest_ip
            $vdfData = $this->getDomainForwardingWithConfig($server, $vpsId);
            $srcIp = $vdfData['src_ip'] ?? '';
            $destIp = $vdfData['dest_ip'] ?? '';

            // For TCP, src_hostname should be the haproxy source IP
            // For HTTP/HTTPS, src_hostname should be the domain
            $srcHostname = $data['domain'] ?? '';
            if ($protocol === 'TCP' && empty($srcHostname)) {
                $srcHostname = $srcIp;
            }

            $post = [
                'svs' => $vpsId,
                'vdf_action' => 'addvdf',
                'src_hostname' => $srcHostname,
                'protocol' => $protocol,
                'src_port' => (string) ($data['source_port'] ?? 80),
                'dest_ip' => $destIp,
                'dest_port' => (string) ($data['destination_port'] ?? 80),
            ];

            Log::debug('Creating VDF rule', ['post' => $post]);

            $result = $client->vdf($post);

            Log::debug('VDF create response', ['result' => $result]);

            if ($result === false) {
                return ActionResult::failure('Failed to create domain forwarding rule');
            }

            if (isset($result['error']) && !empty($result['error'])) {
                $errorMsg = is_array($result['error']) 
                    ? (is_array($result['error']['action'] ?? null) ? implode(', ', $result['error']) : ($result['error']['action'] ?? implode(', ', $result['error'])))
                    : $result['error'];
                return ActionResult::failure('API error: ' . $errorMsg, ['error' => $result['error']]);
            }

            if (isset($result['done']) && $result['done']) {
                return ActionResult::success('Domain forwarding rule created successfully', [
                    'record_id' => $result['vdf_id'] ?? $result['newid'] ?? null,
                ]);
            }

            // Check if haproxydata was updated (success indicator)
            if (isset($result['haproxydata'])) {
                return ActionResult::success('Domain forwarding rule created successfully');
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
            
            // Based on Virtualizor API docs for delete VDF
            $post = [
                'svs' => $vpsId,
                'vdf_action' => 'delvdf',
                'vdfid' => $recordId,
            ];

            Log::debug('Deleting VDF rule', ['post' => $post]);

            $result = $client->vdf($post);

            Log::debug('VDF delete response', ['result' => $result]);

            if ($result === false) {
                return ActionResult::failure('Failed to delete domain forwarding rule');
            }

            if (isset($result['error']) && !empty($result['error'])) {
                $errorMsg = is_array($result['error']) 
                    ? ($result['error']['action'] ?? implode(', ', $result['error']))
                    : $result['error'];
                return ActionResult::failure('API error: ' . $errorMsg, ['error' => $result['error']]);
            }

            if (isset($result['done']) && $result['done']) {
                return ActionResult::success('Domain forwarding rule deleted successfully');
            }

            // Check if the record is no longer in haproxydata (success indicator)
            if (isset($result['haproxydata']) && !isset($result['haproxydata'][$recordId])) {
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

    /**
     * Update a domain forwarding rule.
     */
    public function updateDomainForwarding(Server $server, int $vpsId, int $recordId, array $data): ActionResult
    {
        try {
            $client = $this->createClient($server);
            
            $protocol = strtoupper($data['protocol'] ?? 'TCP');
            
            // Get VDF config to get src_ip and dest_ip
            $vdfData = $this->getDomainForwardingWithConfig($server, $vpsId);
            $srcIp = $vdfData['src_ip'] ?? '';
            $destIp = $vdfData['dest_ip'] ?? '';

            // For TCP, src_hostname should be the haproxy source IP
            // For HTTP/HTTPS, src_hostname should be the domain
            $srcHostname = $data['domain'] ?? '';
            if ($protocol === 'TCP' && empty($srcHostname)) {
                $srcHostname = $srcIp;
            }

            $post = [
                'svs' => $vpsId,
                'vdf_action' => 'editvdf',
                'vdfid' => $recordId,
                'src_hostname' => $srcHostname,
                'protocol' => $protocol,
                'src_port' => (string) ($data['source_port'] ?? 80),
                'dest_ip' => $destIp,
                'dest_port' => (string) ($data['destination_port'] ?? 80),
            ];

            Log::debug('Updating VDF rule', ['post' => $post]);

            $result = $client->vdf($post);

            Log::debug('VDF update response', ['result' => $result]);

            if ($result === false) {
                return ActionResult::failure('Failed to update domain forwarding rule');
            }

            if (isset($result['error']) && !empty($result['error'])) {
                $errorMsg = is_array($result['error']) 
                    ? ($result['error']['action'] ?? implode(', ', $result['error']))
                    : $result['error'];
                return ActionResult::failure('API error: ' . $errorMsg, ['error' => $result['error']]);
            }

            if (isset($result['done']) && $result['done']) {
                return ActionResult::success('Domain forwarding rule updated successfully');
            }

            // Check if haproxydata exists (success indicator)
            if (isset($result['haproxydata'])) {
                return ActionResult::success('Domain forwarding rule updated successfully');
            }

            return ActionResult::failure('Failed to update domain forwarding rule');
        } catch (\Exception $e) {
            Log::error('Failed to update domain forwarding', [
                'server_id' => $server->id,
                'vps_id' => $vpsId,
                'record_id' => $recordId,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return ActionResult::failure('Failed to update domain forwarding: ' . $e->getMessage());
        }
    }
}
