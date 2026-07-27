@extends('layouts.app')
@section('title', 'Contact Messages')
@section('content')
<h1>Contact Messages</h1>
<div class="card">
    <table>
        <thead><tr><th>Sender</th><th>Subject</th><th>Message</th><th>Status</th><th>Received</th><th>Action</th></tr></thead>
        <tbody>
        @forelse($messages as $message)
            <tr>
                <td>{{ $message->name }}<br><span class="muted">{{ $message->email }}</span></td>
                <td>{{ $message->subject }}</td>
                <td>{{ $message->message }}</td>
                <td><span class="badge">{{ ucfirst($message->status) }}</span></td>
                <td>{{ $message->created_at->format('M d, Y') }}</td>
                <td>
                    <form class="form" method="post" action="{{ route('admin.messages.update', $message) }}">
                        @csrf
                        @method('PUT')
                        <select name="status">
                            @foreach(['new', 'read', 'resolved'] as $status)
                                <option value="{{ $status }}" @selected($message->status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <button class="btn secondary">Save</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">No contact messages yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
{{ $messages->links() }}
@endsection
