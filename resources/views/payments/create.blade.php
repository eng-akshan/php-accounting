@extends('layouts.master')

@section('title', 'New Payment')
@section('page-title', 'New Payment')

@section('content')
<div class="card">
    <div class="card-header">New Payment</div>
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

        <form method="POST" action="{{ route('payments.store') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Customer *</label>
                    <select name="customer_id" class="form-select" required id="customerSelect" onchange="loadInvoices()">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Invoice</label>
                    <select name="invoice_id" class="form-select" id="invoiceSelect">
                        <option value="">Select Invoice</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment Date *</label>
                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Amount *</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Method *</label>
                    <select name="method" class="form-select" required>
                        <option value="">Select Method</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="check">Check</option>
                        <option value="credit_card">Credit Card</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Reference Number</label>
                    <input type="text" name="reference_number" class="form-control" placeholder="Check #, Transaction ID, etc.">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Payment</button>
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
const invoicesByCustomer = @json($invoicesByCustomer);

function loadInvoices() {
    const customerId = document.getElementById('customerSelect').value;
    const invoiceSelect = document.getElementById('invoiceSelect');
    invoiceSelect.innerHTML = '<option value="">Select Invoice</option>';

    if (customerId && invoicesByCustomer[customerId]) {
        invoicesByCustomer[customerId].forEach(inv => {
            const option = document.createElement('option');
            option.value = inv.id;
            option.textContent = inv.invoice_number + ' - $' + inv.balance_due.toFixed(2);
            option.dataset.balance = inv.balance_due;
            invoiceSelect.appendChild(option);
        });
    }
}
</script>
@endsection