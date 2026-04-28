@extends('layouts.master')

@section('title', 'Stock Movement')
@section('page-title', 'Stock Movement - ' . $product->name)

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Stock Movement: {{ $product->name }} (Current: {{ $product->quantity }} {{ $product->unit }})</span>
        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-secondary">Back</a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Description</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $tx)
                <tr>
                    <td>{{ $tx->date->format('Y-m-d') }}</td>
                    <td>
                        @if($tx->type === 'in')
                            <span class="badge bg-success">IN</span>
                        @else
                            <span class="badge bg-danger">OUT</span>
                        @endif
                    </td>
                    <td class="{{ $tx->type === 'out' ? 'text-danger' : 'text-success' }}">
                        {{ $tx->type === 'out' ? '-' : '+' }}{{ $tx->quantity }}
                    </td>
                    <td>{{ $tx->description ?? '-' }}</td>
                    <td>{{ $tx->reference_type ? class_basename($tx->reference_type) . '#' . $tx->reference_id : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No transactions</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $transactions->links() }}
    </div>
</div>
@endsection