@extends('layouts.master')
@section('title', 'Quotations')
@section('page-title', 'Quotation Management')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Quotations</span>
        <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> New Quotation
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="GET" class="mb-3 row g-3">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Valid Until</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $quotation)
                        <tr>
                            <td>{{ $quotation->quotation_number }}</td>
                            <td>{{ $quotation->customer->name }}</td>
                            <td>{{ $quotation->issue_date->format('M d, Y') }}</td>
                            <td>{{ $quotation->valid_until->format('M d, Y') }}</td>
                            <td>{{ number_format($quotation->total, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $quotation->status == 'approved' ? 'success' : ($quotation->status == 'rejected' ? 'danger' : ($quotation->status == 'converted' ? 'info' : 'secondary')) }}">
                                    {{ ucfirst($quotation->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('quotations.show', $quotation->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($quotation->status !== 'converted')
                                    <a href="{{ route('quotations.edit', $quotation->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('quotations.convert', $quotation->id) }}" class="btn btn-sm btn-success" onclick="return confirm('Convert to Invoice?')">
                                        <i class="fas fa-file-invoice"></i> Convert
                                    </a>
                                @endif
                                <a href="{{ route('quotations.pdf', $quotation->id) }}" class="btn btn-sm btn-danger" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No quotations found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $quotations->links() }}
    </div>
</div>
@endsection