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
    Schema::table('listing_subscriptions', function (Blueprint $table) {
        $table->string('locale', 5)->default('en')->after('email');
    });

    Schema::table('listing_updates', function (Blueprint $table) {
        $table->string('translation_key')->nullable()->after('type');
        $table->json('attributes')->nullable()->after('translation_key');
        $table->text('content')->nullable()->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions_and_updates', function (Blueprint $table) {
            //
        });
    }
};
