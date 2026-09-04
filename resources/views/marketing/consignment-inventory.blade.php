<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            border-left: 5px solid #dc3545;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-card.customers { border-left-color: #0d6efd; }
        .stat-card.teams { border-left-color: #198754; }
        .stat-card.quantity { border-left-color: #ffc107; }

        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 0.25rem 0;
            color: #212529;
        }

        .stat-card p {
            margin: 0;
            color: #6c757d;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .consignment-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .badge-consigned { background: #e0f2fe; color: #0369a1; }
        .badge-returned { background: #fef3c7; color: #92400e; }
        .badge-invoiced { background: #dcfce7; color: #15803d; }
        .badge-cancelled { background: #fee2e2; color: #b91c1c; }
    </style>
    @endpush

    @php
        $areaItems = $items->filter(function($cInv) {
            $soType = $cInv->salesOrder->type ?? '';
            return in_array($soType, ['area_consignment', 'area_sales_consignment']);
        });

        $directItems = $items->filter(function($cInv) {
            $soType = $cInv->salesOrder->type ?? '';
            $soNum = $cInv->salesOrder->so_number ?? '';
            return $soType === 'direct_consignment' || str_starts_with($soNum, 'SO-NBS-');
        });
    @endphp

    <div class="content-body" style="min-height: 800px;">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1" style="color: #212529;">
                        <i class="las la-boxes text-danger me-2"></i>Consignment Inventory
                    </h3>
                    <p class="text-muted mb-0 small">Real-time overview of Area Consignment & Direct Consignment inventory.</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>{{ number_format($items->where('status', 'consigned')->sum('quantity')) }}</h3>
                    <p>Total Active Consigned Units</p>
                </div>
                <div class="stat-card customers">
                    <h3>{{ number_format($areaItems->where('status', 'consigned')->sum('quantity')) }}</h3>
                    <p>Area Consignment Units</p>
                </div>
                <div class="stat-card teams">
                    <h3>{{ number_format($directItems->where('status', 'consigned')->sum('quantity')) }}</h3>
                    <p>Direct Consignment Units (NBS / Direct)</p>
                </div>
            </div>

            <!-- Main Inventory Tabs Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom p-3">
                    <ul class="nav nav-pills card-header-pills" id="consignmentTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold me-2" id="area-tab" data-bs-toggle="pill" data-bs-target="#area-consignment" type="button" role="tab">
                                <i class="las la-users me-1"></i>Area Consignment Inventory ({{ $areaItems->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold" id="direct-tab" data-bs-toggle="pill" data-bs-target="#direct-consignment" type="button" role="tab">
                                <i class="las la-store me-1"></i>Direct Consignment Inventory (NBS / Direct Accounts) ({{ $directItems->count() }})
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="consignmentTabsContent">
                        <!-- Area Consignment Tab -->
                        <div class="tab-pane fade show active" id="area-consignment" role="tabpanel">
                            <div class="table-responsive">
                                <table id="areaTable" class="table table-hover align-middle mb-0" style="width: 100%;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ref / SO #</th>
                                            <th>Sales Staff / Team</th>
                                            <th>Customer / Account</th>
                                            <th>Item Title / Code</th>
                                            <th>Type</th>
                                            <th class="text-center">Consigned Qty</th>
                                            <th>Date Consigned</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($areaItems as $cInv)
                                        @php
                                            $title = $cInv->bookIndex ? $cInv->bookIndex->display_name : ($cInv->book ? $cInv->book->name : ($cInv->bookBundle ? $cInv->bookBundle->name : 'N/A'));
                                            $itemType = $cInv->bookIndex ? 'Book Index' : ($cInv->bookBundle ? 'Book Bundle' : 'Book');
                                            $barcodeVal = $cInv->bookIndex ? ($cInv->bookIndex->barcode ?: ($cInv->bookIndex->nbs_barcode ?: $cInv->bookIndex->article)) : ($cInv->book ? ($cInv->book->barcode ?: ($cInv->book->isbn ?: $cInv->book->item_code)) : ($cInv->bookBundle ? $cInv->bookBundle->sku : ''));
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $cInv->salesOrder->so_number ?? ('REF-' . $cInv->id) }}</strong></td>
                                            <td><span class="badge bg-primary fs-6">{{ $cInv->team_name }}</span></td>
                                            <td><span class="fw-semibold text-dark">{{ $cInv->customer->customer_name ?? ($cInv->salesOrder->customer_representative ?: 'N/A') }}</span></td>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $title }}</span>
                                                @if($barcodeVal)
                                                    <br><small class="text-muted"><i class="las la-barcode me-1"></i>Barcode: <code>{{ $barcodeVal }}</code></small>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $itemType }}</span></td>
                                            <td class="text-center fw-bold text-success fs-6">{{ number_format($cInv->quantity) }} pcs</td>
                                            <td data-order="{{ optional($cInv->created_at)->timestamp }}">{{ optional($cInv->created_at)->format('Y-m-d h:i A') }}</td>
                                            <td><span class="consignment-badge badge-{{ $cInv->status }}">{{ ucfirst($cInv->status) }}</span></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Direct Consignment Tab -->
                        <div class="tab-pane fade" id="direct-consignment" role="tabpanel">
                            <div class="table-responsive">
                                <table id="directTable" class="table table-hover align-middle mb-0" style="width: 100%;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ref / SO #</th>
                                            <th>Customer / Account</th>
                                            <th>Source Warehouse</th>
                                            <th>Item Title / Code</th>
                                            <th>Type</th>
                                            <th class="text-center">Consigned Qty</th>
                                            <th>Date Consigned</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($directItems as $cInv)
                                        @php
                                            $title = $cInv->bookIndex ? $cInv->bookIndex->display_name : ($cInv->book ? $cInv->book->name : ($cInv->bookBundle ? $cInv->bookBundle->name : 'N/A'));
                                            $itemType = $cInv->bookIndex ? 'Book Index' : ($cInv->bookBundle ? 'Book Bundle' : 'Book');
                                            $barcodeVal = $cInv->bookIndex ? ($cInv->bookIndex->barcode ?: ($cInv->bookIndex->nbs_barcode ?: $cInv->bookIndex->article)) : ($cInv->book ? ($cInv->book->barcode ?: ($cInv->book->isbn ?: $cInv->book->item_code)) : ($cInv->bookBundle ? $cInv->bookBundle->sku : ''));
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $cInv->salesOrder->so_number ?? ('REF-' . $cInv->id) }}</strong></td>
                                            <td><span class="fw-semibold text-dark">{{ $cInv->customer->customer_name ?? ($cInv->salesOrder->customer_representative ?: 'Direct Consignment Account') }}</span></td>
                                            <td><span class="badge bg-secondary">{{ $cInv->team_name }}</span></td>
                                            <td>
                                                <span class="fw-bold text-dark">{{ $title }}</span>
                                                @if($barcodeVal)
                                                    <br><small class="text-muted"><i class="las la-barcode me-1"></i>Barcode: <code>{{ $barcodeVal }}</code></small>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $itemType }}</span></td>
                                            <td class="text-center fw-bold text-danger fs-6">{{ number_format($cInv->quantity) }} pcs</td>
                                            <td data-order="{{ optional($cInv->created_at)->timestamp }}">{{ optional($cInv->created_at)->format('Y-m-d h:i A') }}</td>
                                            <td><span class="consignment-badge badge-{{ $cInv->status }}">{{ ucfirst($cInv->status) }}</span></td>
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
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#areaTable').DataTable({
                order: [[6, 'desc']],
                pageLength: 15,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search Area Consignment..."
                }
            });

            $('#directTable').DataTable({
                order: [[6, 'desc']],
                pageLength: 15,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search Direct Consignment..."
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
