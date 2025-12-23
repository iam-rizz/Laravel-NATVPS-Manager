<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


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
            'user_id' => 'integer',
            'server_id' => 'integer',
            'vps_id' => 'integer',
            'ssh_port' => 'integer',
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
     * Check if the VPS is owned by the given user.
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->user_id !== null && $this->user_id === $user->id;
    }
}
