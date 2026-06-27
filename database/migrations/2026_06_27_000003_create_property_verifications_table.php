<?php

use App\Enums\VerificationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Public users' ownership-verification attempts against an approved property.
     */
    public function up(): void
    {
        Schema::create('property_verifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            // Nullable: guests may verify without an account.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('image_disk')->default('local');
            $table->string('image_path');

            // Rule-based OCR comparison output and (optional) Gemini AI verdict.
            $table->json('ocr_data');
            $table->json('ai_result')->nullable();

            $table->enum('final_status', array_column(VerificationStatus::cases(), 'value'));

            $table->timestamps();

            $table->index(['property_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_verifications');
    }
};
