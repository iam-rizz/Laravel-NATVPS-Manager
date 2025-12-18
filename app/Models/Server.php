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
        'last_checked',
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
            'last_checked' => 'datetime',
        ];
    }

    /**
     * Get the NAT VPS instances belonging to this server.
     */
    public function natVps(): HasMany
    {
        return $this->hasMany(NatVps::class);
    }
}
