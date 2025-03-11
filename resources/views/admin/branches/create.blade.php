@extends('layouts.app')

@section('title', 'Add Branch')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800">Add New Branch</h2>

    <form action="{{ route('branches.store') }}" method="POST" class="mt-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" name="name" class="p-3 border rounded" placeholder="Branch Name" required>
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 mt-4 rounded hover:bg-blue-700">Save Branch</button>
    </form>
</div>
@endsection
