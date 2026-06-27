<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DocumentType;
use Database\Factories\PropertyDocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Number;

/**
 * @property int $id
 * @property int $property_id
 * @property DocumentType $type
 * @property string $disk
 * @property string $path
 * @property string $original_name
 * @property string $mime_type
 * @property int $size
 * @property string $file_hash
 * @property int|null $uploaded_by
 */
class PropertyDocument extends Model
{
    /** @use HasFactory<PropertyDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'file_hash',
        'uploaded_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'size' => 'integer',
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
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function humanSize(): string
    {
        return Number::fileSize($this->size);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }
}
