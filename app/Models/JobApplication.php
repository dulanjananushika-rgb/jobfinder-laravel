<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'job_seeker_id',
        'cover_letter',
        'resume_path',
        'status',
        'withdrawn_at',
        'withdraw_reason',
    ];

    protected function casts(): array
    {
        return [
            'withdrawn_at' => 'datetime',
        ];
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function jobSeeker()
    {
        return $this->belongsTo(User::class, 'job_seeker_id');
    }
}
