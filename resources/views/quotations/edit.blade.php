@extends('layouts.master')
@section('title', 'Edit Quotation')
@section('page-title', 'Edit Quotation')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit Quotation #{{ $quotation->quotation_number }}</h5>
    </div>
    <div class="card-body">
        <form id="quotationForm" method="POST" action="{{ route('quotations.update', $quotation->id) }}">
            @csrf
            @method('PUT')
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">Customer *</label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $quotation->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Issue Date *</label>
                    <input type="date" name="issue_date" class="form-control" value="{{ $quotation->issue_date->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valid Until *</label>
                    <input type="date" name="valid_until" class="form-control" value="{{ $quotation->valid_until->format('Y-m-d') }}" required>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Items</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="addItem">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="itemsTable">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th width="120">Quantity</th>
                                <th width="150">Unit Price</th>
                                <th width="150">Total</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            @foreach($quotation->items as $index => $item)
                            <tr class="item-row">
                                <td>
                                    <input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ $item->description }}" required>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" value="{{ $item->quantity }}" min="1" required>
                                </td>
                                <td>
                                    <input type="number" name="items[{{ $index }}][unit_price]" class="form-control unit-price" value="{{ $item->unit_price }}" min="0" step="0.01" required>
                                </td>
                                <td class="item-total">{{ number_format($item->total, 2) }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Subtotal:</th>
                                <th id="subtotal">{{ number_format($quotation->subtotal, 2) }}</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Discount:</th>
                                <th>
                                    <input type="number" name="discount_amount" id="discountAmount" class="form-control" value="{{ $quotation->discount_amount }}" min="0" step="0.01">
                                </th>
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Tax ({{ config('settings.tax_rate', 10) }}%):</th>
                                <th id="taxAmount">{{ number_format($quotation->tax_amount, 2) }}</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th id="grandTotal">{{ number_format($quotation->total, 2) }}</th>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="draft" {{ $quotation->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ $quotation->status == 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="approved" {{ $quotation->status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $quotation->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ $quotation->notes }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Terms & Conditions</label>
                    <textarea name="terms" class="form-control" rows="3">{{ $quotation->terms }}</textarea>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Quotation</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let itemCount = {{ $quotation->items->count() }};
const taxRate = {{ config('settings.tax_rate', 10) }};

function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.quantity').value) || 0;
        const price = parseFloat(row.querySelector('.unit-price').value) || 0;
        const total = qty * price;
        row.querySelector('.item-total').textContent = total.toFixed(2);
        subtotal += total;
    });

    const discount = parseFloat(document.getElementById('discountAmount').value) || 0;
    const tax = (subtotal - discount) * (taxRate / 100);
    const grandTotal = subtotal - discount + tax;

    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('taxAmount').textContent = tax.toFixed(2);
    document.getElementById('grandTotal').textContent = grandTotal.toFixed(2);
}

document.getElementById('addItem').addEventListener('click', function() {
    const tbody = document.getElementById('itemsBody');
    const row = document.createElement('tr');
    row.className = 'item-row';
    row.innerHTML = `
        <td>
            <input type="text" name="items[${itemCount}][description]" class="form-control" placeholder="Item description" required>
        </td>
        <td>
            <input type="number" name="items[${itemCount}][quantity]" class="form-control quantity" value="1" min="1" required>
        </td>
        <td>
            <input type="number" name="items[${itemCount}][unit_price]" class="form-control unit-price" value="0" min="0" step="0.01" required>
        </td>
        <td class="item-total">0.00</td>
        <td>
            <button type="button" class="btn btn-sm btn-danger remove-item">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    itemCount++;
    attachRowListeners(row);
});

function attachRowListeners(row) {
    row.querySelector('.quantity').addEventListener('input', calculateTotals);
    row.querySelector('.unit-price').addEventListener('input', calculateTotals);
    row.querySelector('.remove-item').addEventListener('click', function() {
        row.remove();
        calculateTotals();
    });
}

document.querySelectorAll('.item-row').forEach(row => {
    attachRowListeners(row);
});

document.getElementById('discountAmount').addEventListener('input', calculateTotals);
</script>
@endpush
@endsection