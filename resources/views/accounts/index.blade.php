@extends('layouts.master')

@section('title', 'Chart of Accounts')
@section('page-title', 'Chart of Accounts')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Accounts List</span>
        <a href="{{ route('accounts.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> New Account
        </a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="Asset">Asset</option>
                    <option value="Liability">Liability</option>
                    <option value="Equity">Equity</option>
                    <option value="Revenue">Revenue</option>
                    <option value="Expense">Expense</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search accounts...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">Filter</button>
            </div>
        </form>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Account #</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Nature</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                    <tr>
                        <td>{{ $account->account_number }}</td>
                        <td>
                            @if($account->parent_id)
                                <span class="text-muted">└─</span>
                            @endif
                            {{ $account->name }}
                        </td>
                        <td>
                            <span class="badge bg-{{ match($account->type) {
                                'Asset' => 'primary',
                                'Liability' => 'danger',
                                'Equity' => 'info',
                                'Revenue' => 'success',
                                'Expense' => 'warning'
                            } }}">
                                {{ $account->type }}
                            </span>
                        </td>
                        <td>{{ $account->nature }}</td>
                        <td>${{ number_format($account->getBalance(), 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $account->is_active ? 'success' : 'secondary' }}">
                                {{ $account->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('accounts.show', $account) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if(!$account->journalEntryLines()->exists())
                                <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this account?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No accounts found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $accounts->links() }}
    </div>
</div>
@endsection