@extends('layouts.master')
@section('title', 'View Quotation')
@section('page-title', 'Quotation Details')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Quotation #{{ $quotation->quotation_number }}</h5>
        <div>
            @if($quotation->status !== 'converted')
                <a href="{{ route('quotations.convert', $quotation->id) }}" class="btn btn-success" onclick="return confirm('Convert to Invoice?')">
                    <i class="fas fa-file-invoice"></i> Convert to Invoice
                </a>
            @endif
            <a href="{{ route('quotations.pdf', $quotation->id) }}" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Customer</h6>
                <p class="mb-1"><strong>{{ $quotation->customer->name }}</strong></p>
                <p class="mb-0">{{ $quotation->customer->email }}</p>
                <p class="mb-0">{{ $quotation->customer->phone }}</p>
                <p class="mb-0">{{ $quotation->customer->address }}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-1"><strong>Status:</strong> 
                    <span class="badge bg-{{ $quotation->status == 'approved' ? 'success' : ($quotation->status == 'rejected' ? 'danger' : ($quotation->status == 'converted' ? 'info' : 'secondary')) }}">
                        {{ ucfirst($quotation->status) }}
                    </span>
                </p>
                <p class="mb-1"><strong>Issue Date:</strong> {{ $quotation->issue_date->format('M d, Y') }}</p>
                <p class="mb-1"><strong>Valid Until:</strong> {{ $quotation->valid_until->format('M d, Y') }}</p>
                <p class="mb-0"><strong>Created By:</strong> {{ $quotation->user->name }}</p>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">{{ number_format($item->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Subtotal:</th>
                    <th class="text-end">{{ number_format($quotation->subtotal, 2) }}</th>
                </tr>
                @if($quotation->discount_amount > 0)
                    <tr>
                        <th colspan="3" class="text-end">Discount:</th>
                        <th class="text-end">-{{ number_format($quotation->discount_amount, 2) }}</th>
                    </tr>
                @endif
                <tr>
                    <th colspan="3" class="text-end">Tax ({{ $quotation->subtotal > 0 ? number_format(($quotation->tax_amount / $quotation->subtotal) * 100, 1) : 0 }}%):</th>
                    <th class="text-end">{{ number_format($quotation->tax_amount, 2) }}</th>
                </tr>
                <tr>
                    <th colspan="3" class="text-end">Total:</th>
                    <th class="text-end">{{ number_format($quotation->total, 2) }}</th>
                </tr>
            </tfoot>
        </table>

        @if($quotation->notes)
            <div class="mt-3">
                <h6>Notes</h6>
                <p>{{ $quotation->notes }}</p>
            </div>
        @endif

        @if($quotation->terms)
            <div class="mt-3">
                <h6>Terms & Conditions</h6>
                <p>{{ $quotation->terms }}</p>
            </div>
        @endif
    </div>
</div>
@endsection