<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_code',
        'title',
        'target_amount',
        'raised_amount',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class, 'campaign_id');
    }

    public function recalculateRaised()
    {
        $raised = (float) $this->donations()->where('donation_type', 'Cash')->sum('amount');
        $this->raised_amount = round($raised, 2);
        return $this;
    }
}
