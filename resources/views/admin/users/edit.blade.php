@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container">
    <h1>Edit User</h1>

    <!-- Display success or error messages -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Name field -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Email field -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Phone field -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Role selection -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="admin" @if(old('role', $user->role) == 'admin') selected @endif>Admin</option>
                        <option value="worker" @if(old('role', $user->role) == 'worker') selected @endif>Worker</option>
                        <option value="manager" @if(old('role', $user->role) == 'manager') selected @endif>Manager</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Branch selection -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="branch_id" class="form-label">Branch</label>
                    <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" @if(old('branch_id', $user->branch_id) == $branch->id) selected @endif>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit button -->
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Update User</button>
        </div>
    </form>
</div>
@endsection
