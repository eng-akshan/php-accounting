@extends('layouts.master')

@section('title', 'Incomes')
@section('page-title', 'Incomes')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Incomes</span>
        <a href="{{ route('incomes.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> New Income
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('incomes.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <option value="sales" {{ request('category') == 'sales' ? 'selected' : '' }}>Sales</option>
                        <option value="service" {{ request('category') == 'service' ? 'selected' : '' }}>Service</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('incomes.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
                </div>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                    <th>Date</th>
                    <th>Category</th>
                    <th>Account</th>
                    <th>Customer</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomes as $income)
                    <tr>
                        <td><a href="{{ route('incomes.show', $income) }}">{{ $income->reference_number }}</a></td>
                        <td>{{ $income->description }}</td>
                        <td class="text-end">${{ number_format($income->amount, 2) }}</td>
                        <td>{{ $income->date->format('M d, Y') }}</td>
                        <td><span class="badge bg-info">{{ ucfirst($income->category) }}</span></td>
                        <td>{{ $income->account->name ?? 'N/A' }}</td>
                        <td>{{ $income->customer->name ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No incomes found</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $incomes->links() }}
    </div>
</div>
@endsection