<?php

use App\Enums\AreaUnit;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table): void {
            $table->id();

            // Official parcel / deed number that also appears on the uploaded
            // documents. Unique within the registry and matched during OCR.
            $table->string('property_number')->unique();
            $table->string('title');
            $table->enum('type', array_column(PropertyType::cases(), 'value'));
            $table->text('description')->nullable();

            // Owner identity is stored as data (this is an officer-operated
            // registry, not a self-service portal).
            $table->string('owner_name');
            $table->string('owner_cnic', 20);
            $table->string('owner_contact')->nullable();

            // Location & measurement.
            $table->string('address');
            $table->string('city');
            $table->string('province');
            $table->decimal('area_value', 12, 2);
            $table->enum('area_unit', array_column(AreaUnit::cases(), 'value'));

            $table->enum('status', array_column(PropertyStatus::cases(), 'value'))
                ->default(PropertyStatus::Pending->value);

            // Staff member who registered the property.
            $table->foreignId('registered_by')->constrained('users')->cascadeOnDelete();

            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('city');
            $table->index('owner_cnic');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
