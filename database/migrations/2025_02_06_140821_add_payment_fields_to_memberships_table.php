<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->decimal('amount_paid', 10, 2)->nullable()->after('end_date'); // Amount paid by the user
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending')->after('amount_paid'); // Payment status
            $table->string('payment_method')->nullable()->after('payment_status'); // Payment method used
            $table->string('transaction_reference')->nullable()->after('payment_method'); // Reference for tracking the payment
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'payment_status', 'payment_method', 'transaction_reference']);
        });
    }
};
