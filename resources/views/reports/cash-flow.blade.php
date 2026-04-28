@extends('layouts.master')

@section('title', 'Cash Flow Report')
@section('page-title', 'Cash Flow Report')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Cash Flow Statement</span>
        <form method="GET" class="d-flex gap-2">
            <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        </form>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card success">
                    <div class="text-white-50">Total Inflow</div>
                    <div class="value">${{ number_format($totalInflow, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card danger">
                    <div class="text-white-50">Total Outflow</div>
                    <div class="value">${{ number_format($totalOutflow, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card {{ $netCashFlow >= 0 ? 'info' : 'danger' }}">
                    <div class="text-white-50">Net Cash Flow</div>
                    <div class="value">${{ number_format($netCashFlow, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">Cash Inflows</h5>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inflow as $item)
                            <tr>
                                <td>{{ ucfirst($item->category) }}</td>
                                <td class="text-end">${{ number_format($item->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No inflow</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-success">
                            <th>Total Inflow</th>
                            <th class="text-end">${{ number_format($totalInflow, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="mb-3">Cash Outflows</h5>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($outflow as $item)
                            <tr>
                                <td>{{ ucfirst($item->category) }}</td>
                                <td class="text-end">${{ number_format($item->total, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No outflow</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-danger">
                            <th>Total Outflow</th>
                            <th class="text-end">${{ number_format($totalOutflow, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection