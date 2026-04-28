@extends('layouts.master')

@section('title', 'General Ledger')
@section('page-title', 'General Ledger')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>General Ledger</span>
        <form method="GET" class="d-flex gap-2">
            <select name="account_id" class="form-select form-control-sm" onchange="this.form.submit()">
                <option value="">Select Account</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                        {{ $account->account_number }} - {{ $account->name }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        </form>
    </div>
    <div class="card-body">
        @if(isset($account))
            <h5 class="mb-3">{{ $account->account_number }} - {{ $account->name }}</h5>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Entry #</th>
                        <th>Description</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Credit</th>
                        <th class="text-end">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $balance = 0; ?>
                    @forelse($entries as $entry)
                        <?php
                        $balance += $entry->debit - $entry->credit;
                        if ($account->nature === 'Credit') {
                            $balance = $entry->credit - $entry->debit;
                        }
                        ?>
                        <tr>
                            <td>{{ $entry->journalEntry->date->format('M d, Y') }}</td>
                            <td>{{ $entry->journalEntry->entry_number }}</td>
                            <td>{{ $entry->description ?? $entry->journalEntry->description }}</td>
                            <td class="text-end">${{ number_format($entry->debit, 2) }}</td>
                            <td class="text-end">${{ number_format($entry->credit, 2) }}</td>
                            <td class="text-end">${{ number_format($balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No entries</td></tr>
                    @endforelse
                </tbody>
            </table>
        @else
            <p class="text-center text-muted">Select an account to view its ledger</p>
        @endif
    </div>
</div>
@endsection