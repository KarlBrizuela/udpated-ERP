<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .content-body .container-fluid {
            padding-left: 15px !important;
            padding-right: 15px !important;
            max-width: 100% !important;
            padding-bottom: 80px !important;
        }

        .don-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        .receipt-box {
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 1.5rem;
            background-color: #f8fafc;
        }
    </style>
    @endpush

    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <a href="{{ route('admin-finance.donations.index') }}" class="btn btn-sm btn-light border mb-2 px-3 fw-semibold text-secondary" style="border-radius: 6px;">
                        <i class="las la-arrow-left me-1"></i> Back to Donations Ledger
                    </a>
                    <h4 class="fs-22 fw-bold text-dark mb-0">Donation Entry: <span class="font-monospace text-danger">{{ $donation->donation_no }}</span></h4>
                    <p class="text-muted small mb-0">Official Acknowledgement Receipt & Tax Certificate Summary</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-light border px-3 d-flex align-items-center gap-2 fw-semibold text-secondary" style="height: 38px; border-radius: 6px;" onclick="window.print()">
                        <i class="las la-print fs-16"></i> Print Official Receipt
                    </button>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-9 mb-4">
                <div class="don-card">
                    <!-- OFFICIAL ACKNOWLEDGEMENT RECEIPT HEADER -->
                    <div class="d-flex justify-content-between align-items-start pb-3 border-bottom mb-4" style="border-color: #cbd5e1 !important;">
                        <div>
                            <h4 class="fw-bold mb-1" style="color: #D9251C;">CLARETIAN PUBLICATIONS</h4>
                            <p class="text-muted small mb-0">Official Donation Acknowledgement Receipt</p>
                            <span class="badge bg-light text-dark border font-monospace mt-1">Receipt No: {{ $donation->receipt_number }}</span>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-dark d-block" style="font-size: 0.85rem;">Date Issued:</span>
                            <span class="text-dark fw-semibold" style="color: #0f172a !important;">{{ $donation->donation_date ? $donation->donation_date->format('F d, Y') : 'N/A' }}</span>
                        </div>
                    </div>

                    <!-- DONOR INFORMATION & CONTRIBUTION VALUE -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded border" style="border-color: #cbd5e1 !important;">
                                <span class="small d-block text-uppercase fw-bold mb-1" style="font-size: 0.65rem; color: #475569;">Received From Donor:</span>
                                <h5 class="fw-bold text-dark mb-1" style="color: #0f172a !important;">{{ $donation->donor ? $donation->donor->name : 'Anonymous Donor' }}</h5>
                                <span class="badge px-2 py-1 rounded" style="background-color: rgba(71, 85, 105, 0.08); color: #475569; font-weight: 600; font-size: 0.72rem;">{{ $donation->donor ? $donation->donor->type : 'Individual' }}</span>
                                @if($donation->donor && $donation->donor->tax_id)
                                    <span class="small font-monospace text-muted ms-2">TIN: {{ $donation->donor->tax_id }}</span>
                                @endif
                                <div class="mt-2 text-muted small">
                                    <span style="color: #475569 !important;">Email: {{ $donation->donor ? ($donation->donor->email ?: 'N/A') : 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded text-white" style="background-color: #D9251C; box-shadow: 0 4px 10px rgba(217, 37, 28, 0.15);">
                                <span class="small d-block text-uppercase fw-bold mb-1" style="font-size: 0.65rem; color: rgba(255,255,255,0.8) !important;">Total Contribution Amount / Value:</span>
                                <h2 class="fw-bold mb-0" style="color: #ffffff !important; font-size: 1.8rem;">₱{{ number_format($donation->amount, 2) }}</h2>
                                <span class="small" style="color: rgba(255,255,255,0.85) !important;">Type: {{ $donation->donation_type }} Donation</span>
                            </div>
                        </div>
                    </div>

                    <!-- DONATION SPECIFICATIONS -->
                    <div class="receipt-box mb-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="las la-info-circle me-1"></i>Donation Details & Allocation</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Contribution Type:</span>
                                <span class="fw-bold text-dark fs-15" style="color: #0f172a !important;">{{ $donation->donation_type }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Project Supported:</span>
                                <span class="fw-bold text-dark fs-15" style="color: #0f172a !important;">{{ $donation->project_supported ?: 'General Ministry & Educational Fund' }}</span>
                            </div>
                            <div class="col-12">
                                <span class="text-muted small d-block">In-Kind Description / Items Donated:</span>
                                <span class="fw-bold text-dark fs-15" style="color: #0f172a !important;">{{ $donation->item_description ?: 'Monetary Contribution' }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Fund Restriction Status:</span>
                                @if($donation->is_restricted)
                                    <span class="badge bg-warning-subtle text-warning px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">Restricted Fund</span>
                                    <p class="text-muted small mb-0 mt-1">Purpose: {{ $donation->restricted_fund_purpose }}</p>
                                @else
                                    <span class="badge bg-success-subtle text-success px-2.5 py-1" style="font-size: 0.72rem; font-weight: 600;">Unrestricted Fund</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Linked Campaign:</span>
                                <span class="fw-bold text-dark fs-15" style="color: #0f172a !important;">{{ $donation->campaign ? $donation->campaign->title : 'General / Independent Contribution' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- TAX DOCUMENTATION CERTIFICATE STATUS -->
                    <div class="p-3 border rounded d-flex justify-content-between align-items-center mb-4" style="border-color: #cbd5e1 !important; background-color: #f8fafc;">
                        <div>
                            <span class="fw-bold d-block" style="color: #0f172a !important;">Donation Tax Deduction Certificate Status</span>
                            <span class="text-muted small">Issued for donor's tax deduction documentation</span>
                        </div>
                        <div>
                            @if($donation->tax_doc_issued)
                                <span class="badge bg-success-subtle text-success px-3 py-2 fs-13" style="font-weight: 600;"><i class="las la-certificate me-1"></i> Issued: {{ $donation->tax_cert_number }}</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2 fs-13" style="font-weight: 600;">Certificate Not Issued</span>
                            @endif
                        </div>
                    </div>

                    <div class="row pt-4 text-center border-top" style="border-color: #cbd5e1 !important;">
                        <div class="col-md-6 mb-3">
                            <span class="text-muted small d-block">Received & Prepared By:</span>
                            <div class="mt-4 pt-2 border-top mx-auto" style="width: 200px; border-color: #cbd5e1 !important;">
                                <span class="fw-bold text-dark d-block">Finance Manager</span>
                                <span class="text-muted small">Claretian Publications</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="text-muted small d-block">Approved By:</span>
                            <div class="mt-4 pt-2 border-top mx-auto" style="width: 200px; border-color: #cbd5e1 !important;">
                                <span class="fw-bold text-dark d-block">Executive Director</span>
                                <span class="text-muted small">Claretian Communications</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
