<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'gasoline_price',
        'van_price',
        'driver_price',
        'assistant_price',
        'commission_rate',
    ];
}
