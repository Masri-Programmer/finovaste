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
      Schema::create('listings', function (Blueprint $table) {
    $table->id();
    $table->uuid()->unique();

    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('category_id')->constrained()->onDelete('cascade');
    $table->foreignId('address_id')->nullable()->constrained()->onDelete('set null');

    $table->morphs('listable');

    $table->json('title');
    $table->json('description');
    $table->string('slug')->unique();
    $table->string('type')->index();
    $table->string('currency', 3)->default('EUR');

    $table->enum('status', ['pending', 'rejected', 'active', 'expired', 'sold', 'completed', 'withdrawn'])
          ->default('pending')
          ->index();
          
    $table->boolean('is_featured')->default(false);
    $table->timestamp('published_at')->nullable();
    $table->timestamp('expires_at')->nullable();

    $table->enum('visibility', ['public', 'private'])->default('public');
    $table->unsignedInteger('views_count')->default(0);
    $table->unsignedInteger('likes_count')->default(0);
    $table->decimal('average_rating', 3, 1)->default(0);
    $table->unsignedInteger('reviews_count')->default(0);
    $table->unsignedInteger('comments_count')->default(0);
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
        Schema::dropIfExists('listings');
    }
};
