<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('donation_listings', function (Blueprint $table) {
            $table->id();
            $table->decimal('donation_goal', 15, 2);
            $table->decimal('amount_raised', 15, 2)->default(0);
            $table->unsignedInteger('donors_count')->default(0);
            $table->boolean('is_goal_flexible')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donation_listings');
    }
};
