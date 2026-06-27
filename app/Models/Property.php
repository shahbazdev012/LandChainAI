<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AreaUnit;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $property_number
 * @property string $title
 * @property PropertyType $type
 * @property string|null $description
 * @property string $owner_name
 * @property string $owner_cnic
 * @property string|null $owner_contact
 * @property string $address
 * @property string $city
 * @property string|null $province
 * @property string $area_value
 * @property AreaUnit $area_unit
 * @property PropertyStatus $status
 * @property int $registered_by
 * @property Carbon|null $verified_at
 */
class Property extends Model
{
    /** @use HasFactory<PropertyFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_number',
        'title',
        'type',
        'description',
        'owner_name',
        'owner_cnic',
        'owner_contact',
        'address',
        'city',
        'province',
        'area_value',
        'area_unit',
        'status',
        'registered_by',
        'verified_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => PropertyType::class,
            'area_unit' => AreaUnit::class,
            'status' => PropertyStatus::class,
            'area_value' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /**
     * @return HasMany<PropertyDocument, $this>
     */
    public function documents(): HasMany
    {
        return $this->hasMany(PropertyDocument::class);
    }

    /**
     * @return HasMany<PropertyVerification, $this>
     */
    public function verifications(): HasMany
    {
        return $this->hasMany(PropertyVerification::class);
    }

    /**
     * @return HasOne<PropertyVerification, $this>
     */
    public function latestVerification(): HasOne
    {
        return $this->hasOne(PropertyVerification::class)->latestOfMany();
    }

    /**
     * @return HasOne<PropertyBlock, $this>
     */
    public function block(): HasOne
    {
        return $this->hasOne(PropertyBlock::class);
    }

    public function isVerified(): bool
    {
        return $this->status === PropertyStatus::Verified;
    }

    /**
     * Free-text search across the human-meaningful columns.
     *
     * @param  Builder<Property>  $query
     * @return Builder<Property>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function (Builder $query) use ($like): void {
            $query->where('property_number', 'like', $like)
                ->orWhere('title', 'like', $like)
                ->orWhere('owner_name', 'like', $like)
                ->orWhere('owner_cnic', 'like', $like)
                ->orWhere('city', 'like', $like)
                ->orWhere('address', 'like', $like);
        });
    }

    /**
     * @param  Builder<Property>  $query
     * @return Builder<Property>
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }
}
