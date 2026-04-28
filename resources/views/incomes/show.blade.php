@extends('layouts.master')

@section('title', 'Income Details')
@section('page-title', 'Income Details')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Income #{{ $income->reference_number }}</span>
        <div>
            <a href="{{ route('incomes.edit', $income) }}" class="btn btn-sm btn-primary">Edit</a>
            <a href="{{ route('incomes.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-muted small">Date</div>
                <strong>{{ $income->date->format('M d, Y') }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Amount</div>
                <strong>${{ number_format($income->amount, 2) }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Category</div>
                <span class="badge bg-info">{{ ucfirst($income->category) }}</span>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Account</div>
                <strong>{{ $income->account->name ?? 'N/A' }}</strong>
            </div>
        </div>
        <div class="mb-4">
            <div class="text-muted small">Description</div>
            <p class="mb-0">{{ $income->description }}</p>
        </div>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-muted small">Customer</div>
                <strong>{{ $income->customer->name ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Recurring</div>
                <strong>{{ $income->is_recurring ? ucfirst($income->recurring_frequency) : 'No' }}</strong>
            </div>
            @if($income->is_recurring && $income->recurring_end_date)
                <div class="col-md-3">
                    <div class="text-muted small">End Date</div>
                    <strong>{{ $income->recurring_end_date->format('M d, Y') }}</strong>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection