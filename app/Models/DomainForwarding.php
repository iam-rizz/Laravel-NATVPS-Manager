<?php

namespace App\Models;

use App\Enums\DomainProtocol;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DomainForwarding extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nat_vps_id',
        'virtualizor_record_id',
        'domain',
        'protocol',
        'source_port',
        'destination_port',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'protocol' => DomainProtocol::class,
        ];
    }

    /**
     * Get the NAT VPS that this domain forwarding belongs to.
     */
    public function natVps(): BelongsTo
    {
        return $this->belongsTo(NatVps::class);
    }
}
