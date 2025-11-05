<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = [
        'filename',
        'path',
        'session_id',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
