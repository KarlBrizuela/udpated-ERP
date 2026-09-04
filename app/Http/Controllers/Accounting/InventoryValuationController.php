<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Book;
use App\Models\Site;
use App\Models\SiteInventory;
use App\Models\JournalEntryItem;
use App\Models\ChartOfAccount;

class InventoryValuationController extends Controller
{
    /**
     * Display the Cost of Goods Sold & Inventory Valuation Report.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $sidebar = 'admin-finance';
        $role = 'Finance Manager';

        // Virtual category warehouses to exclude from physical stock valuation
        $masterCategoryWarehouseNames = [
            'Bookstore Warehouse',
            'Area Sales Warehouse',
            'Consignment Warehouse',
            'Reserved Warehouse',
            'Book Sale Warehouse',
            'E-commerce Warehouse',
            'Damaged Stock Warehouse',
            'Returned Stock Warehouse',
            'In Transit Warehouse',
        ];

        // Retrieve physical sites
        $siteIds = Site::where('is_active', true)
            ->whereNotIn('name', $masterCategoryWarehouseNames)
            ->pluck('id')
            ->toArray();

        // Retrieve all inventory records with non-zero quantity in the physical sites
        $inventories = SiteInventory::whereIn('site_id', $siteIds)
            ->where('quantity', '>', 0)
            ->with(['book', 'bookIndex.book', 'bookBundle.books'])
            ->get();

        $totalStock = 0;
        $totalCostValue = 0;
        $totalRetailValue = 0;

        foreach ($inventories as $inv) {
            $qty = $inv->quantity;
            $totalStock += $qty;

            $unitCost = 0;
            $unitPrice = 0;

            if ($inv->book_id && $inv->book) {
                $unitCost = (float)($inv->book->cost ?? 0);
                $unitPrice = (float)($inv->book->price ?? 0);
            } elseif ($inv->book_index_id && $inv->bookIndex && $inv->bookIndex->book) {
                $unitCost = (float)($inv->bookIndex->book->cost ?? 0);
                $unitPrice = (float)($inv->bookIndex->price ?? $inv->bookIndex->book->price ?? 0);
            } elseif ($inv->book_bundle_id && $inv->bookBundle) {
                // Calculate bundle cost by summing component costs
                $bundleCost = 0;
                foreach ($inv->bookBundle->books as $b) {
                    $bundleCost += ($b->cost ?? 0) * ($b->pivot->quantity ?? 1);
                }
                $unitCost = (float)$bundleCost;
                $unitPrice = (float)($inv->bookBundle->price ?? 0);
            }

            $totalCostValue += $unitCost * $qty;
            $totalRetailValue += $unitPrice * $qty;
        }

        // Calculate Cost of Goods Sold (COGS) from Account 5000
        $cogsAccount = ChartOfAccount::where('code', '5000')
            ->orWhere('name', 'like', '%COGS%')
            ->orWhere('name', 'like', '%Cost of Sales%')
            ->first();

        $totalCogs = 0;
        if ($cogsAccount) {
            $debits = JournalEntryItem::where('chart_of_account_id', $cogsAccount->id)->sum('debit') ?: 0;
            $credits = JournalEntryItem::where('chart_of_account_id', $cogsAccount->id)->sum('credit') ?: 0;
            $totalCogs = max(0, $debits - $credits);
        }

        // Group Valuation by Site
        $sitesValuation = Site::where('is_active', true)
            ->whereNotIn('name', $masterCategoryWarehouseNames)
            ->get()
            ->map(function ($site) use ($inventories) {
                $siteInv = $inventories->where('site_id', $site->id);
                
                $stockCount = 0;
                $costVal = 0;
                $retailVal = 0;

                foreach ($siteInv as $inv) {
                    $qty = $inv->quantity;
                    $stockCount += $qty;

                    $unitCost = 0;
                    $unitPrice = 0;

                    if ($inv->book_id && $inv->book) {
                        $unitCost = (float)($inv->book->cost ?? 0);
                        $unitPrice = (float)($inv->book->price ?? 0);
                    } elseif ($inv->book_index_id && $inv->bookIndex && $inv->bookIndex->book) {
                        $unitCost = (float)($inv->bookIndex->book->cost ?? 0);
                        $unitPrice = (float)($inv->bookIndex->price ?? $inv->bookIndex->book->price ?? 0);
                    } elseif ($inv->book_bundle_id && $inv->bookBundle) {
                        $bundleCost = 0;
                        foreach ($inv->bookBundle->books as $b) {
                            $bundleCost += ($b->cost ?? 0) * ($b->pivot->quantity ?? 1);
                        }
                        $unitCost = (float)$bundleCost;
                        $unitPrice = (float)($inv->bookBundle->price ?? 0);
                    }

                    $costVal += $unitCost * $qty;
                    $retailVal += $unitPrice * $qty;
                }

                $site->stock_count = $stockCount;
                $site->cost_value = $costVal;
                $site->retail_value = $retailVal;

                return $site;
            });

        // Itemized Valuation Registry (Paginated list of books)
        $booksQuery = Book::latest();

        if (!empty($search)) {
            $booksQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhere('publisher', 'like', '%' . $search . '%');
            });
        }

        $booksPaginated = $booksQuery->with(['inventory'])->paginate(15)->withQueryString();

        $booksPaginated->getCollection()->transform(function ($book) use ($siteIds) {
            $stock = $book->inventory->whereIn('site_id', $siteIds)->sum('quantity') ?: 0;
            
            $book->total_stock = $stock;
            $book->total_cost_value = $stock * ($book->cost ?? 0);
            $book->total_retail_value = $stock * ($book->price ?? 0);
            
            return $book;
        });

        // Recent COGS Transactions
        $recentCogsTransactions = [];
        if ($cogsAccount) {
            $recentCogsTransactions = JournalEntryItem::where('chart_of_account_id', $cogsAccount->id)
                ->with(['journalEntry'])
                ->orderBy('created_at', 'desc')
                ->take(50)
                ->get();
        }

        return view('admin-finance.accounting.inventory-valuation', compact(
            'totalStock',
            'totalCostValue',
            'totalRetailValue',
            'totalCogs',
            'sitesValuation',
            'booksPaginated',
            'recentCogsTransactions',
            'sidebar',
            'role',
            'search'
        ));
    }
}
