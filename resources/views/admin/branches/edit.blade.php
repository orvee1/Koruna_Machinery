@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Branch</h1>
    <form action="{{ route('admin.branches.update', $branch->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Branch Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $branch->name }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update Branch</button>
    </form>
</div>
@endsection
