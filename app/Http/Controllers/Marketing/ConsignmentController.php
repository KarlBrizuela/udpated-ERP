<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\ConsignmentOwner;
use App\Models\SalesOrderItem;
use Illuminate\Http\Request;
use App\Models\ConsignmentSettlement;

class ConsignmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Always fetch all owners for configuration modal dropdowns
        $allOwners = ConsignmentOwner::orderBy('name', 'asc')->get();

        $ownersQuery = ConsignmentOwner::withCount('books')->orderBy('name', 'asc');
        if (!empty($search)) {
            $ownersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('contact_person', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhereHas('books', function($bq) use ($search) {
                      $bq->where('name', 'like', '%' . $search . '%')
                         ->orWhere('sku', 'like', '%' . $search . '%');
                  });
            });
        }
        $owners = $ownersQuery->get();
        
        // Fetch all books marked as Consignment
        $booksQuery = Book::where('book_type', 'Consignment')
            ->with('consignmentOwner')
            ->orderBy('name', 'asc');
        if (!empty($search)) {
            $booksQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('sku', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%')
                  ->orWhereHas('consignmentOwner', function($oq) use ($search) {
                      $oq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        $consignmentBooks = $booksQuery->get();
    
        // Compile settlement data (Only for unsettled sales)
        $settlementOwnersQuery = ConsignmentOwner::query();
        if (!empty($search)) {
            $settlementOwnersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('contact_person', 'like', '%' . $search . '%')
                  ->orWhereHas('books', function($bq) use ($search) {
                      $bq->where('name', 'like', '%' . $search . '%')
                         ->orWhere('sku', 'like', '%' . $search . '%');
                  });
            });
        }
        $settlements = $settlementOwnersQuery->get()->map(function($owner) {
            $query = SalesOrderItem::whereHas('book', function($q) use ($owner) {
                $q->where('consignment_owner_id', $owner->id);
            });
    
            if ($owner->last_settled_at) {
                $query->where('created_at', '>', $owner->last_settled_at);
            }
    
            $soldItems = $query->get();
    
            $totalOwed = $soldItems->sum(function($item) {
                return ($item->source_price_at_sale ?: 0) * $item->quantity;
            });
    
            $totalSoldQty = $soldItems->sum('quantity');
    
            return [
                'owner' => $owner,
                'total_sold_qty' => $totalSoldQty,
                'total_owed' => $totalOwed,
                'status' => $totalOwed > 0 ? 'Pending' : 'Settled'
            ];
        });
    
        // Fetch history
        $historyQuery = ConsignmentSettlement::with('owner')->latest();
        if (!empty($search)) {
            $historyQuery->whereHas('owner', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        $history = $historyQuery->get();
    
        return view('marketing.consignment.index', [
            'title' => 'Consignment Management',
            'role' => 'Marketing Manager',
            'sidebar' => 'marketing',
            'owners' => $owners,
            'allOwners' => $allOwners,
            'books' => $consignmentBooks,
            'settlements' => $settlements,
            'history' => $history,
            'search' => $search
        ]);
    }
    
    public function settle(Request $request, $ownerId)
    {
        $owner = ConsignmentOwner::findOrFail($ownerId);
    
        // Calculate amount to settle (same logic as index)
        $query = SalesOrderItem::whereHas('book', function($q) use ($owner) {
            $q->where('consignment_owner_id', $owner->id);
        });
    
        if ($owner->last_settled_at) {
            $query->where('created_at', '>', $owner->last_settled_at);
        }
    
        $soldItems = $query->get();
        $totalOwed = $soldItems->sum(function($item) {
            return ($item->source_price_at_sale ?: 0) * $item->quantity;
        });
        $totalSoldQty = $soldItems->sum('quantity');
    
        if ($totalOwed <= 0) {
            return response()->json(['error' => 'Nothing to settle for this owner.'], 422);
        }
    
        // Create settlement record
        ConsignmentSettlement::create([
            'owner_id' => $owner->id,
            'amount' => $totalOwed,
            'total_qty' => $totalSoldQty,
            'settled_at' => now()
        ]);
    
        // Update owner
        $owner->update(['last_settled_at' => now()]);
    
        return response()->json(['message' => 'Consignment settled successfully!']);
    }

    public function storeOwner(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'account_number' => 'nullable|string|unique:consignment_owners,account_number',
        ]);

        ConsignmentOwner::create($validated);

        return response()->json(['message' => 'Consignment owner added successfully.']);
    }

    public function updateBookConsignment(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'consignment_owner_id' => 'required|exists:consignment_owners,id',
            'source_price' => 'required|numeric|min:0',
            'markup_amount' => 'required|numeric|min:0',
        ]);

        // Automatically update the selling price
        $validated['price'] = $validated['source_price'] + $validated['markup_amount'];

        $book->update($validated);

        return response()->json(['message' => 'Book consignment settings updated. Selling price adjusted to ₱' . number_format($validated['price'], 2)]);
    }

    public function getOwnerDetails($id)
    {
        $owner = ConsignmentOwner::findOrFail($id);
        return response()->json($owner);
    }
    
    public function updateOwner(Request $request, $id)
    {
        $owner = ConsignmentOwner::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'account_number' => 'nullable|string|unique:consignment_owners,account_number,' . $id,
        ]);

        $owner->update($validated);

        return response()->json(['message' => 'Consignment owner updated successfully.']);
    }

    public function destroyOwner($id)
    {
        $owner = ConsignmentOwner::findOrFail($id);
        
        if ($owner->books()->count() > 0) {
            return response()->json(['error' => 'Cannot delete owner with assigned books.'], 422);
        }

        $owner->delete();
        return response()->json(['message' => 'Owner deleted successfully.']);
    }
}
