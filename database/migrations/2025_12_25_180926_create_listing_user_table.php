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
        Schema::create('listing_user', function (Blueprint $table) {
            $table->primary(['listing_id', 'user_id']);

            // The Listing being liked
            $table->foreignId('listing_id')
                  ->constrained('listings')
                  ->onDelete('cascade');

            // The User who liked it
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Timestamps are required because your model has ->withTimestamps()
            $table->timestamps(); 

            // Unique constraint: A user cannot like the same listing twice
            $table->unique(['listing_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_user');
    }
};