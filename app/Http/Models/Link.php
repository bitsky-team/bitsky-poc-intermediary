<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = [
        'first_key',
        'second_key',
        'first_agreement',
        'second_agreement'
    ];
}
