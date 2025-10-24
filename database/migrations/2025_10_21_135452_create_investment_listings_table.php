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
        Schema::create('investment_listings', function (Blueprint $table) {
            $table->id();
            $table->decimal('investment_goal', 15, 2);
            $table->decimal('minimum_investment', 10, 2)->default(0);
            $table->unsignedInteger('shares_offered');
            $table->decimal('share_price', 10, 2);
            $table->decimal('amount_raised', 15, 2)->default(0);
            $table->unsignedInteger('investors_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
