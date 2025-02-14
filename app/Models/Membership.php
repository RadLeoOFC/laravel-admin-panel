<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'desk_id', 'start_date', 'end_date', 'membership_type', 'price', 
        'amount_paid', 'payment_status', 'payment_method', 'transaction_reference'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function desk()
    {
        return $this->belongsTo(Desk::class);
    }

    
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('end_date')
            ->orWhere('end_date', '>=', now());
        });
    }

}

