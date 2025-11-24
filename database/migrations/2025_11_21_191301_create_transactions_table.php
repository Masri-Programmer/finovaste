<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Public facing ID (Security best practice: don't expose auto-increment IDs in URLs)
            $table->uuid('uuid')->unique()->index();

            // The User (Payer/Donor/Investor)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // POLYMORPHIC RELATIONSHIP
            // This replaces 'listing_id'. It creates 'payable_type' and 'payable_id'.
            // It allows this transaction to belong to an Investment, BuyNow, or Donation.
            $table->morphs('payable');

            // Financials
            // (15, 2) matches your other tables. 
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 10, 2)->default(0); // Platform fee if applicable
            $table->string('currency', 3)->default('USD'); // Always store currency

            // Quantity (Nullable because Donations don't usually have quantity)
            // Using 4 decimal places allows for fractional investment shares if needed
            $table->decimal('quantity', 15, 4)->nullable();

            // Simplified Type for easy filtering (Redundant to payable_type, but faster for analytics)
            $table->string('type')->index(); // 'buy_now', 'donation', 'investment'

            // Payment Gateway Info (Crucial for professional apps)
            $table->string('payment_method')->nullable(); // e.g., 'stripe', 'paypal', 'wallet'
            $table->string('transaction_ref')->nullable(); // External ID from Stripe/PayPal

            // Snapshot Data
            // Store a copy of the listing title/price here. 
            // If the listing is deleted later, the receipt remains accurate.
            $table->json('metadata')->nullable();

            // Status Management
            $table->string('status')->default('pending')->index(); // pending, paid, failed, refunded
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Financial records should be archived, not deleted
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
