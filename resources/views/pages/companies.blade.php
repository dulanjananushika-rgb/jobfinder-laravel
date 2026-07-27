@extends('layouts.app')
@section('title', 'Companies')
@section('content')
<h1>Companies</h1>
<div class="grid grid-3">
@foreach(\App\Models\User::where('role','employer')->latest()->get() as $company)
    <div class="card"><div class="company-logo">{{ strtoupper(substr($company->company_name ?: $company->name,0,1)) }}</div><h3>{{ $company->company_name ?: $company->name }}</h3><p class="muted">{{ $company->bio ?: 'Hiring through JobFinder.' }}</p><p>{{ $company->jobs()->active()->count() }} active jobs</p></div>
@endforeach
</div>
@endsection
