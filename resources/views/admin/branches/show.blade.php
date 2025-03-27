@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Branch Details</h1>
    <p><strong>Name:</strong> {{ $branch->name }}</p>
    <p><strong>Code:</strong> {{ $branch->code }}</p>
</div>
@endsection
