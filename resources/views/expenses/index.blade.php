@extends('layouts.master')

@section('title', 'Expenses')
@section('page-title', 'Expenses')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Expenses</span>
        <a href="{{ route('expenses.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> New Expense
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('expenses.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All</option>
                        <option value="salary" {{ request('category') == 'salary' ? 'selected' : '' }}>Salary</option>
                        <option value="rent" {{ request('category') == 'rent' ? 'selected' : '' }}>Rent</option>
                        <option value="utilities" {{ request('category') == 'utilities' ? 'selected' : '' }}>Utilities</option>
                        <option value="supplies" {{ request('category') == 'supplies' ? 'selected' : '' }}>Supplies</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Payment</label>
                    <select name="payment_status" class="form-select">
                        <option value="">All</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
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
                    <th>Payment</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td><a href="{{ route('expenses.show', $expense) }}">{{ $expense->reference_number }}</a></td>
                        <td>{{ $expense->description }}</td>
                        <td class="text-end">${{ number_format($expense->amount, 2) }}</td>
                        <td>{{ $expense->date->format('M d, Y') }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($expense->category) }}</span></td>
                        <td>
                            @if($expense->payment_status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-warning text-dark">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            @if($expense->payment_status !== 'paid')
                                <form action="{{ route('expenses.pay-bill', $expense) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Pay</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No expenses found</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $expenses->links() }}
    </div>
</div>
@endsection