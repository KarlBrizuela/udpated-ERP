<x-app-layout :title="$title" :role="$role" :sidebar="'admin-finance'">
    @push('styles')
    <style>
        /* Container Gutter & Viewport Padding per SKILL.md Section 2 & 8 */
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 80px !important;
        }

        .generated-letter-card {
            background: #ffffff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            border: none;
        }
        .generated-letter {
            background: #ffffff;
            padding: 30px;
            min-height: 500px;
            font-family: 'Times New Roman', serif;
        }
        .memo-header {
            margin-bottom: 25px;
            font-size: 1.1rem;
            color: #0f172a;
        }
        .memo-header-row {
            margin-bottom: 6px;
            display: flex;
        }
        .memo-header-label {
            font-weight: 700;
            width: 110px;
            color: #475569;
        }
        .memo-header-value {
            flex: 1;
            color: #000000;
            font-weight: 600;
        }
        .memo-body {
            margin: 20px 0;
            line-height: 1.8;
            font-size: 1.05rem;
            color: #0f172a;
        }
        .memo-body-text {
            margin-bottom: 15px;
        }
        .memo-footer {
            margin-top: 50px;
        }
        .memo-signature {
            margin-top: 30px;
            display: flex;
            gap: 50px;
            flex-wrap: wrap;
        }
        .signature-col {
            flex: 1;
            min-width: 200px;
            text-align: center;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            margin-top: 45px;
            margin-bottom: 5px;
        }

        /* Status Badges per SKILL.md */
        .status-badge-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.5px;
        }
        .status-badge-posted {
            background: #d1e7dd;
            color: #0f5132;
            border: 1px solid #a3cfbb;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.5px;
        }

        @media print {
            .sidebar-wrapper, .header, .print-actions, nav, header { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .generated-letter-card { box-shadow: none !important; padding: 0 !important; border: none !important; }
            .generated-letter { padding: 0 !important; border: none !important; }
        }
    </style>
    @endpush

    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-xl-12">
                <div class="card generated-letter-card shadow-sm">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="fw-bold text-dark mb-0 fs-18">
                            <i class="las la-file-alt me-1 text-slate-700"></i> Payment Posting Details (PP-{{ str_pad($posting->id, 5, '0', STR_PAD_LEFT) }})
                        </h4>
                        <div>
                            <span class="{{ $posting->status === 'posted' ? 'status-badge-posted' : 'status-badge-pending' }}">
                                {{ strtoupper($posting->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
                                <i class="las la-check-circle me-1 fs-16"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="generated-letter">
                            <div class="memo-header">
                                <div class="memo-header-row">
                                    <span class="memo-header-label">DATE</span>
                                    <span class="memo-header-value">: {{ date('F d, Y', strtotime($posting->date)) }}</span>
                                </div>
                                <div class="memo-header-row">
                                    <span class="memo-header-label">TO</span>
                                    <span class="memo-header-value">: ACCOUNTING DEPT.</span>
                                </div>
                                <div class="memo-header-row">
                                    <span class="memo-header-label">FROM</span>
                                    <span class="memo-header-value">: FOREIGN ORDER AND RIGHTS DEPT.</span>
                                </div>
                                <div class="memo-header-row">
                                    <span class="memo-header-label">RE</span>
                                    <span class="memo-header-value">: CLIENT'S PAYMENT FOR POSTING</span>
                                </div>
                            </div>

                            <div class="memo-body">
                                <div class="memo-body-text">
                                    Please see attached copy of the deposited check / remittance as payment of the following client(s):
                                </div>
                                <table class="table table-bordered mt-3" style="border: 1px solid #000 !important; font-size: 1rem;">
                                    <thead style="background: #f8f9fa;">
                                        <tr>
                                            <th style="border: 1px solid #000; text-align: center; width: 40px; font-weight: bold;">NO.</th>
                                            <th style="border: 1px solid #000; font-weight: bold;">CLIENT NAME</th>
                                            <th style="border: 1px solid #000; font-weight: bold;">REFERENCES</th>
                                            <th style="border: 1px solid #000; font-weight: bold;">METHOD & DEPOSIT COA</th>
                                            <th style="border: 1px solid #000; font-weight: bold;">CHECK DETAILS</th>
                                            <th style="border: 1px solid #000; text-align: right; font-weight: bold; width: 140px;">AMOUNT</th>
                                            <th style="border: 1px solid #000; text-align: center; font-weight: bold; width: 150px;" class="print-actions">PROOF ATTACHMENT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalAmount = 0; @endphp
                                        @foreach($posting->items as $index => $item)
                                        <tr>
                                            <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
                                            <td style="border: 1px solid #000;">
                                                <strong>{{ $item->customer->customer_name ?? $item->client_name }}</strong>
                                            </td>
                                            <td style="border: 1px solid #000;">
                                                @if($item->invoice_no) <div><small class="text-muted">SI No:</small> {{ $item->invoice_no }}</div> @endif
                                                @if($item->receipt_no) <div><small class="text-muted">OR/CR No:</small> {{ $item->receipt_no }}</div> @endif
                                                @if($item->reference_no) <div><small class="text-muted">Ref No:</small> {{ $item->reference_no }}</div> @endif
                                                @if(!$item->invoice_no && !$item->receipt_no && !$item->reference_no) {{ $item->document_no ?? '—' }} @endif
                                            </td>
                                            <td style="border: 1px solid #000;">
                                                <strong>{{ ucfirst($item->payment_method ?? 'cash') }}</strong>
                                                <div class="small text-muted">
                                                    COA: {{ $item->account ? ($item->account->code . ' - ' . $item->account->name) : 'Cash in Bank' }}
                                                </div>
                                            </td>
                                            <td style="border: 1px solid #000;">
                                                @if(strtolower($item->payment_method ?? '') === 'check' || $item->check_number)
                                                    <div><small class="text-muted">Check #:</small> {{ $item->check_number ?: '—' }}</div>
                                                    <div><small class="text-muted">Bank:</small> {{ $item->bank_name ?: '—' }}</div>
                                                    <div><small class="text-muted">Date:</small> {{ $item->check_date ? date('M d, Y', strtotime($item->check_date)) : '—' }}</div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td style="border: 1px solid #000; text-align: right;"><strong>₱ {{ number_format($item->amount, 2) }}</strong></td>
                                            <td style="border: 1px solid #000; text-align: center;" class="print-actions">
                                                @if($item->proof_attachment)
                                                    @php
                                                        $fileUrl = str_starts_with($item->proof_attachment, 'proof_attachments') 
                                                            ? asset($item->proof_attachment) 
                                                            : asset('storage/' . $item->proof_attachment);
                                                    @endphp
                                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-outline-info btn-xs rounded shadow-sm px-2 py-1">
                                                        <i class="las la-paperclip me-1"></i>View Attachment
                                                    </a>
                                                @else
                                                    <span class="text-muted small">No file</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @php $totalAmount += $item->amount; @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr style="background: #f8f9fa;">
                                            <td colspan="5" style="border: 1px solid #000; text-align: right; font-weight: bold;">TOTAL:</td>
                                            <td style="border: 1px solid #000; text-align: right; font-weight: bold;" class="text-danger">₱ {{ number_format($totalAmount, 2) }}</td>
                                            <td class="print-actions" style="border: 1px solid #000;"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <div class="memo-body-text mt-3">
                                    For your appropriate action.
                                </div>
                            </div>

                            <div class="memo-footer">
                                <div class="memo-signature">
                                    <div class="signature-col">
                                        <div>Prepared by:</div>
                                        <div class="sig-line"></div>
                                        <strong>MICHELLE MACALABON</strong><br>
                                        FORD Clerk
                                    </div>
                                    <div class="signature-col">
                                        <div>Noted by:</div>
                                        <div class="sig-line"></div>
                                        <strong>CRISTINA J. GALANG</strong><br>
                                        FORD Head
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons in View --}}
                        <div class="print-actions d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4 pt-3 border-top">
                            <a href="{{ route('admin-finance.accounting.payment-posting.index') }}" class="btn btn-light border d-inline-flex align-items-center" style="height: 38px; padding: 0 20px; font-weight: 600; color: #475569; border-color: #cbd5e1;">
                                <i class="las la-arrow-left me-1 fs-16"></i> Back to List
                            </a>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-light border d-inline-flex align-items-center" style="height: 38px; padding: 0 20px; font-weight: 600; color: #475569; border-color: #cbd5e1;" onclick="window.print()">
                                    <i class="las la-print me-1 fs-16"></i> Print Letter
                                </button>
                                @if($posting->status === 'pending')
                                <form action="{{ route('admin-finance.accounting.payment-posting.post', $posting->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Mark this Payment Posting as Posted? This will create a Journal Entry in GL & COA.');">
                                    @csrf
                                    <button type="submit" class="btn text-white fw-bold d-inline-flex align-items-center" style="background-color: #10b981; border-color: #10b981; height: 38px; padding: 0 22px; border-radius: 6px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);">
                                        <i class="las la-check-circle me-1 fs-16"></i> Mark as Posted
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
