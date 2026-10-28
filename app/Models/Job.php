<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employer_id',
        'title',
        'slug',
        'main_category',
        'sub_category',
        'location',
        'type',
        'salary',
        'min_salary',
        'max_salary',
        'salary_currency',
        'salary_period',
        'description',
        'requirements',
        'responsibilities',
        'screening_questions',
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
            'min_salary' => 'decimal:2',
            'max_salary' => 'decimal:2',
            'screening_questions' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Job $job) {
            if (empty($job->slug) && ! empty($job->title)) {
                $baseSlug = Str::slug($job->title);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->where('id', '!=', $job->id ?? 0)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $job->slug = $slug;
            }
        });
    }

    public function getFormattedSalaryAttribute(): string
    {
        if ($this->min_salary && $this->max_salary) {
            return $this->salary_currency . ' ' . number_format($this->min_salary) . ' - ' . number_format($this->max_salary) . ' / ' . $this->salary_period;
        } elseif ($this->min_salary) {
            return 'From ' . $this->salary_currency . ' ' . number_format($this->min_salary) . ' / ' . $this->salary_period;
        } elseif ($this->salary) {
            return $this->salary;
        }
        return 'Negotiable';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->orWhere('slug', $value)
            ->firstOrFail();
    }

    public function employer()
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function reports()
    {
        return $this->hasMany(JobReport::class);
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
