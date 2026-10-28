<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'category',
        'location',
        'keyword',
        'frequency',
        'is_active',
        'last_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_sent_at' => 'datetime',
        ];
    }
}
