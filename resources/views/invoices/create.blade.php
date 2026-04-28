@extends('layouts.master')

@section('title', 'New Invoice')
@section('page-title', 'New Invoice')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>New Invoice</span>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openProductModal()">
                <i class="fas fa-plus"></i> Add Product
            </button>
            <button type="button" class="btn btn-sm btn-primary" onclick="openCustomerModal()">
                <i class="fas fa-plus"></i> Add Customer
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        @if($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

        <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Customer *</label>
                    <select name="customer_id" id="customerSelect" class="form-select" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Issue Date *</label>
                    <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Due Date *</label>
                    <input type="date" name="due_date" class="form-control" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required>
                </div>
            </div>

            <h5 class="mt-4">Invoice Items</h5>
            <table class="table" id="itemsTable">
                <thead>
                    <tr>
                        <th style="width: 200px;">Product</th>
                        <th>Description</th>
                        <th style="width: 100px;">Qty</th>
                        <th style="width: 130px;">Unit Price</th>
                        <th style="width: 130px;">Total</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr class="item-row">
                        <td>
                            <select name="items[0][product_id]" class="form-select product-select" onchange="selectProduct(this)">
                                <option value="">-- Manual --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-stock="{{ $product->quantity }}">
                                        {{ $product->sku }} - {{ $product->name }} ({{ $product->quantity }} in stock)
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="items[0][description]" class="form-control description-input" placeholder="Item description"></td>
                        <td><input type="number" name="items[0][quantity]" class="form-control text-end" min="1" value="1" onchange="calculateTotals()"></td>
                        <td><input type="number" name="items[0][unit_price]" class="form-control text-end" step="0.01" min="0" value="0" onchange="calculateTotals()"></td>
                        <td class="text-end line-total">$0.00</td>
                        <td></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Subtotal</strong></td>
                        <td class="text-end"><strong id="subtotal">$0.00</strong></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end">Tax (%) <input type="number" name="tax_rate" id="taxRate" class="d-inline-block" style="width: 60px;" min="0" max="100" value="0" onchange="calculateTotals()"></td>
                        <td class="text-end" id="taxAmount">$0.00</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end">Discount ($) <input type="number" name="discount_amount" id="discountAmountInput" class="d-inline-block" style="width: 80px;" min="0" value="0" onchange="calculateTotals()"></td>
                        <td class="text-end" id="discountDisplay">$0.00</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total</strong></td>
                        <td class="text-end"><strong id="total">$0.00</strong></td>
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
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Invoice</button>
                <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickCustomerForm">
                <div class="modal-body">
                    <div id="customerFormError" class="alert alert-danger d-none"></div>
                    <div class="mb-3"><label class="form-label">Name *</label><input type="text" id="customerName" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" id="customerEmail" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input type="text" id="customerPhone" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveCustomerBtn">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let itemCount = 1;
let customerModal = null;

function selectProduct(select) {
    const row = select.closest('tr');
    const option = select.options[select.selectedIndex];
    if (option.value) {
        row.querySelector('.description-input').value = option.dataset.name || option.text;
        row.querySelector('[name$="[unit_price]"]').value = option.dataset.price || 0;
    }
    calculateTotals();
}

function addItem() {
    itemCount++;
    const html = `<tr class="item-row">
        <td>
            <select name="items[${itemCount}][product_id]" class="form-select product-select" onchange="selectProduct(this)">
                <option value="">-- Manual --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-stock="{{ $product->quantity }}">
                        {{ $product->sku }} - {{ $product->name }} ({{ $product->quantity }})
                    </option>
                @endforeach
            </select>
        </td>
        <td><input type="text" name="items[${itemCount}][description]" class="form-control description-input" placeholder="Item description"></td>
        <td><input type="number" name="items[${itemCount}][quantity]" class="form-control text-end" min="1" value="1" onchange="calculateTotals()"></td>
        <td><input type="number" name="items[${itemCount}][unit_price]" class="form-control text-end" step="0.01" min="0" value="0" onchange="calculateTotals()"></td>
        <td class="text-end line-total">$0.00</td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove(); calculateTotals()"><i class="fas fa-times"></i></button></td>
    </tr>`;
    document.getElementById('itemsBody').insertAdjacentHTML('beforeend', html);
}

function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('#itemsBody tr').forEach(row => {
        const qty = parseFloat(row.querySelector('[name$="[quantity]"]').value) || 0;
        const price = parseFloat(row.querySelector('[name$="[unit_price]"]').value) || 0;
        const lineTotal = qty * price;
        row.querySelector('.line-total').textContent = '$' + lineTotal.toFixed(2);
        subtotal += lineTotal;
    });
    const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
    const discountAmount = parseFloat(document.getElementById('discountAmountInput').value) || 0;
    const taxAmount = subtotal * (taxRate / 100);
    const total = subtotal + taxAmount - discountAmount;
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('taxAmount').textContent = '$' + taxAmount.toFixed(2);
    document.getElementById('discountDisplay').textContent = '-$' + discountAmount.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}

function openCustomerModal() {
    if (!customerModal) customerModal = new bootstrap.Modal(document.getElementById('customerModal'));
    customerModal.show();
}

document.getElementById('quickCustomerForm').onsubmit = async function(e) {
    e.preventDefault();
    const formData = new FormData();
    formData.append('name', document.getElementById('customerName').value);
    formData.append('email', document.getElementById('customerEmail').value);
    formData.append('phone', document.getElementById('customerPhone').value);
    try {
        const response = await fetch('{{ route("customers.quick-store") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ name: document.getElementById('customerName').value, email: document.getElementById('customerEmail').value, phone: document.getElementById('customerPhone').value })
        });
        const data = await response.json();
        if (data.success) {
            const select = document.getElementById('customerSelect');
            select.add(new Option(data.customer.name, data.customer.id, true, true));
            customerModal.hide();
            document.getElementById('quickCustomerForm').reset();
        }
    } catch (err) { alert('Error: ' + err.message); }
};

document.addEventListener('DOMContentLoaded', calculateTotals);
</script>
@endsection