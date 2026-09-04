<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar ?? 'production'">
    @push('styles')
    <style>
        .generated-letter-card {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }
        .generated-letter {
            background: #fff;
            border: 1px solid #ddd;
            padding: 40px;
            margin-top: 20px;
            min-height: 600px;
            font-family: 'Times New Roman', serif;
        }
        .memo-header {
            margin-bottom: 30px;
        }
        .memo-header-row {
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
        }
        .memo-header-label {
            display: inline-block;
            width: 80px;
            font-weight: bold;
            color: #000;
        }
        .memo-header-value {
            display: inline-block;
            color: #000;
        }
        .memo-body {
            margin: 30px 0;
            line-height: 1.8;
            font-size: 1.05rem;
        }
        .memo-body-text {
            margin-bottom: 15px;
        }
        .memo-footer {
            margin-top: 40px;
        }
        .sig-line {
            border-bottom: 1px solid #000;
            margin-top: 40px;
            margin-bottom: 5px;
        }
        .approval-trail {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.25rem;
            margin-top: 1.5rem;
        }
        .approval-step {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #dee2e6;
        }
        .approval-step:last-child {
            border-bottom: none;
        }
        .approval-step .step-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .step-icon.done { background: #d1e7dd; color: #0f5132; }
        .step-icon.pending { background: #fff3cd; color: #856404; }
        .step-icon.rejected { background: #f8d7da; color: #842029; }
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
                    @php
                        $statusColors = [
                            'pending_director' => 'warning',
                            'pending_finance' => 'info',
                            'approved' => 'success',
                            'rejected' => 'danger',
                        ];
                        $statusLabels = [
                            'pending_director' => 'Pending Director Approval',
                            'pending_finance' => 'Pending Finance Mgr/Supervisor Approval',
                            'approved' => 'Fully Approved',
                            'rejected' => 'Rejected',
                        ];
                        $color = $statusColors[$debit->status] ?? 'secondary';
                        $label = $statusLabels[$debit->status] ?? ucfirst($debit->status);
                    @endphp
                    <span class="badge bg-{{ $color }} px-3 py-2">{{ $label }}</span>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Official Letter --}}
                    <div class="generated-letter" style="background: #fff; padding: 40px 50px; font-family: 'Times New Roman', Times, serif; color: #000; font-size: 11pt; line-height: 1.6; max-width: 800px; margin: 0 auto;">
                        {{-- Memo Header --}}
                        <div class="memo-header" style="margin-bottom: 25px;">
                            <table style="width: 100%; border-collapse: collapse; font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000;">
                                <tr>
                                    <td style="width: 80px; vertical-align: top; font-weight: bold; padding-bottom: 8px;">To</td>
                                    <td style="width: 20px; vertical-align: top; font-weight: bold; padding-bottom: 8px;">:</td>
                                    <td style="vertical-align: top; padding-bottom: 8px;">
                                        <strong>Fr. Louie Guades III, CMF</strong><br>
                                        Production Manager
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
                                    <div>Production Manager</div>
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
                            <div class="step-icon done">
                                <i class="las la-user-edit"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Generated by</div>
                                <div class="text-muted small">{{ $debit->preparer->name ?? 'Unknown' }} — {{ $debit->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                        <div class="approval-step">
                            <div class="step-icon {{ $debit->director_approved_by ? 'done' : ($debit->status === 'rejected' ? 'rejected' : 'pending') }}">
                                <i class="las la-{{ $debit->director_approved_by ? 'check' : ($debit->status === 'rejected' ? 'times' : 'clock') }}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Director Approval</div>
                                @if($debit->director_approved_by)
                                    <div class="text-muted small">Approved by {{ $debit->directorApprover->name ?? 'Unknown' }} — {{ $debit->director_approved_at ? \Carbon\Carbon::parse($debit->director_approved_at)->format('M d, Y h:i A') : '—' }}</div>
                                @elseif($debit->status === 'rejected')
                                    <div class="text-danger small">Rejected</div>
                                @else
                                    <div class="text-warning small">Waiting for Director approval</div>
                                @endif
                            </div>
                        </div>
                        <div class="approval-step">
                            <div class="step-icon {{ $debit->finance_approved_by ? 'done' : ($debit->status === 'rejected' ? 'rejected' : 'pending') }}">
                                <i class="las la-{{ $debit->finance_approved_by ? 'check-double' : ($debit->status === 'rejected' ? 'times' : 'clock') }}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Admin & Finance Manager/Supervisor Approval</div>
                                @if($debit->finance_approved_by)
                                    <div class="text-muted small">Approved by {{ $debit->financeApprover->name ?? 'Unknown' }} — {{ $debit->finance_approved_at ? \Carbon\Carbon::parse($debit->finance_approved_at)->format('M d, Y h:i A') : '—' }}</div>
                                @elseif($debit->status === 'rejected')
                                    <div class="text-danger small">Rejected</div>
                                @else
                                    <div class="text-muted small">{{ $debit->director_approved_by ? 'Waiting for Finance Manager/Supervisor approval' : 'Waiting for Director approval first' }}</div>
                                @endif
                            </div>
                        </div>
                        @if($debit->status === 'approved')
                        <div class="approval-step">
                            <div class="step-icon done">
                                <i class="las la-bank"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Sent to Accounting</div>
                                <div class="text-success small">This Auto Debit letter has been fully approved and is now visible in the Accounting — Auto Debits page.</div>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="print-actions d-flex justify-content-between align-items-center gap-2 mt-4 pt-3 border-top">
                        <a href="javascript:history.back()" class="btn btn-secondary rounded shadow-sm px-4">
                            <i class="las la-arrow-left me-1"></i>Back to List
                        </a>
                        <div class="d-flex gap-2">
                            @if($debit->status === 'pending_director' && (str_contains(strtolower(auth()->user()->position ?? ''), 'director') || auth()->user()->isSuperAdmin()))
                                <form action="{{ route('production.ford.auto-debit.approve-director', $debit->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success text-white rounded shadow-sm px-4">
                                        <i class="las la-check me-1"></i>Approve as Director
                                    </button>
                                </form>
                                <form action="{{ route('production.ford.auto-debit.reject', $debit->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="button" class="btn btn-danger text-white rounded shadow-sm px-4" onclick="handleAutoDebitReject(this.form)">
                                        <i class="las la-times me-1"></i>Reject
                                    </button>
                                </form>
                            @elseif($debit->status === 'pending_finance' && (str_contains(auth()->user()->position ?? '', 'Manager') || str_contains(auth()->user()->position ?? '', 'Supervisor') || (auth()->user()->position ?? '') === 'A&F Manager' || auth()->user()->isSuperAdmin()))
                                <form action="{{ route('production.ford.auto-debit.approve-finance', $debit->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success text-white rounded shadow-sm px-4">
                                        <i class="las la-check-double me-1"></i>Approve as Finance Manager/Supervisor
                                    </button>
                                </form>
                                <form action="{{ route('production.ford.auto-debit.reject', $debit->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="button" class="btn btn-danger text-white rounded shadow-sm px-4" onclick="handleAutoDebitReject(this.form)">
                                        <i class="las la-times me-1"></i>Reject
                                    </button>
                                </form>
                            @endif
                            <button type="button" class="btn btn-light rounded shadow-sm px-4" onclick="window.print()">
                                <i class="las la-print me-1"></i>Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function handleAutoDebitReject(form) {
            if (typeof window.openTwoStepRejectionFlow === 'function') {
                window.openTwoStepRejectionFlow('', function(reason) {
                    let input = form.querySelector('input[name="rejection_reason"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'rejection_reason';
                        form.appendChild(input);
                    }
                    input.value = reason;
                    form.submit();
                });
            } else {
                let reason = prompt('Please enter rejection reason:');
                if (reason !== null && reason.trim() !== '') {
                    let input = form.querySelector('input[name="rejection_reason"]');
                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'rejection_reason';
                        form.appendChild(input);
                    }
                    input.value = reason.trim();
                    form.submit();
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
