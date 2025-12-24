<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'ip_address',
        'api_key',
        'api_pass',
        'port',
        'is_active',
        'location_data',
        'location_cached_at',
        'last_checked',
        'last_check_status',
        'last_check_error',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'api_pass' => 'encrypted',
            'is_active' => 'boolean',
            'location_data' => 'array',
            'location_cached_at' => 'datetime',
            'last_checked' => 'datetime',
            'last_check_status' => 'string',
        ];
    }

    /**
     * Get the NAT VPS instances belonging to this server.
     */
    public function natVps(): HasMany
    {
        return $this->hasMany(NatVps::class);
    }

    /**
     * Get location display string.
     */
    public function getLocationString(): ?string
    {
        if (!$this->location_data) {
            return null;
        }

        $country = $this->location_data['country'] ?? null;
        $countryCode = $this->location_data['country_code'] ?? null;

        if ($country && $countryCode) {
            return "{$country} / {$countryCode}";
        }

        return $country ?? $countryCode ?? null;
    }

    /**
     * Check if location has map coordinates.
     */
    public function hasMapCoordinates(): bool
    {
        return $this->location_data
            && !empty($this->location_data['latitude'])
            && !empty($this->location_data['longitude']);
    }

    /**
     * Get health status for display.
     *
     * @return string 'online'|'offline'|'unchecked'
     */
    public function getHealthStatus(): string
    {
        if ($this->last_check_status === null) {
            return 'unchecked';
        }

        return $this->last_check_status === 'success' ? 'online' : 'offline';
    }
}
