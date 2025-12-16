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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('listing_id')
                ->constrained('listings')
                ->onDelete('cascade');

            $table->decimal('amount', 12, 2);

            $table->enum('status', ['active', 'retracted', 'outbid', 'won'])
                ->default('active');

            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index(['listing_id', 'amount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
