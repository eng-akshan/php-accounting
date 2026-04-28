@extends('layouts.master')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="card">
    <div class="card-header">Edit Product</div>
    <div class="card-body">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf
            @method('PUT')
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">SKU *</label>
                    <input type="text" name="sku" class="form-control" value="{{ $product->sku }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" value="{{ $product->category }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Unit</label>
                    <input type="text" name="unit" class="form-control" value="{{ $product->unit }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Current Stock</label>
                    <input type="text" class="form-control" value="{{ $product->quantity }} {{ $product->unit }}" disabled>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Selling Price *</label>
                    <input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ $product->price }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cost Price</label>
                    <input type="number" name="cost" class="form-control" step="0.01" min="0" value="{{ $product->cost }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Min Stock Alert</label>
                    <input type="number" name="min_stock" class="form-control" min="0" value="{{ $product->min_stock }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2">{{ $product->description }}</textarea>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" {{ $product->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="isActive">Active</label>
                </div>
            </div>

            <hr>
            <h5>Quick Stock Adjustment</h5>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Add Stock</label>
                    <form method="POST" action="{{ route('products.add-stock', $product) }}" class="input-group">
                        @csrf
                        <input type="number" name="quantity" class="form-control" min="1" placeholder="Quantity">
                        <input type="text" name="description" class="form-control" placeholder="Note (optional)">
                        <button type="submit" class="btn btn-success">Add</button>
                    </form>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                <a href="{{ route('products.stock-movement', $product) }}" class="btn btn-info">Stock History</a>
            </div>
        </form>
    </div>
</div>
@endsection