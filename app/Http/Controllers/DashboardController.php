<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();

        // Cash Balance - ONLY from Cash account (1000) using journal_lines
        $cashAccount = Account::where('account_number', '1000')->first();
        $cashBalance = 0;
        if ($cashAccount) {
            $cashDebit = JournalEntryLine::where('account_id', $cashAccount->id)
                ->whereHas('journalEntry', function ($q) {
                    $q->where('status', 'posted');
                })
                ->sum('debit');
            $cashCredit = JournalEntryLine::where('account_id', $cashAccount->id)
                ->whereHas('journalEntry', function ($q) {
                    $q->where('status', 'posted');
                })
                ->sum('credit');
            $cashBalance = $cashDebit - $cashCredit;
        }

        // Total Revenue - sum of credit on Revenue type accounts from journal_lines
        $revenueAccountIds = Account::where('type', 'Revenue')->pluck('id');
        $totalRevenue = JournalEntryLine::whereIn('account_id', $revenueAccountIds)
            ->whereHas('journalEntry', function ($q) {
                $q->where('status', 'posted');
            })
            ->sum('credit');

        // Total Expenses - sum of debit on Expense type accounts from journal_lines
        $expenseAccountIds = Account::where('type', 'Expense')->pluck('id');
        $totalExpenses = JournalEntryLine::whereIn('account_id', $expenseAccountIds)
            ->whereHas('journalEntry', function ($q) {
                $q->where('status', 'posted');
            })
            ->sum('debit');

        // Inventory Stats
        $lowStockProducts = Product::lowStock()->limit(5)->get();
        $inventoryValue = Product::where('is_active', true)->sum(DB::raw('quantity * cost'));
        $totalProducts = Product::where('is_active', true)->count();

        // Net Profit
        $netProfit = $totalRevenue - $totalExpenses;

        // Previous month for trend calculation
        $prevMonthStart = $now->copy()->subMonth()->startOfMonth();
        $prevMonthEnd = $now->copy()->subMonth()->endOfMonth();

        $prevRevenue = JournalEntryLine::whereIn('account_id', $revenueAccountIds)
            ->whereHas('journalEntry', function ($q) use ($prevMonthStart, $prevMonthEnd) {
                $q->where('status', 'posted')->whereBetween('date', [$prevMonthStart, $prevMonthEnd]);
            })
            ->sum('credit');

        $prevExpenses = JournalEntryLine::whereIn('account_id', $expenseAccountIds)
            ->whereHas('journalEntry', function ($q) use ($prevMonthStart, $prevMonthEnd) {
                $q->where('status', 'posted')->whereBetween('date', [$prevMonthStart, $prevMonthEnd]);
            })
            ->sum('debit');

        $prevNetProfit = $prevRevenue - $prevExpenses;

        // Trends
        $revenueTrend = $prevRevenue > 0 ? (($totalRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;
        $expenseTrend = $prevExpenses > 0 ? (($totalExpenses - $prevExpenses) / $prevExpenses) * 100 : 0;
        $profitTrend = $prevNetProfit != 0 ? (($netProfit - $prevNetProfit) / abs($prevNetProfit)) * 100 : 0;

        // Accounts Receivable (AR) - account 1100
        $arAccount = Account::where('account_number', '1100')->first();
        $receivable = 0;
        if ($arAccount) {
            $arDebit = JournalEntryLine::where('account_id', $arAccount->id)
                ->whereHas('journalEntry', function ($q) {
                    $q->where('status', 'posted');
                })
                ->sum('debit');
            $arCredit = JournalEntryLine::where('account_id', $arAccount->id)
                ->whereHas('journalEntry', function ($q) {
                    $q->where('status', 'posted');
                })
                ->sum('credit');
            $receivable = $arDebit - $arCredit;
        }

        // Accounts Payable (AP) - account 2000
        $apAccount = Account::where('account_number', '2000')->first();
        $payable = 0;
        if ($apAccount) {
            $apDebit = JournalEntryLine::where('account_id', $apAccount->id)
                ->whereHas('journalEntry', function ($q) {
                    $q->where('status', 'posted');
                })
                ->sum('debit');
            $apCredit = JournalEntryLine::where('account_id', $apAccount->id)
                ->whereHas('journalEntry', function ($q) {
                    $q->where('status', 'posted');
                })
                ->sum('credit');
            $payable = $apCredit - $apDebit;
        }

        // Recent Transactions
        $recentTransactions = JournalEntry::with('creator')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Monthly data for chart - from journal_lines
        $monthlyIncome = JournalEntryLine::select(
            DB::raw('MONTH(journal_entries.date) as month'),
            DB::raw('SUM(credit) as total')
        )
            ->whereIn('account_id', $revenueAccountIds)
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.status', 'posted')
            ->whereBetween('journal_entries.date', [$startOfYear, $endOfYear])
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyExpense = JournalEntryLine::select(
            DB::raw('MONTH(journal_entries.date) as month'),
            DB::raw('SUM(debit) as total')
        )
            ->whereIn('account_id', $expenseAccountIds)
            ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
            ->where('journal_entries.status', 'posted')
            ->whereBetween('journal_entries.date', [$startOfYear, $endOfYear])
            ->groupBy('month')
            ->pluck('total', 'month');

        // Pending Invoices
        $pendingInvoices = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])
            ->with('customer')
            ->limit(5)
            ->get();

        // Unpaid Invoices List
        $unpaidInvoices = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])
            ->with('customer')
            ->get()
            ->map(function ($inv) {
                $inv->balance = $inv->total - $inv->paid_amount;
                return $inv;
            })
            ->filter(fn($inv) => $inv->balance > 0);

        // Unpaid Bills (from expenses table - informational only)
        $unpaidBills = Expense::orderByDesc('date')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'totalRevenue',
            'totalExpenses',
            'netProfit',
            'cashBalance',
            'recentTransactions',
            'monthlyIncome',
            'monthlyExpense',
            'pendingInvoices',
            'revenueTrend',
            'expenseTrend',
            'profitTrend',
            'receivable',
            'payable',
            'unpaidInvoices',
            'unpaidBills',
            'lowStockProducts',
            'inventoryValue',
            'totalProducts'
        ));
    }
}