@extends('layouts.master')

@section('title', 'Payments')
@section('page-title', 'Payments')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Payments</span>
        <a href="{{ route('payments.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> New Payment
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('payments.index') }}" class="mb-4">
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
                    <label class="form-label">Method</label>
                    <select name="method" class="form-select">
                        <option value="">All Methods</option>
                        <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="check" {{ request('method') == 'check' ? 'selected' : '' }}>Check</option>
                        <option value="credit_card" {{ request('method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
                </div>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Payment #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Invoice</th>
                    <th class="text-end">Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td><a href="{{ route('payments.show', $payment) }}">{{ $payment->payment_number }}</a></td>
                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td>{{ $payment->customer->name }}</td>
                        <td>{{ $payment->invoice->invoice_number ?? 'N/A' }}</td>
                        <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $payment->method)) }}</td>
                        <td><span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($payment->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No payments found</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $payments->links() }}
    </div>
</div>
@endsection