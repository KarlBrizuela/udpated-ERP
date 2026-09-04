<x-app-layout :title="$title" :sidebar="$sidebar">
    <div class="container-fluid">
        <!-- Control actions / header -->
        <div class="d-print-none mb-4 d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('admin-finance.accounting.eford-payouts') }}" class="btn btn-light">
                    <i class="las la-arrow-left"></i> Back to Listing
                </a>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" onclick="window.print();">
                    <i class="las la-print"></i> Print Summary Report
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success d-print-none">
                <i class="las la-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <!-- Sidebar for Attachments -->
            <div class="col-xl-3 col-lg-4 d-print-none">
                <div class="card shadow">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-18 text-black mb-0"><i class="las la-paperclip"></i> Attachments</h4>
                    </div>
                    <div class="card-body">
                        @if($report->attachments && count($report->attachments) > 0)
                            <div class="list-group">
                                @foreach($report->attachments as $index => $path)
                                    <a href="{{ route('admin-finance.accounting.eford-payouts.download', ['id' => $report->id, 'index' => $index]) }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-3 border-light rounded mb-2 bg-light">
                                        <div class="d-flex align-items-center text-truncate">
                                            <i class="las la-file-pdf text-danger fs-24 me-2"></i>
                                            <div class="text-truncate">
                                                <span class="d-block text-black font-weight-bold text-truncate" title="{{ basename($path) }}">{{ basename($path) }}</span>
                                                <small class="text-muted">Click to download</small>
                                            </div>
                                        </div>
                                        <i class="las la-download text-primary fs-18"></i>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="las la-folder-open fs-32 d-block mb-2"></i>
                                <span>No attachments uploaded for this report.</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow mt-4">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-18 text-black mb-0"><i class="las la-info-circle"></i> Metadata</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <span class="text-muted d-block small">Report Status</span>
                                <span class="badge bg-success text-white">Submitted to Accounting</span>
                            </li>
                            <li class="mb-3">
                                <span class="text-muted d-block small">Prepared By</span>
                                <strong>{{ $report->creator->name ?? 'N/A' }}</strong>
                            </li>
                            <li class="mb-3">
                                <span class="text-muted d-block small">Period Covered</span>
                                <strong>{{ $report->period }}</strong>
                            </li>
                            <li class="mb-3">
                                <span class="text-muted d-block small">Date Created</span>
                                <strong>{{ $report->created_at->format('F d, Y h:i A') }}</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Printable Document Area -->
            <div class="col-xl-9 col-lg-8 col-md-12">
                <div class="card shadow printable-card">
                    <div class="card-body printable-body">
                        <!-- Claretian Letterhead -->
                        <div class="report-header">
                            <div class="report-logo">C</div>
                            <div class="report-company">
                                <h3>CLARETIAN COMMUNICATIONS FOUNDATION INC.</h3>
                                <p class="address">8 Mayumi St., UP Village, Diliman, Quezon City</p>
                                <p class="contact">Tel. No.: 921-3984</p>
                            </div>
                        </div>

                        <h2 class="report-title">E-FORD SALES SUMMARY REPORT</h2>

                        <!-- Report Meta Grid -->
                        <table class="report-meta-table">
                            <tr>
                                <td style="width: 15%; font-weight: bold;">PERIOD:</td>
                                <td style="border-bottom: 1px solid #000; padding-bottom: 2px;">{{ $report->period }}</td>
                                <td style="width: 10%;"></td>
                                <td style="width: 15%; font-weight: bold;">CUSTOMER:</td>
                                <td style="border-bottom: 1px solid #000; padding-bottom: 2px;">{{ $report->customer->customer_name ?? 'N/A' }}</td>
                            </tr>
                        </table>

                        <!-- Items Table -->
                        <table class="report-items-table">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">NO.</th>
                                    <th style="width: 15%;">ORDER NO.</th>
                                    <th style="width: 12%;">DATE</th>
                                    <th style="width: 15%;">SI NO.</th>
                                    <th style="width: 20%;">CUSTOMER</th>
                                    <th style="width: 11%;">AMOUNT</th>
                                    <th style="width: 10%;">FREIGHT</th>
                                    <th style="width: 12%;">GROSS SALES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report->items as $index => $item)
                                    <tr>
                                        <td style="text-align: center;">{{ $index + 1 }}</td>
                                        <td>{{ $item->order_no }}</td>
                                        <td style="text-align: center;">{{ $item->date ? \Carbon\Carbon::parse($item->date)->format('m/d/Y') : '' }}</td>
                                        <td>{{ $item->si_no }}</td>
                                        <td>{{ $item->customer_name }}</td>
                                        <td style="text-align: right;">{{ number_format($item->amount, 2) }}</td>
                                        <td style="text-align: right;">{{ number_format($item->freight, 2) }}</td>
                                        <td style="text-align: right; font-weight: bold;">{{ number_format($item->gross_sales, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" style="text-align: right; font-weight: bold; padding: 8px;">TOTALS:</td>
                                    <td style="text-align: right; font-weight: bold; border-top: 2px double #000; border-bottom: 2px double #000;">
                                        PhP {{ number_format($report->total_amount, 2) }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold; border-top: 2px double #000; border-bottom: 2px double #000;">
                                        PhP {{ number_format($report->total_freight, 2) }}
                                    </td>
                                    <td style="text-align: right; font-weight: bold; border-top: 2px double #000; border-bottom: 2px double #000; background-color: #f8f9fa;">
                                        PhP {{ number_format($report->total_gross_sales, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <!-- Signatures & Sign-offs -->
                        <div class="report-signatures">
                            <div class="sig-row">
                                <div class="sig-col">
                                    <div class="sig-label">Prepared by:</div>
                                    <div class="sig-line"></div>
                                    <div class="sig-name">{{ $report->creator->name ?? 'FORD Representative' }}</div>
                                    <div class="sig-title">FORD Department</div>
                                </div>
                                <div class="sig-col">
                                    <div class="sig-label">Checked by:</div>
                                    <div class="sig-line"></div>
                                    <div class="sig-name">MICHELLE MACALABON</div>
                                    <div class="sig-title">FORD Clerk</div>
                                </div>
                                <div class="sig-col">
                                    <div class="sig-label">Noted by:</div>
                                    <div class="sig-line"></div>
                                    <div class="sig-name">CRISTINA J. GALANG</div>
                                    <div class="sig-title">FORD Head</div>
                                </div>
                            </div>
                            <div class="sig-row justify-content-center" style="margin-top: 40px;">
                                <div class="sig-col" style="max-width: 320px; margin: 0 auto;">
                                    <div class="sig-label text-center">Approved by:</div>
                                    <div class="sig-line"></div>
                                    <div class="sig-name text-center">FR. DENNIS TAMAYO, CMF</div>
                                    <div class="sig-title text-center">Executive Director</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Times New Roman Printable Report Sheet */
        .printable-card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .printable-body {
            font-family: 'Times New Roman', Times, serif !important;
            color: #000000 !important;
            padding: 40px !important;
            line-height: 1.4;
        }

        .report-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .report-logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: #dc3545;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 34px;
            margin-right: 20px;
            font-family: sans-serif !important;
        }

        .report-company h3 {
            font-family: 'Times New Roman', Times, serif !important;
            font-size: 19px;
            font-weight: bold;
            margin: 0 0 4px 0;
            color: #000;
            letter-spacing: 0.5px;
        }

        .report-company .address,
        .report-company .contact {
            font-size: 13px;
            margin: 0;
            color: #000;
        }

        .report-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 30px;
            letter-spacing: 0.5px;
        }

        .report-meta-table {
            width: 100%;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .report-meta-table td {
            padding: 4px 0;
            vertical-align: bottom;
        }

        .report-items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 35px;
            font-size: 13px;
        }

        .report-items-table th,
        .report-items-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .report-items-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f8f9fa;
        }

        /* Signatures styling */
        .report-signatures {
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .sig-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }

        .sig-col {
            flex: 1;
            text-align: center;
            min-width: 180px;
        }

        .sig-label {
            font-size: 14px;
            margin-bottom: 35px;
            text-align: left;
        }

        .sig-line {
            border-bottom: 1px solid #000;
            width: 90%;
            margin: 0 auto 5px auto;
        }

        .sig-name {
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
        }

        .sig-title {
            font-size: 12px;
            color: #333;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #fff !important;
                color: #000 !important;
            }

            .main-content, 
            .container-fluid,
            .content-body {
                padding: 0 !important;
                margin: 0 !important;
            }

            .printable-card {
                border: none !important;
                box-shadow: none !important;
            }

            .printable-body {
                padding: 0 !important;
            }

            /* Adjust columns for full width printable */
            .col-xl-9, .col-lg-8 {
                width: 100% !important;
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }

            /* Hide everything that is not printable */
            .d-print-none, 
            .nav-header, 
            .header, 
            .deznav, 
            .footer,
            .sidebar {
                display: none !important;
            }

            /* Enable custom page layout rules */
            @page {
                size: portrait;
                margin: 15mm;
            }
        }
    </style>
    @endpush
</x-app-layout>
