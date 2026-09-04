@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">Collect Payment</h1>
            <p class="text-muted small">Record COD collection from customer</p>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('rider.collections.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Collections
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Collection Details -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header border-bottom py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Order Details</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">SO Number:</label>
                                <p class="text-dark">{{ $collection->salesOrder->so_number }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Customer:</label>
                                <p class="text-dark">{{ $collection->salesOrder->customer->customer_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">Transaction Type:</label>
                                <p class="text-dark">
                                    @if($collection->transaction_type === 'COD')
                                        <span class="badge badge-danger">{{ $collection->transaction_type }}</span>
                                    @elseif($collection->transaction_type === 'Credit')
                                        <span class="badge badge-info">{{ $collection->transaction_type }}</span>
                                    @elseif($collection->transaction_type === 'Prepaid')
                                        <span class="badge badge-success">{{ $collection->transaction_type }}</span>
                                    @elseif($collection->transaction_type === 'Evaluation')
                                        <span class="badge badge-primary">{{ $collection->transaction_type }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $collection->transaction_type ?? 'Other' }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Address:</label>
                                <p class="text-dark small">{{ $collection->salesOrder->billing_address ?? $collection->salesOrder->customer->billing_address ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Contact:</label>
                                <p class="text-dark">{{ $collection->salesOrder->customer->mobile ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Items -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">
                            Items:
                            @if($collection->isEvaluationCollection() && $collection->evaluation_completed_at)
                                <span class="badge badge-success ml-2">Selected Items Only</span>
                            @endif
                        </label>
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
                                    @php
                                        // If evaluation is completed, show only selected items
                                        $itemsToDisplay = $collection->salesOrder->items;
                                        if ($collection->isEvaluationCollection() && $collection->evaluation_completed_at && $collection->items_selection) {
                                            $itemsToDisplay = $itemsToDisplay->filter(function($item) use ($collection) {
                                                return isset($collection->items_selection[$item->book_id]) && 
                                                       $collection->items_selection[$item->book_id]['purchased_qty'] > 0;
                                            });
                                        }
                                    @endphp
                                    @forelse($itemsToDisplay as $item)
                                        <tr>
                                            <td>{{ $item->book->name ?? 'Unknown' }}</td>
                                            <td class="text-right">
                                                @if($collection->isEvaluationCollection() && $collection->evaluation_completed_at)
                                                    {{ $collection->items_selection[$item->book_id]['purchased_qty'] ?? $item->quantity }}
                                                @else
                                                    {{ $item->quantity }}
                                                @endif
                                            </td>
                                            <td class="text-right">₱{{ number_format($item->price, 2) }}</td>
                                            <td class="text-right">
                                                @if($collection->isEvaluationCollection() && $collection->evaluation_completed_at)
                                                    ₱{{ number_format(($collection->items_selection[$item->book_id]['purchased_qty'] ?? 0) * $item->price, 2) }}
                                                @else
                                                    ₱{{ number_format($item->subtotal, 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No items to display</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light font-weight-bold">
                                        <td colspan="3" class="text-right">TOTAL TO COLLECT:</td>
                                        <td class="text-right text-success" style="font-size: 1.1rem;">
                                            ₱{{ number_format($collection->amount_to_collect, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collection Form -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header border-bottom py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Record Collection</h6>
                </div>
                <div class="card-body">
                    @if($collection->status === 'pending')
                        <form id="collectionForm" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="amount" class="font-weight-bold">Amount Collected</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">₱</span>
                                    </div>
                                    <input type="number" class="form-control" id="amount" name="amount_collected" 
                                        step="0.01" min="0" value="{{ $collection->amount_to_collect }}" required>
                                </div>
                                <small class="form-text text-muted">Expected: ₱{{ number_format($collection->amount_to_collect, 2) }}</small>
                            </div>

                            <div class="form-group mb-3">
                                <label for="notes" class="font-weight-bold">Notes</label>
                                <textarea class="form-control" id="notes" name="collection_notes" rows="3" placeholder="Any issues or remarks..."></textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="signature" class="font-weight-bold">Customer Signature/Photo</label>
                                <input type="file" class="form-control-file" id="signature" name="customer_signature_photo" accept="image/*">
                                <small class="form-text text-muted">Optional: Upload proof of collection</small>
                            </div>

                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check"></i> Record Collection
                            </button>
                        </form>
                    @elseif($collection->status === 'collected')
                        <div class="alert alert-info">
                            <strong>✓ Collection Recorded</strong><br>
                            Amount: ₱{{ number_format($collection->amount_collected, 2) }}<br>
                            Collected on: {{ $collection->collected_at->format('M d, Y h:i A') }}
                        </div>

                        <button type="button" id="handoverBtn" class="btn btn-warning btn-block" onclick="handleHandover()">
                            <i class="fas fa-handshake"></i> Mark as Handed Over
                        </button>
                    @elseif($collection->status === 'handed_over')
                        <div class="alert alert-warning">
                            <strong>⏳ Awaiting Verification</strong><br>
                            Your collection has been handed over and is waiting for cashier verification.
                        </div>
                    @elseif($collection->status === 'verified')
                        <div class="alert alert-success">
                            <strong>✓ Verified & Reconciled</strong><br>
                            Amount: ₱{{ number_format($collection->amount_collected, 2) }}<br>
                            Verified on: {{ $collection->verified_at->format('M d, Y h:i A') }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Collection History -->
            @if($collection->collection_notes)
                <div class="card shadow mt-4">
                    <div class="card-header border-bottom py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Notes</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">{{ $collection->collection_notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Include Evaluation Item Selection Partial -->
    @include('rider.collections.partials.evaluation-selection')
</div>

<script>
    // Handle Record Collection Form
    document.getElementById('collectionForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');

        let formData = new FormData(this);

        fetch('{{ route("rider.collections.record", $collection->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => {
            console.log('Response received:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Data:', data);
            if (data.success) {
                alert('✓ Collection recorded successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
    });

    // Handle Hand Over Button
    function handleHandover() {
        console.log('Handover clicked');
        
        if (!confirm('Are you sure you want to mark this collection as handed over to cashier?')) {
            return;
        }

        fetch('{{ route("rider.collections.hand-over", $collection->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(response => {
            console.log('Response:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert('✓ ' + data.message);
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
    }
</script>
@endsection
