<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .invoice-form {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }
        .form-header .company-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .form-header .company-logo {
            width: 60px; height: 60px;
            background: #ff0000; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 2rem; font-weight: bold;
        }
        .form-header .company-name {
            font-size: 1.25rem; font-weight: 700; color: #333;
            text-transform: uppercase;
        }
        .form-header .document-title {
            text-align: center; font-size: 1.75rem; font-weight: 700;
            color: #333; margin-top: 1rem;
        }
        .invoice-number {
            text-align: center; font-size: 1.25rem; font-weight: 700;
            color: #ff0000; margin-top: 0.5rem;
        }
        .customer-section {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 2rem; margin-bottom: 1.5rem;
        }
        .customer-details, .transaction-details {
            background: #f8f9fa; padding: 1rem; border-radius: 6px;
        }
        .invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .invoice-table thead { background: #6c757d; color: #fff; }
        .invoice-table th, .invoice-table td { padding: 0.75rem; border: 1px solid #ddd; }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <form action="{{ route('admin-finance.accounting.ar.store', $order->id) }}" method="POST">
                @csrf
                <div class="card invoice-form">
                    <div class="form-header">
                        <div class="company-info">
                            <div class="company-logo">C</div>
                            <div class="company-details">
                                <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                                <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                                <div class="company-contact">Tel. No.: 921-3984</div>
                            </div>
                        </div>
                        <div class="document-title">ACKNOWLEDGEMENT RECEIPT (COMPLIMENTARY)</div>
                        <div class="invoice-number">SO Ref: #{{ $order->so_number }}</div>
                    </div>

                    <div class="customer-section">
                        <div class="customer-details">
                            <h5 class="fw-bold mb-3">Recipient Information</h5>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Issued to:</label>
                                <input type="text" class="form-control" value="{{ $order->customer->customer_name ?? 'N/A' }}" readonly>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Address:</label>
                                <textarea class="form-control" rows="2" readonly>{{ $order->billing_address ?? ($order->customer->address ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="transaction-details">
                            <h5 class="fw-bold mb-3">Transaction Details</h5>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Date:</label>
                                <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-bold">Type:</label>
                                <input type="text" class="form-control" value="Complimentary / Donation" readonly>
                            </div>
                        </div>
                    </div>

                    <table class="invoice-table">
                        <thead>
                            <tr>
                                <th style="width: 80px;">QTY</th>
                                <th>DESCRIPTION</th>
                                <th style="width: 120px;">ISBN</th>
                                <th style="width: 120px;">AREA</th>
                                <th style="width: 150px;">REMARKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td class="text-center">{{ $item->quantity }} {{ $item->unit ?? 'pcs' }}</td>
                                <td>{{ $item->product?->name ?? $item->book?->name ?? $item->bundle?->name ?? 'Unknown Product' }}</td>
                                <td>{{ $item->isbn ?? '-' }}</td>
                                <td>{{ $item->area ?? '-' }}</td>
                                <td>Complimentary</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                <i class="las la-info-circle me-2"></i>
                                This Acknowledgement Receipt is for <strong>Complimentary/Donation</strong> purposes. No payment is expected.
                            </div>
                        </div>
                    </div>

                    <div class="form-actions d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-light" onclick="window.history.back()">Cancel</button>
                        <a href="{{ route('admin-finance.accounting.ar.print', $order->id) }}" target="_blank" class="btn btn-outline-primary">
                            <i class="las la-print me-2"></i>Print AR
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="las la-check-circle me-2"></i>Mark as Complete &amp; Send to Packing
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
