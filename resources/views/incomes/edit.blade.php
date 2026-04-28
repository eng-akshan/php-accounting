@extends('layouts.master')

@section('title', 'Edit Income')
@section('page-title', 'Edit Income')

@section('content')
<div class="card">
    <div class="card-header">Edit Income</div>
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

        <form method="POST" action="{{ route('incomes.update', $income) }}">
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Description *</label>
                    <input type="text" name="description" class="form-control" value="{{ $income->description }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Amount *</label>
                    <input type="number" name="amount" class="form-control" step="0.01" min="0" value="{{ $income->amount }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date *</label>
                    <input type="date" name="date" class="form-control" value="{{ $income->date->format('Y-m-d') }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Category *</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select Category</option>
                        <option value="sales" {{ $income->category == 'sales' ? 'selected' : '' }}>Sales</option>
                        <option value="service" {{ $income->category == 'service' ? 'selected' : '' }}>Service</option>
                        <option value="other" {{ $income->category == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Account *</label>
                    <select name="account_id" class="form-select" required>
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ $income->account_id == $account->id ? 'selected' : '' }}>
                                {{ $account->account_number }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-select">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $income->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="form-check">
                        <input type="checkbox" name="is_recurring" class="form-check-input" id="isRecurring" onchange="toggleRecurring()" {{ $income->is_recurring ? 'checked' : '' }}>
                        <label class="form-check-label" for="isRecurring">Recurring Income</label>
                    </div>
                </div>
            </div>

            <div id="recurringFields" style="display: {{ $income->is_recurring ? 'block' : 'none' }};">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Frequency</label>
                        <select name="recurring_frequency" class="form-select">
                            <option value="">Select Frequency</option>
                            <option value="daily" {{ $income->recurring_frequency == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="weekly" {{ $income->recurring_frequency == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            <option value="monthly" {{ $income->recurring_frequency == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarterly" {{ $income->recurring_frequency == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="yearly" {{ $income->recurring_frequency == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="recurring_end_date" class="form-control" value="{{ $income->recurring_end_date?->format('Y-m-d') }}">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Income</button>
                <a href="{{ route('incomes.show', $income) }}" class="btn btn-secondary">Cancel</a>
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