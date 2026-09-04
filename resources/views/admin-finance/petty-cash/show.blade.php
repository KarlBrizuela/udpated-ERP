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
        .voucher-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .voucher-table thead { background: #ff0000; color: #fff; }
        .voucher-table th { padding: 0.75rem; border: 1px solid #ddd; }
        .voucher-table td { padding: 0.65rem 0.75rem; border: 1px solid #ddd; }
        .voucher-table tfoot { background: #f8f9fa; font-weight: 600; }
        .info-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; background: #f8f9fa; padding: 1rem; border-radius: 6px; }
        .info-item label { font-size: 0.75rem; font-weight: 600; color: #888; text-transform: uppercase; margin-bottom: 0; }
        .info-item .value { font-size: 1rem; font-weight: 600; color: #333; }
        .signature-row { display: flex; gap: 2rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e0e0e0; }
        .signature-box { flex: 1; text-align: center; }
        .signature-box .sig-name { font-weight: 600; margin-top: 0.5rem; }
        .signature-box .sig-label { font-size: 0.8rem; color: #888; border-top: 1px solid #ccc; padding-top: 0.5rem; margin-top: 2rem; }
        .status-badge-open { background: #fff3cd; color: #856404; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem; }
        .status-badge-liquidated { background: #d1e7dd; color: #0f5132; padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem; }
        @media print {
            .sidebar-wrapper, .header, .print-actions { display: none !important; }
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
                        <div>
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                            <div>8 Mayumi St., UP Village, Diliman, Quezon City | Tel. No.: 921-3984</div>
                        </div>
                        <div class="ms-auto text-end">
                            @php
                                $badgeStyle = 'background: #fff3cd; color: #856404;';
                                if ($voucher->status === 'ongoing') {
                                    $badgeStyle = 'background: #cff4fc; color: #087990;';
                                } elseif ($voucher->status === 'completed' || $voucher->status === 'liquidated') {
                                    $badgeStyle = 'background: #d1e7dd; color: #0f5132;';
                                } elseif ($voucher->status === 'rejected') {
                                    $badgeStyle = 'background: #f8d7da; color: #842029;';
                                }
                            @endphp
                            <span style="{{ $badgeStyle }} padding: 4px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem; text-transform: uppercase;">
                                {{ $voucher->status }}
                            </span>
                        </div>
                    </div>
                    <div class="document-title">PETTY CASH VOUCHER</div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <label>PCV No.</label>
                        <div class="value text-danger">{{ $voucher->pcv_number }}</div>
                    </div>
                    <div class="info-item">
                        <label>Voucher Type</label>
                        <div class="value text-uppercase" style="font-size: 0.9rem;">{{ $voucher->type === 'freight' ? 'Freight' : 'Fund' }}</div>
                    </div>
                    <div class="info-item">
                        <label>Pay To</label>
                        <div class="value">{{ $voucher->pay_to }}</div>
                    </div>
                    <div class="info-item">
                        <label>Date</label>
                        <div class="value">{{ date('F d, Y', strtotime($voucher->date)) }}</div>
                    </div>
                </div>

                <table class="voucher-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>PARTICULARS</th>
                            <th style="width: 160px;" class="text-end">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $grandTotal = 0; @endphp
                        @foreach($voucher->items as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item->particulars }}</td>
                            <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                            @php $grandTotal += $item->amount; @endphp
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-end"><strong>TOTAL</strong></td>
                            <td class="text-end text-danger fs-16"><strong>₱ {{ number_format($grandTotal, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>


                @if($voucher->journal_entry_id)
                <div class="mt-4 p-3 border rounded bg-light-success">
                    <small class="text-success"><i class="las la-check-circle me-1"></i>
                        Liquidated & Journalized — <a href="{{ route('accounting.journal.show', $voucher->journal_entry_id) }}" class="text-success fw-bold">View Journal Entry</a>
                    </small>
                </div>
                @endif

                {{-- Proof / Attachment Section --}}
                <div class="mt-4 p-4 border rounded bg-light">
                    <h5 class="fw-bold mb-3"><i class="las la-paperclip me-1"></i>Proof of Payment / Copy of Cheque</h5>
                    @if($voucher->proof_attachment)
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                @php
                                    $ext = pathinfo($voucher->proof_attachment, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp
                                @if($isImage)
                                    <a href="/storage/{{ $voucher->proof_attachment }}" target="_blank">
                                        <img src="/storage/{{ $voucher->proof_attachment }}" alt="Proof" class="img-thumbnail" style="max-height: 150px;">
                                    </a>
                                @else
                                    <a href="/storage/{{ $voucher->proof_attachment }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="las la-download me-1"></i>Download Proof File ({{ strtoupper($ext) }})
                                    </a>
                                @endif
                            </div>
                        </div>
                    @else
                        @if($voucher->status === 'ongoing' && (auth()->id() === $voucher->created_by || auth()->user()->isSuperAdmin() || auth()->user()->position === 'Cashier'))
                            <form action="{{ route('admin-finance.petty-cash.upload-proof', $voucher->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small text-muted">Upload image of proof or copy of cheque:</label>
                                    <div class="d-flex gap-2 align-items-center">
                                        <input type="file" name="proof_file" class="form-control" accept="image/*,application/pdf" required>
                                        <button type="submit" class="btn btn-primary btn-sm rounded shadow-sm px-4" style="background: #ff0000; border: none; height: 35px;">Upload</button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <p class="text-muted mb-0 small">No proof of payment attached yet.</p>
                        @endif
                    @endif
                </div>

                {{-- Cashier Actions Section --}}
                @if($voucher->status === 'pending' && (auth()->user()->position === 'Cashier' || auth()->user()->isSuperAdmin()))
                <div class="mt-4 p-3 border rounded bg-light-warning d-flex gap-2">
                    <form action="{{ route('admin-finance.accounting.cashier.approve', $voucher->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-success"><i class="las la-check me-1"></i>Approve Petty Cash</button>
                    </form>
                    <form action="{{ route('admin-finance.accounting.cashier.reject', $voucher->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger"><i class="las la-times me-1"></i>Reject Petty Cash</button>
                    </form>
                </div>
                @endif



                <div class="print-actions d-flex justify-content-between gap-2 mt-4">
                    @if(request('from') === 'cashier')
                    <a href="{{ route('admin-finance.accounting.cashier.index') }}" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                        <i class="las la-arrow-left me-1"></i>Back to Cashier
                    </a>
                    @else
                    <a href="{{ route('admin-finance.petty-cash.index') }}" class="btn btn-primary rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                        <i class="las la-arrow-left me-1"></i>Back to List
                    </a>
                    @endif
                    <button type="button" class="btn btn-light rounded shadow-sm px-5 d-flex align-items-center justify-content-center" style="height: 35px !important; padding-top: 0 !important; padding-bottom: 0 !important;" onclick="window.print()"><i class="las la-print me-1"></i>Print</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
