<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .voucher-container {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            max-width: 1000px;
            margin: 0 auto;
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
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .info-group {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #666;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            padding: 0.5rem;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .voucher-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        .voucher-table thead {
            background: #ff0000;
            color: #fff;
        }

        .voucher-table th {
            padding: 0.75rem;
            border: 1px solid #ddd;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .voucher-table td {
            padding: 0.75rem;
            border: 1px solid #ddd;
            color: #333;
        }

        .voucher-table tfoot {
            background: #f8f9fa;
            font-weight: 700;
        }

        .memo-box {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            border-left: 4px solid #ff0000;
            margin-bottom: 2rem;
        }

        .memo-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .memo-content {
            font-style: italic;
            color: #333;
        }

        @media print {
            .sidebar-wrapper, .header, .form-actions, .btn-primary { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .voucher-container { box-shadow: none; border: none; max-width: 100%; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="voucher-container shadow-sm">
                <!-- Header Actions -->
                <div class="d-flex justify-content-between align-items-center mb-4 form-actions">
                    <a href="{{ route('admin-finance.check-voucher') }}" class="btn btn-light rounded shadow-sm px-4 d-flex align-items-center" style="height: 40px !important;">
                        <i class="las la-arrow-left me-2"></i>Back to List
                    </a>
                    <div class="d-flex gap-2">
                        <button onclick="window.print()" class="btn btn-primary rounded shadow-sm px-4 d-flex align-items-center" style="background: #ff0000; color: #ffffff; border: none; height: 40px !important;">
                            <i class="las la-print me-2"></i>Print Voucher
                        </button>
                    </div>
                </div>

                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div class="company-details">
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                            <div class="company-address">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                            <div class="company-contact">Tel. No.: 921-3984</div>
                        </div>
                    </div>
                    <div class="document-title">CHECK VOUCHER</div>
                </div>

                <div class="form-info-row">
                    <div class="info-group">
                        <span class="info-label">CV Number</span>
                        <div class="info-value text-primary">{{ $entry->entry_no }}</div>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Check / Reference No.</span>
                        <div class="info-value">{{ $entry->reference ?? 'N/A' }}</div>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Date</span>
                        <div class="info-value">{{ date('F d, Y', strtotime($entry->date)) }}</div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="info-group">
                        <span class="info-label">Payee / Description</span>
                        <div class="info-value" style="background: #fff8f8; border-color: #ffcccc;">
                            {{ $entry->memo }}
                        </div>
                    </div>
                </div>

                <table class="voucher-table">
                    <thead>
                        <tr>
                            <th>Account Title</th>
                            <th class="text-end" style="width: 200px;">Debit</th>
                            <th class="text-end" style="width: 200px;">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entry->items as $item)
                        <tr>
                            <td>
                                <div><strong>{{ $item->memo ?: $item->account->name }}</strong></div>
                                <div class="small text-muted">{{ $item->account->name }} - {{ $item->account->code }}</div>
                            </td>
                            <td class="text-end">
                                {{ $item->debit > 0 ? '₱ ' . number_format($item->debit, 2) : '-' }}
                            </td>
                            <td class="text-end">
                                {{ $item->credit > 0 ? '₱ ' . number_format($item->credit, 2) : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @php
                            $totalDebit = $entry->items->sum('debit');
                            $totalCredit = $entry->items->sum('credit');
                        @endphp
                        <tr>
                            <td class="text-end"><strong>Total Amount</strong></td>
                            <td class="text-end text-primary"><strong>₱ {{ number_format($totalDebit, 2) }}</strong></td>
                            <td class="text-end text-primary"><strong>₱ {{ number_format($totalCredit, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="row mt-5 pt-4 text-center">
                    <div class="col-md-4">
                        <div style="border-bottom: 1px solid #333; margin-bottom: 5px;">{{ $entry->creator->name ?? 'N/A' }}</div>
                        <div class="small text-muted fw-bold">Prepared By</div>
                    </div>
                    <div class="col-md-4">
                        <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 21px;"></div>
                        <div class="small text-muted fw-bold">Checked By</div>
                    </div>
                    <div class="col-md-4">
                        <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 21px;"></div>
                        <div class="small text-muted fw-bold">Approved By</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
