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
    $table->uuid('uuid')->unique();

    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    
    $table->morphs('payable'); 

    $table->decimal('amount', 12, 2);
    $table->decimal('fee', 10, 2)->default(0);
    $table->string('currency')->default('USD');

    $table->string('payment_provider')->default('stripe');
    $table->string('transaction_ref')->unique();

    $table->string('status')->default('pending')->index();
    $table->string('type')->index();

    $table->json('metadata')->nullable(); 

    $table->timestamps();
    $table->softDeletes(); 
});
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
