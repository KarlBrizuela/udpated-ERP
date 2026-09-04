<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .request-form {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .form-header {
            margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e0e0e0;
        }

        .form-header .company-info { display: flex; align-items: center; gap: 1rem; }
        .form-header .company-logo {
            width: 60px; height: 60px; background: #ff0000; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 2rem; font-weight: bold;
        }

        .document-title {
            text-align: center; font-size: 1.75rem; font-weight: 700;
            color: #333; margin-top: 1rem; text-transform: uppercase;
        }

        .nav-tabs { border-bottom: 2px solid #e0e0e0; margin-bottom: 2rem; }
        .nav-tabs .nav-link { font-weight: 600; color: #666; border: none; border-bottom: 3px solid transparent; }
        .nav-tabs .nav-link.active { color: #ff0000; border-bottom-color: #ff0000; background: transparent; }

        .form-section { margin-bottom: 1.5rem; }
        .section-title { font-size: 1.1rem; font-weight: 700; color: #333; text-transform: uppercase; margin: 1.5rem 0; text-align: center; }

        .table-responsive {
            background: #fff;
            border-radius: 8px;
            padding: 0;
        }
        
        @media print {
            .sidebar-wrapper, .header, .nav-tabs, .btn { display: none !important; }
            .content-body { margin-left: 0 !important; padding: 0 !important; }
            .request-form { box-shadow: none; }
            .tab-pane { display: block !important; opacity: 1 !important; }
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12">
            <div class="card request-form">
                <div class="form-header border-0 mb-3 pb-0">
                    <div class="document-title">Cash Advance Liquidation</div>
                </div>

                <!-- Cash Advance Table -->
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-title mt-0 text-start">Existing Cash Advance Liquidation</div>
                        <a href="{{ route('admin-finance.accounting.cash-advance.create') }}" class="btn btn-primary rounded shadow-sm p-0 px-5 d-flex align-items-center justify-content-center" style="background: #ff0000; color: #ffffff; border: none; height: 40px !important; padding-top: 0 !important; padding-bottom: 0 !important;">
                            <i class="las la-plus"></i>Add Liquidation
                        </a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Ref No.</th>
                                    <th>Requested By</th>
                                    <th>Amount</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No records found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
