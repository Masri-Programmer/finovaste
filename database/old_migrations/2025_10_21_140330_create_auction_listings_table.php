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
        Schema::create('auction_listings', function (Blueprint $table) {
            $table->id();
            $table->decimal('start_price', 10, 2);
            $table->decimal('reserve_price', 10, 2)->nullable();
            $table->decimal('buy_it_now_price', 10, 2)->nullable();
            $table->decimal('current_bid', 10, 2)->nullable();

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_listings');
    }
};
