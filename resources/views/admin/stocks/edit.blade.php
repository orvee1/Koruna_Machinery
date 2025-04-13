@extends('layouts.app')

@section('title', 'Edit Stock')

@section('content')
<div class="container">
    <h1>Edit Stock</h1>

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

    <!-- Stock Edit Form -->
    <form action="{{ route('admin.stocks.update', $stock->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Similar fields as create view, with pre-filled data -->
        <!-- Product -->
        <!-- Supplier Name -->
        <!-- Purchase Details -->
        <!-- Branch -->

        <button type="submit" class="btn btn-primary">Update Stock</button>
    </form>
</div>
@endsection
