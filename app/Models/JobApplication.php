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
        'ai_match_score',
        'ai_summary',
        'apply_type',
        'withdrawn_at',
        'withdraw_reason',
    ];

    protected function casts(): array
    {
        return [
            'withdrawn_at' => 'datetime',
            'ai_match_score' => 'integer',
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

    public function messages()
    {
        return $this->hasMany(ApplicationMessage::class, 'job_application_id')->oldest();
    }

    public function interview()
    {
        return $this->hasOne(Interview::class, 'job_application_id');
    }
}
