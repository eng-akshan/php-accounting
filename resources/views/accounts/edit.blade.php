@extends('layouts.master')

@section('title', 'Edit Account')
@section('page-title', 'Edit Account')

@section('content')
<div class="card">
    <div class="card-header">Edit Account</div>
    <div class="card-body">
        <form method="POST" action="{{ route('accounts.update', $account) }}">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Account Number</label>
                        <input type="text" name="account_number" class="form-control" value="{{ $account->account_number }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Account Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $account->name }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="Asset" {{ $account->type === 'Asset' ? 'selected' : '' }}>Asset</option>
                            <option value="Liability" {{ $account->type === 'Liability' ? 'selected' : '' }}>Liability</option>
                            <option value="Equity" {{ $account->type === 'Equity' ? 'selected' : '' }}>Equity</option>
                            <option value="Revenue" {{ $account->type === 'Revenue' ? 'selected' : '' }}>Revenue</option>
                            <option value="Expense" {{ $account->type === 'Expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nature</label>
                        <select name="nature" class="form-select" required>
                            <option value="Debit" {{ $account->nature === 'Debit' ? 'selected' : '' }}>Debit</option>
                            <option value="Credit" {{ $account->nature === 'Credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Parent Account</label>
                <select name="parent_id" class="form-select">
                    <option value="">No Parent</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" {{ $account->parent_id === $parent->id ? 'selected' : '' }}>
                            {{ $parent->account_number }} - {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ $account->description }}</textarea>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ $account->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Account</button>
                <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection