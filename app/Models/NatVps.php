<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NatVps extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nat_vps';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'server_id',
        'user_id',
        'vps_id',
        'hostname',
        'ssh_username',
        'ssh_password',
        'ssh_port',
        'cached_specs',
        'specs_cached_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ssh_username' => 'encrypted',
            'ssh_password' => 'encrypted',
            'cached_specs' => 'array',
            'specs_cached_at' => 'datetime',
        ];
    }

    /**
     * Get the server that this NAT VPS belongs to.
     */
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Get the user that this NAT VPS is assigned to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the domain forwarding rules for this NAT VPS.
     */
    public function domainForwardings(): HasMany
    {
        return $this->hasMany(DomainForwarding::class);
    }
}
