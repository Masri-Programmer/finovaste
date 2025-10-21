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
        Schema::create('component_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('component_id')->constrained('components')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('component_items')->onDelete('cascade');

            $table->string('translation_key')->nullable()->unique();
            $table->string('display_text')->nullable();
            $table->string('item_type')->default('link');

            $table->string('value')->nullable();
            $table->text('content')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_items');
    }
};
