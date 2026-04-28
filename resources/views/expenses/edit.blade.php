@extends('layouts.master')

@section('title', 'Edit Expense')
@section('page-title', 'Edit Expense')

@section('content')
<div class="card">
    <div class="card-header">Edit Expense</div>
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

        <form method="POST" action="{{ route('expenses.update', $expense) }}">
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Description *</label>
                    <input type="text" name="description" class="form-control" value="{{ $expense->description }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Amount *</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0" value="{{ $expense->amount }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date *</label>
                    <input type="date" name="date" class="form-control" value="{{ $expense->date->format('Y-m-d') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Category *</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select Category</option>
                        <option value="salary" {{ $expense->category == 'salary' ? 'selected' : '' }}>Salary</option>
                        <option value="rent" {{ $expense->category == 'rent' ? 'selected' : '' }}>Rent</option>
                        <option value="utilities" {{ $expense->category == 'utilities' ? 'selected' : '' }}>Utilities</option>
                        <option value="supplies" {{ $expense->category == 'supplies' ? 'selected' : '' }}>Supplies</option>
                        <option value="other" {{ $expense->category == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Account *</label>
                    <select name="account_id" class="form-select" required>
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ $expense->account_id == $account->id ? 'selected' : '' }}>
                                {{ $account->account_number }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Vendor</label>
                    <input type="text" name="vendor" class="form-control" value="{{ $expense->vendor }}">
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_recurring" class="form-check-input" id="isRecurring" onchange="toggleRecurring()" {{ $expense->is_recurring ? 'checked' : '' }}>
                        <label class="form-check-label" for="isRecurring">Recurring Expense</label>
                    </div>
                </div>
            </div>

            <div id="recurringFields" style="display: {{ $expense->is_recurring ? 'block' : 'none' }};">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Frequency</label>
                        <select name="recurring_frequency" class="form-select">
                            <option value="">Select Frequency</option>
                            <option value="daily" {{ $expense->recurring_frequency == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ $expense->recurring_frequency == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ $expense->recurring_frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarterly" {{ $expense->recurring_frequency == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="yearly" {{ $expense->recurring_frequency == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="recurring_end_date" class="form-control" value="{{ $expense->recurring_end_date?->format('Y-m-d') }}">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Expense</button>
                <a href="{{ route('expenses.show', $expense) }}" class="btn btn-secondary">Cancel</a>
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