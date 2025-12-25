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
    
    $table->decimal('target', 15, 2);
    $table->decimal('raised', 15, 2)->default(0); 
    $table->unsignedInteger('donors_count')->default(0);
    
    $table->boolean('is_capped')->default(false);
    $table->boolean('requires_verification')->default(false);

    $table->timestamps();
    $table->softDeletes();
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
