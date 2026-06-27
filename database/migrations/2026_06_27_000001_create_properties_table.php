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

            // Official parcel / plot number. Unique within the registry and the
            // value a public user's document is matched against.
            $table->string('property_number')->unique();
            $table->string('title');
            $table->enum('type', array_column(PropertyType::cases(), 'value'));
            $table->text('description')->nullable();

            // Owner identity is stored as ground-truth data.
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
                ->default(PropertyStatus::PendingApproval->value);

            // Data Entry staff who created the record.
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            // Officer/Admin who approved it (set on approval).
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

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
