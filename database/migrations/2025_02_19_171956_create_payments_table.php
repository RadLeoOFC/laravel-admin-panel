<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // Unique payment identifier
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relationship with the user
            $table->foreignId('membership_id')->nullable()->constrained()->onDelete('cascade'); // Relationship with the membership
            $table->decimal('amount', 10, 2); // Payment amount
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending'); // Payment status
            $table->string('payment_method')->nullable(); // Payment method (Stripe, PayPal, etc.)
            $table->string('transaction_reference')->unique(); // Unique transaction ID from Stripe
            $table->json('response_data')->nullable(); // Response data from Stripe
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('payments');
    }
};
