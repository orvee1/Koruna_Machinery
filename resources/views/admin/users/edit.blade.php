@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container">
    <h1>Edit User</h1>

    <!-- Success or Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Name -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

        <div class="row">
            <!-- Phone -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required>
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Role -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select @error('role') is-invalid @enderror"
                            id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="worker" {{ old('role', $user->role) == 'worker' ? 'selected' : '' }}>Worker</option>
                        <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                    </select>
                    @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Branch -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="branch_id" class="form-label">Branch</label>
                    <select class="form-select @error('branch_id') is-invalid @enderror"
                            id="branch_id" name="branch_id">
                        <option value="">No Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Password -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                           id="password_confirmation" name="password_confirmation">
                    @error('password_confirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Update User</button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role');
        const branchSelect = document.getElementById('branch_id');

        function handleRoleChange() {
            if (roleSelect.value === 'admin') {
                branchSelect.value = '';
                branchSelect.disabled = true;
            } else {
                branchSelect.disabled = false;
            }
        }

        // Run on load
        handleRoleChange();

        // Change listener
        roleSelect.addEventListener('change', handleRoleChange);
    });
</script>
@endsection
