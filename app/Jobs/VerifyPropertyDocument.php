<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\Verification\RunPropertyVerification;
use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Queued wrapper around {@see RunPropertyVerification}. Used when OCR should run
 * out of the request lifecycle (config: verification.async).
 */
class VerifyPropertyDocument implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $propertyId,
        public ?int $documentId = null,
        public ?int $runnerId = null,
    ) {}

    public function handle(RunPropertyVerification $action): void
    {
        $property = Property::query()->findOrFail($this->propertyId);
        $document = $this->documentId ? PropertyDocument::query()->find($this->documentId) : null;
        $runner = $this->runnerId ? User::query()->find($this->runnerId) : null;

        $action->handle($property, $document, $runner);
    }
}
