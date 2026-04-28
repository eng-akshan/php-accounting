@extends('layouts.master')

@section('title', 'Journal Entry Details')
@section('page-title', 'Journal Entry Details')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Entry #{{ $journalEntry->entry_number }}</span>
        <div>
            @if($journalEntry->status === 'draft')
                <form action="{{ route('journal-entries.post', $journalEntry) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">Post Entry</button>
                </form>
            @endif
            <a href="{{ route('journal-entries.edit', $journalEntry) }}" class="btn btn-sm btn-secondary">Edit</a>
            @if($journalEntry->status !== 'voided')
                <form action="{{ route('journal-entries.void', $journalEntry) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Void this entry?')">Void</button>
                </form>
            @endif
            <a href="{{ route('journal-entries.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="text-muted small">Date</div>
                <strong>{{ $journalEntry->date->format('M d, Y') }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Status</div>
                <span class="badge bg-{{ $journalEntry->status === 'posted' ? 'success' : ($journalEntry->status === 'voided' ? 'danger' : 'warning') }}">
                    {{ ucfirst($journalEntry->status) }}
                </span>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Created By</div>
                <strong>{{ $journalEntry->creator->name ?? 'N/A' }}</strong>
            </div>
            <div class="col-md-3">
                <div class="text-muted small">Approved By</div>
                <strong>{{ $journalEntry->approver->name ?? 'N/A' }}</strong>
            </div>
        </div>
        <div class="mb-4">
            <div class="text-muted small">Description</div>
            <p class="mb-0">{{ $journalEntry->description }}</p>
        </div>

        <h5>Journal Lines</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Account</th>
                    <th>Description</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Credit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($journalEntry->lines as $line)
                    <tr>
                        <td>{{ $line->account->account_number }} - {{ $line->account->name }}</td>
                        <td>{{ $line->description }}</td>
                        <td class="text-end">${{ number_format($line->debit, 2) }}</td>
                        <td class="text-end">${{ number_format($line->credit, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><strong>Totals</strong></td>
                    <td class="text-end"><strong>${{ number_format($journalEntry->getTotalDebit(), 2) }}</strong></td>
                    <td class="text-end"><strong>${{ number_format($journalEntry->getTotalCredit(), 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection