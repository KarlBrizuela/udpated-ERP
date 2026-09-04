@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">Verify Payment Collection</h1>
            <p class="text-muted small">Verify and record COD payment from rider</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('cashier.collections.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Collection Details -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header border-bottom py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Collection Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div>
                                <label class="font-weight-bold">SO Number:</label>
                                <p class="text-dark">{{ $collection->salesOrder->so_number }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="font-weight-bold">Customer:</label>
                                <p class="text-dark">{{ $collection->salesOrder->customer->customer_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div>
                                <label class="font-weight-bold">Rider:</label>
                                <p class="text-dark">{{ $collection->rider->first_name ?? '' }} {{ $collection->rider->last_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <label class="font-weight-bold">Rider Contact:</label>
                                <p class="text-dark">{{ $collection->rider->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Amount Details -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="alert alert-info">
                                <small class="d-block text-muted">Amount to Collect</small>
                                <strong class="h5">₱{{ number_format($collection->amount_to_collect, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-warning">
                                <small class="d-block text-muted">Amount Collected by Rider</small>
                                <strong class="h5">₱{{ number_format($collection->amount_collected, 2) }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            @if($hasDiscrepancy)
                                <div class="alert alert-danger">
                                    <small class="d-block text-muted">Discrepancy</small>
                                    <strong class="h5">
                                        {{ $discrepancy > 0 ? '+' : '' }}₱{{ number_format($discrepancy, 2) }}
                                    </strong>
                                </div>
                            @else
                                <div class="alert alert-success">
                                    <small class="d-block text-muted">Discrepancy</small>
                                    <strong class="h5">No Discrepancy</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="mt-4">
                        <label class="font-weight-bold">Items Delivered:</label>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-right">Qty</th>
                                        <th class="text-right">Price</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($collection->items_selection && is_array($collection->items_selection))
                                        @foreach($collection->items_selection as $bookId => $itemData)
                                            @php
                                                // Find the corresponding book/item to get product name and price
                                                $soItem = $collection->salesOrder->items->firstWhere('book_id', $bookId);
                                                $productName = $soItem ? ($soItem->book->name ?? 'Unknown') : 'Unknown';
                                                $unitPrice = $soItem ? ($soItem->price ?? 0) : 0;
                                                $purchasedQty = $itemData['purchased_qty'] ?? 0;
                                                $subtotal = $purchasedQty * $unitPrice;
                                            @endphp
                                            <tr>
                                                <td>{{ $productName }}</td>
                                                <td class="text-right">{{ $purchasedQty }}</td>
                                                <td class="text-right">₱{{ number_format($unitPrice, 2) }}</td>
                                                <td class="text-right">₱{{ number_format($subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @foreach($collection->salesOrder->items as $item)
                                            <tr>
                                                <td>{{ $item->book->name ?? 'Unknown' }}</td>
                                                <td class="text-right">{{ $item->quantity }}</td>
                                                <td class="text-right">₱{{ number_format($item->price, 2) }}</td>
                                                <td class="text-right">₱{{ number_format($item->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Rider Notes -->
                    @if($collection->collection_notes)
                        <div class="mt-3">
                            <label class="font-weight-bold">Rider Notes:</label>
                            <div class="alert alert-light border">
                                {{ $collection->collection_notes }}
                            </div>
                        </div>
                    @endif

                    <!-- Collection Proof -->
                    @if($collection->customer_signature_photo)
                        <div class="mt-3">
                            <label class="font-weight-bold">Customer Signature/Proof:</label>
                            <div class="text-center">
                                <img src="/storage/{{ $collection->customer_signature_photo }}" 
                                    class="img-fluid" style="max-width: 300px; max-height: 300px; border: 1px solid #ddd; padding: 5px;">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Verification Form -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header border-bottom py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Verify Collection</h6>
                </div>
                <div class="card-body">
                    <form id="verificationForm" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="received" class="font-weight-bold">Amount Received from Rider</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">₱</span>
                                </div>
                                <input type="number" class="form-control" id="received" name="amount_received" 
                                    step="0.01" min="0" value="{{ $collection->amount_collected }}" required>
                            </div>
                        </div>

                        @if($hasDiscrepancy)
                            <div class="alert alert-warning">
                                <small><strong>⚠ Discrepancy detected!</strong></small>
                                <p class="mb-0 small">Difference: ₱{{ number_format($discrepancy, 2) }}</p>
                            </div>

                            <div class="form-group mb-3">
                                <label for="discrepancy" class="font-weight-bold">Discrepancy Notes</label>
                                <textarea class="form-control" id="discrepancy" name="discrepancy_notes" 
                                    rows="3" placeholder="Explain the discrepancy..."></textarea>
                            </div>
                        @endif

                        <div class="form-group mb-3">
                            <label for="proof" class="font-weight-bold">Verification Photo</label>
                            <input type="file" class="form-control-file" id="proof" name="attach_proof" accept="image/*">
                            <small class="form-text text-muted">Optional: Upload proof of cash receipt</small>
                        </div>

                        <div class="btn-group w-100" role="group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Approve & Record
                            </button>
                            <button type="button" class="btn btn-danger" id="rejectBtn">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('verificationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);

        fetch('{{ route("cashier.collections.verify", $collection->id) }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✓ Collection verified and recorded successfully!');
                window.location.href = '{{ route("cashier.collections.index") }}';
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
    });

    document.getElementById('rejectBtn')?.addEventListener('click', function() {
        let reason = prompt('Please provide a reason for rejection:', '');
        
        if (reason !== null && reason.trim() !== '') {
            fetch('{{ route("cashier.collections.reject", $collection->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ Collection rejected successfully!');
                    window.location.href = '{{ route("cashier.collections.index") }}';
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error: ' + error.message);
            });
        }
    });
</script>
@endsection
