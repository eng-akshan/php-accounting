<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AuditLog;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\InventoryTransaction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JournalEntry;
use App\Models\Product;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with(['customer'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->customer_id, fn($q) => $q->where('customer_id', $request->customer_id))
            ->when($request->date_from, fn($q) => $q->where('issue_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->where('issue_date', '<=', $request->date_to))
            ->orderByDesc('issue_date')
            ->paginate(15);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->get();
        $products = Product::active()->where('quantity', '>', 0)->get();
        return view('invoices.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $subtotal = 0;
        $totalCost = 0;
        $hasInventory = false;
        $insufficientStock = [];

        foreach ($data['items'] as &$item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
            $subtotal += $item['total'];

            if (!empty($item['product_id'])) {
                $product = Product::find($item['product_id']);
                if ($product && $product->cost > 0) {
                    $totalCost += $product->cost * $item['quantity'];
                }
                if (!$product->hasStock($item['quantity'])) {
                    $insufficientStock[] = $product->name;
                }
                $hasInventory = true;
            }
        }

        if (!empty($insufficientStock)) {
            return back()->with('error', 'Insufficient stock for: ' . implode(', ', $insufficientStock));
        }

        $taxRate = CompanySetting::getSettings()->tax_rate ?? 0;
        $taxAmount = $subtotal * ($taxRate / 100);
        $discountAmount = $data['discount_amount'] ?? 0;
        $total = $subtotal + $taxAmount - $discountAmount;

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'customer_id' => $data['customer_id'],
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total' => $total,
                'paid_amount' => 0,
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $invoiceItem = $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                    'product_id' => $item['product_id'] ?? null,
                ]);

                if (!empty($item['product_id'])) {
                    $product = Product::find($item['product_id']);
                    $product->reduceStock($item['quantity']);

                    InventoryTransaction::create([
                        'product_id' => $product->id,
                        'type' => 'out',
                        'quantity' => $item['quantity'],
                        'reference_type' => Invoice::class,
                        'reference_id' => $invoice->id,
                        'description' => 'Invoice #' . $invoice->id,
                        'date' => $data['issue_date'],
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            $this->createSalesJournalEntry($invoice, $total, $totalCost);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }

        AuditLog::log('create', 'Invoice', $invoice->id, null, $invoice->toArray());

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully');
    }

    private function createSalesJournalEntry(Invoice $invoice, float $total, float $cogs = 0)
    {
        $cashAccount = Account::where('account_number', '1000')->first();
        $arAccount = Account::where('account_number', '1100')->first();
        $revenueAccount = Account::where('account_number', '4000')->first();
        $cogsAccount = Account::where('account_number', '5000')->first();

        if (!$revenueAccount) return;

        $entry = JournalEntry::create([
            'date' => $invoice->issue_date,
            'description' => 'Sale Invoice #' . $invoice->id,
            'status' => 'posted',
            'created_by' => auth()->id(),
        ]);

        $entry->lines()->create([
            'account_id' => $cashAccount ? $cashAccount->id : $arAccount->id,
            'debit' => $total,
            'credit' => 0,
            'description' => 'Payment received',
        ]);

        $entry->lines()->create([
            'account_id' => $revenueAccount->id,
            'debit' => 0,
            'credit' => $total,
            'description' => 'Sales revenue',
        ]);

        if ($cogs > 0 && $cogsAccount) {
            $entry->lines()->create([
                'account_id' => $cogsAccount->id,
                'debit' => $cogs,
                'credit' => 0,
                'description' => 'Cost of goods sold',
            ]);

            $entry->lines()->create([
                'account_id' => $arAccount ? $arAccount->id : ($cashAccount ? $cashAccount->id : null),
                'debit' => 0,
                'credit' => $cogs,
                'description' => 'Inventory reduction',
            ]);
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'payments']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Can only edit draft invoices');
        }

        $customers = Customer::where('is_active', true)->get();
        $invoice->load('items');
        return view('invoices.edit', compact('invoice', 'customers'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Can only edit draft invoices');
        }

        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $subtotal = 0;
        foreach ($data['items'] as &$item) {
            $item['total'] = $item['quantity'] * $item['unit_price'];
            $subtotal += $item['total'];
        }

        $taxRate = CompanySetting::getSettings()->tax_rate ?? 0;
        $taxAmount = $subtotal * ($taxRate / 100);
        $discountAmount = $data['discount_amount'] ?? 0;
        $total = $subtotal + $taxAmount - $discountAmount;

        $oldValues = $invoice->toArray();
        
        $invoice->update([
            'customer_id' => $data['customer_id'],
            'issue_date' => $data['issue_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'notes' => $data['notes'] ?? null,
        ]);

        $invoice->items()->delete();
        foreach ($data['items'] as $item) {
            $invoice->items()->create($item);
        }

        AuditLog::log('update', 'Invoice', $invoice->id, $oldValues, $invoice->fresh()->toArray());

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully');
    }

    public function send(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Can only send draft invoices');
        }

        $invoice->update(['status' => 'sent']);
        
        AuditLog::log('send', 'Invoice', $invoice->id, null, ['status' => 'sent']);

        return back()->with('success', 'Invoice sent to customer');
    }

    public function markAsPaid(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Invoice is already paid');
        }

        DB::beginTransaction();
        try {
            $cashAccount = Account::where('account_number', '1000')->first();
            $arAccount = Account::where('account_number', '1100')->first();

            $entry = JournalEntry::create([
                'date' => now()->format('Y-m-d'),
                'description' => 'Payment received - Invoice #' . $invoice->id,
                'status' => 'posted',
                'created_by' => auth()->id(),
            ]);

            $entry->lines()->create([
                'account_id' => $cashAccount->id,
                'debit' => $invoice->total,
                'credit' => 0,
                'description' => 'Cash received from invoice',
            ]);

            $entry->lines()->create([
                'account_id' => $arAccount->id,
                'debit' => 0,
                'credit' => $invoice->total,
                'description' => 'AR cleared - Invoice #' . $invoice->id,
            ]);

            $invoice->update([
                'status' => 'paid',
                'paid_amount' => $invoice->total,
                'paid_date' => now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to mark as paid: ' . $e->getMessage());
        }

        AuditLog::log('paid', 'Invoice', $invoice->id, null, ['status' => 'paid']);

        return back()->with('success', 'Invoice marked as paid successfully');
    }

    public function cancel(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Cannot cancel paid invoice');
        }

        $invoice->update(['status' => 'cancelled']);
        
        AuditLog::log('cancel', 'Invoice', $invoice->id, null, ['status' => 'cancelled']);

        return back()->with('success', 'Invoice cancelled');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Can only delete draft invoices');
        }

        $invoice->items()->delete();
        $oldValues = $invoice->toArray();
        $invoice->delete();

        AuditLog::log('delete', 'Invoice', $invoice->id, $oldValues, null);

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'payments', 'creator']);
        $company = CompanySetting::getSettings();

        $html = view('invoices.pdf', compact('invoice', 'company'))->render();
        
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return response()->streamDownload(
            fn() => print($dompdf->output()),
            'invoice-' . $invoice->invoice_number . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}