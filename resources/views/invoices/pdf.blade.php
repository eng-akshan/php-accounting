<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.5; color: #333; padding: 20px; }
        .invoice-container { max-width: 800px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #1e293b; }
        .company-info h1 { font-size: 24px; color: #1e293b; margin-bottom: 5px; }
        .company-info p { font-size: 11px; color: #666; }
        .invoice-details { text-align: right; }
        .invoice-details h2 { font-size: 28px; color: #1e293b; margin-bottom: 10px; }
        .invoice-details p { margin: 3px 0; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .badge-draft { background: #e2e8f0; color: #475569; }
        .badge-sent { background: #fef3c7; color: #92400e; }
        .badge-paid { background: #dcfce7; color: #166534; }
        .badge-partial { background: #dbeafe; color: #1e40af; }
        .badge-overdue { background: #fee2e2; color: #991b1b; }
        .badge-cancelled { background: #f3f4f6; color: #6b7280; }
        .parties { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .party-box { width: 48%; }
        .party-box h3 { font-size: 12px; color: #1e293b; margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid #e2e8f0; }
        .party-box p { margin: 2px 0; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; color: #1e293b; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .totals { margin-top: 20px; }
        .totals table { width: 300px; margin-left: auto; }
        .totals th, .totals td { border: none; padding: 5px 10px; }
        .totals th { background: #f8fafc; }
        .totals .grand-total { font-size: 16px; font-weight: bold; background: #1e293b; color: white; }
        .status-bar { margin-top: 20px; padding: 15px; background: #f8fafc; border-radius: 8px; }
        .status-bar .row { display: flex; justify-content: space-between; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .footer h4 { font-size: 11px; color: #1e293b; margin-bottom: 8px; }
        .footer p { font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                @if($company->logo && file_exists(public_path('storage/' . $company->logo)))
                    <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo" style="max-height: 60px; margin-bottom: 10px;">
                @endif
                <h1>{{ $company->company_name }}</h1>
                <p>{{ $company->address }}</p>
                <p>Phone: {{ $company->phone }} | Email: {{ $company->email }}</p>
            </div>
            <div class="invoice-details">
                <h2>INVOICE</h2>
                <p><strong>No:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Date:</strong> {{ $invoice->issue_date->format('M d, Y') }}</p>
                <p><strong>Due:</strong> {{ $invoice->due_date->format('M d, Y') }}</p>
                <span class="badge badge-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span>
            </div>
        </div>

        <div class="parties">
            <div class="party-box">
                <h3>Bill To:</h3>
                <p><strong>{{ $invoice->customer->name }}</strong></p>
                <p>{{ $invoice->customer->email }}</p>
                <p>{{ $invoice->customer->phone }}</p>
                <p>{{ $invoice->customer->address }}</p>
            </div>
            <div class="party-box">
                <h3>From:</h3>
                <p><strong>{{ $company->company_name }}</strong></p>
                <p>{{ $company->email }}</p>
                <p>{{ $company->phone }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">{{ $company->currency }} {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">{{ $company->currency }} {{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <th>Subtotal</th>
                    <td class="text-end">{{ $company->currency }} {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->discount_amount > 0)
                <tr>
                    <th>Discount</th>
                    <td class="text-end">-{{ $company->currency }} {{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <th>Tax ({{ number_format($company->tax_rate, 1) }}%)</th>
                    <td class="text-end">{{ $company->currency }} {{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
                <tr class="grand-total">
                    <th>Total ({{ $company->currency }})</th>
                    <td class="text-end">{{ number_format($invoice->total, 2) }}</td>
                </tr>
                <tr>
                    <th>Paid</th>
                    <td class="text-end">-{{ $company->currency }} {{ number_format($invoice->paid_amount, 2) }}</td>
                </tr>
                <tr class="grand-total" style="background: #dc2626;">
                    <th>Balance Due ({{ $company->currency }})</th>
                    <td class="text-end">{{ number_format($invoice->total - $invoice->paid_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($invoice->notes)
        <div class="footer">
            <h4>Notes</h4>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="status-bar">
            <div class="row">
                <div><strong>Status:</strong> {{ ucfirst($invoice->status) }}</div>
                <div><strong>Created By:</strong> {{ $invoice->creator->name ?? 'N/A' }}</div>
            </div>
        </div>
    </div>
</body>
</html>