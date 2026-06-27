<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add the "awaiting_approval" state between the automated check and the
     * human approval that finalises a property as "verified".
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE properties MODIFY status ENUM('draft','pending','awaiting_approval','verified','suspicious','rejected') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("UPDATE properties SET status = 'pending' WHERE status = 'awaiting_approval'");
        DB::statement("ALTER TABLE properties MODIFY status ENUM('draft','pending','verified','suspicious','rejected') NOT NULL DEFAULT 'pending'");
    }
};
