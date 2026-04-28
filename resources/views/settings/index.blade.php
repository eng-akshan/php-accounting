@extends('layouts.master')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-building me-2"></i>Company Settings
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row mb-4">
                <div class="col-md-3 text-center">
                    @if($settings->logo && file_exists(public_path('storage/' . $settings->logo)))
                        <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo" class="img-thumbnail" style="max-height: 120px;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
                            <i class="fas fa-building fa-3x text-muted"></i>
                        </div>
                    @endif
                    <div class="mt-2">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" name="company_name" class="form-control" value="{{ $settings->company_name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $settings->email }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ $settings->phone }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Currency</label>
                                <select name="currency" class="form-select">
                                    <option value="USD" {{ $settings->currency === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="SOS" {{ $settings->currency === 'SOS' ? 'selected' : '' }}>SOS - Somali Shilling</option>
                                    <option value="EUR" {{ $settings->currency === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="GBP" {{ $settings->currency === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ $settings->address }}</textarea>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tax Rate (%)</label>
                        <input type="number" name="tax_rate" class="form-control" value="{{ $settings->tax_rate }}" step="0.01" min="0">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Invoice Prefix</label>
                        <input type="text" name="invoice_prefix" class="form-control" value="{{ $settings->invoice_prefix }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Quotation Prefix</label>
                        <input type="text" name="quotation_prefix" class="form-control" value="{{ $settings->quotation_prefix }}">
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection