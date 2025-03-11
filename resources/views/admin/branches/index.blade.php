@extends('layouts.app')

@section('title', 'Manage Branches')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800">Manage Branches</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mt-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('branches.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Add New Branch
        </a>
    </div>

    <table class="w-full border mt-4">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-3">Branch Name</th>
                <th class="p-3">Branch Code</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($branches as $branch)
            <tr class="border-t">
                <td class="p-3">{{ $branch->name }}</td>
                <td class="p-3">{{ $branch->code }}</td>
                <td class="p-3">
                    <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
