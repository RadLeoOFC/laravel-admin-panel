<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // A regular user who sent a message
            $table->unsignedBigInteger('admin_id')->nullable(); // The administrator who responded to the message
            $table->text('message'); // Message from regular user
            $table->text('response')->nullable(); // Administrator's response
            $table->enum('status', ['open', 'closed'])->default('open'); // Ticket status
            $table->timestamps();

            // Relationships with the users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down() {
        Schema::dropIfExists('support_tickets');
    }
};

