<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $quotations = Quotation::with(['customer', 'user'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->customer_id, fn($q) => $q->where('customer_id', $request->customer_id))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->get();
        return view('quotations.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date|after:issue_date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $quotation = Quotation::create([
            'customer_id' => $data['customer_id'],
            'issue_date' => $data['issue_date'],
            'valid_until' => $data['valid_until'],
            'notes' => $data['notes'],
            'terms' => $data['terms'],
            'discount_amount' => $data['discount_amount'] ?? 0,
            'user_id' => auth()->id(),
            'status' => 'draft',
        ]);

        foreach ($data['items'] as $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $quotation->calculateTotals();

        AuditLog::log('create', 'Quotation', $quotation->id, null, $quotation->toArray());

        return redirect()->route('quotations.index')->with('success', 'Quotation created successfully');
    }

    public function show(Quotation $quotation)
    {
        $quotation->load(['customer', 'user', 'items']);
        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation)
    {
        $customers = Customer::where('is_active', true)->get();
        $quotation->load('items');
        return view('quotations.edit', compact('quotation', 'customers'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date|after:issue_date',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,sent,approved,rejected',
        ]);

        $oldValues = $quotation->toArray();

        $quotation->update($data);

        $quotation->items()->delete();
        foreach ($request->items as $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $quotation->calculateTotals();

        AuditLog::log('update', 'Quotation', $quotation->id, $oldValues, $quotation->fresh()->toArray());

        return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully');
    }

    public function destroy(Quotation $quotation)
    {
        $oldValues = $quotation->toArray();
        $quotation->delete();

        AuditLog::log('delete', 'Quotation', $quotation->id, $oldValues, null);

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully');
    }

    public function convertToInvoice(Quotation $quotation)
    {
        if ($quotation->status === 'converted') {
            return back()->with('error', 'Quotation already converted to invoice');
        }

        $invoice = $quotation->toInvoice();

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Quotation converted to invoice successfully');
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load(['customer', 'user', 'items']);
        $company = CompanySetting::getSettings();

        $html = view('quotations.pdf', compact('quotation', 'company'))->render();
        
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return response()->streamDownload(
            fn() => print($dompdf->output()),
            'quotation-' . $quotation->quotation_number . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}
