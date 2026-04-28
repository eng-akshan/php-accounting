<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Quotation extends Model
{
    protected $fillable = [
        'quotation_number', 'customer_id', 'issue_date', 'valid_until',
        'subtotal', 'tax_amount', 'discount_amount', 'total',
        'status', 'user_id', 'branch_id', 'notes', 'terms'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->quotation_number)) {
                $prefix = CompanySetting::getSettings()->quotation_prefix ?? 'QUO';
                $model->quotation_number = $prefix . '-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function calculateTotals(): self
    {
        $this->subtotal = $this->items->sum('total');
        $company = CompanySetting::getSettings();
        $this->tax_amount = $this->subtotal * ($company->tax_rate / 100);
        $this->total = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->save();
        return $this;
    }

    public function toInvoice(): Invoice
    {
        $invoice = Invoice::create([
            'invoice_number' => (CompanySetting::getSettings()->invoice_prefix ?? 'INV') . '-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'customer_id' => $this->customer_id,
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'total' => $this->total,
            'status' => 'sent',
            'notes' => $this->notes,
            'created_by' => $this->user_id,
        ]);

        foreach ($this->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
            ]);
        }

        $this->update(['status' => 'converted']);

        return $invoice;
    }
}