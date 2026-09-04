<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'vendor_code',
        'vendor_name',
        'contact_person',
        'contact_number',
        'email',
        'address',
        'status',
    ];

    /**
     * Generate a unique vendor code.
     */
    public static function generateVendorCode(): string
    {
        $latest = static::orderBy('id', 'desc')->first();
        $nextNum = $latest ? ((int) ltrim(substr($latest->vendor_code, 4), '0') + 1) : 1;
        return 'VND-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
    }
}
