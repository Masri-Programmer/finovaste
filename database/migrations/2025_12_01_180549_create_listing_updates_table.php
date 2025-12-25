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
    Schema::create('listing_updates', function (Blueprint $table) {
        $table->id();
        $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
        $table->string('title')->nullable();
        $table->text('content')->nullable()->change();
        $table->string('type')->default('general');
        $table->string('translation_key')->nullable();
        $table->json('attributes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_updates');
    }
};
