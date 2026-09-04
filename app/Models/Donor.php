<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;

    protected $fillable = [
        'donor_code',
        'name',
        'type',
        'email',
        'phone',
        'tax_id',
        'is_recurring',
        'total_donated_cash',
        'total_donations_count',
        'status',
        'notes',
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class, 'donor_id')->latest('donation_date');
    }

    public function recalculateTotals()
    {
        $cashTotal = (float) $this->donations()->where('donation_type', 'Cash')->sum('amount');
        $count = $this->donations()->count();

        $this->total_donated_cash = round($cashTotal, 2);
        $this->total_donations_count = $count;

        return $this;
    }
}
