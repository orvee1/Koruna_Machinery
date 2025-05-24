@extends('layouts.app')

@section('title', 'Manager Dashboard')

@section('content')
<div class="container-fluid">

    {{-- 🔔 Welcome Note --}}
    <div class="alert alert-primary d-flex justify-content-between align-items-center mb-4">
        <div>
            👋 স্বাগতম, <strong>{{ auth()->user()->name }}</strong>!
        </div>
    </div>

    <h1 class="mb-4 text-center">Manager Dashboard</h1>

    {{-- 🔍 Filter Form --}}
    <form method="GET" class="card shadow-sm p-4 mb-4 border-0 bg-light">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="from_date" class="form-label fw-semibold">📅 From Date</label>
                <input type="date" id="from_date" name="from_date" class="form-control border-primary"
                       value="{{ request('from_date') }}">
            </div>
            <div class="col-md-3">
                <label for="to_date" class="form-label fw-semibold">📅 To Date</label>
                <input type="date" id="to_date" name="to_date" class="form-control border-primary"
                       value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2">
                <label for="month" class="form-label fw-semibold">📆 Month</label>
                <select name="month" id="month" class="form-select border-primary">
                    <option value="">All</option>
                    @foreach([1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'] as $num => $label)
                        <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="year" class="form-label fw-semibold">📅 Year</label>
                <select name="year" id="year" class="form-select border-primary">
                    <option value="">All</option>
                    @for($y = now()->year; $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2 d-flex flex-column flex-md-row align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel-fill me-1"></i> Filter
                </button>
                <a href="{{ route('manager.dashboard') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-x-circle me-1"></i> Clear
                </a>
            </div>
        </div>
    </form>

    {{-- 📊 Dashboard Cards --}}
    <div class="row">
        {{-- Total Product Value --}}
         <div class="col-md-3 mb-4">
            <div class="card shadow-sm rounded-lg p-4 text-center h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Value</h5>
                    <p class="card-text fs-4 text-info">{{ number_format($totalProductValue, 2) }} ৳</p>
                    <div class="card-footer text-muted">Total product value</div>
                </div>
            </div>
        </div>

        {{-- Total Sales --}}
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm rounded-lg p-4 text-center h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Sales</h5>
                    <p class="card-text fs-4 text-success">{{ number_format($totalSales ?? 0, 2) }} ৳</p>
                    <div class="card-footer text-muted">Sales revenue</div>
                </div>
            </div>
        </div>

        {{-- Total Profit --}}
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm rounded-lg p-4 text-center h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Profit</h5>
                    <p class="card-text fs-4 text-warning">{{ number_format($totalProfit ?? 0, 2) }} ৳</p>
                    <div class="card-footer text-muted">
                        Showing profit for
                        <strong>{{ request()->filled('from_date') || request('month') || request('year') ? 'filtered data' : 'today' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Due --}}
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm rounded-lg p-4 text-center h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Total Due</h5>
                    <p class="card-text fs-4 {{ $totalDue > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($totalDue ?? 0, 2) }} ৳
                    </p>
                    <div class="card-footer text-muted">Supplier payments pending</div>
                </div>
            </div>
        </div>

        {{-- Due To Receive --}}
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm rounded-lg p-4 text-center h-100">
                <div class="card-body">
                    <h5 class="card-title text-primary">Due To Receive</h5>
                    <p class="card-text fs-4 {{ $totalDueToHave > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($totalDueToHave ?? 0, 2) }} ৳
                    </p>
                    <div class="card-footer text-muted">Customer dues to collect</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
