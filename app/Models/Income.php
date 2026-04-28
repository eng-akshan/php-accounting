<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Income extends Model
{
    protected $fillable = [
        'reference_number', 'description', 'amount', 'date', 'category',
        'account_id', 'customer_id', 'is_recurring', 'recurring_frequency',
        'recurring_end_date', 'branch_id', 'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'recurring_end_date' => 'date',
        'amount' => 'decimal:2',
        'is_recurring' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->reference_number)) {
                $model->reference_number = 'INC-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}