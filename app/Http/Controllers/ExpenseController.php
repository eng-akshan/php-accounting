<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AuditLog;
use App\Models\Expense;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::with(['account'])
            ->when($request->category, function($q) use ($request) { $q->where('category', $request->category); })
            ->when($request->payment_status, function($q) use ($request) { $q->where('payment_status', $request->payment_status); })
            ->when($request->date_from, function($q) use ($request) { $q->where('date', '>=', $request->date_from); })
            ->when($request->date_to, function($q) use ($request) { $q->where('date', '<=', $request->date_to); })
            ->orderByDesc('date')
            ->paginate(15);

        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $accounts = Account::where('type', 'Expense')->where('is_active', true)->get();
        return view('expenses.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category' => 'required|in:salary,utilities,rent,supplies,maintenance,marketing,transportation,other',
            'account_id' => 'required|exists:accounts,id',
            'vendor' => 'nullable|string',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|in:daily,weekly,monthly,quarterly,yearly',
            'recurring_end_date' => 'nullable|date',
            'payment_status' => 'nullable|in:paid,unpaid',
        ]);

        $data['created_by'] = auth()->id();
        $data['is_recurring'] = $request->boolean('is_recurring');
        $paymentStatus = $request->input('payment_status', 'paid');

        $expense = Expense::create($data);

        $cashAccount = Account::where('account_number', '1000')->first();
        $expenseAccount = Account::find($data['account_id']);

        $journalEntry = JournalEntry::create([
            'date' => $data['date'],
            'description' => 'Expense: ' . $data['description'],
            'created_by' => auth()->id(),
            'status' => 'posted',
        ]);

        $journalEntry->lines()->create([
            'account_id' => $expenseAccount->id,
            'debit' => $data['amount'],
            'credit' => 0,
            'description' => $data['description'],
        ]);

        if ($paymentStatus === 'paid' && $cashAccount) {
            $journalEntry->lines()->create([
                'account_id' => $cashAccount->id,
                'debit' => 0,
                'credit' => $data['amount'],
                'description' => 'Payment for: ' . $data['description'],
            ]);
        } else {
            $APAccount = Account::where('account_number', '2000')->first();
            if ($APAccount) {
                $journalEntry->lines()->create([
                    'account_id' => $APAccount->id,
                    'debit' => 0,
                    'credit' => $data['amount'],
                    'description' => 'Payable for: ' . $data['description'],
                ]);
            }
        }

        AuditLog::log('create', 'Expense', $expense->id, null, $expense->toArray());

        return redirect()->route('expenses.index')->with('success', 'Expense recorded with journal entry #' . $journalEntry->id);
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $accounts = Account::where('type', 'Expense')->where('is_active', true)->get();
        return view('expenses.edit', compact('expense', 'accounts'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category' => 'required|in:salary,utilities,rent,supplies,maintenance,marketing,transportation,other',
            'account_id' => 'required|exists:accounts,id',
            'vendor' => 'nullable|string',
            'is_recurring' => 'boolean',
            'recurring_frequency' => 'nullable|in:daily,weekly,monthly,quarterly,yearly',
            'recurring_end_date' => 'nullable|date',
        ]);

        $data['is_recurring'] = $request->boolean('is_recurring');
        $oldValues = $expense->toArray();
        $expense->update($data);

        AuditLog::log('update', 'Expense', $expense->id, $oldValues, $expense->fresh()->toArray());

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully');
    }

    public function destroy(Expense $expense)
    {
        $oldValues = $expense->toArray();
        $expense->delete();

        AuditLog::log('delete', 'Expense', $expense->id, $oldValues, null);

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully');
    }

    public function payBill(Expense $expense)
    {
        if ($expense->payment_status === 'paid') {
            return back()->with('error', 'Bill is already paid');
        }

        $cashAccount = Account::where('account_number', '1000')->first();
        $APAccount = Account::where('account_number', '2000')->first();
        $expenseAccount = $expense->account;

        $journalEntry = JournalEntry::create([
            'date' => now()->format('Y-m-d'),
            'description' => 'Bill Payment: ' . $expense->description,
            'created_by' => auth()->id(),
            'status' => 'posted',
        ]);

        $journalEntry->lines()->create([
            'account_id' => $APAccount->id,
            'debit' => $expense->amount,
            'credit' => 0,
            'description' => 'AP cleared - ' . $expense->description,
        ]);

        $journalEntry->lines()->create([
            'account_id' => $cashAccount->id,
            'debit' => 0,
            'credit' => $expense->amount,
            'description' => 'Cash payment - ' . $expense->description,
        ]);

        $expense->update([
            'payment_status' => 'paid',
            'paid_date' => now(),
        ]);

        AuditLog::log('pay-bill', 'Expense', $expense->id, null, ['status' => 'paid']);

        return back()->with('success', 'Bill marked as paid');
    }
}