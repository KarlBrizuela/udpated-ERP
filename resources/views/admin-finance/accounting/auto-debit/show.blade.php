<x-app-layout :title="$title" :role="$role" :sidebar="'admin-finance'">
    @push('styles')
    <style>
        .generated-letter-card { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        .generated-letter { background: #fff; border: 1px solid #ddd; padding: 40px; margin-top: 20px; min-height: 600px; font-family: 'Times New Roman', serif; }
        .memo-header { margin-bottom: 30px; }
        .memo-header-row { margin-bottom: 10px; display: flex; align-items: flex-start; }
        .memo-header-label { display: inline-block; width: 80px; font-weight: bold; color: #000; }
        .memo-body { margin: 30px 0; line-height: 1.8; font-size: 1.05rem; }
        .memo-body-text { margin-bottom: 15px; }
        .sig-line { border-bottom: 1px solid #000; margin-top: 40px; margin-bottom: 5px; }
        .approval-trail { background: #f8f9fa; border-radius: 8px; padding: 1.25rem; margin-top: 1.5rem; }
        .approval-step { display: flex; align-items: center; gap: 1rem; padding: 0.75rem 0; border-bottom: 1px solid #dee2e6; }
        .approval-step:last-child { border-bottom: none; }
        .step-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
        .step-icon.done { background: #d1e7dd; color: #0f5132; }
        .step-icon.pending { background: #fff3cd; color: #856404; }
        @media print {
            @page {
                size: A4 portrait;
                margin: 20mm 20mm 20mm 20mm !important;
            }
            body, html {
                background: #fff !important;
                color: #000 !important;
                font-family: 'Times New Roman', Times, serif !important;
                width: 100% !important;
            }
            .sidebar-wrapper, .header, .print-actions, .approval-trail, .card-header, .alert, .nav-header, .deznav {
                display: none !important;
            }
            .content-body, .container-fluid, .row, .col-xl-12, .card, .generated-letter-card, .card-body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                border: none !important;
                box-shadow: none !important;
            }
            .generated-letter {
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                font-size: 11pt !important;
                line-height: 1.6 !important;
            }
            .memo-header {
                margin-bottom: 20px !important;
            }
            .memo-header hr {
                border: none !important;
                border-top: 1px solid #777 !important;
                margin: 15px 0 25px 0 !important;
            }
            .memo-body {
                margin-bottom: 35px !important;
                line-height: 1.8 !important;
                font-size: 11pt !important;
            }
            .memo-body p {
                margin-bottom: 18px !important;
            }
            .memo-signatories {
                margin-top: 35px !important;
                font-size: 11pt !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card generated-letter-card">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Auto Debit Letter — AD-{{ str_pad($debit->id, 5, '0', STR_PAD_LEFT) }}</h5>
                    <span class="badge bg-success px-3 py-2">Fully Approved</span>
                </div>
                <div class="card-body">
                    <div class="generated-letter" style="background: #fff; padding: 40px 50px; font-family: 'Times New Roman', Times, serif; color: #000; font-size: 11pt; line-height: 1.6; max-width: 800px; margin: 0 auto;">
                        {{-- Memo Header --}}
                        <div class="memo-header" style="margin-bottom: 25px;">
                            <table style="width: 100%; border-collapse: collapse; font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000;">
                                <tr>
                                    <td style="width: 80px; vertical-align: top; font-weight: bold; padding-bottom: 8px;">To</td>
                                    <td style="width: 20px; vertical-align: top; font-weight: bold; padding-bottom: 8px;">:</td>
                                    <td style="vertical-align: top; padding-bottom: 8px;">
                                        <strong>Fr. Louie Guades III, CMF</strong><br>
                                        Executive Director
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; font-weight: bold; padding-bottom: 8px;">From</td>
                                    <td style="vertical-align: top; font-weight: bold; padding-bottom: 8px;">:</td>
                                    <td style="vertical-align: top; padding-bottom: 8px;">FORD</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; font-weight: bold; padding-bottom: 8px;">Re</td>
                                    <td style="vertical-align: top; font-weight: bold; padding-bottom: 8px;">:</td>
                                    <td style="vertical-align: top; padding-bottom: 8px;">
                                        <strong>DEBITED AMOUNT FROM METROBANK ACCOUNT</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; font-weight: bold; padding-bottom: 8px;">Date</td>
                                    <td style="vertical-align: top; font-weight: bold; padding-bottom: 8px;">:</td>
                                    <td style="vertical-align: top; padding-bottom: 8px;">{{ date('F d, Y', strtotime($debit->date)) }}</td>
                                </tr>
                            </table>
                            <hr style="border: none; border-top: 1px solid #777; margin: 15px 0 25px 0;">
                        </div>

                        {{-- Memo Body --}}
                        <div class="memo-body" style="line-height: 1.8; font-size: 11pt; margin-bottom: 45px;">
                            <p style="margin-bottom: 20px; text-align: justify;">
                                Please note that the amount of <strong>PHP {{ number_format($debit->amount, 2) }}</strong> was debited against our Metropolitan Bank account on {{ date('F d, Y', strtotime($debit->debit_date)) }}. Said amount represents the {{ $debit->item_reason }} for <strong>{{ strtoupper($debit->source_origin) }}</strong>.
                            </p>
                            <p style="margin-bottom: 20px;">
                                For your reference, please see the attachment.
                            </p>
                            <p style="margin-bottom: 25px;">
                                Thank you very much!
                            </p>
                        </div>

                        {{-- Memo Signatories --}}
                        <div class="memo-signatories" style="font-size: 11pt; margin-top: 35px;">
                            <div style="margin-bottom: 45px;">
                                <div style="font-weight: bold; margin-bottom: 30px;">Prepared by:</div>
                                <div style="font-style: italic; font-weight: 500;">{{ $debit->preparer->name ?? 'Jhay F. Santiago' }}</div>
                                <div>Foreign Order & Rights Department</div>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div style="width: 45%;">
                                    <div style="font-weight: bold; margin-bottom: 30px;">Noted by:</div>
                                    <div>Fr. Louie R. Guades III, CMF</div>
                                    <div>Executive Director</div>
                                </div>
                                <div style="width: 45%;">
                                    <div style="font-weight: bold; margin-bottom: 30px;">Received by:</div>
                                    <div>Lhai C. Abobon</div>
                                    <div>Admin and Finance Manager</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Approval Trail --}}
                    <div class="approval-trail mt-4">
                        <h6 class="fw-bold mb-3"><i class="las la-check-double me-1"></i>Approval Trail</h6>
                        <div class="approval-step">
                            <div class="step-icon done"><i class="las la-user-edit"></i></div>
                            <div>
                                <div class="fw-semibold">Generated by</div>
                                <div class="text-muted small">{{ $debit->preparer->name ?? 'Unknown' }} — {{ $debit->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                        <div class="approval-step">
                            <div class="step-icon done"><i class="las la-check"></i></div>
                            <div>
                                <div class="fw-semibold">Director Approval</div>
                                <div class="text-muted small">Approved by {{ $debit->directorApprover->name ?? 'Unknown' }} — {{ $debit->director_approved_at ? \Carbon\Carbon::parse($debit->director_approved_at)->format('M d, Y h:i A') : '—' }}</div>
                            </div>
                        </div>
                        <div class="approval-step">
                            <div class="step-icon done"><i class="las la-check-double"></i></div>
                            <div>
                                <div class="fw-semibold">Admin & Finance Manager/Supervisor Approval</div>
                                <div class="text-muted small">Approved by {{ $debit->financeApprover->name ?? 'Unknown' }} — {{ $debit->finance_approved_at ? \Carbon\Carbon::parse($debit->finance_approved_at)->format('M d, Y h:i A') : '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="print-actions d-flex justify-content-between gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('admin-finance.accounting.auto-debits.index') }}" class="btn btn-secondary rounded shadow-sm px-4">
                            <i class="las la-arrow-left me-1"></i>Back to List
                        </a>
                        <button type="button" class="btn btn-light rounded shadow-sm px-4" onclick="window.print()">
                            <i class="las la-print me-1"></i>Print Letter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
