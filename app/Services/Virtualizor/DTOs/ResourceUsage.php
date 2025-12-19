<?php

namespace App\Services\Virtualizor\DTOs;

class ResourceUsage
{
    public function __construct(
        public readonly ?CpuUsage $cpu = null,
        public readonly ?RamUsage $ram = null,
        public readonly ?DiskUsage $disk = null,
        public readonly ?BandwidthUsage $bandwidth = null,
        public readonly ?array $rawData = null
    ) {}

    public static function fromApiResponse(array $monitorData, array $bandwidthData = []): self
    {
        return new self(
            cpu: isset($monitorData['cpu']['cpu']) ? CpuUsage::fromArray($monitorData['cpu']['cpu']) : null,
            ram: isset($monitorData['ram']) ? RamUsage::fromArray($monitorData['ram']) : null,
            disk: isset($monitorData['disk']['disk']) ? DiskUsage::fromArray($monitorData['disk']['disk'], $monitorData['disk']['inodes'] ?? []) : null,
            bandwidth: !empty($bandwidthData) ? BandwidthUsage::fromArray($bandwidthData) : null,
            rawData: ['monitor' => $monitorData, 'bandwidth' => $bandwidthData]
        );
    }

    public function toArray(): array
    {
        return [
            'cpu' => $this->cpu?->toArray(),
            'ram' => $this->ram?->toArray(),
            'disk' => $this->disk?->toArray(),
            'bandwidth' => $this->bandwidth?->toArray(),
        ];
    }
}

class CpuUsage
{
    public function __construct(
        public readonly float $limit,
        public readonly float $used,
        public readonly float $free,
        public readonly float $percent,
        public readonly float $percentFree,
        public readonly ?string $manufacturer = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            limit: (float) ($data['limit'] ?? 0),
            used: (float) ($data['used'] ?? 0),
            free: (float) ($data['free'] ?? 0),
            percent: (float) ($data['percent'] ?? 0),
            percentFree: (float) ($data['percent_free'] ?? 100),
            manufacturer: $data['manu'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'limit' => $this->limit,
            'used' => $this->used,
            'free' => $this->free,
            'percent' => $this->percent,
            'percent_free' => $this->percentFree,
            'manufacturer' => $this->manufacturer,
        ];
    }

    public function getColorClass(): string
    {
        if ($this->percent <= 60) {
            return 'bg-green-500';
        }
        if ($this->percent <= 80) {
            return 'bg-yellow-500';
        }
        return 'bg-red-500';
    }
}

class RamUsage
{
    public function __construct(
        public readonly int $limit,
        public readonly int $used,
        public readonly int $free,
        public readonly float $percent,
        public readonly float $percentFree,
        public readonly int $guaranteed,
        public readonly int $swap
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            limit: (int) ($data['limit'] ?? 0),
            used: (int) ($data['used'] ?? 0),
            free: (int) ($data['free'] ?? 0),
            percent: (float) ($data['percent'] ?? 0),
            percentFree: (float) ($data['percent_free'] ?? 100),
            guaranteed: (int) ($data['guaranteed'] ?? 0),
            swap: (int) ($data['swap'] ?? 0)
        );
    }

    public function toArray(): array
    {
        return [
            'limit' => $this->limit,
            'used' => $this->used,
            'free' => $this->free,
            'percent' => $this->percent,
            'percent_free' => $this->percentFree,
            'guaranteed' => $this->guaranteed,
            'swap' => $this->swap,
        ];
    }

    public function getUsedGb(): float
    {
        return round($this->used / 1024, 2);
    }

    public function getLimitGb(): float
    {
        return round($this->limit / 1024, 2);
    }

    public function getColorClass(): string
    {
        if ($this->percent <= 60) {
            return 'bg-green-500';
        }
        if ($this->percent <= 80) {
            return 'bg-yellow-500';
        }
        return 'bg-red-500';
    }
}

class DiskUsage
{
    public function __construct(
        public readonly float $limitGb,
        public readonly float $usedGb,
        public readonly float $freeGb,
        public readonly float $percent,
        public readonly float $percentFree,
        public readonly ?InodeUsage $inodes = null
    ) {}

