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
 * @property string $province
 * @property string $area_value
 * @property AreaUnit $area_unit
 * @property PropertyStatus $status
 * @property int $created_by
 * @property int|null $approved_by
 * @property Carbon|null $approved_at
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
        'created_by',
        'approved_by',
        'approved_at',
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
            'approved_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return HasMany<PropertyVerification, $this>
     */
    public function verifications(): HasMany
    {
        return $this->hasMany(PropertyVerification::class);
    }

    /**
     * @return HasOne<PropertyBlock, $this>
     */
    public function block(): HasOne
    {
        return $this->hasOne(PropertyBlock::class);
    }

    public function isApproved(): bool
    {
        return $this->status === PropertyStatus::Approved;
    }

    /**
     * Staff-side free-text search.
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
                ->orWhere('city', 'like', $like);
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

    /**
     * Public lookup: approved records only, filtered by the given criteria.
     * Returns no rows unless at least one filter is supplied.
     *
     * @param  Builder<Property>  $query
     * @param  array<string, string|null>  $filters
     * @return Builder<Property>
     */
    public function scopePublicSearch(Builder $query, array $filters): Builder
    {
        $map = [
            'owner_cnic' => 'owner_cnic',
            'owner_name' => 'owner_name',
            'property_number' => 'property_number',
            'city' => 'city',
            'province' => 'province',
        ];

        $applied = false;

        foreach ($map as $key => $column) {
            $value = trim((string) ($filters[$key] ?? ''));

            if ($value !== '') {
                $query->where($column, 'like', '%'.$value.'%');
                $applied = true;
            }
        }

        if (! $applied) {
            $query->whereRaw('1 = 0');
        }

        return $query->where('status', PropertyStatus::Approved->value);
    }
}
