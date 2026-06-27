<?php

use App\Enums\VerificationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_verifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_id')->nullable()
                ->constrained('property_documents')->nullOnDelete();

            $table->enum('status', array_column(VerificationStatus::cases(), 'value'));
            $table->unsignedTinyInteger('score'); // 0-100 confidence

            // Structured rule results and OCR-extracted fields for auditability.
            $table->json('checks');
            $table->json('extracted')->nullable();
            $table->longText('ocr_text')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('run_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['property_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_verifications');
    }
};
