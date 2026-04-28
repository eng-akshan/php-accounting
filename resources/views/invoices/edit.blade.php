@extends('layouts.master')

@section('title', 'Edit Invoice')
@section('page-title', 'Edit Invoice')

@section('content')
<div class="card">
    <div class="card-header">Edit Invoice</div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('invoices.update', $invoice) }}" id="invoiceForm">
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Customer *</label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $invoice->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Issue Date *</label>
                    <input type="date" name="issue_date" class="form-control" value="{{ $invoice->issue_date->format('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Due Date *</label>
                    <input type="date" name="due_date" class="form-control" value="{{ $invoice->due_date->format('Y-m-d') }}" required>
                </div>
            </div>

            <h5 class="mt-4">Invoice Items</h5>
            <table class="table" id="itemsTable">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th style="width: 120px;">Quantity</th>
                        <th style="width: 150px;">Unit Price</th>
                        <th style="width: 150px;">Total</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $index => $item)
                        <tr>
                            <td><input type="text" name="items[{{ $index }}][description]" class="form-control" value="{{ $item->description }}"></td>
                            <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control text-end" min="1" value="{{ $item->quantity }}" onchange="calculateTotals()"></td>
                            <td><input type="number" name="items[{{ $index }}][unit_price]" class="form-control text-end" step="0.01" min="0" value="{{ $item->unit_price }}" onchange="calculateTotals()"></td>
                            <td class="text-end line-total">${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                            <td>
                                @if($loop->index > 0)
                                    <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); calculateTotals()"><i class="fas fa-times"></i></button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
                        <td class="text-end"><strong id="subtotal">${{ number_format($invoice->subtotal, 2) }}</strong></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end">Tax (%) <input type="number" name="tax_rate" id="taxRate" class="d-inline-block" style="width: 60px;" min="0" max="100" value="{{ $invoice->tax_amount > 0 && $invoice->subtotal > 0 ? ($invoice->tax_amount / $invoice->subtotal * 100) : 0 }}" onchange="calculateTotals()"></td>
                        <td class="text-end" id="taxAmount">${{ number_format($invoice->tax_amount, 2) }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end">Discount (%) <input type="number" name="discount_rate" id="discountRate" class="d-inline-block" style="width: 60px;" min="0" max="100" value="{{ $invoice->discount_amount > 0 && $invoice->subtotal > 0 ? ($invoice->discount_amount / $invoice->subtotal * 100) : 0 }}" onchange="calculateTotals()"></td>
                        <td class="text-end" id="discountAmount">${{ number_format($invoice->discount_amount, 2) }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total</strong></td>
                        <td class="text-end"><strong id="total">${{ number_format($invoice->total, 2) }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addItem()">
                <i class="fas fa-plus"></i> Add Item
            </button>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ $invoice->notes }}</textarea>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Invoice</button>
                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
let itemCount = {{ $invoice->items->count() }};

function addItem() {
    const html = `
        <tr>
            <td><input type="text" name="items[${itemCount}][description]" class="form-control" placeholder="Item description"></td>
            <td><input type="number" name="items[${itemCount}][quantity]" class="form-control text-end" min="1" value="1" onchange="calculateTotals()"></td>
            <td><input type="number" name="items[${itemCount}][unit_price]" class="form-control text-end" step="0.01" min="0" value="0" onchange="calculateTotals()"></td>
            <td class="text-end line-total">$0.00</td>
            <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); calculateTotals()"><i class="fas fa-times"></i></button></td>
        </tr>
    `;
    document.querySelector('#itemsTable tbody').insertAdjacentHTML('beforeend', html);
    itemCount++;
}

function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('[name$="[quantity]"]').value) || 0;
        const price = parseFloat(row.querySelector('[name$="[unit_price]"]').value) || 0;
        const lineTotal = qty * price;
        row.querySelector('.line-total').textContent = '$' + lineTotal.toFixed(2);
        subtotal += lineTotal;
    });

    const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
    const discountRate = parseFloat(document.getElementById('discountRate').value) || 0;
    const taxAmount = subtotal * (taxRate / 100);
    const discountAmount = subtotal * (discountRate / 100);
    const total = subtotal + taxAmount - discountAmount;

    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('taxAmount').textContent = '$' + taxAmount.toFixed(2);
    document.getElementById('discountAmount').textContent = '-$' + discountAmount.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}

document.addEventListener('DOMContentLoaded', calculateTotals);
</script>
@endsection