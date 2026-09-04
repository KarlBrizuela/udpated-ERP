@if($collection->isEvaluationCollection())
<!-- Evaluation Item Selection Section -->
<div class="col-md-8 mt-4">
    <div class="card shadow mb-4">
        <div class="card-header border-bottom py-3 bg-info text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-list-check"></i> Customer Item Selection (Evaluation)
                @if($collection->evaluation_completed_at)
                    <span class="float-right badge badge-success">✓ Completed</span>
                @endif
            </h6>
        </div>
        <div class="card-body">
            {{-- Show completion status if already done --}}
            @if($collection->evaluation_completed_at)
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle"></i> 
                    <strong>Evaluation completed on {{ $collection->evaluation_completed_at->format('M d, Y H:i A') }}</strong><br>
                    <small>You can still edit the customer selection below</small>
                </div>
            @endif

            {{-- Always show editable form --}}
            @if(true)
                <p class="text-muted mb-3">
                    <i class="fas fa-info-circle"></i> 
                    Customer can select which items they want to purchase. Unselected items will be returned to inventory.
                </p>

                <form id="evaluationSelectionForm">
                    @csrf
                    
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered" id="evaluationItemsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center" style="width: 80px;">Sent Qty</th>
                                    <th class="text-center" style="width: 100px;">
                                        <label class="mb-0">Purchased Qty</label>
                                    </th>
                                    <th class="text-center" style="width: 80px;">Returned Qty</th>
                                    <th class="text-right" style="width: 120px;">Unit Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($collection->salesOrder->items as $index => $item)
                                    <tr class="evaluation-item-row" data-item-id="{{ $item->id }}" data-book-id="{{ $item->book_id }}">
                                        <td>
                                            <strong>{{ $item->book->name ?? 'Unknown' }}</strong><br>
                                            <small class="text-muted">ID: {{ $item->book_id }}</small>
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" name="items_selection[{{ $item->book_id }}][sent_qty]" 
                                                value="{{ $item->quantity }}" class="sent-qty-input">
                                            <span class="sent-qty-display font-weight-bold">{{ $item->quantity }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $savedQty = 0;
                                                $savedReturned = 0;
                                                if ($collection->evaluation_completed_at && isset($collection->items_selection[$item->book_id])) {
                                                    $savedQty = $collection->items_selection[$item->book_id]['purchased_qty'] ?? 0;
                                                    $savedReturned = $collection->items_selection[$item->book_id]['returned_qty'] ?? 0;
                                                }
                                            @endphp
                                            <input type="number" 
                                                name="items_selection[{{ $item->book_id }}][purchased_qty]" 
                                                class="form-control form-control-sm purchased-qty-input text-center" 
                                                value="{{ $savedQty }}" 
                                                min="0" 
                                                max="{{ $item->quantity }}"
                                                required>
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" 
                                                name="items_selection[{{ $item->book_id }}][returned_qty]" 
                                                class="returned-qty-input" value="{{ $savedReturned }}">
                                            <span class="returned-qty-display font-weight-bold" style="color: #dc3545;">{{ $savedReturned }}</span>
                                        </td>
                                        <td class="text-right">
                                            ₱{{ number_format($item->price, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="2" class="text-right">TOTAL TO COLLECT (Updated):</td>
                                    <td colspan="3" class="text-right text-success" style="font-size: 1.1rem;">
                                        ₱<span id="evaluationTotal">{{ $collection->amount_to_collect }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="form-group mb-3">
                        <label for="evaluationNotes" class="font-weight-bold">Notes</label>
                        <textarea class="form-control" id="evaluationNotes" name="evaluation_notes" rows="2" 
                            placeholder="Any issues or remarks about the evaluation...">{{ $collection->collection_notes ?? '' }}</textarea>
                    </div>

                    @if(!$collection->evaluation_completed_at)
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-check"></i> Record Collection & Customer Selection
                        </button>
                    @else
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-edit"></i> Update Customer Selection
                        </button>
                    @endif
                </form>

                <script>
                    // Handle purchased qty changes for evaluation
                    document.querySelectorAll('.purchased-qty-input').forEach(input => {
                        input.addEventListener('change', function() {
                            const row = this.closest('.evaluation-item-row');
                            const sentQty = parseInt(row.querySelector('.sent-qty-input').value);
                            const purchasedQty = parseInt(this.value) || 0;
                            const returnedQty = sentQty - purchasedQty;
                            
                            row.querySelector('.returned-qty-display').textContent = returnedQty;
                            row.querySelector('.returned-qty-input').value = returnedQty;
                            
                            // Update total
                            calculateEvaluationTotal();
                        });
                    });

                    function calculateEvaluationTotal() {
                        let total = 0;
                        document.querySelectorAll('.evaluation-item-row').forEach(row => {
                            const purchasedQty = parseInt(row.querySelector('.purchased-qty-input').value) || 0;
                            const priceText = row.querySelector('td:nth-child(5)').textContent;
                            const price = parseFloat(priceText.replace('₱', '').replace(/,/g, ''));
                            total += purchasedQty * price;
                        });
                        document.getElementById('evaluationTotal').textContent = total.toFixed(2);
                    }

                    // Handle form submission
                    document.getElementById('evaluationSelectionForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        // Build JSON object from form data
                        const formData = new FormData(this);
                        const data = {
                            items_selection: {},
                            evaluation_notes: formData.get('evaluation_notes')
                        };
                        
                        // Parse nested array items_selection from FormData
                        for (let [key, value] of formData.entries()) {
                            if (key.startsWith('items_selection[')) {
                                const match = key.match(/items_selection\[(\d+)\]\[(\w+)\]/);
                                if (match) {
                                    const bookId = match[1];
                                    const fieldName = match[2];
                                    if (!data.items_selection[bookId]) {
                                        data.items_selection[bookId] = {};
                                    }
                                    data.items_selection[bookId][fieldName] = parseInt(value);
                                }
                            }
                        }
                        
                        fetch('{{ route("rider.collections.evaluation-selection", $collection->id) }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.text().then(text => {
                                    throw new Error(`Server error (${response.status}): ${text.substring(0, 200)}`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                alert('✓ Customer selection recorded successfully!');
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

                    // Initialize on load
                    document.addEventListener('DOMContentLoaded', calculateEvaluationTotal);
                </script>
            @endif
        </div>
    </div>
</div>
@endif
