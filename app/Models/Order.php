<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'pickup_location',
        'dropoff_location',
        'pickup_time',
        'dropoff_time',
        'fare',
    ];
}
