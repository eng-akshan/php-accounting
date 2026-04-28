@extends('layouts.master')

@section('title', 'Invoices')
@section('page-title', 'Invoices')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Invoices</span>
        <a href="{{ route('invoices.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> New Invoice
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('invoices.index') }}" class="mb-4">
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
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary ms-2">Clear</a>
                </div>
            </div>
        </form>

        <table class="table">
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Paid</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td><a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                        <td>{{ $invoice->customer->name }}</td>
                        <td>{{ $invoice->issue_date->format('M d, Y') }}</td>
                        <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                        <td class="text-end">${{ number_format($invoice->total, 2) }}</td>
                        <td class="text-end">${{ number_format($invoice->paid_amount, 2) }}</td>
                        <td><span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'sent' ? 'info' : ($invoice->status === 'cancelled' ? 'danger' : 'warning')) }}">{{ ucfirst($invoice->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No invoices found</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $invoices->links() }}
    </div>
</div>
@endsection