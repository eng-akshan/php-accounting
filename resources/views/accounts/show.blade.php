@extends('layouts.master')

@section('title', 'Account Details')
@section('page-title', 'Account Details')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>{{ $account->account_number }} - {{ $account->name }}</span>
        <div>
            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-primary">Edit</a>
            <a href="{{ route('accounts.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-muted small">Type</div>
                <strong>{{ $account->type }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Nature</div>
                <strong>{{ $account->nature }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Status</div>
                <span class="badge bg-{{ $account->is_active ? 'success' : 'secondary' }}">
                    {{ $account->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Balance</div>
                <strong>${{ number_format($account->getBalance(), 2) }}</strong>
            </div>
        </div>
        <hr>
        <h5>Transaction History</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Entry</th>
                    <th>Description</th>
                    <th>Debit</th>
                    <th>Credit</th>
                </tr>
            </thead>
            <tbody>
                @forelse($account->journalEntryLines as $line)
                    <tr>
                        <td>{{ $line->journalEntry->date->format('M d, Y') }}</td>
                        <td>{{ $line->journalEntry->entry_number }}</td>
                        <td>{{ $line->description ?? $line->journalEntry->description }}</td>
                        <td>${{ number_format($line->debit, 2) }}</td>
                        <td>${{ number_format($line->credit, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No transactions</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection