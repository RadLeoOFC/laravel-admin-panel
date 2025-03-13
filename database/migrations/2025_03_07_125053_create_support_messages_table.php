<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // Кто отправил
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('support_messages');
    }
};

