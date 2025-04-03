@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Branch Details</h1>
    <p><strong>Branch Name:</strong> {{ $branch->name }}</p>
    <p><strong>Branch Code:</strong> {{ $branch->code }}</p>
    <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">Back to Branches</a>
</div>
@endsection
