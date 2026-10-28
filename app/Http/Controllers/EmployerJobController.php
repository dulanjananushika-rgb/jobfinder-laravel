<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;

class EmployerJobController extends Controller
{
    public function index()
    {
        return view('employer.jobs.index', [
            'jobs' => auth()->user()->jobs()->withCount('applications')->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()->isVerifiedEmployer(), 403, 'Your employer account must be verified before posting jobs.');
        return view('employer.jobs.form', $this->formOptions(new Job()));
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isVerifiedEmployer(), 403, 'Your employer account must be verified before posting jobs.');

        auth()->user()->jobs()->create([
            ...$this->validated($request),
            'status' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
        ]);

        return redirect()->route('employer.jobs.index')->with('status', 'Job submitted for admin approval.');
    }

    public function edit(Job $job)
    {
        $this->authorizeEmployer($job);
        return view('employer.jobs.form', $this->formOptions($job));
    }

    public function update(Request $request, Job $job)
    {
        $this->authorizeEmployer($job);
        $job->update([
            ...$this->validated($request),
            'status' => 'pending',
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
        ]);

        return redirect()->route('employer.jobs.index')->with('status', 'Job updated and sent for re-approval.');
    }

    public function destroy(Job $job)
    {
        $this->authorizeEmployer($job);
        $job->delete();

        return back()->with('status', 'Job deleted successfully.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'main_category' => ['required', 'string', 'max:100'],
            'sub_category' => ['nullable', 'string', 'max:100'],
            'location' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string', 'max:100'],
            'salary' => ['nullable', 'string', 'max:100'],
            'min_salary' => ['nullable', 'numeric', 'min:0'],
            'max_salary' => ['nullable', 'numeric', 'gte:min_salary'],
            'salary_currency' => ['nullable', 'string', 'max:10'],
            'salary_period' => ['nullable', 'string', 'in:monthly,yearly,hourly,weekly'],
            'description' => ['required', 'string', 'min:20'],
            'requirements' => ['required', 'string', 'min:10'],
            'responsibilities' => ['nullable', 'string'],
            'screening_questions' => ['nullable', 'array', 'max:5'],
            'screening_questions.*' => ['nullable', 'string', 'max:255'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        if (isset($validated['screening_questions'])) {
            $validated['screening_questions'] = array_values(array_filter(array_map('trim', $validated['screening_questions'])));
        }

        if (empty($validated['salary']) && (! empty($validated['min_salary']) || ! empty($validated['max_salary']))) {
            $currency = $validated['salary_currency'] ?? 'LKR';
            $period = $validated['salary_period'] ?? 'monthly';
            if (! empty($validated['min_salary']) && ! empty($validated['max_salary'])) {
                $validated['salary'] = "{$currency} " . number_format($validated['min_salary']) . " - " . number_format($validated['max_salary']) . " / {$period}";
            } elseif (! empty($validated['min_salary'])) {
                $validated['salary'] = "From {$currency} " . number_format($validated['min_salary']) . " / {$period}";
            }
        }

        return $validated;
    }

    private function formOptions(Job $job): array
    {
        $categories = collect(config('jobfinder.categories', []))
            ->reject(fn ($c) => $c === 'Other')
            ->sort()
            ->push('Other')
            ->values()
            ->all();

        return [
            'job' => $job,
            'categories' => $categories,
            'locations' => config('jobfinder.districts'),
        ];
    }

    private function authorizeEmployer(Job $job): void
    {
        abort_unless($job->employer_id === auth()->id(), 403);
    }
}
