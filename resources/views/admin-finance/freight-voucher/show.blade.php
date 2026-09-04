<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .voucher-form {
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
        .form-info-row {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.5rem; padding: 0.75rem;
            background: #f8f9fa; border-radius: 6px; gap: 2rem;
            flex-wrap: wrap;
        }
        .form-info-item { display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 250px; }
        .form-info-item label { font-weight: 600; color: #333; min-width: 120px; }
        .form-info-item span { color: #555; }
        .voucher-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .voucher-table thead { background: #ff0000; color: #fff; }
        .voucher-table th { padding: 0.75rem; border: 1px solid #ddd; }
        .voucher-table td { padding: 0.75rem; border: 1px solid #ddd; }
        .voucher-table tfoot { background: #f8f9fa; font-weight: 600; }
        @media print {
            .sidebar-wrapper, .header, .form-actions { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .voucher-form { box-shadow: none; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card voucher-form">
                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div class="company-details">
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                            <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                            <div class="company-contact">Tel. No.: 921-3984</div>
                        </div>
                    </div>
                    <div class="document-title">FREIGHT VOUCHER</div>
                </div>

                <div class="form-info-row">
                    <div class="form-info-item">
                        <label>FV No.:</label>
                        <span>{{ $voucher->fv_number }}</span>
                    </div>
                    <div class="form-info-item">
                        <label>Pay To:</label>
                        <span>{{ $voucher->pay_to ?? 'N/A' }}</span>
                    </div>
                    <div class="form-info-item">
                        <label>Date:</label>
                        <span>{{ date('M d, Y', strtotime($voucher->date)) }}</span>
                    </div>
                </div>

                <div class="form-info-row">
                    <div class="form-info-item">
                        <label>Created By:</label>
                        <span>{{ $voucher->creator?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="form-info-item">
                        <label>Status:</label>
                        <span>
                            <span class="badge {{ $voucher->status === 'paid' ? 'badge-success' : ($voucher->status === 'liquidated' ? 'badge-info' : 'badge-warning') }}">
                                {{ ucfirst($voucher->status) }}
                            </span>
                        </span>
                    </div>
                    <div class="form-info-item">
                        <label>Created Date:</label>
                        <span>{{ date('M d, Y', strtotime($voucher->created_at)) }}</span>
                    </div>
                </div>

                <table class="voucher-table">
                    <thead>
                        <tr>
                            <th>PARTICULARS</th>
                            <th class="text-end" style="width: 160px;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($voucher->items as $item)
                        <tr>
                            <td>{{ $item->particulars }}</td>
                            <td class="text-end">₱ {{ number_format($item->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">No items</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-end"><strong>TOTAL ADVANCE PAYMENT</strong></td>
                            <td class="text-end"><strong>₱ {{ number_format($voucher->items->sum('amount'), 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="form-actions d-flex justify-content-between gap-2 mt-4">
                    <a href="{{ route('admin-finance.freight-voucher.index') }}" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                        <i class="las la-arrow-left me-1"></i>Back to List
                    </a>
                    <button onclick="window.print()" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #007bff; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                        <i class="las la-print me-1"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
