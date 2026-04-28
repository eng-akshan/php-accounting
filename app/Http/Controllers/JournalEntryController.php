<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AuditLog;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $entries = JournalEntry::with(['creator', 'lines.account'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->date_from, fn($q) => $q->where('date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->where('date', '<=', $request->date_to))
            ->orderByDesc('date')
            ->paginate(15);

        return view('journal-entries.index', compact('entries'));
    }

    public function create()
    {
        $accounts = Account::where('is_active', true)->where('allow_transaction', true)->get();
        return view('journal-entries.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'status' => 'required|in:draft,posted,voided',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
        ]);

        $totalDebit = collect($data['lines'])->sum('debit');
        $totalCredit = collect($data['lines'])->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->with('error', 'Journal entry must be balanced (Debits = Credits)');
        }

        $entry = JournalEntry::create([
            'date' => $data['date'],
            'description' => $data['description'],
            'created_by' => auth()->id(),
            'status' => 'draft',
        ]);

        foreach ($data['lines'] as $line) {
            if ($line['debit'] > 0 || $line['credit'] > 0) {
                $entry->lines()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?: 0,
                    'credit' => $line['credit'] ?: 0,
                    'description' => $line['description'] ?? null,
                ]);
            }
        }

        AuditLog::log('create', 'JournalEntry', $entry->id, null, $entry->toArray());

        return redirect()->route('journal-entries.show', $entry)->with('success', 'Journal entry created');
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load(['lines.account', 'creator', 'approver']);
        return view('journal-entries.show', compact('journalEntry'));
    }

    public function edit(JournalEntry $journalEntry)
    {
        $accounts = Account::where('is_active', true)->where('allow_transaction', true)->get();
        return view('journal-entries.edit', compact('journalEntry', 'accounts'));
    }

    public function update(Request $request, JournalEntry $journalEntry)
    {

        $data = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string',
            'status' => 'required|in:draft,posted,voided',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'required|numeric|min:0',
            'lines.*.credit' => 'required|numeric|min:0',
        ]);

        $totalDebit = collect($data['lines'])->sum('debit');
        $totalCredit = collect($data['lines'])->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->with('error', 'Journal entry must be balanced');
        }

        $oldValues = $journalEntry->toArray();
        
        $journalEntry->update([
            'date' => $data['date'],
            'description' => $data['description'],
            'status' => $data['status'],
        ]);

        $journalEntry->lines()->delete();

        foreach ($data['lines'] as $line) {
            if ($line['debit'] > 0 || $line['credit'] > 0) {
                $journalEntry->lines()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?: 0,
                    'credit' => $line['credit'] ?: 0,
                    'description' => $line['description'] ?? null,
                ]);
            }
        }

        AuditLog::log('update', 'JournalEntry', $journalEntry->id, $oldValues, $journalEntry->fresh()->toArray());

        return redirect()->route('journal-entries.show', $journalEntry)->with('success', 'Journal entry updated');
    }

    public function post(JournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'draft') {
            return back()->with('error', 'Only draft entries can be posted');
        }

        if (!$journalEntry->isBalanced()) {
            return back()->with('error', 'Entry must be balanced before posting');
        }

        $oldValues = $journalEntry->toArray();
        $journalEntry->update([
            'status' => 'posted',
            'approved_by' => auth()->id(),
        ]);

        AuditLog::log('post', 'JournalEntry', $journalEntry->id, $oldValues, $journalEntry->fresh()->toArray());

        return redirect()->route('journal-entries.show', $journalEntry)->with('success', 'Journal entry posted');
    }

    public function void(JournalEntry $journalEntry)
    {
        if ($journalEntry->status !== 'posted') {
            return back()->with('error', 'Only posted entries can be voided');
        }

        $oldValues = $journalEntry->toArray();
        $journalEntry->update(['status' => 'voided']);

        AuditLog::log('void', 'JournalEntry', $journalEntry->id, $oldValues, $journalEntry->fresh()->toArray());

        return redirect()->route('journal-entries.index')->with('success', 'Journal entry voided');
    }
}