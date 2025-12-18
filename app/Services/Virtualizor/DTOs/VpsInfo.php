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
        public readonly ?array $rawData = null
    ) {}

    /**
     * Create VpsInfo from Virtualizor API response.
     */
    public static function fromApiResponse(array $data): self
    {
        return new self(
            vpsId: (int) ($data['vpsid'] ?? $data['vid'] ?? 0),
            uuid: $data['uuid'] ?? $data['vps_uuid'] ?? null,
            hostname: $data['hostname'] ?? $data['vps_name'] ?? null,
            cpu: isset($data['cores']) ? (int) $data['cores'] : (isset($data['cpus']) ? (int) $data['cpus'] : null),
            ram: isset($data['ram']) ? (int) $data['ram'] : null,
            disk: isset($data['space']) ? (int) $data['space'] : (isset($data['disk']) ? (int) $data['disk'] : null),
            bandwidth: isset($data['bandwidth']) ? (int) $data['bandwidth'] : null,
            usedBandwidth: isset($data['used_bandwidth']) ? (int) $data['used_bandwidth'] : null,
            status: isset($data['status']) ? (int) $data['status'] : null,
            ips: $data['ips'] ?? $data['ip'] ?? null,
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
        ];
    }
}
