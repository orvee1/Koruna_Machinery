@extends('layouts.app')

@section('title', 'Edit Branch')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Branch</h2>
        <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">← Back to Branches</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.branches.update', $branch->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Branch Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $branch->name) }}" required>
                </div>

                <button type="submit" class="btn btn-success w-100">Update Branch</button>
            </form>
        </div>
    </div>
</div>
@endsection
