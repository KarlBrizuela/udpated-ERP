<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionCosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_number',
        'book_id',
        'job_title',
        'quantity_produced',
        'pages_count',
        'paper_cost',
        'ink_cost',
        'labor_cost',
        'electricity_cost',
        'machine_cost',
        'binding_cost',
        'uv_cost',
        'shrink_wrap_cost',
        'packaging_cost',
        'freight_cost',
        'warehouse_cost',
        'overhead_cost',
        'total_cogs',
        'unit_cogs',
        'status',
        'notes',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function recalculateTotals()
    {
        $sum = $this->paper_cost
             + $this->ink_cost
             + $this->labor_cost
             + $this->electricity_cost
             + $this->machine_cost
             + $this->binding_cost
             + $this->uv_cost
             + $this->shrink_wrap_cost
             + $this->packaging_cost
             + $this->freight_cost
             + $this->warehouse_cost
             + $this->overhead_cost;

        $this->total_cogs = round($sum, 2);
        $qty = max(1, $this->quantity_produced);
        $this->unit_cogs = round($sum / $qty, 2);
        return $this;
    }
}