    public static function fromArray(array $data, array $inodesData = []): self
    {
        return new self(
            limitGb: (float) ($data['limit_gb'] ?? ($data['limit'] / 1024)),
            usedGb: (float) ($data['used_gb'] ?? ($data['used'] / 1024)),
            freeGb: (float) ($data['free_gb'] ?? ($data['free'] / 1024)),
            percent: (float) ($data['percent'] ?? 0),
            percentFree: (float) ($data['percent_free'] ?? 100),
            inodes: !empty($inodesData) ? InodeUsage::fromArray($inodesData) : null
        );
    }

    public function toArray(): array
    {
        return [
            'limit_gb' => $this->limitGb,
            'used_gb' => $this->usedGb,
            'free_gb' => $this->freeGb,
            'percent' => $this->percent,
            'percent_free' => $this->percentFree,
            'inodes' => $this->inodes?->toArray(),
        ];
    }

    public function getColorClass(): string
    {
        if ($this->percent <= 60) {
            return 'bg-green-500';
        }
        if ($this->percent <= 80) {
            return 'bg-yellow-500';
        }
        return 'bg-red-500';
    }
}

class InodeUsage
{
    public function __construct(
        public readonly int $limit,
        public readonly int $used,
        public readonly int $free,
        public readonly float $percent,
        public readonly float $percentFree
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            limit: (int) ($data['limit'] ?? 0),
            used: (int) ($data['used'] ?? 0),
            free: (int) ($data['free'] ?? 0),
            percent: (float) ($data['percent'] ?? 0),
            percentFree: (float) ($data['percent_free'] ?? 100)
        );
    }

    public function toArray(): array
    {
        return [
            'limit' => $this->limit,
            'used' => $this->used,
            'free' => $this->free,
            'percent' => $this->percent,
            'percent_free' => $this->percentFree,
        ];
    }
}

class BandwidthUsage
{
    public function __construct(
        public readonly float $limitGb,
        public readonly float $usedGb,
        public readonly float $freeGb,
        public readonly float $percent,
        public readonly float $percentFree,
        public readonly ?array $dailyUsage = null,
        public readonly ?array $dailyIn = null,
        public readonly ?array $dailyOut = null,
        public readonly ?array $yearlyUsage = null
    ) {}

    public static function fromArray(array $data): self
    {
        // Process yearly bandwidth data
        $yearlyUsage = null;
        if (isset($data['yr_bandwidth']) && is_array($data['yr_bandwidth'])) {
            $yearlyUsage = [];
            foreach ($data['yr_bandwidth'] as $item) {
                if (isset($item['date'])) {
                    $yearlyUsage[$item['date']] = [
                        'in' => (float) ($item['in'] ?? 0),
                        'out' => (float) ($item['out'] ?? 0),
                        'total' => (float) (($item['in'] ?? 0) + ($item['out'] ?? 0)),
                    ];
                }
            }
            ksort($yearlyUsage);
        }

        return new self(
            limitGb: (float) ($data['limit_gb'] ?? ($data['limit'] / 1024 / 1024)),
            usedGb: (float) ($data['used_gb'] ?? ($data['used'] / 1024)),
            freeGb: (float) ($data['free_gb'] ?? ($data['free'] / 1024 / 1024)),
            percent: (float) ($data['percent'] ?? 0),
            percentFree: (float) ($data['percent_free'] ?? 100),
            dailyUsage: $data['usage'] ?? null,
            dailyIn: $data['in'] ?? null,
            dailyOut: $data['out'] ?? null,
            yearlyUsage: $yearlyUsage
        );
    }

    public function toArray(): array
    {
        return [
            'limit_gb' => $this->limitGb,
            'used_gb' => $this->usedGb,
            'free_gb' => $this->freeGb,
            'percent' => $this->percent,
            'percent_free' => $this->percentFree,
            'daily_usage' => $this->dailyUsage,
            'daily_in' => $this->dailyIn,
            'daily_out' => $this->dailyOut,
            'yearly_usage' => $this->yearlyUsage,
        ];
    }

    public function getColorClass(): string
    {
        if ($this->percent <= 60) {
            return 'bg-green-500';
        }
        if ($this->percent <= 80) {
            return 'bg-yellow-500';
        }
        return 'bg-red-500';
    }
}
