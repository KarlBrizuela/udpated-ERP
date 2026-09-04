<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_no',
        'donor_id',
        'campaign_id',
        'donation_type',
        'amount',
        'item_description',
        'is_restricted',
        'restricted_fund_purpose',
        'project_supported',
        'receipt_number',
        'tax_doc_issued',
        'tax_cert_number',
        'donation_date',
        'notes',
    ];

    protected $casts = [
        'is_restricted' => 'boolean',
        'tax_doc_issued' => 'boolean',
        'donation_date' => 'date',
    ];

    public function donor()
    {
        return $this->belongsTo(Donor::class, 'donor_id');
    }

    public function campaign()
    {
        return $this->belongsTo(DonationCampaign::class, 'campaign_id');
    }
}
