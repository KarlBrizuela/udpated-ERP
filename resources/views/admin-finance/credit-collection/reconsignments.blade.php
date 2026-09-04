<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .billing-card {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .document-title {
            text-align: center; 
            font-size: 1.75rem; 
            font-weight: 700;
            color: #333; 
            margin-top: 1rem; 
            text-transform: uppercase;
        }

        .btn-xs {
            padding: 0.25rem 0.6rem;
            font-size: 0.75rem;
            border-radius: 4px;
        }

        /* Official Sales Order UI Styling for Modal */
        .so-document-box {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .so-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ce1126;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        .so-logo {
            width: 50px;
            height: 50px;
            background: #ce1126;
            color: #fff;
            font-size: 28px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin-right: 15px;
        }
        .so-company-name {
            font-size: 1.1rem;
            font-weight: 800;
            color: #1e293b;
        }
        .so-title {
            text-align: center;
            font-size: 1.4rem;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: 0.5px;
            margin-bottom: 1.5rem;
        }
        .so-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            background: #f8fafc;
            padding: 1.25rem;
            border-radius: 8px;
            border: 1px solid #f1f5f9;
        }
        .so-info-table td {
            padding: 4px 6px;
            font-size: 0.85rem;
        }
        .so-items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }
        .so-items-table th {
            background: #f1f5f9;
            color: #334155;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 10px;
            border: 1px solid #e2e8f0;
        }
        .so-items-table td {
            padding: 10px;
            font-size: 0.85rem;
            border: 1px solid #e2e8f0;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card billing-card">
                <div class="document-title mb-4 d-flex justify-content-between align-items-center">
                    <span>RECONSIGNMENT REQUESTS</span>
                </div>

                <div class="card-body p-0">
                    <div class="px-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>SO #</th>
                                        <th>Date Requested</th>
                                        <th>Reconsignment Value</th>
                                        <th class="text-center" style="width: 380px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reconsignmentRequests ?? [] as $req)
                                    @php
                                        // Calculate return books for this request (remaining = sent_qty - picked_qty)
                                        $returnedBooks = [];
                                        $reconsignmentTotal = 0;
                                        foreach($req->items as $item) {
                                            $alreadyPurchasedQty = \App\Models\SalesInvoiceItem::whereHas('invoice', function($query) use ($req) {
                                                $query->where('so_id', $req->id)->where('status', '!=', 'cancelled');
                                            })->where('book_id', $item->book_id)->sum('quantity');
                                            
                                            $sentQty = (int)$item->quantity;
                                            $pickedQty = max($alreadyPurchasedQty, (int)($item->customer_selected_qty ?? 0));
                                            $returnedQty = max(0, $sentQty - $pickedQty);
                                            
                                            if ($returnedQty > 0) {
                                                $price = $item->price ?? $item->unit_price ?? 0;
                                                $subtotal = $returnedQty * $price;
                                                $reconsignmentTotal += $subtotal;
                                                $returnedBooks[] = [
                                                    'name' => $item->book->name ?? ($item->product->name ?? 'Unknown Book'),
                                                    'isbn' => $item->book->isbn ?? 'N/A',
                                                    'sent_qty' => $sentQty,
                                                    'picked_qty' => $pickedQty,
                                                    'qty' => $returnedQty,
                                                    'price' => $price,
                                                    'subtotal' => $subtotal
                                                ];
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="fw-bold text-primary">
                                            <button class="btn btn-xs btn-link p-0 me-2 text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#books-{{ $req->id }}" aria-expanded="false" aria-controls="books-{{ $req->id }}">
                                                <i class="las la-angle-down text-primary" style="font-size: 1.1rem; transition: transform 0.2s;"></i>
                                            </button>
                                            {{ $req->so_number }}
                                        </td>
                                        <td>{{ $req->updated_at->format('M d, Y') }}</td>
                                        <td class="fw-bold text-dark">₱ {{ number_format($reconsignmentTotal > 0 ? $reconsignmentTotal : $req->total_amount, 2) }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-2">
                                                <!-- View SO Button -->
                                                <button type="button" class="btn btn-info text-white btn-xs px-2 shadow-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#viewSOModal-{{ $req->id }}" title="View Official Sales Order" style="height: 30px;">
                                                    <i class="las la-file-alt me-1"></i> View SO
                                                </button>
                                                
                                                <!-- Approve Form -->
                                                <form action="{{ route('admin-finance.credit-collection.reconsignment.approve', $req->id) }}" method="POST" class="d-flex align-items-center gap-1 mb-0">
                                                    @csrf
                                                    <input type="text" name="terms" class="form-control form-control-sm px-2" placeholder="Terms" value="{{ $req->terms }}" style="width: 90px; font-size: 0.75rem; height: 30px;" title="Update Terms">
                                                    <button type="submit" class="btn btn-success btn-xs px-3 shadow-sm d-flex align-items-center" title="Approve Request" style="height: 30px;">
                                                        <i class="las la-check me-1"></i> Go
                                                    </button>
                                                </form>

                                                <!-- Reject Form -->
                                                <form action="{{ route('admin-finance.credit-collection.reconsignment.reject', $req->id) }}" method="POST" class="mb-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-xs px-2 shadow-sm d-flex align-items-center" title="Reject Request" style="height: 30px;">
                                                        <i class="las la-times me-1"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Collapsible Book Details Row -->
                                    <tr class="collapse" id="books-{{ $req->id }}">
                                        <td colspan="4" class="bg-light p-3 border-top-0">
                                            <div class="card border-0 shadow-sm rounded-3">
                                                <div class="card-body p-3">
                                                    <h6 class="fw-bold text-secondary mb-3"><i class="las la-book-open me-1"></i> Reconsignment Books Breakdown (Remaining Qty)</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered mb-0 bg-white align-middle">
                                                            <thead class="bg-light text-secondary">
                                                                <tr>
                                                                    <th>Book Description</th>
                                                                    <th class="text-center" style="width: 100px;">Sent Qty</th>
                                                                    <th class="text-center" style="width: 100px;">Picked Qty</th>
                                                                    <th class="text-center bg-warning bg-opacity-10 text-danger" style="width: 140px;">Qty to Return</th>
                                                                    <th class="text-end" style="width: 130px;">Unit Price</th>
                                                                    <th class="text-end" style="width: 150px;">Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($returnedBooks as $book)
                                                                <tr>
                                                                    <td class="text-dark fw-medium">{{ $book['name'] }}</td>
                                                                    <td class="text-center text-muted">{{ $book['sent_qty'] }}</td>
                                                                    <td class="text-center text-primary fw-bold">{{ $book['picked_qty'] }}</td>
                                                                    <td class="text-center fw-bold text-danger bg-warning bg-opacity-10">{{ $book['qty'] }}</td>
                                                                    <td class="text-end">₱{{ number_format($book['price'], 2) }}</td>
                                                                    <td class="text-end fw-bold text-dark">₱{{ number_format($book['subtotal'], 2) }}</td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="6" class="text-center py-2 text-muted italic">No returned books found. All items were picked or purchased.</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted italic">
                                            <i class="las la-folder-open fs-2 d-block mb-2"></i>
                                            No pending reconsignment requests found.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALS ARE PLACED OUTSIDE THE TABLE FOR PROPER BOOTSTRAP OVERLAY -->
    @foreach($reconsignmentRequests ?? [] as $req)
    @php
        $returnedBooks = [];
        $reconsignmentTotal = 0;
        foreach($req->items as $item) {
            $alreadyPurchasedQty = \App\Models\SalesInvoiceItem::whereHas('invoice', function($query) use ($req) {
                $query->where('so_id', $req->id)->where('status', '!=', 'cancelled');
            })->where('book_id', $item->book_id)->sum('quantity');
            
            $sentQty = (int)$item->quantity;
            $pickedQty = max($alreadyPurchasedQty, (int)($item->customer_selected_qty ?? 0));
            $returnedQty = max(0, $sentQty - $pickedQty);
            
            if ($returnedQty > 0) {
                $price = $item->price ?? $item->unit_price ?? 0;
                $subtotal = $returnedQty * $price;
                $reconsignmentTotal += $subtotal;
                $returnedBooks[] = [
                    'name' => $item->book->name ?? ($item->product->name ?? 'Unknown Book'),
                    'isbn' => $item->book->isbn ?? 'N/A',
                    'sent_qty' => $sentQty,
                    'picked_qty' => $pickedQty,
                    'qty' => $returnedQty,
                    'price' => $price,
                    'subtotal' => $subtotal
                ];
            }
        }
    @endphp
    <div class="modal fade" id="viewSOModal-{{ $req->id }}" tabindex="-1" aria-labelledby="viewSOModalLabel-{{ $req->id }}" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-light border-0 py-3">
                    <h5 class="modal-title fw-bold text-dark" id="viewSOModalLabel-{{ $req->id }}">
                        <i class="las la-file-alt text-primary me-2"></i>Sales Order #{{ $req->so_number }} - Reconsignment SO View
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <!-- OFFICIAL SALES ORDER UI CONTAINER -->
                    <div class="so-document-box shadow-sm">
                        <!-- Header / Company Info -->
                        <div class="so-header">
                            <div class="d-flex align-items-center">
                                <div class="so-logo">C</div>
                                <div>
                                    <div class="so-company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                                    <div class="text-muted small">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                                    <div class="text-muted small">Tel. No.: 921-3984</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-danger fs-14 px-3 py-2">RECONSIGNMENT SO</span>
                            </div>
                        </div>

                        <div class="so-title">
                            SALES ORDER <span class="text-danger">#{{ $req->so_number }}</span>
                        </div>

                        <!-- Order Information Grid -->
                        <div class="so-info-grid">
                            <div>
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="las la-info-circle me-1 text-primary"></i> Consignment Details</h6>
                                <table class="table table-sm table-borderless so-info-table mb-0">
                                    @if($req->areaSalesStaff)
                                    <tr>
                                        <td class="fw-bold text-secondary" style="width: 140px;">Sales Rep / Staff:</td>
                                        <td><span class="badge bg-success">{{ $req->areaSalesStaff->name }}</span></td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="fw-bold text-secondary" style="width: 140px;">Transaction Type:</td>
                                        <td><span class="badge bg-info text-white">{{ strtoupper(str_replace('_', ' ', $req->type)) }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-secondary">Delivery Address:</td>
                                        <td class="text-dark">{{ $req->shipping_address ?: ($req->customer->shipping_address ?? '—') }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="las la-clock me-1 text-primary"></i> Order Metadata</h6>
                                <table class="table table-sm table-borderless so-info-table mb-0">
                                    <tr>
                                        <td class="fw-bold text-secondary" style="width: 130px;">Date Requested:</td>
                                        <td class="text-dark">{{ $req->updated_at->format('F d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-secondary">Status:</td>
                                        <td><span class="badge bg-warning text-dark">{{ strtoupper(str_replace('_', ' ', $req->status)) }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-secondary">Prepared By:</td>
                                        <td class="text-dark">{{ $req->preparedBy->name ?? 'System' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold text-secondary">Terms:</td>
                                        <td class="text-dark">{{ $req->terms ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Items Table matching official SO UI -->
                        <h6 class="fw-bold text-dark mb-3"><i class="las la-list me-1 text-primary"></i> Reconsignment Items (Deducted Qty = Sent - Picked)</h6>
                        <table class="so-items-table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;" class="text-center">SENT QTY</th>
                                    <th style="width: 90px;" class="text-center">PICKED QTY</th>
                                    <th style="width: 130px;" class="text-center bg-danger text-white">RECONSIGNMENT QTY</th>
                                    <th>DESCRIPTION</th>
                                    <th style="width: 130px;" class="text-end">UNIT PRICE</th>
                                    <th style="width: 140px;" class="text-end">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returnedBooks as $book)
                                <tr>
                                    <td class="text-center text-muted">{{ $book['sent_qty'] }}</td>
                                    <td class="text-center text-primary fw-bold">{{ $book['picked_qty'] }}</td>
                                    <td class="text-center fw-bold text-danger bg-warning bg-opacity-10">{{ $book['qty'] }}</td>
                                    <td class="fw-bold text-dark">{{ $book['name'] }}</td>
                                    <td class="text-end">₱{{ number_format($book['price'], 2) }}</td>
                                    <td class="text-end fw-bold text-dark">₱{{ number_format($book['subtotal'], 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">No reconsignment items found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Total Section -->
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
                            <span class="text-muted small">* This document serves as a sales order for reconsignment items after deducting picked quantities.</span>
                            <div class="text-end">
                                <span class="text-muted small d-block">Total Reconsignment Amount:</span>
                                <h4 class="mb-0 fw-bold text-danger">₱ {{ number_format($reconsignmentTotal > 0 ? $reconsignmentTotal : $req->total_amount, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-secondary px-4 shadow-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</x-app-layout>
