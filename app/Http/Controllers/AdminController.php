<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function users(Request $request)
    {
        $users = User::query()
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->role))
            ->when($request->filled('search'), fn ($query) => $query->where(function ($inner) use ($request) {
                $inner->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%')
                    ->orWhere('company_name', 'like', '%'.$request->search.'%');
            }))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)->numbers()->symbols()],
            'role' => ['required', Rule::in(['admin', 'employer', 'job_seeker'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'employer_verified' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'regex:/^0\d{9}$/'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        $data['password'] = Hash::make($data['password']);
        $data['employer_verified_at'] = ($data['role'] === 'employer' && $request->boolean('employer_verified')) ? now() : null;
        User::create($data);

        return back()->with('status', 'User created.');
    }

    public function showUser(User $user)
    {
        return view('admin.user-show', [
            'user' => $user->loadCount(['jobs', 'applications', 'savedJobs']),
        ]);
    }

    public function editUser(User $user)
    {
        return view('admin.user-edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'employer', 'job_seeker'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'employer_verified' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'regex:/^0\d{9}$/'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('profile-pictures', 'public');
        }

        $data['employer_verified_at'] = ($data['role'] === 'employer' && $request->boolean('employer_verified')) ? ($user->employer_verified_at ?: now()) : null;

        $user->update($data);

        return back()->with('status', 'User updated.');
    }

    public function deleteUser(User $user)
    {
        abort_if($user->id === auth()->id(), 422, 'You cannot delete your own account.');
        $user->delete();

        return back()->with('status', 'User deleted.');
    }

    public function jobs()
    {
        return view('admin.jobs', [
            'jobs' => Job::with('employer')->withCount('applications')->latest()->paginate(12),
        ]);
    }

    public function updateJob(Request $request, Job $job)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive', 'pending', 'rejected'])],
            'rejection_reason' => ['nullable', 'required_if:status,rejected', 'string', 'max:1000'],
        ]);

        if ($data['status'] === 'active') {
            abort_unless($job->employer->isVerifiedEmployer() && $job->employer->status === 'active', 422, 'Employer must be active and verified before approving jobs.');
            abort_if($job->deadline && $job->deadline->isPast(), 422, 'Expired jobs cannot be approved.');
            $data['approved_at'] = now();
            $data['approved_by'] = auth()->id();
            $data['rejection_reason'] = null;
        } elseif ($data['status'] === 'rejected') {
            $data['approved_at'] = null;
            $data['approved_by'] = null;
        } else {
            $data['approved_at'] = null;
            $data['approved_by'] = null;
            $data['rejection_reason'] = null;
        }

        $job->update($data);

        return back()->with('status', 'Job updated.');
    }

    public function deleteJob(Job $job)
    {
        $job->delete();

        return back()->with('status', 'Job deleted.');
    }

    public function applications()
    {
        return view('admin.applications', [
            'applications' => JobApplication::with('job.employer', 'jobSeeker')->latest()->paginate(12),
        ]);
    }

    public function messages()
    {
        return view('admin.messages', [
            'messages' => ContactMessage::latest()->paginate(15),
        ]);
    }

    public function updateMessage(Request $request, ContactMessage $message)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['new', 'read', 'resolved'])],
        ]);

        $message->update($data);

        return back()->with('status', 'Message updated.');
    }
}
