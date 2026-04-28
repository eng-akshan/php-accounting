@extends('layouts.master')

@section('title', 'Journal Entries')
@section('page-title', 'Journal Entries')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Journal Entries</span>
        <a href="{{ route('journal-entries.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> New Entry
        </a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="posted">Posted</option>
                    <option value="voided">Voided</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date_from" class="form-control" placeholder="From Date">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_to" class="form-control" placeholder="To Date">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
            </div>
        </form>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Entry #</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                    <tr>
                        <td>{{ $entry->entry_number }}</td>
                        <td>{{ $entry->date->format('M d, Y') }}</td>
                        <td>{{ Str::limit($entry->description, 50) }}</td>
                        <td>${{ number_format($entry->getTotalDebit(), 2) }}</td>
                        <td>
                            <span class="badge bg-{{ match($entry->status) {
                                'posted' => 'success',
                                'voided' => 'danger',
                                default => 'warning'
                            } }}">
                                {{ ucfirst($entry->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('journal-entries.show', $entry) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($entry->status === 'draft')
                                <a href="{{ route('journal-entries.edit', $entry) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">No entries found</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $entries->links() }}
    </div>
</div>
@endsection