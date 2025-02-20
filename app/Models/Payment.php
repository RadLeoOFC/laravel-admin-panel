<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'membership_id', 'amount', 'status', 'payment_method', 'transaction_reference', 'response_data'];

    protected $casts = [
        'response_data' => 'array', // Convert JSON to Array
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function membership() {
        return $this->belongsTo(Membership::class);
    }
}

