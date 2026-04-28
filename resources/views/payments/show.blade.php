@extends('layouts.master')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Payment #{{ $payment->payment_number }}</span>
        <div>
            <a href="{{ route('payments.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-muted small">Date</div>
                <strong>{{ $payment->payment_date->format('M d, Y') }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Amount</div>
                <strong>${{ number_format($payment->amount, 2) }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Method</div>
                <strong>{{ ucwords(str_replace('_', ' ', $payment->method)) }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Status</div>
                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-muted small">Customer</div>
                <strong>{{ $payment->customer->name }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Invoice</div>
                <strong>{{ $payment->invoice->invoice_number ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Reference</div>
                <strong>{{ $payment->reference_number ?? 'N/A' }}</strong>
            </div>
        </div>
        @if($payment->notes)
            <div class="mb-4">
                <div class="text-muted small">Notes</div>
                <p class="mb-0">{{ $payment->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection