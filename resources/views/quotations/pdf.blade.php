<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quotation {{ $quotation->quotation_number }}</title>
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
        .badge-approved { background: #dcfce7; color: #166534; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-converted { background: #dbeafe; color: #1e40af; }
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
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .footer h4 { font-size: 11px; color: #1e293b; margin-bottom: 8px; }
        .footer p { font-size: 10px; color: #666; }
        .signature { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-box { width: 200px; text-align: center; }
        .signature-box .line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; font-size: 10px; }
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
                <h2>QUOTATION</h2>
                <p><strong>No:</strong> {{ $quotation->quotation_number }}</p>
                <p><strong>Date:</strong> {{ $quotation->issue_date->format('M d, Y') }}</p>
                <p><strong>Valid Until:</strong> {{ $quotation->valid_until->format('M d, Y') }}</p>
                <span class="badge badge-{{ $quotation->status }}">{{ strtoupper($quotation->status) }}</span>
            </div>
        </div>

        <div class="parties">
            <div class="party-box">
                <h3>Quotation To:</h3>
                <p><strong>{{ $quotation->customer->name }}</strong></p>
                <p>{{ $quotation->customer->email }}</p>
                <p>{{ $quotation->customer->phone }}</p>
                <p>{{ $quotation->customer->address }}</p>
            </div>
            <div class="party-box">
                <h3>Prepared By:</h3>
                <p><strong>{{ $quotation->user->name }}</strong></p>
                <p>{{ $quotation->user->email }}</p>
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
                @foreach($quotation->items as $item)
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
                    <td class="text-end">{{ $company->currency }} {{ number_format($quotation->subtotal, 2) }}</td>
                </tr>
                @if($quotation->discount_amount > 0)
                <tr>
                    <th>Discount</th>
                    <td class="text-end">-{{ $company->currency }} {{ number_format($quotation->discount_amount, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <th>Tax ({{ number_format($company->tax_rate, 1) }}%)</th>
                    <td class="text-end">{{ $company->currency }} {{ number_format($quotation->tax_amount, 2) }}</td>
                </tr>
                <tr class="grand-total">
                    <th>Total ({{ $company->currency }})</th>
                    <td class="text-end">{{ number_format($quotation->total, 2) }}</td>
                </tr>
            </table>
        </div>

        @if($quotation->notes)
        <div class="footer">
            <h4>Notes</h4>
            <p>{{ $quotation->notes }}</p>
        </div>
        @endif

        @if($quotation->terms)
        <div class="footer">
            <h4>Terms & Conditions</h4>
            <p>{{ $quotation->terms }}</p>
        </div>
        @endif

        <div class="signature">
            <div class="signature-box">
                <div class="line">Authorized Signature</div>
            </div>
            <div class="signature-box">
                <div class="line">Customer Signature</div>
            </div>
        </div>
    </div>
</body>
</html>