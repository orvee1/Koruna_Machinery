@extends('layouts.app')

@section('title', 'Switch Branch')

@section('content')
<div class="container mt-5">
    <h3>🔁 Switch Branch</h3>

    <form action="{{ route('admin.switch-branch.set') }}" method="POST" class="mt-3">
        @csrf
        <div class="mb-3">
            <label for="branch_id" class="form-label">Choose Branch</label>
            <select name="branch_id" id="branch_id" class="form-select" required>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ session('active_branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Switch</button>
    </form>
</div>
@endsection
