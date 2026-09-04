<x-app-layout :title="'Receiving Reports'" :sidebar="'production'">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 d-block d-sm-flex">
                        <div>
                            <h4 class="fs-24 mb-0 text-black">Receiving Reports</h4>
                        </div>
                        <a href="{{ route('production.logistic.receiving-report.create') }}" class="btn btn-primary rounded d-flex align-items-center" style="gap: 0.5rem; background: #ff0000; border: none;">
                            <i class="las la-plus"></i>
                            <span>Create New RR</span>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="receivingReportsTable" class="display" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th>RR Number</th>
                                        <th>Date Received</th>
                                        <th>P.O. Number</th>
                                        <th>Supplier</th>
                                        <th>Received By</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reports as $rr)
                                    <tr>
                                        <td><strong>{{ $rr->rr_number }}</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($rr->received_date)->format('Y-m-d') }}</td>
                                        <td>{{ $rr->purchaseOrder->po_number ?? 'N/A' }}</td>
                                        <td>{{ $rr->supplier->company_name ?? 'N/A' }}</td>
                                        <td>{{ $rr->receivedBy->name ?? 'System' }}</td>
                                        <td>
                                            <span class="badge {{ $rr->status === 'posted' ? 'badge-success' : 'badge-warning' }} text-capitalize">{{ $rr->status }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="javascript:void(0);" 
                                                   class="btn btn-primary shadow btn-xs sharp view-rr-details" 
                                                   data-id="{{ $rr->id }}"
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn-info shadow btn-xs sharp" title="Print RR" onclick="printRR({{ $rr->id }})">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receiving Report Details Modal -->
    <div class="modal fade" id="rrDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Receiving Report Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="rrModalBody">
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printModalContent('rrModalBody')">Print</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .order-form {
            background: #fff;
            padding: 1rem;
        }
        .form-header {
            margin-bottom: 1.5rem;
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
            width: 50px;
            height: 50px;
            background: #ff0000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
            font-weight: bold;
            flex-shrink: 0;
        }
        .form-header .company-name {
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .form-header .company-details {
            font-size: 0.8rem;
            line-height: 1.2;
        }
        .document-title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 0.5rem;
        }
        .customer-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1rem;
        }
        .customer-details, .order-details {
            background: #f8f9fa;
            padding: 0.75rem;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        .signature-section {
            font-size: 0.9rem;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#receivingReportsTable').DataTable({
                order: [[1, 'desc']],
                pageLength: 25
            });

            $('.view-rr-details').on('click', function() {
                const rrId = $(this).data('id');
                const modal = new bootstrap.Modal(document.getElementById('rrDetailsModal'));
                
                $('#rrModalBody').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
                modal.show();

                $.ajax({
                    url: `/production/logistic/receiving-report/${rrId}`,
                    method: 'GET',
                    success: function(response) {
                        $('#rrModalBody').html(response);
                    },
                    error: function() {
                        $('#rrModalBody').html('<div class="alert alert-danger">Failed to load receiving report details.</div>');
                    }
                });
            });
        });

        function printRR(id) {
            const printWindow = window.open(`/production/logistic/receiving-report/${id}`, '_blank');
            printWindow.onload = function() {
                printWindow.print();
            };
        }

        function printModalContent(divId) {
            const content = document.getElementById(divId).innerHTML;
            const printWindow = window.open('', '', 'height=600,width=800');
            
            printWindow.document.write('<html><head><title>Print</title>');
            const styles = document.getElementsByTagName('style');
            for (let i = 0; i < styles.length; i++) {
                printWindow.document.write(styles[i].outerHTML);
            }
            printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<div class="p-4">' + content + '</div>');
            printWindow.document.write('</body></html>');
            
            printWindow.document.close();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }
    </script>
    @endpush
</x-app-layout>
