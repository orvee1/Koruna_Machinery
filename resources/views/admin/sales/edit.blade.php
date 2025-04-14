@extends('layouts.app')

@section('title', 'Edit Sale')

@section('content')
<div class="container">
    <h1>Edit Sale</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.sales.update', $sale->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Include similar form fields as the create form -->
        <!-- Only need to change the data to reflect existing sale -->

        <button type="submit" class="btn btn-success mt-3">Update Sale</button>
    </form>
</div>
@endsection
