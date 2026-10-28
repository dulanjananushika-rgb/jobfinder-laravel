<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobReport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobReportController extends Controller
{
    public function store(Request $request, Job $job)
    {
        $data = $request->validate([
            'reason' => [
                'required',
                Rule::in([
                    'fraud_or_scam',
                    'asks_for_money',
                    'misleading_salary',
                    'inappropriate_content',
                    'expired_or_closed',
                    'other',
                ]),
            ],
            'details' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        JobReport::create([
            'job_id' => $job->id,
            'user_id' => auth()->id(),
            'reason' => $data['reason'],
            'details' => $data['details'],
            'status' => 'pending',
        ]);

        return back()->with('status', 'Thank you for your vigilance. Our safety team will review this listing.');
    }
}
