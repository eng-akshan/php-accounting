<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::with(['parent', 'branch'])
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('account_number')
            ->paginate(15);

        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        $parents = Account::whereNull('parent_id')->get();
        return view('accounts.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'account_number' => 'required|unique:accounts',
            'name' => 'required|string|max:255',
            'type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'nature' => 'required|in:Debit,Credit',
            'parent_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();
        
        $account = Account::create($data);

        AuditLog::log('create', 'Account', $account->id, null, $account->toArray());

        return redirect()->route('accounts.index')->with('success', 'Account created successfully');
    }

    public function show(Account $account)
    {
        $account->load(['parent', 'children', 'journalEntryLines.journalEntry']);
        return view('accounts.show', compact('account'));
    }

    public function edit(Account $account)
    {
        $parents = Account::whereNull('parent_id')->where('id', '!=', $account->id)->get();
        return view('accounts.edit', compact('account', 'parents'));
    }

    public function update(Request $request, Account $account)
    {
        $data = $request->validate([
            'account_number' => 'required|unique:accounts,account_number,' . $account->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'nature' => 'required|in:Debit,Credit',
            'parent_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $oldValues = $account->toArray();
        $account->update($data);
        
        AuditLog::log('update', 'Account', $account->id, $oldValues, $account->fresh()->toArray());

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully');
    }

    public function destroy(Account $account)
    {
        if ($account->journalEntryLines()->exists()) {
            return back()->with('error', 'Cannot delete account with journal entries');
        }

        $oldValues = $account->toArray();
        $account->delete();
        
        AuditLog::log('delete', 'Account', $account->id, $oldValues, null);

        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully');
    }
}