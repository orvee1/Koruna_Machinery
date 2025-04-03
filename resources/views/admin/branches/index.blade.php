@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Branches</h1>
    <a href="{{ route('admin.branches.create') }}" class="btn btn-primary mb-3">Add New Branch</a>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Code</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($branches as $branch)
                <tr>
                    <td>{{ $branch->id }}</td>
                    <td>{{ $branch->name }}</td>
                    <td>{{ $branch->code }}</td>
                    <td>
                        <a href="{{ route('admin.branches.edit', $branch->id) }}" class="btn btn-warning">Edit</a>
                        {{-- <form action="{{ route('admin.branches.destroy', $branch->id) }}" method="POST" style="display:inline;">
                            @csrf
                        </form> --}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
