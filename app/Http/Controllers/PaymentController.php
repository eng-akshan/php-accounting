<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['customer', 'invoice'])
            ->when($request->method, fn($q) => $q->where('method', $request->method))
            ->when($request->date_from, fn($q) => $q->where('payment_date', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->where('payment_date', '<=', $request->date_to))
            ->orderByDesc('payment_date')
            ->paginate(15);

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->get();
        $invoices = Invoice::whereIn('status', ['sent', 'partial', 'overdue'])
            ->whereRaw('total > paid_amount')
            ->get();
        $invoicesByCustomer = $invoices->groupBy('customer_id')->map(fn($inv) => $inv->toArray());
        return view('payments.create', compact('customers', 'invoices', 'invoicesByCustomer'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:cash,bank_transfer,mobile_money,cheque,other',
            'reference_number' => 'nullable|string',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();
        $data['status'] = 'completed';

        $payment = Payment::create($data);

        if ($data['invoice_id']) {
            $invoice = Invoice::find($data['invoice_id']);
            $newPaidAmount = $invoice->paid_amount + $payment->amount;
            
            if ($newPaidAmount >= $invoice->total) {
                $invoice->update([
                    'paid_amount' => $newPaidAmount,
                    'status' => 'paid',
                ]);
            } else {
                $invoice->update([
                    'paid_amount' => $newPaidAmount,
                    'status' => 'partial',
                ]);
            }
        }

        AuditLog::log('create', 'Payment', $payment->id, null, $payment->toArray());

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully');
    }

    public function show(Payment $payment)
    {
        return view('payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        $oldValues = $payment->toArray();
        $payment->delete();

        AuditLog::log('delete', 'Payment', $payment->id, $oldValues, null);

        return redirect()->route('payments.index')->with('success', 'Payment deleted successfully');
    }
}