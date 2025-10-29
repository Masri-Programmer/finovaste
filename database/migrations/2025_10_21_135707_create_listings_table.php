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
            // CORE IDENTIFIERS
            $table->id();
            $table->uuid()->unique();

            // RELATIONSHIPS
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('address_id')->nullable()->constrained('addresses')->onDelete('set null');

            // ESSENTIAL DETAILS
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');

            // LISTING MANAGEMENT
            $table->enum('status', ['pending', 'rejected', 'active', 'expired', 'sold', 'withdrawn'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->string('listable_type');
            $table->unsignedBigInteger('listable_id');
            $table->index(['listable_type', 'listable_id']);

            // SOCIAL COUNTERS & METADATA
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('likes_count')->default(0);
            $table->decimal('average_rating', 2, 1)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->json('meta')->nullable();

            // TIMESTAMPS
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
