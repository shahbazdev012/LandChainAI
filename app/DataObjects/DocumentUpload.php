<?php

declare(strict_types=1);

namespace App\DataObjects;

use App\Enums\DocumentType;
use Illuminate\Http\UploadedFile;

final readonly class DocumentUpload
{
    public function __construct(
        public UploadedFile $file,
        public DocumentType $type,
    ) {}
}
