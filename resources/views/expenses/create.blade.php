@extends('layouts.master')

@section('title', 'New Expense')
@section('page-title', 'New Expense')

@section('content')
<div class="card">
    <div class="card-header">New Expense</div>
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

        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Description *</label>
                    <input type="text" name="description" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Amount *</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date *</label>
                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Category *</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select Category</option>
                        <option value="salary">Salary</option>
                        <option value="rent">Rent</option>
                        <option value="utilities">Utilities</option>
                        <option value="supplies">Supplies</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Account *</label>
                    <select name="account_id" class="form-select" required>
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->account_number }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Vendor</label>
                    <input type="text" name="vendor" class="form-control" placeholder="Vendor name">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Payment</label>
                    <select name="payment_status" class="form-select">
                        <option value="paid">Paid Now</option>
                        <option value="unpaid">Pay Later</option>
                    </select>
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_recurring" class="form-check-input" id="isRecurring" onchange="toggleRecurring()">
                        <label class="form-check-label" for="isRecurring">Recurring Expense</label>
                    </div>
                </div>
            </div>

            <div id="recurringFields" style="display: none;">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Frequency</label>
                        <select name="recurring_frequency" class="form-select">
                            <option value="">Select Frequency</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="recurring_end_date" class="form-control">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Expense</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleRecurring() {
    const checkbox = document.getElementById('isRecurring');
    const fields = document.getElementById('recurringFields');
    fields.style.display = checkbox.checked ? 'block' : 'none';
}
</script>
@endsection