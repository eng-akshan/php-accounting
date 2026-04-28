<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    protected $fillable = [
        'account_number', 'name', 'type', 'nature', 'parent_id',
        'is_active', 'allow_transaction', 'description', 'branch_id', 'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_transaction' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function getDebitTotal($from = null, $to = null): float
    {
        $query = $this->journalEntryLines()->whereHas('journalEntry', function ($q) {
            $q->where('status', 'posted');
        });

        if ($from && $to) {
            $query->whereHas('journalEntry', function ($q) use ($from, $to) {
                $q->whereBetween('date', [$from, $to]);
            });
        }

        return (float) $query->sum('debit');
    }

    public function getCreditTotal($from = null, $to = null): float
    {
        $query = $this->journalEntryLines()->whereHas('journalEntry', function ($q) {
            $q->where('status', 'posted');
        });

        if ($from && $to) {
            $query->whereHas('journalEntry', function ($q) use ($from, $to) {
                $q->whereBetween('date', [$from, $to]);
            });
        }

        return (float) $query->sum('credit');
    }

    public function getBalance(): float
    {
        $debit = $this->getDebitTotal();
        $credit = $this->getCreditTotal();

        if ($this->nature === 'Debit') {
            return $debit - $credit;
        }
        return $credit - $debit;
    }
}