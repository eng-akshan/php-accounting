<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Income;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $incomes = Income::with(['account', 'customer'])
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->when($request->date_from, fn($q) => $q->where('date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->where('date', '<=', $request->date_to))
            ->orderByDesc('date')
            ->paginate(15);

        return view('incomes.index', compact('incomes'));
    }

    public function create()
    {
        $accounts = Account::where('type', 'Revenue')->where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->get();
        return view('incomes.create', compact('accounts', 'customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category' => 'required|in:sales,service,rent,interest,other',
            'account_id' => 'required|exists:accounts,id',
            'customer_id' => 'nullable|exists:customers,id',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|in:daily,weekly,monthly,quarterly,yearly',
            'recurring_end_date' => 'nullable|date',
        ]);

        $data['created_by'] = auth()->id();
        $data['is_recurring'] = $request->boolean('is_recurring');

        $income = Income::create($data);

        // Create Journal Entry for this income
        $revenueAccount = Account::find($data['account_id']);
        $cashAccount = Account::where('account_number', '1000')->first();

        $journalEntry = JournalEntry::create([
            'entry_number' => 'JE-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'date' => $data['date'],
            'description' => 'Income: ' . $data['description'],
            'status' => 'posted',
            'created_by' => auth()->id(),
        ]);

        // Debit to Cash, Credit to Revenue
        $journalEntry->lines()->createMany([
            [
                'account_id' => $cashAccount->id,
                'description' => 'Cash received',
                'debit' => $data['amount'],
                'credit' => 0,
            ],
            [
                'account_id' => $revenueAccount->id,
                'description' => $data['description'],
                'debit' => 0,
                'credit' => $data['amount'],
            ],
        ]);

        AuditLog::log('create', 'Income', $income->id, null, $income->toArray());

        return redirect()->route('incomes.index')->with('success', 'Income recorded successfully');
    }

    public function show(Income $income)
    {
        return view('incomes.show', compact('income'));
    }

    public function edit(Income $income)
    {
        $accounts = Account::where('type', 'Revenue')->where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->get();
        return view('incomes.edit', compact('income', 'accounts', 'customers'));
    }

    public function update(Request $request, Income $income)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category' => 'required|in:sales,service,rent,interest,other',
            'account_id' => 'required|exists:accounts,id',
            'customer_id' => 'nullable|exists:customers,id',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|in:daily,weekly,monthly,quarterly,yearly',
            'recurring_end_date' => 'nullable|date',
        ]);

        $data['is_recurring'] = $request->boolean('is_recurring');
        $oldValues = $income->toArray();
        $income->update($data);

        AuditLog::log('update', 'Income', $income->id, $oldValues, $income->fresh()->toArray());

        return redirect()->route('incomes.index')->with('success', 'Income updated successfully');
    }

    public function destroy(Income $income)
    {
        $oldValues = $income->toArray();
        $income->delete();

        AuditLog::log('delete', 'Income', $income->id, $oldValues, null);

        return redirect()->route('incomes.index')->with('success', 'Income deleted successfully');
    }
}