@extends('layouts.master')

@section('title', 'Invoice Details')
@section('page-title', 'Invoice Details')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Invoice #{{ $invoice->invoice_number }}</span>
        <div>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                <form action="{{ route('invoices.mark-as-paid', $invoice) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">Mark as Paid</button>
                </form>
            @endif
            @if($invoice->status === 'draft')
                <form action="{{ route('invoices.send', $invoice) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-info">Send to Customer</button>
                </form>
            @endif
            @if($invoice->status !== 'cancelled' && $invoice->status !== 'paid')
                <form action="{{ route('invoices.cancel', $invoice) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this invoice?')">Cancel</button>
                </form>
            @endif
            @if($invoice->status === 'draft')
                <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-primary">Edit</a>
            @endif
            <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-muted small">Customer</div>
                <strong>{{ $invoice->customer->name }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Issue Date</div>
                <strong>{{ $invoice->issue_date->format('M d, Y') }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Due Date</div>
                <strong>{{ $invoice->due_date->format('M d, Y') }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Status</div>
                @if($invoice->status === 'paid')
                    <span class="badge bg-success">Paid</span>
                @elseif($invoice->status === 'sent')
                    <span class="badge bg-primary">Sent</span>
                @elseif($invoice->status === 'cancelled')
                    <span class="badge bg-danger">Cancelled</span>
                @else
                    <span class="badge bg-warning text-dark">Draft</span>
                @endif
            </div>
        </div>

        <h5>Items</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-end">Quantity</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end">Subtotal</td>
                    <td class="text-end">${{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->tax_amount > 0)
                    <tr>
                        <td colspan="3" class="text-end">Tax</td>
                        <td class="text-end">${{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                @endif
                @if($invoice->discount_amount > 0)
                    <tr>
                        <td colspan="3" class="text-end">Discount</td>
                        <td class="text-end">-${{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                    <td class="text-end"><strong>${{ number_format($invoice->total, 2) }}</strong></td>
                </tr>
                @if($invoice->paid_amount > 0)
                    <tr>
                        <td colspan="3" class="text-end">Paid</td>
                        <td class="text-end">${{ number_format($invoice->paid_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Balance Due</strong></td>
                        <td class="text-end"><strong>${{ number_format($invoice->getBalanceDue(), 2) }}</strong></td>
                    </tr>
                @endif
            </tfoot>
        </table>

        @if($invoice->notes)
            <div class="mt-3">
                <div class="text-muted small">Notes</div>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection