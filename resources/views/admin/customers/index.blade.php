@extends('layouts.app')

@section('title', 'Customer List')

@section('content')
<div class="bg-white shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800">Customer List</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mt-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter Form for Branch -->
    <form action="{{ route('customers.index') }}" method="GET" class="mb-4">
        <div class="flex items-center gap-4">
            <select name="branch_id" class="p-3 border rounded">
                <option value="" disabled selected>Select Branch</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filter</button>
        </div>
    </form>

    <!-- Customer Table -->
    <table class="w-full border mt-4">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-3">Name</th>
                <th class="p-3">Phone</th>
                <th class="p-3">District</th>
                <th class="p-3">Customer ID</th>
                <th class="p-3">Branch</th>
                <th class="p-3">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
            <tr class="border-t">
                <td class="p-3">{{ $customer->name }}</td>
                <td class="p-3">{{ $customer->phone }}</td>
                <td class="p-3">{{ $customer->district }}</td>
                <td class="p-3">{{ $customer->customer_id }}</td>
                <td class="p-3">{{ $customer->branch->name }}</td>
                <td class="p-3">
                    <a href="{{ route('customers.edit', $customer->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Edit</a>
                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $customers->links() }} <!-- This will display the pagination controls -->
    </div>
</div>
@endsection
