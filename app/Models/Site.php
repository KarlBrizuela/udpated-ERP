<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'name',
        'location',
        'code',
        'description',
        'is_active'
    ];

    public function inventory()
    {
        return $this->hasMany(SiteInventory::class);
    }

    public function outgoingTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'from_site_id');
    }

    public function incomingTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'to_site_id');
    }

    public function getTotalInventoryQuantity()
    {
        return (int) $this->inventory()->sum('quantity');
    }

    public function getTotalInventoryValue()
    {
        $val = 0;
        $invs = $this->inventory()->with(['book', 'bookIndex.book', 'bookBundle'])->get();
        foreach ($invs as $inv) {
            $unitPrice = 0;
            if ($inv->book_id && $inv->book) {
                $unitPrice = ($inv->book->cost && $inv->book->cost > 0) ? $inv->book->cost : ($inv->book->price ?? 0);
            } elseif ($inv->book_index_id && $inv->bookIndex) {
                $unitPrice = ($inv->bookIndex->price && $inv->bookIndex->price > 0) ? $inv->bookIndex->price : ($inv->bookIndex->book->price ?? $inv->bookIndex->book->cost ?? 0);
            } elseif ($inv->book_bundle_id && $inv->bookBundle) {
                $unitPrice = $inv->bookBundle->price ?? 0;
            }
            $val += ($inv->quantity * $unitPrice);
        }
        return $val;
    }

    public function getActiveSites()
    {
        return self::where('is_active', true)->orderBy('name')->get();
    }
}
