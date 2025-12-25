<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any listings without a UUID
        DB::table('listings')
            ->whereNull('uuid')
            ->orWhere('uuid', '')
            ->chunkById(100, function ($listings) {
                foreach ($listings as $listing) {
                    DB::table('listings')
                        ->where('id', $listing->id)
                        ->update(['uuid' => (string) Str::uuid()]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback needed - UUIDs can stay
    }
};
