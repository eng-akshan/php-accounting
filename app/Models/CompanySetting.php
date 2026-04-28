<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name', 'logo', 'address', 'phone', 'email',
        'currency', 'tax_rate', 'invoice_prefix', 'quotation_prefix'
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
    ];

    public static function getSettings()
    {
        return self::firstOrCreate([], [
            'company_name' => 'Redsom Group',
            'currency' => 'USD',
            'tax_rate' => 10,
            'invoice_prefix' => 'INV',
            'quotation_prefix' => 'QUO',
        ]);
    }
}