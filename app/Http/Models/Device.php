<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'bitsky_ip',
        'bitsky_key'
    ];
}
