<x-app-layout :title="'Purchase Orders'" :sidebar="'production'">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-24 mb-0 text-black">Purchase Orders</h4>
                    </div>
                    <a href="{{ route('production.logistic.purchase-order') }}" class="btn btn-primary rounded d-flex align-items-center" style="gap: 0.5rem; background: #ff0000; border: none;">
                        <i class="las la-plus"></i>
                        <span>Create New P.O.</span>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="purchaseOrdersTable" class="display" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>P.O. Number</th>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Prepared By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrders as $po)
                                <tr>
                                    <td><strong>{{ $po->po_number }}</strong></td>
                                    <td>{{ $po->date ? \Carbon\Carbon::parse($po->date)->format('Y-m-d') : 'N/A' }}</td>
                                    <td>{{ $po->supplier->company_name ?? 'N/A' }}</td>
                                    <td>{{ $po->currency_symbol }}{{ number_format($po->total_amount, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'draft' => 'badge-light',
                                                'ordered' => 'badge-primary',
                                                'partially_received' => 'badge-info',
                                                'received' => 'badge-success',
                                                'cancelled' => 'badge-danger'
                                            ][$po->status] ?? 'badge-secondary';
                                        @endphp
                                        <span class="badge {{ $statusClass }} text-capitalize">{{ str_replace('_', ' ', $po->status) }}</span>
                                    </td>
                                    <td>{{ $po->preparedBy->name ?? 'System' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="javascript:void(0);" 
                                               class="btn btn-primary shadow btn-xs sharp view-po-details" 
                                               data-id="{{ $po->id }}"
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(in_array($po->status, ['ordered', 'partially_received']))
                                            <a href="{{ route('production.logistic.receiving-report.create', $po->id) }}" class="btn btn-success shadow btn-xs sharp" title="Create RR">
                                                <i class="fas fa-file-invoice"></i>
                                            </a>
                                            @endif
                                            <form action="{{ route('production.logistic.purchase-order.destroy', $po->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this Purchase Order? This will also remove any related receiving reports.')" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger shadow btn-xs sharp" title="Delete PO">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No purchase orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Order Details Modal -->
    <div class="modal fade" id="poDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Purchase Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="poModalBody">
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printModalContent('poModalBody')">Print</button>
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
            $('#purchaseOrdersTable').DataTable({
                order: [[1, 'desc']],
                pageLength: 25
            });

            $('.view-po-details').on('click', function() {
                const poId = $(this).data('id');
                const modal = new bootstrap.Modal(document.getElementById('poDetailsModal'));
                
                $('#poModalBody').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
                modal.show();

                $.ajax({
                    url: `/production/logistic/purchase-order/${poId}`,
                    method: 'GET',
                    success: function(response) {
                        $('#poModalBody').html(response);
                    },
                    error: function() {
                        $('#poModalBody').html('<div class="alert alert-danger">Failed to load purchase order details.</div>');
                    }
                });
            });
        });

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
