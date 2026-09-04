<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <style>
        .document-container {
            background-color: #f8f9fa;
            padding: 20px;
            display: flex;
            justify-content: center;
        }
        .blue-form {
            background-color: #dceefc; /* Light blue like the form in the photo */
            width: 100%;
            max-width: 900px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            color: #2c3e50;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            border: 1px solid #b8daff;
            position: relative;
        }
        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        .logo-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo-placeholder {
            width: 50px;
            height: 50px;
            background: #2c3e50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .company-info h5 {
            margin: 0;
            font-weight: 800;
            color: #000;
            text-transform: uppercase;
            font-size: 1.1rem;
        }
        .company-info p {
            margin: 0;
            font-size: 0.8rem;
            color: #333;
        }
        .date-area {
            font-weight: bold;
            font-size: 1rem;
        }
        .date-underline {
            border-bottom: 1px solid #000;
            min-width: 150px;
            display: inline-block;
            padding: 0 10px;
        }
        .dept-pill {
            background-color: #000;
            color: #fff;
            padding: 5px 20px;
            border-radius: 50px;
            display: inline-block;
            font-weight: bold;
            font-size: 0.8rem;
            margin-bottom: 15px;
        }
        .form-title {
            text-align: center;
            font-weight: 800;
            font-size: 1.2rem;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 25px;
        }
        .field-row {
            margin-bottom: 15px;
            display: flex;
            align-items: baseline;
            gap: 10px;
        }
        .field-label {
            font-weight: bold;
            white-space: nowrap;
        }
        .field-input-underline {
            flex-grow: 1;
            border: none;
            border-bottom: 1px solid #000;
            background: transparent;
            padding: 2px 5px;
            font-family: inherit;
        }
        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border: 1px solid #000;
            margin-top: 20px;
        }
        .grid-box {
            padding: 10px;
            min-height: 250px;
            border: 0.5px solid #000;
        }
        .grid-box-title {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.85rem;
            margin-bottom: 10px;
            display: block;
        }
        .grid-box textarea {
            width: 100%;
            height: 90%;
            border: none;
            background: transparent;
            resize: none;
            font-family: 'Courier New', Courier, monospace; /* To mimic handwriting or typewriter */
            font-size: 1rem;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-top: 30px;
            gap: 40px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 25px;
            text-align: center;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding-top: 5px;
        }
        .jv-no-area {
            position: absolute;
            bottom: 20px;
            right: 40px;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .jv-no-red {
            color: #d9534f;
            font-size: 1.3rem;
            margin-left: 10px;
            border-bottom: 1px solid #d9534f;
            padding: 0 10px;
        }
        .btn-floating-save {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 15px 30px;
            border-radius: 50px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .sidebar-tools {
            position: fixed;
            left: 20px;
            top: 150px;
            width: 250px;
            z-index: 100;
        }
        @media print {
            .btn-floating-save, .sidebar-tools, .page-titles, .nav-header, .header, .quixnav {
                display: none !important;
            }
            .document-container {
                padding: 0;
                background: white;
            }
            .blue-form {
                box-shadow: none;
                border: none;
                max-width: 100%;
            }
        }
    </style>

    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0 d-flex align-items-center">
                <a href="{{ route('admin-finance.credit-collection.billing') }}" class="btn btn-outline-primary btn-xxs me-3"><i class="las la-arrow-left"></i> Back to Billing</a>
                <div class="welcome-text">
                    <h4>Request for Adjustments / Revisions</h4>
                    <p class="mb-0">Preparing the official Blue Form</p>
                </div>
            </div>
        </div>

        <div class="document-container">
            <form action="{{ route('admin-finance.credit-collection.jv-requests.update-adjustment', $request->id) }}" method="POST" enctype="multipart/form-data" class="w-100 d-flex justify-content-center">
                @csrf
                <div class="blue-form">
                    <div class="form-header">
                        <div class="logo-area">
                            <div class="logo-placeholder">C</div>
                            <div class="company-info">
                                <h5>Claretian Communications Foundation, Inc.</h5>
                                <p>No. 8 Mayumi Street, U.P. Village, Diliman, Quezon City</p>
                            </div>
                        </div>
                        <div class="date-area">
                            Date: <div class="date-underline">
                                <input type="date" name="date" class="border-0 bg-transparent" value="{{ \Carbon\Carbon::parse($request->date)->format('Y-m-d') }}" style="outline: none;">
                            </div>
                        </div>
                    </div>

                    <div class="dept-pill">ACCOUNTING DEPARTMENT</div>

                    <div class="form-title">REQUEST FOR ADJUSTMENTS / REVISIONS</div>

                    <div class="field-row">
                        <div class="field-label">Client's Name:</div>
                        <input type="text" name="client_name" class="field-input-underline" value="{{ $request->client_name ?? ($request->items->first()->customer_name ?? '') }}" placeholder="Enter client name" required>
                    </div>

                    <div class="field-row">
                        <div class="field-label">Documents:</div>
                        <input type="text" name="documents" class="field-input-underline" value="{{ $request->documents ?? 'Billing Request' }}" placeholder="e.g. Billing Request from Marketing" required>
                    </div>

                    <div class="grid-container">
                        <div class="grid-box border-end bg-white">
                            <label class="grid-box-title">REASON:</label>
                            <textarea name="reason" placeholder="Type the reason for adjustment here..." required>{{ $request->reason }}</textarea>
                        </div>
                        <div class="grid-box bg-white">
                            <label class="grid-box-title">ACCOUNTING DEPARTMENT: <br>REMARKS:</label>
                            <textarea name="remarks" placeholder="Add accounting remarks here...">{{ $request->accounting_remarks }}</textarea>
                        </div>
                    </div>

                    <div class="footer-grid">
                        <div class="left-signatures">
                            <div class="sig-group mb-5">
                                <div class="signature-line">Requested by: SIGNATURE OVER PRINTED NAME</div>
                            </div>
                            <div class="sig-group">
                                <div class="signature-line">Approved by: SIGNATURE OVER PRINTED NAME</div>
                            </div>
                        </div>
                        <div class="right-signatures">
                            <div class="sig-group mb-5">
                                <div class="signature-line">Approved by: SIGNATURE OVER PRINTED NAME</div>
                            </div>
                            <div class="sig-group">
                                <div class="signature-line">Noted by: SIGNATURE OVER PRINTED NAME</div>
                            </div>
                        </div>
                    </div>

                    <div class="jv-no-area">
                        JV No. <span class="jv-no-red">{{ $request->jv_number }}</span>
                    </div>
                </div>

                <!-- Floating Save Button -->
                <button type="submit" class="btn btn-primary btn-floating-save">
                    <i class="las la-save me-2"></i> Submit Adjustment Request
                </button>

                <!-- Sidebar Content (File Upload and Metadata) -->
                <div class="sidebar-tools">
                    <div class="card shadow">
                        <div class="card-header bg-dark text-white p-3">
                            <h6 class="mb-0 text-white">Supporting Files</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="form-group mb-3">
                                <label class="small fw-bold">Upload Supporting Docs</label>
                                <input type="file" name="supporting_documents" class="form-control form-control-sm" accept=".pdf,.jpg,.png,.zip" required>
                                <small class="text-muted italic xsmall">Attach advertising contracts, SOA, etc.</small>
                            </div>
                            <div class="form-group mb-0">
                                <label class="small fw-bold">Adjustment Category</label>
                                <select name="category" class="form-control form-control-sm" required>
                                    <option value="Account Statement" {{ $request->category == 'Account Statement' ? 'selected' : '' }}>Account Statement</option>
                                    <option value="Freight Charges" {{ $request->category == 'Freight Charges' ? 'selected' : '' }}>Freight Charges</option>
                                    <option value="Ads & Promo" {{ $request->category == 'Ads & Promo' ? 'selected' : '' }}>Ads & Promo</option>
                                    <option value="Reversal" {{ $request->category == 'Reversal' ? 'selected' : '' }}>Reversal</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow mt-3">
                        <div class="card-header bg-info text-white p-3">
                            <h6 class="mb-0 text-white">Summary</h6>
                        </div>
                        <div class="card-body p-3 small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Total Amount:</span>
                                <span class="fw-bold">₱ {{ number_format($request->total_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Items:</span>
                                <span class="fw-bold">{{ $request->items->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
