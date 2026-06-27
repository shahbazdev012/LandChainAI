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
 * @property int|null $document_id
 * @property VerificationStatus $status
 * @property int $score
 * @property array<int, array{key: string, label: string, passed: bool, message: string}> $checks
 * @property array<string, mixed>|null $extracted
 * @property string|null $ocr_text
 * @property string|null $notes
 * @property int|null $run_by
 */
class PropertyVerification extends Model
{
    /** @use HasFactory<PropertyVerificationFactory> */
    use HasFactory;

    protected $fillable = [
        'document_id',
        'status',
        'score',
        'checks',
        'extracted',
        'ocr_text',
        'notes',
        'run_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => VerificationStatus::class,
            'score' => 'integer',
            'checks' => 'array',
            'extracted' => 'array',
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
     * @return BelongsTo<PropertyDocument, $this>
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(PropertyDocument::class, 'document_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function runBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'run_by');
    }
}
