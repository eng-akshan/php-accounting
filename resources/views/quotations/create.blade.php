@extends('layouts.master')
@section('title', 'Create Quotation')
@section('page-title', 'New Quotation')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Create New Quotation</h5>
    </div>
    <div class="card-body">
        <form id="quotationForm" method="POST" action="{{ route('quotations.store') }}">
            @csrf
            <div class="row mb-4">
                <div class="col-md-4">
                    <label class="form-label">Customer *</label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Issue Date *</label>
                    <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Valid Until *</label>
                    <input type="date" name="valid_until" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
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
                            <tr class="item-row">
                                <td>
                                    <input type="text" name="items[0][description]" class="form-control" placeholder="Item description" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" class="form-control quantity" value="1" min="1" required>
                                </td>
                                <td>
                                    <input type="number" name="items[0][unit_price]" class="form-control unit-price" value="0" min="0" step="0.01" required>
                                </td>
                                <td class="item-total">0.00</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger remove-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Subtotal:</th>
                                <th id="subtotal">0.00</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Discount:</th>
                                <th>
                                    <input type="number" name="discount_amount" id="discountAmount" class="form-control" value="0" min="0" step="0.01">
                                </th>
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Tax ({{ config('settings.tax_rate', 10) }}%):</th>
                                <th id="taxAmount">0.00</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th id="grandTotal">0.00</th>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Terms & Conditions</label>
                    <textarea name="terms" class="form-control" rows="3">Payment due within 30 days.</textarea>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Quotation</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let itemCount = 1;
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