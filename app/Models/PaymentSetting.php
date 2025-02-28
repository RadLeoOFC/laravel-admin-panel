<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model {
    use HasFactory;

    protected $fillable = ['period', 'cost_of_membership_for_this_period_of_time'];

    protected $casts = [
        'response_data' => 'array', // Convert JSON to Array
    ];

    public function payment() {
        return $this->belongsTo(Payment::class);
    }

    public function membership() {
        return $this->belongsTo(Membership::class);
    }
}


