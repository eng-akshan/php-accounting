@extends('layouts.master')

@section('title', 'Products')
@section('page-title', 'Products')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Products</span>
        <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Add Product
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Stock</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="{{ $product->isLowStock() ? 'table-warning' : '' }}">
                    <td>{{ $product->sku }}</td>
                    <td>
                        <a href="{{ route('products.edit', $product) }}">{{ $product->name }}</a>
                        @if($product->isLowStock())
                            <span class="badge bg-warning">Low Stock</span>
                        @endif
                    </td>
                    <td>{{ $product->category ?? '-' }}</td>
                    <td class="text-end">${{ number_format($product->price, 2) }}</td>
                    <td class="text-end">
                        <span class="{{ $product->quantity <= $product->min_stock ? 'text-danger fw-bold' : '' }}">
                            {{ $product->quantity }} {{ $product->unit }}
                        </span>
                    </td>
                    <td>
                        @if($product->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-link">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No products found. <a href="{{ route('products.create') }}">Add one</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $products->links() }}
    </div>
</div>
@endsection