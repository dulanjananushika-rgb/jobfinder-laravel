<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'status',
        'employer_verified_at',
        'company_name',
        'company_slug',
        'company_banner',
        'company_size',
        'company_perks',
        'website',
        'profile_picture',
        'address',
        'bio',
        'headline',
        'skills',
        'experience',
        'education',
        'portfolio_url',
        'github_url',
        'linkedin_url',
        'google_id',
        'linkedin_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'employer_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'employer_id');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'job_seeker_id');
    }

    public function savedJobs()
    {
        return $this->belongsToMany(Job::class, 'saved_jobs', 'job_seeker_id', 'job_id')
            ->withTimestamps();
    }

    public function interviewsAsEmployer()
    {
        return $this->hasMany(Interview::class, 'employer_id');
    }

    public function interviewsAsCandidate()
    {
        return $this->hasMany(Interview::class, 'candidate_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(ApplicationMessage::class, 'sender_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    public function isVerifiedEmployer(): bool
    {
        return $this->isEmployer() && ! is_null($this->employer_verified_at);
    }

    public function isJobSeeker(): bool
    {
        return $this->role === 'job_seeker';
    }

    public function getSkillsArrayAttribute(): array
    {
        if (empty($this->skills)) {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $this->skills))));
    }

    public function getCompanySlugAttribute(): string
    {
        return $this->attributes['company_slug'] ?? str($this->company_name ?: $this->name)->slug()->toString();
    }
}
