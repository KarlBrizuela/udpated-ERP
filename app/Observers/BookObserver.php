<?php

namespace App\Observers;

use App\Models\Book;
use App\Models\SiteInventory;

class BookObserver
{
    /**
     * Handle the Book "created" event.
     *
     * @param  \App\Models\Book  $book
     * @return void
     */
    public function created(Book $book)
    {
        //
    }

    /**
     * Handle the Book "updated" event.
     * Automatically sync stock changes to Main Warehouse site inventory
     *
     * @param  \App\Models\Book  $book
     * @return void
     */
    public function updated(Book $book)
    {
        // Check if stock was changed
        if ($book->isDirty('stock')) {
            $oldStock = $book->getOriginal('stock');
            $newStock = $book->stock;
            
            // Get Main Warehouse (site_id = 1)
            $mainWarehouseId = 1;
            
            // Update or create SiteInventory for Main Warehouse
            SiteInventory::updateOrCreate(
                [
                    'site_id' => $mainWarehouseId,
                    'book_id' => $book->id,
                    'book_index_id' => null,
                    'book_bundle_id' => null,
                ],
                [
                    'quantity' => $newStock
                ]
            );
        }
    }

    /**
     * Handle the Book "deleted" event.
     *
     * @param  \App\Models\Book  $book
     * @return void
     */
    public function deleted(Book $book)
    {
        //
    }

    /**
     * Handle the Book "restored" event.
     *
     * @param  \App\Models\Book  $book
     * @return void
     */
    public function restored(Book $book)
    {
        //
    }

    /**
     * Handle the Book "force deleted" event.
     *
     * @param  \App\Models\Book  $book
     * @return void
     */
    public function forceDeleted(Book $book)
    {
        //
    }
}
