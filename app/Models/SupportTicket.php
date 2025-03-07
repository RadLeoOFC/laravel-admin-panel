<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model {
    protected $fillable = ['user_id', 'admin_id', 'message', 'status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function messages() {
        return $this->hasMany(SupportMessage::class, 'ticket_id');
    }
}

