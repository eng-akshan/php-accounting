@extends('layouts.master')

@section('title', 'Expense Details')
@section('page-title', 'Expense Details')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Expense #{{ $expense->reference_number }}</span>
        <div>
            @if($expense->payment_status !== 'paid')
                <form action="{{ route('expenses.pay-bill', $expense) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">Pay Bill</button>
                </form>
            @endif
            <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-primary">Edit</a>
            <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-muted small">Date</div>
                <strong>{{ $expense->date->format('M d, Y') }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Amount</div>
                <strong>${{ number_format($expense->amount, 2) }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Payment</div>
                @if($expense->payment_status === 'paid')
                    <span class="badge bg-success">Paid</span>
                @else
                    <span class="badge bg-warning text-dark">Unpaid</span>
                @endif
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Account</div>
                <strong>{{ $expense->account->name ?? 'N/A' }}</strong>
            </div>
        </div>
        <div class="mb-4">
            <div class="text-muted small">Description</div>
            <p class="mb-0">{{ $expense->description }}</p>
        </div>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-muted small">Vendor</div>
                <strong>{{ $expense->vendor ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Recurring</div>
                <strong>{{ $expense->is_recurring ? ucfirst($expense->recurring_frequency) : 'No' }}</strong>
            </div>
            @if($expense->is_recurring && $expense->recurring_end_date)
                <div class="col-md-3">
                    <div class="text-muted small">End Date</div>
                    <strong>{{ $expense->recurring_end_date->format('M d, Y') }}</strong>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection