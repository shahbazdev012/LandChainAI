<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * A single, immutable block in the property hash chain.
 *
 * @property int $id
 * @property int $sequence
 * @property int $property_id
 * @property string $hash
 * @property string $previous_hash
 * @property array<string, mixed> $data
 * @property Carbon|null $created_at
 */
class PropertyBlock extends Model
{
    /**
     * Genesis predecessor hash for the very first block in the chain.
     */
    public const string GENESIS_HASH = '0000000000000000000000000000000000000000000000000000000000000000';

    public $timestamps = false;

    protected $fillable = [
        'sequence',
        'property_id',
        'hash',
        'previous_hash',
        'data',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Property, $this>
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
