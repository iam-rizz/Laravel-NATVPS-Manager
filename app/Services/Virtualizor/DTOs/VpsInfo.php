<?php

namespace App\Services\Virtualizor\DTOs;

class VpsInfo
{
    public function __construct(
        public readonly int $vpsId,
        public readonly ?string $uuid = null,
        public readonly ?string $hostname = null,
        public readonly ?int $cpu = null,
        public readonly ?int $ram = null,
        public readonly ?int $disk = null,
        public readonly ?int $bandwidth = null,
        public readonly ?int $usedBandwidth = null,
        public readonly ?int $status = null,
        public readonly ?array $ips = null,
        public readonly ?string $osName = null,
        public readonly ?string $osDistro = null,
        public readonly ?array $mapAddress = null,
        public readonly ?array $rawData = null
    ) {}

    /**
     * Create VpsInfo from Virtualizor API response.
     */
    public static function fromApiResponse(array $data): self
    {
        // Handle nested 'vps' structure from vpsinfo() API response
        // The vpsinfo() API returns specs inside $data['vps'] while status/hostname are at root level
        $vpsData = $data['vps'] ?? [];
        
        // Merge vps data with root data, preferring vps data for specs
        $cores = $vpsData['cores'] ?? $data['cores'] ?? null;
        $ram = $vpsData['ram'] ?? $data['ram'] ?? null;
        $space = $vpsData['space'] ?? $data['space'] ?? $data['disk'] ?? null;
        
        // Handle bandwidth - can be array from vpsinfo() or integer from listvs()
        $bandwidthData = $data['bandwidth'] ?? $vpsData['bandwidth'] ?? null;
        $bandwidth = null;
        $usedBandwidth = null;
        
        if (is_array($bandwidthData)) {
            // vpsinfo() returns bandwidth as array with limit_gb and used_gb
            $bandwidth = $bandwidthData['limit_gb'] ?? ($bandwidthData['limit'] ? (int)($bandwidthData['limit'] / 1024) : null);
            $usedBandwidth = isset($bandwidthData['used_gb']) ? (int) round($bandwidthData['used_gb']) : null;
        } else {
            // listvs() returns bandwidth as integer
            $bandwidth = $bandwidthData;
            $usedBandwidth = $vpsData['used_bandwidth'] ?? $data['used_bandwidth'] ?? null;
        }
        
        // OS info
        $osName = $vpsData['os_name'] ?? $data['os_name'] ?? $data['os'] ?? null;
        $osDistro = $vpsData['os_distro'] ?? $data['os_distro'] ?? $data['distro'] ?? null;
        
        // Map/Location info
        $mapAddress = $data['map_address'] ?? $vpsData['map_address'] ?? null;
        
        return new self(
            vpsId: (int) ($data['vpsid'] ?? $vpsData['vpsid'] ?? $data['vid'] ?? 0),
            uuid: $vpsData['uuid'] ?? $data['uuid'] ?? $data['vps_uuid'] ?? null,
            hostname: $data['hostname'] ?? $vpsData['hostname'] ?? $data['vps_name'] ?? null,
            cpu: isset($cores) ? (int) $cores : null,
            ram: isset($ram) ? (int) $ram : null,
            disk: isset($space) ? (int) $space : null,
            bandwidth: isset($bandwidth) ? (int) $bandwidth : null,
            usedBandwidth: isset($usedBandwidth) ? (int) $usedBandwidth : null,
            status: isset($data['status']) ? (int) $data['status'] : null,
            ips: $data['ips'] ?? $data['ip'] ?? $vpsData['ips'] ?? null,
            osName: $osName,
            osDistro: $osDistro,
            mapAddress: $mapAddress,
            rawData: $data
        );
    }

    /**
     * Convert to array for caching.
     */
    public function toArray(): array
    {
        return [
            'vps_id' => $this->vpsId,
            'uuid' => $this->uuid,
            'hostname' => $this->hostname,
            'cpu' => $this->cpu,
            'ram' => $this->ram,
            'disk' => $this->disk,
            'bandwidth' => $this->bandwidth,
            'used_bandwidth' => $this->usedBandwidth,
            'status' => $this->status,
            'ips' => $this->ips,
            'os_name' => $this->osName,
            'os_distro' => $this->osDistro,
            'map_address' => $this->mapAddress,
        ];
    }

    /**
     * Get OS logo/icon name based on distro.
     */
    public function getOsIcon(): string
    {
        $distro = strtolower($this->osDistro ?? $this->osName ?? '');
        
        return match (true) {
            str_contains($distro, 'ubuntu') => 'ubuntu',
            str_contains($distro, 'debian') => 'debian',
            str_contains($distro, 'centos') => 'centos',
            str_contains($distro, 'rocky') => 'rocky',
            str_contains($distro, 'alma') => 'almalinux',
            str_contains($distro, 'fedora') => 'fedora',
            str_contains($distro, 'arch') => 'arch',
            str_contains($distro, 'windows') => 'windows',
            str_contains($distro, 'freebsd') => 'freebsd',
            str_contains($distro, 'opensuse'), str_contains($distro, 'suse') => 'opensuse',
            str_contains($distro, 'rhel'), str_contains($distro, 'redhat') => 'redhat',
            default => 'linux',
        };
    }

    /**
     * Get location display string.
     */
    public function getLocationString(): ?string
    {
        if (!$this->mapAddress) {
            return null;
        }
        
        $parts = array_filter([
            $this->mapAddress['city'] ?? null,
            $this->mapAddress['country_code'] ?? null,
        ]);
        
        return !empty($parts) ? implode(', ', $parts) : null;
    }
}
