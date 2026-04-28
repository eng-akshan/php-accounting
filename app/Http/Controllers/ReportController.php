<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function profitLoss(Request $request)
    {
        $dateFrom = $request->date_from ?? Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ?? Carbon::now()->endOfMonth();

        $revenueAccounts = Account::where('type', 'Revenue')->where('is_active', true)->get();
        $expenseAccounts = Account::where('type', 'Expense')->where('is_active', true)->get();

        $revenue = [];
        foreach ($revenueAccounts as $account) {
            $total = JournalEntryLine::whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date', [$dateFrom, $dateTo])->where('status', 'posted');
            })->where('account_id', $account->id)->sum('credit');

            $debit = JournalEntryLine::whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date', [$dateFrom, $dateTo])->where('status', 'posted');
            })->where('account_id', $account->id)->sum('debit');

            $revenue[] = [
                'account' => $account,
                'total' => $total - $debit,
            ];
        }

        $totalRevenue = collect($revenue)->sum('total');

        $expenses = [];
        foreach ($expenseAccounts as $account) {
            $total = JournalEntryLine::whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date', [$dateFrom, $dateTo])->where('status', 'posted');
            })->where('account_id', $account->id)->sum('debit');

            $credit = JournalEntryLine::whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date', [$dateFrom, $dateTo])->where('status', 'posted');
            })->where('account_id', $account->id)->sum('credit');

            $expenses[] = [
                'account' => $account,
                'total' => $total - $credit,
            ];
        }

        $totalExpenses = collect($expenses)->sum('total');
        $netProfit = $totalRevenue - $totalExpenses;

        return view('reports.profit-loss', compact('revenue', 'expenses', 'totalRevenue', 'totalExpenses', 'netProfit', 'dateFrom', 'dateTo'));
    }

    public function balanceSheet(Request $request)
    {
        $asOfDate = $request->date_to ?? Carbon::now();

        $assetAccounts = Account::where('type', 'Asset')->where('is_active', true)->get();
        $liabilityAccounts = Account::where('type', 'Liability')->where('is_active', true)->get();
        $equityAccounts = Account::where('type', 'Equity')->where('is_active', true)->get();

        $assets = [];
        foreach ($assetAccounts as $account) {
            $balance = $account->getBalance();
            $assets[] = [
                'account' => $account,
                'balance' => $balance,
            ];
        }

        $totalAssets = collect($assets)->sum('balance');

        $liabilities = [];
        foreach ($liabilityAccounts as $account) {
            $balance = $account->getBalance();
            $liabilities[] = [
                'account' => $account,
                'balance' => $balance,
            ];
        }

        $totalLiabilities = collect($liabilities)->sum('balance');

        $equity = [];
        foreach ($equityAccounts as $account) {
            $balance = $account->getBalance();
            $equity[] = [
                'account' => $account,
                'balance' => $balance,
            ];
        }

        $totalEquity = collect($equity)->sum('balance');

        $retainedEarnings = Income::where('date', '<=', $asOfDate)->sum('amount') - Expense::where('date', '<=', $asOfDate)->sum('amount');

        return view('reports.balance-sheet', compact('assets', 'liabilities', 'equity', 'totalAssets', 'totalLiabilities', 'totalEquity', 'retainedEarnings', 'asOfDate'));
    }

    public function cashFlow(Request $request)
    {
        $dateFrom = $request->date_from ?? Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ?? Carbon::now()->endOfMonth();

        $inflow = Income::whereBetween('date', [$dateFrom, $dateTo])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        $outflow = Expense::whereBetween('date', [$dateFrom, $dateTo])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        $totalInflow = $inflow->sum('total');
        $totalOutflow = $outflow->sum('total');
        $netCashFlow = $totalInflow - $totalOutflow;

        return view('reports.cash-flow', compact('inflow', 'outflow', 'totalInflow', 'totalOutflow', 'netCashFlow', 'dateFrom', 'dateTo'));
    }

    public function generalLedger(Request $request)
    {
        $dateFrom = $request->date_from ?? Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ?? Carbon::now()->endOfMonth();

        $accountId = $request->account_id;

        $accounts = Account::where('is_active', true)
            ->where('allow_transaction', true)
            ->orderBy('account_number')
            ->get();

        if ($accountId) {
            $account = Account::findOrFail($accountId);
            $entries = JournalEntryLine::whereHas('journalEntry', function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date', [$dateFrom, $dateTo])->where('status', 'posted');
            })->where('account_id', $accountId)
                ->with('journalEntry')
                ->orderBy('id')
                ->get();

            return view('reports.general-ledger', compact('accounts', 'account', 'entries', 'dateFrom', 'dateTo'));
        }

        $entries = $entries ?? new Collection;
        return view('reports.general-ledger', compact('accounts', 'dateFrom', 'dateTo', 'entries'));
    }
}