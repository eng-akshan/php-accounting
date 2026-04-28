@extends('layouts.master')

@section('title', 'Customer Details')
@section('page-title', 'Customer Details')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>{{ $customer->name }}</span>
        <div>
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-primary">Edit</a>
            <a href="{{ route('customers.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-muted small">Email</div>
                <strong>{{ $customer->email }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Phone</div>
                <strong>{{ $customer->phone ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Company</div>
                <strong>{{ $customer->company ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Status</div>
                <span class="badge bg-{{ $customer->is_active ? 'success' : 'secondary' }}">{{ $customer->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
        </div>
        @if($customer->address)
            <div class="mb-4">
                <div class="text-muted small">Address</div>
                <p class="mb-0">{{ $customer->address }}</p>
            </div>
        @endif
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">Invoices</div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Paid</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer->invoices as $invoice)
                    <tr>
                        <td><a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                        <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                        <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                        <td class="text-end">${{ number_format($invoice->total, 2) }}</td>
                        <td class="text-end">${{ number_format($invoice->paid_amount, 2) }}</td>
                        <td><span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'sent' ? 'info' : 'warning') }}">{{ ucfirst($invoice->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No invoices</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">Payments</div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Payment #</th>
                    <th>Date</th>
                    <th>Invoice</th>
                    <th class="text-end">Amount</th>
                    <th>Method</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer->payments as $payment)
                    <tr>
                        <td><a href="{{ route('payments.show', $payment) }}">{{ $payment->payment_number }}</a></td>
                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td>{{ $payment->invoice->invoice_number ?? 'N/A' }}</td>
                        <td class="text-end">${{ number_format($payment->amount, 2) }}</td>
                        <td>{{ ucfirst($payment->method) }}</td>
                        <td><span class="badge bg-success">{{ ucfirst($payment->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No payments</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection