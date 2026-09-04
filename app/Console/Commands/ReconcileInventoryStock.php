<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Models\BookIndex;
use App\Models\BookBundle;
use App\Models\SiteInventory;
use App\Models\TeamStockTransfer;
use App\Models\Site;

class ReconcileInventoryStock extends Command
{
    protected $signature = 'inventory:reconcile';
    protected $description = 'Reconcile Master Book Stock and Site Inventory with accurate transaction logs';

    public function handle()
    {
        $this->info('Starting Inventory Reconciliation & Synchronization...');

        $mainWarehouse = Site::where('name', 'Main Warehouse')->first();
        $mainSiteId = $mainWarehouse ? $mainWarehouse->id : 1;

        $syncedCount = 0;
        $books = Book::all();
        foreach ($books as $b) {
            $siteInv = SiteInventory::where('site_id', $mainSiteId)
                ->where('book_id', $b->id)
                ->first();

            if (!$siteInv || (int)$siteInv->quantity !== (int)$b->stock) {
                SiteInventory::updateOrCreate(
                    ['site_id' => $mainSiteId, 'book_id' => $b->id],
                    ['quantity' => $b->stock]
                );
                $syncedCount++;
            }
        }

        $this->info("Successfully reconciled {$syncedCount} book stock discrepancies between Book Master Stock and Main Warehouse!");
        return 0;
    }
}
