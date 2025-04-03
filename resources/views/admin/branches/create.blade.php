@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create New Branch</h1>
    <form action="{{ route('admin.branches.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Branch Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Create Branch</button>
    </form>
</div>
@endsection
