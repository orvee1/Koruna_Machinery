@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800">Manage Users</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mt-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-3 rounded mt-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- User List -->
    <table class="w-full border mt-4">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-3">Name</th>
                <th class="p-3">Email</th>
                <th class="p-3">Phone</th>
                <th class="p-3">Role</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="border-t">
                <td class="p-3">{{ $user->name }}</td>
                <td class="p-3">{{ $user->email }}</td>
                <td class="p-3">{{ $user->phone }}</td>
                <td class="p-3">{{ ucfirst($user->role) }}</td>
                <td class="p-3">
                    <!-- Prevent deleting self -->
                    @if(auth()->id() !== $user->id)
                        <form action="{{ route('admin.deleteUser', $user->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                                Delete
                            </button>
                        </form>
                    @else
                        <span class="text-gray-500">You</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Create User Form -->
    <h3 class="mt-6 text-xl font-semibold">Create New User</h3>
    <form action="{{ route('admin.createUser') }}" method="POST" class="mt-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="name" class="p-3 border rounded" placeholder="Full Name" required>
            <input type="email" name="email" class="p-3 border rounded" placeholder="Email Address" required>
            <input type="text" name="phone" class="p-3 border rounded" placeholder="Phone Number" required>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
            <input type="password" name="password" class="p-3 border rounded" placeholder="Password" required>
            <select name="role" class="p-3 border rounded" required>
                <option value="worker">Worker</option>
                <option value="admin">Admin</option>
            </select>
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Create User
            </button>
        </div>
    </form>
</div>
@endsection
