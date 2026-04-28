@extends('layouts.master')

@section('title', 'Balance Sheet')
@section('page-title', 'Balance Sheet')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Balance Sheet</span>
        <form method="GET" class="d-flex gap-2">
            <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $asOfDate }}">
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        </form>
    </div>
    <div class="card-body">
        <h5 class="mb-3">Assets</h5>
        <table class="table table-sm">
            <tbody>
                @forelse($assets as $item)
                    <tr>
                        <td>{{ $item['account']->name }}</td>
                        <td class="text-end">${{ number_format($item['balance'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-muted">No assets</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-primary">
                    <th>Total Assets</th>
                    <th class="text-end">${{ number_format($totalAssets, 2) }}</th>
                </tr>
            </tfoot>
        </table>

        <h5 class="mb-3">Liabilities</h5>
        <table class="table table-sm">
            <tbody>
                @forelse($liabilities as $item)
                    <tr>
                        <td>{{ $item['account']->name }}</td>
                        <td class="text-end">${{ number_format($item['balance'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-muted">No liabilities</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-danger">
                    <th>Total Liabilities</th>
                    <th class="text-end">${{ number_format($totalLiabilities, 2) }}</th>
                </tr>
            </tfoot>
        </table>

        <h5 class="mb-3">Equity</h5>
        <table class="table table-sm">
            <tbody>
                @forelse($equity as $item)
                    <tr>
                        <td>{{ $item['account']->name }}</td>
                        <td class="text-end">${{ number_format($item['balance'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="text-muted">No equity</td></tr>
                @endforelse
                <tr>
                    <td>Retained Earnings</td>
                    <td class="text-end">${{ number_format($retainedEarnings, 2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="table-info">
                    <th>Total Equity</th>
                    <th class="text-end">${{ number_format($totalEquity + $retainedEarnings, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection