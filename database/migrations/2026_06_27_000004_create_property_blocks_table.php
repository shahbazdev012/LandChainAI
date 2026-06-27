<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The blockchain-inspired, append-only ledger. Each block captures an
     * immutable snapshot of a property at registration time and links to the
     * previous block via its hash, so chain integrity can be re-verified at
     * any point by recomputing hashes.
     */
    public function up(): void
    {
        Schema::create('property_blocks', function (Blueprint $table): void {
            $table->id();

            // Monotonic position within the chain (genesis = 1).
            $table->unsignedBigInteger('sequence')->unique();

            // One block per property in this MVP.
            $table->foreignId('property_id')->unique()->constrained()->cascadeOnDelete();

            $table->char('hash', 64)->unique();
            $table->char('previous_hash', 64)->index();

            // Canonical, hashed snapshot of the property data.
            $table->json('data');

            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_blocks');
    }
};
