<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer_id',
        'title',
        'main_category',
        'sub_category',
        'location',
        'type',
        'salary',
        'description',
        'requirements',
        'responsibilities',
        'deadline',
        'status',
        'is_featured',
        'featured_until',
        'views_count',
        'approved_at',
        'approved_by',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'deadline' => 'date',
            'approved_at' => 'datetime',
            'is_featured' => 'boolean',
            'featured_until' => 'datetime',
            'views_count' => 'integer',
        ];
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_jobs', 'job_id', 'job_seeker_id')
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query
            ->where('status', 'active')
            ->whereNotNull('approved_at')
            ->where(fn ($inner) => $inner->whereNull('deadline')->orWhereDate('deadline', '>=', today()))
            ->whereHas('employer', fn ($employer) => $employer->whereNotNull('employer_verified_at')->where('status', 'active'));
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function isOpenForApplications(): bool
    {
        return $this->status === 'active'
            && ! is_null($this->approved_at)
            && (! $this->deadline || $this->deadline->isFuture() || $this->deadline->isToday())
            && $this->employer
            && $this->employer->isVerifiedEmployer()
            && $this->employer->status === 'active';
    }
}
