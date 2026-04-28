<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JournalEntry extends Model
{
    protected $fillable = [
        'entry_number', 'date', 'description', 'status',
        'branch_id', 'created_by', 'approved_by'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->entry_number)) {
                $model->entry_number = 'JE-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function getTotalDebit(): float
    {
        return $this->lines->sum('debit');
    }

    public function getTotalCredit(): float
    {
        return $this->lines->sum('credit');
    }

    public function isBalanced(): bool
    {
        return $this->getTotalDebit() === $this->getTotalCredit();
    }

    public function scopePosted($query)
    {
        return $query->where('status', 'posted');
    }
}