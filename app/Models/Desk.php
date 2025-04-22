<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desk extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'status',
        'coordinates_x',
        'coordinates_y',
    ];

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }
}
