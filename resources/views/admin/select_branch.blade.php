@extends('layouts.app')

@section('title', 'Select Branch')

@section('content')
<h3>Select a Branch to Continue</h3>
<form method="POST" action="{{ route('admin.select-branch.set') }}">
    @csrf
    <div class="mb-3">
        <label for="branch" class="form-label">Branch</label>
        <select name="branch_id" class="form-select" required>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Continue</button>
</form>
@endsection
