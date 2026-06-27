<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\VerificationStatus;
use Database\Factories\PropertyVerificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $property_id
 * @property int|null $user_id
 * @property string $image_disk
 * @property string $image_path
 * @property array<string, mixed> $ocr_data
 * @property array<string, mixed>|null $ai_result
 * @property VerificationStatus $final_status
 */
class PropertyVerification extends Model
{
    /** @use HasFactory<PropertyVerificationFactory> */
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'image_disk',
        'image_path',
        'ocr_data',
        'ai_result',
        'final_status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ocr_data' => 'array',
            'ai_result' => 'array',
            'final_status' => VerificationStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Property, $this>
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
