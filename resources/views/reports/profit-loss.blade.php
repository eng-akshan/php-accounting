@extends('layouts.master')

@section('title', 'Profit & Loss Report')
@section('page-title', 'Profit & Loss Report')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Profit & Loss Statement</span>
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
                    <div class="text-white-50">Total Revenue</div>
                    <div class="value">${{ number_format($totalRevenue, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card danger">
                    <div class="text-white-50">Total Expenses</div>
                    <div class="value">${{ number_format($totalExpenses, 2) }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card {{ $netProfit >= 0 ? 'info' : 'danger' }}">
                    <div class="text-white-50">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</div>
                    <div class="value">${{ number_format(abs($netProfit), 2) }}</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">Revenue</h5>
                <table class="table table-sm">
                    <tbody>
                        @forelse($revenue as $item)
                            <tr>
                                <td>{{ $item['account']->name }}</td>
                                <td class="text-end">${{ number_format($item['total'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No revenue</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-success">
                            <th>Total Revenue</th>
                            <th class="text-end">${{ number_format($totalRevenue, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="mb-3">Expenses</h5>
                <table class="table table-sm">
                    <tbody>
                        @forelse($expenses as $item)
                            <tr>
                                <td>{{ $item['account']->name }}</td>
                                <td class="text-end">${{ number_format($item['total'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">No expenses</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-danger">
                            <th>Total Expenses</th>
                            <th class="text-end">${{ number_format($totalExpenses, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection