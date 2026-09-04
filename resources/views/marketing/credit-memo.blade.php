<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .memo-form { background: #fff; border-radius: 8px; padding: 2rem; box-shadow: 0 0 20px rgba(0, 0, 0, 0.05); }
        .form-header { margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0; position: relative; }
        .form-header .company-info { display: flex; align-items: flex-start; gap: 1rem; }
        .form-header .company-logo { width: 60px; height: 60px; background: #ff0000; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: bold; flex-shrink: 0; }
        .form-header .document-number { position: absolute; top: 0; right: 0; background: #f8f9fa; padding: 0.5rem 1rem; border: 1px solid #ddd; border-radius: 4px; font-weight: 600; }
        .form-header .document-title { text-align: center; font-size: 1.75rem; font-weight: 700; color: #333; margin-top: 2rem; }
        .main-content-box { border: 2px solid #333; border-radius: 4px; overflow: hidden; margin-top: 1.5rem; display: flex; }
        .content-left { flex: 1; padding: 1rem; border-right: 2px solid #333; min-height: 250px; }
        .content-right { flex: 1; padding: 1rem; background: #f8f9fa; }
        @media print { .sidebar, .header, .form-actions { display: none !important; } }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <form id="memoForm" onsubmit="event.preventDefault(); alert('Credit Memo Saved Successfully!');">
                <div class="card memo-form">
                <!-- Form Header -->
                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div class="company-details">
                            <div class="company-name">CLARETIAN COMMUNICATIONS FOUNDATION, INC.</div>
                            <div class="company-address">8 Mayumi Street, Diliman, Quezon City</div>
                            <div class="company-contact">Tel: (02)921-3984</div>
                        </div>
                    </div>
                    <div class="document-number">Doc No.: 2026-012</div>
                    <div class="document-title">REQUEST FOR ADJUSTMENTS / REVISIONS</div>
                </div>

                <!-- Info Row -->
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-w600">DEPARTMENT:</label>
                        <input type="text" class="form-control" value="Marketing" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-w600">DATE:</label>
                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <!-- Client -->
                <div class="mb-3">
                    <label class="form-label font-w600">Client's Name:</label>
                    <select class="form-control" required>
                        <option value="">Select Customer</option>
                        <option value="National Book Store">National Book Store</option>
                    </select>
                </div>

                <!-- Content Box -->
                <div class="main-content-box">
                    <div class="content-left">
                        <label class="form-label font-w600">REASON:</label>
                        <textarea class="form-control border-0" rows="8" placeholder="Enter reason for adjustment..." required></textarea>
                    </div>
                    <div class="content-right">
                        <label class="form-label font-w600">ACCOUNTING DEPARTMENT</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="approved">
                            <label class="form-check-label" for="approved">Approved</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="disapproved">
                            <label class="form-check-label" for="disapproved">Disapproved</label>
                        </div>
                        <label class="form-label font-w600">REMARKS:</label>
                        <textarea class="form-control" rows="4"></textarea>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="row mt-4 pt-4 border-top">
                    <div class="col-md-6 text-center">
                        <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 30px;"></div>
                        <label class="font-w600">Requested by</label>
                    </div>
                    <div class="col-md-6 text-center">
                        <div style="border-bottom: 1px solid #333; margin-bottom: 5px; height: 30px;"></div>
                        <label class="font-w600">Authorized Official</label>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions mt-4 text-end">
                    <button type="button" class="btn btn-light" onclick="window.print()">Print</button>
                    <button type="submit" class="btn btn-primary" style="background:#ff0000; border:none;">Save Memo</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</x-app-layout>
