<x-app-layout :title="'Pick List Details'" :sidebar="'production'">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card pick-list-form">
                    <div class="form-header">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ asset('images/claeritian_logo.png') }}" alt="Claretian Logo" class="header-logo-img" style="height: 52px; width: auto; object-fit: contain; flex-shrink: 0;">
                                <div>
                                    <div class="company-brand" style="font-size: 1.1rem; font-weight: 800; color: #cc0000; letter-spacing: 0.5px; line-height: 1.2;">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                                    <div class="company-sub" style="font-size: 0.8rem; color: #555; font-weight: 500;">8 Mayumi St., U.P. Village, Diliman, Quezon City</div>
                                    <h2 class="document-title" style="margin-top: 4px; margin-bottom: 0; font-size: 1.35rem; font-weight: 800; color: #111; letter-spacing: 1px;">PICK LIST DETAILS</h2>
                                </div>
                            </div>
                            <a href="{{ route('production.logistic.pick-list-list') }}" class="btn btn-secondary no-print" style="background: #6c757d; border: none; color: white;">
                                <i class="las la-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>

                    {{-- Alerts --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert">
                            <i class="las la-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Pick List Details Section -->
                    @php
                        $so = $pickList->salesOrder;
                        $bName = $so?->customer_representative;
                        if (!$bName && $so?->remarks && str_contains($so->remarks, 'Branch:')) {
                            preg_match('/Branch:\s*([^|\n\r]+)/', $so->remarks, $m);
                            $bName = trim($m[1] ?? '');
                        }
                        $bCompany = $bName ? \App\Models\Company::where('company_name', $bName)
                            ->orWhere('company_name', str_replace('AB-', 'AB - ', $bName))
                            ->orWhere('company_name', str_replace('AB - ', 'AB-', $bName))
                            ->first() : null;
                        $accountNo = $bCompany?->account_number ?: ($so?->customer?->account_number ?? null);
                        $acctCompany = $accountNo ? \App\Models\Company::where('account_number', $accountNo)->first() : null;

                        $displayCompanyName = $bCompany?->parent?->company_name 
                            ?: ($bCompany?->company_name 
                            ?: ($acctCompany?->parent?->company_name 
                            ?: ($acctCompany?->company_name 
                            ?: ($so?->customer?->company_name && !in_array(strtolower($so->customer->company_name), ['intracode', 'individual']) ? $so->customer->company_name : ($so?->customer?->customer_name ?? 'N/A')))));
                        $displayAccountNo = $bCompany?->account_number ?: ($acctCompany?->account_number ?: ($so?->customer?->account_number ?? 'N/A'));
                    @endphp
                    <div class="order-info-section">
                        <div class="order-info-box">
                            <h5>Order Information</h5>
                            <div class="form-group">
                                <label>Sales Order Number:</label>
                                <input type="text" value="{{ $so?->so_number ?? 'N/A' }}" readonly>
                            </div>
                            @php
                                $isEcom = ($so?->type === 'ecom_direct' || !empty($so?->ecom_platform) || str_contains(strtolower($so?->so_number ?? ''), 'ecom') || !empty($so?->platform_order_id));
                                $ecomId = $so?->platform_order_id ?: ($so?->ref_number ?: ($so?->po_number ?? null));
                            @endphp
                            @if($isEcom || !empty($ecomId))
                            <div class="form-group">
                                <label>Platform Order ID (E-Com):</label>
                                <input type="text" value="{{ $ecomId ?: 'N/A' }}" readonly>
                            </div>
                            @endif
                            <div class="form-group">
                                <label>Order Date:</label>
                                <input type="text" value="{{ optional(optional($so)->created_at)->format('M d, Y') ?? 'N/A' }}" readonly>
                            </div>
                            @if($so?->cancellation_date)
                            <div class="form-group">
                                <label class="text-danger fw-bold">Cancellation Date:</label>
                                <input type="text" class="fw-bold text-danger" value="{{ \Carbon\Carbon::parse($so->cancellation_date)->format('M d, Y') }}" readonly style="color: #dc3545 !important;">
                            </div>
                            @endif
                            <div class="form-group">
                                <label>Company:</label>
                                <input type="text" value="{{ $displayCompanyName }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Customer Name:</label>
                                <input type="text" value="{{ $so?->customer_representative ?: ($so?->customer?->customer_name ?? 'Unknown') }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Account Number:</label>
                                <input type="text" class="fw-bold font-monospace" value="{{ $displayAccountNo }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Contact:</label>
                                <input type="text" value="{{ $so?->customer_contact ?: ($so?->customer?->mobile ?: ($so?->customer?->main_phone ?: 'N/A')) }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Address:</label>
                                <input type="text" value="{{ $so?->shipping_address ?: ($so?->customer?->address ?: ($so?->customer?->business_address ?? 'N/A')) }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Freight Option:</label>
                                <input type="text" value="{{ $pickList->salesOrder?->freight_option ? ucfirst(str_replace('_', ' ', $pickList->salesOrder->freight_option)) : 'N/A' }}" readonly>
                            </div>
                            @if($pickList->salesOrder?->freight_option === 'bill_client' || $pickList->salesOrder?->forwarder)
                            <div class="form-group">
                                <label>Forwarder:</label>
                                <input type="text" class="fw-bold text-primary" value="{{ $pickList->salesOrder?->forwarder ?? 'N/A' }}" readonly>
                            </div>
                            @endif
                            <div class="form-group">
                                <label>Remarks / Notes:</label>
                                <div class="d-flex flex-column gap-2">
                                    <textarea id="pickListDetailsRemarks" class="form-control screen-only-remarks" style="background:#fff; font-weight:600;" rows="2" placeholder="Enter remarks or special instructions...">{{ $pickList->notes ?: ($pickList->salesOrder?->remarks ?: '') }}</textarea>
                                    <div class="print-only-remarks" id="printRemarksContent">{{ $pickList->notes ?: ($pickList->salesOrder?->remarks ?: 'None') }}</div>
                                    <button type="button" class="btn btn-sm btn-primary align-self-start no-print" onclick="savePickListDetailsRemarks()" style="background:#0d6efd; border:none; border-radius:6px; font-weight:600; padding: 0.5rem 1.25rem;">
                                        <i class="las la-save me-1"></i>Save Remarks
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="order-info-box">
                            <h5>Pick List Information</h5>
                            <div class="form-group">
                                <label>Pick List Number:</label>
                                <input type="text" value="{{ $pickList->pick_list_number }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Status:</label>
                                <div style="padding-top: 0.5rem;">
                                    @if($pickList->status === 'draft')
                                        <span class="badge bg-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Draft</span>
                                    @elseif($pickList->status === 'in_progress')
                                        <span class="badge bg-info" style="padding: 0.5rem 1rem; font-size: 0.875rem;">In Progress</span>
                                    @elseif($pickList->status === 'completed')
                                        <span class="badge bg-success" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Completed</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Prepared By:</label>
                                <input type="text" value="{{ $pickList->preparedByUser?->name ?? 'System' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Pick List Items Table — Inline Editable -->
                    <div style="padding: 0 1.5rem 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; margin-top: 1.5rem;">
                            <h5 style="font-weight: 700; margin: 0;">Pick List Items</h5>
                            <button type="button" id="savePickedBtn" class="btn btn-success" onclick="savePickedItemsInline()" style="background: #28a745; border: none; color: white; padding: 0.6rem 1.75rem; border-radius: 6px; font-weight: 600;">
                                <i class="las la-save me-1"></i>Save Picked Items
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="pick-list-table" style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                                <thead style="background: #cc0000; color: white;">
                                    <tr>
                                        <th style="width: 50px; padding: 0.75rem; border: 1px solid #b30000;">#</th>
                                        <th style="padding: 0.75rem; border: 1px solid #b30000; text-align: left;">Product</th>
                                        <th style="width: 130px; padding: 0.75rem; border: 1px solid #b30000; text-align: center;">ISBN / SKU</th>
                                        <th style="width: 130px; padding: 0.75rem; border: 1px solid #b30000; text-align: center;">Barcode</th>
                                        <th style="width: 110px; padding: 0.75rem; border: 1px solid #b30000; text-align: center;">Requested Qty</th>
                                        <th style="width: 110px; padding: 0.75rem; border: 1px solid #b30000; text-align: right;">Unit Price</th>
                                        <th style="width: 110px; padding: 0.75rem; border: 1px solid #b30000; text-align: right;">Subtotal</th>
                                        <th style="width: 120px; padding: 0.75rem; border: 1px solid #b30000; text-align: center;">Picked Qty</th>
                                        <th style="width: 130px; padding: 0.75rem; border: 1px solid #b30000; text-align: center;">Status</th>
                                        <th style="width: 180px; padding: 0.75rem; border: 1px solid #b30000; text-align: left;">Notes</th>
                                    </tr>
                                </thead>
                                <tbody id="pickListItemsBody">
                                    @forelse($pickList->pickListItems as $item)
                                    @php
                                        $soItem = $item->salesOrderItem;
                                        $book = $soItem?->book;
                                        $bookIndex = $soItem?->bookIndex;
                                        $bundle = $soItem?->bundle;
                                        $prodName = $bookIndex ? $bookIndex->display_name : ($book ? $book->name : ($bundle ? $bundle->name : ($soItem?->item_name ?? 'Unknown Product')));
                                        $itemType = $soItem?->item_type ?? ($bookIndex ? 'Index' : ($bundle ? 'Bundle' : 'Book'));
                                        $isbn = $bookIndex ? ($bookIndex->book?->isbn ?? $bookIndex->book?->sku) : ($book ? ($book->isbn ?? $book->sku) : ($bundle ? ($bundle->sku ?? '') : ''));
                                        $barcode = $bookIndex ? ($bookIndex->book?->barcode) : ($book ? ($book->barcode ?? '') : '');
                                        $pickedQtyInt = (int) $item->picked_qty;
                                        $rowCurr = $pickList->salesOrder?->currency ?? 'USD';
                                        $rowSym = ($rowCurr === 'USD' ? '$' : ($rowCurr === 'EUR' ? '€' : '₱'));
                                    @endphp
                                    <tr data-product="{{ $prodName }}" data-item-id="{{ $item->id }}" data-so-item-id="{{ $soItem?->id }}" data-requested-qty="{{ (int) $item->requested_qty }}">
                                        <td style="padding: 0.6rem; border: 1px solid #ddd; text-align: center;">{{ $loop->iteration }}</td>
                                        <td style="padding: 0.6rem; border: 1px solid #ddd;">
                                            {{ $prodName }}
                                            @if($itemType === 'Bundle')
                                                <span class="badge ms-1" style="background: #6f42c1; color: #fff; font-weight: 600;">Bundle</span>
                                            @elseif($itemType === 'Index')
                                                <span class="badge bg-info text-dark ms-1" style="font-weight: 600;">Index</span>
                                            @else
                                                <span class="badge bg-primary ms-1" style="font-weight: 600;">Book</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.6rem; border: 1px solid #ddd; text-align: center; font-size: 0.8rem; font-family: monospace;">{{ $isbn ?: '—' }}</td>
                                        <td style="padding: 0.6rem; border: 1px solid #ddd; text-align: center; font-size: 0.8rem; font-family: monospace;">{{ $barcode ?: '—' }}</td>
                                        <td style="padding: 0.6rem; border: 1px solid #ddd; text-align: center;">{{ (int) $item->requested_qty }}</td>
                                        <td style="padding: 0.6rem; border: 1px solid #ddd; text-align: right;">{{ $rowSym }}{{ number_format($item->salesOrderItem?->price ?? 0, 2) }}</td>
                                        <td style="padding: 0.6rem; border: 1px solid #ddd; text-align: right;">{{ $rowSym }}{{ number_format($item->salesOrderItem?->subtotal ?? 0, 2) }}</td>
                                        <td style="padding: 0.5rem; border: 1px solid #ddd; text-align: center;">
                                            <input type="number" class="picked-qty form-control form-control-sm text-center"
                                                   value="{{ $pickedQtyInt }}"
                                                   min="0"
                                                   max="{{ (int) $item->requested_qty }}"
                                                   step="1"
                                                   oninput="onPickedQtyChange(this)"
                                                   style="width: 80px; margin: 0 auto; font-weight: 600; border: 1px solid #aaa; border-radius: 4px;">
                                        </td>
                                        <td style="padding: 0.5rem; border: 1px solid #ddd; text-align: center;">
                                            <select class="status-select form-select form-select-sm" style="font-size: 0.82rem; border: 1px solid #aaa; border-radius: 4px;">
                                                <option value="pending" {{ $item->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="picked" {{ $item->status === 'picked' ? 'selected' : '' }}>Picked</option>
                                                <option value="short" {{ $item->status === 'short' ? 'selected' : '' }}>Short</option>
                                            </select>
                                        </td>
                                        <td style="padding: 0.5rem; border: 1px solid #ddd;">
                                            <input type="text" class="notes-input form-control form-control-sm"
                                                   value="{{ $item->notes ?? '' }}"
                                                   placeholder="Notes..."
                                                   style="border: 1px solid #aaa; border-radius: 4px; font-size: 0.85rem;">
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" style="padding: 1rem; text-align: center; color: #999;">No items in this pick list.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="order-info-section summary-info-section" style="margin-top: 1rem;">
                        <div class="order-info-box">
                            <h5>Summary</h5>
                            <div class="form-group">
                                <label>Total Items:</label>
                                <input type="text" value="{{ $pickList->pickListItems->count() }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Total Requested Qty:</label>
                                <input type="text" value="{{ $pickList->pickListItems->sum('requested_qty') }}" readonly>
                            </div>
                            <div class="form-group">
                                <label>Total Picked Qty:</label>
                                <input type="text" id="totalPickedDisplay" value="{{ $pickList->pickListItems->sum('picked_qty') }}" readonly>
                            </div>
                        </div>
                        <div class="order-info-box">
                            <h5>Financial Summary</h5>
                            @php
                                $plCurr = $pickList->salesOrder?->currency ?? 'USD';
                                $plSym = ($plCurr === 'USD' ? '$' : ($plCurr === 'EUR' ? '€' : '₱'));
                            @endphp
                            <div class="form-group">
                                <label>Total Amount:</label>
                                <input type="text" value="{{ $plSym }}{{ number_format($pickList->pickListItems->sum('salesOrderItem.subtotal'), 2) }}" readonly style="font-weight: bold; font-size: 1.1rem; color: #111;">
                            </div>
                            <div class="form-group">
                                <label>Date Created:</label>
                                <input type="text" value="{{ optional($pickList->created_at)->format('M d, Y h:i A') ?? 'N/A' }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="no-print" style="margin-top: 1.5rem; padding: 0 1.5rem 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                        @php
                            $isConsignmentOrder = $pickList->salesOrder && in_array($pickList->salesOrder->type, ['area_consignment', 'area_sales_consignment']);
                            $targetQueueText = $isConsignmentOrder ? 'Delivery Receipt (DR) Preparation' : 'Sales Invoice (SI) Preparation';
                        @endphp
                        <form action="{{ route('production.logistic.mark-as-gathered', $pickList->salesOrder->id ?? 0) }}" method="POST" style="display:inline;" onsubmit="return handleMarkAsGatheredSubmit(this, 'Mark {{ $pickList->pick_list_number }} as gathered and move to {{ $targetQueueText }}?');">
                            @csrf
                            <input type="hidden" name="picked_items_json" id="gatheredPickedItemsJson">
                            <button type="submit" class="btn btn-success" style="background: #28a745; border: none; color: white; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;">
                                <i class="las la-check-circle me-2"></i>Mark as Gathered
                            </button>
                        </form>
                        <a href="{{ route('production.logistic.shipping-label', $pickList->salesOrder?->id ?? $pickList->id) }}" target="_blank" class="btn btn-primary" style="background: #0d6efd; border: none; color: white; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            <i class="las la-tag me-2"></i>Shipping Label
                        </a>
                        <button type="button" class="btn btn-info" style="background: #17a2b8; border: none; color: white; padding: 0.75rem 2rem; border-radius: 6px; cursor: pointer; font-weight: 600;" onclick="window.print()">
                            <i class="las la-print me-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .pick-list-form {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .form-header {
            background: #ffffff;
            color: #333;
            padding: 1.25rem 1.5rem;
            border-bottom: 3px solid #cc0000;
            border-radius: 8px 8px 0 0;
        }
        .document-title {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 1px;
        }
        .order-info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            padding: 1.5rem;
            border-top: 1px solid #dee2e6;
        }
        .order-info-box {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        .order-info-box h5 {
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #cc0000;
            font-size: 1rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.4rem;
        }
        .form-group {
            margin-bottom: 0.75rem;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #444;
            font-size: 0.85rem;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.4rem 0.6rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .picked-qty:focus, .status-select:focus, .notes-input:focus {
            border-color: #cc0000 !important;
            outline: none;
            box-shadow: 0 0 0 2px rgba(204,0,0,0.15);
        }
        #pickListItemsBody tr:hover {
            background: #fff8f8;
        }

        .print-only-remarks {
            display: none;
        }

        /* Print Specific Styles */
        @media print {
            @page {
                size: portrait;
                margin: 8mm;
            }

            nav, header, .deznav, .nav-header, .header, .sidebar, .ez-sidebar,
            .btn, button, a.btn, .no-print, .alert, #savePickedBtn {
                display: none !important;
            }

            body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                color: #000000 !important;
            }

            .container-fluid, .content-body, .row, .col-12 {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            .pick-list-form {
                position: relative !important;
                left: auto !important;
                top: auto !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            .form-header {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0.5rem 0 1rem 0 !important;
                border-bottom: 2px solid #cc0000 !important;
                border-radius: 0 !important;
                margin-bottom: 0.75rem !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                display: block !important;
            }

            .document-title {
                color: #000000 !important;
                font-size: 1.3rem !important;
                font-weight: 800 !important;
                margin-top: 2px !important;
            }

            .company-brand {
                color: #cc0000 !important;
                font-size: 1rem !important;
                font-weight: 800 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .company-sub {
                color: #555555 !important;
                font-size: 0.75rem !important;
            }

            .header-logo-img {
                height: 46px !important;
            }

            .order-info-section {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                gap: 1rem !important;
                padding: 0.5rem 0 !important;
                border-top: 1px solid #ddd !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .summary-info-section {
                margin-top: 0.75rem !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .order-info-box {
                background: #ffffff !important;
                border: 1px solid #ccc !important;
                padding: 0.6rem !important;
                border-radius: 4px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .order-info-box h5 {
                font-size: 0.85rem !important;
                margin-bottom: 0.4rem !important;
                color: #cc0000 !important;
                border-bottom: 1px solid #ddd !important;
                padding-bottom: 0.2rem !important;
            }

            .form-group {
                margin-bottom: 0.35rem !important;
            }

            .form-group label {
                font-size: 0.75rem !important;
                margin-bottom: 0.1rem !important;
                font-weight: 700 !important;
                color: #222 !important;
            }

            .form-group input {
                border: 1px solid #ccc !important;
                padding: 0.2rem 0.4rem !important;
                font-size: 0.8rem !important;
                background: #fff !important;
                color: #000 !important;
                height: auto !important;
            }

            /* Remarks print formatting */
            .screen-only-remarks {
                display: none !important;
            }

            .print-only-remarks {
                display: block !important;
                border: 1px solid #ccc !important;
                padding: 0.4rem 0.5rem !important;
                font-size: 0.8rem !important;
                font-weight: 600 !important;
                background: #fff !important;
                color: #000 !important;
                white-space: pre-wrap !important;
                word-break: break-word !important;
                min-height: 40px !important;
            }

            .table-responsive {
                overflow: visible !important;
            }

            .pick-list-table {
                width: 100% !important;
                border-collapse: collapse !important;
                page-break-inside: auto !important;
            }

            .pick-list-table tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .pick-list-table thead tr th {
                background: #cc0000 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-size: 0.75rem !important;
                padding: 0.35rem 0.4rem !important;
                border: 1px solid #aa0000 !important;
            }

            .pick-list-table tbody tr td {
                padding: 0.35rem 0.4rem !important;
                font-size: 0.75rem !important;
                border: 1px solid #ddd !important;
            }

            .pick-list-table input,
            .pick-list-table select {
                border: none !important;
                background: transparent !important;
                padding: 0 !important;
                font-size: 0.75rem !important;
                box-shadow: none !important;
                -webkit-appearance: none !important;
                -moz-appearance: none !important;
                appearance: none !important;
                text-align: inherit !important;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        const remarksEl = document.getElementById('pickListDetailsRemarks');
        const printRemarksContent = document.getElementById('printRemarksContent');
        if (remarksEl && printRemarksContent) {
            remarksEl.addEventListener('input', function() {
                printRemarksContent.innerText = remarksEl.value || 'None';
            });
        }

        // Live update total picked qty and auto-update status when picked-qty changes
        function onPickedQtyChange(input) {
            const row = input.closest('tr');
            if (!row) return;
            const requestedQty = parseFloat(row.dataset.requestedQty) || 0;
            const pickedQty = parseFloat(input.value) || 0;
            const statusSelect = row.querySelector('.status-select');

            if (statusSelect) {
                if (pickedQty >= requestedQty && requestedQty > 0) {
                    statusSelect.value = 'picked';
                } else if (pickedQty > 0 && pickedQty < requestedQty) {
                    statusSelect.value = 'short';
                } else if (pickedQty === 0) {
                    statusSelect.value = 'pending';
                }
            }
            updateTotalPicked();
        }

        document.querySelectorAll('.picked-qty').forEach(input => {
            input.addEventListener('input', () => onPickedQtyChange(input));
        });

        function updateTotalPicked() {
            let total = 0;
            document.querySelectorAll('.picked-qty').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            const display = document.getElementById('totalPickedDisplay');
            if (display) display.value = total;
        }

        function collectTablePickedItems() {
            const rows = document.querySelectorAll('#pickListItemsBody tr');
            const pickedItems = [];
            let totalPicked = 0;
            let hasError = false;

            rows.forEach((row, idx) => {
                const pickedQtyInput = row.querySelector('.picked-qty');
                const statusSelect = row.querySelector('.status-select');
                const notesInput = row.querySelector('.notes-input');
                if (!pickedQtyInput) return; // empty state row

                const pickedQty = parseFloat(pickedQtyInput.value) || 0;
                const status = statusSelect ? statusSelect.value : 'pending';
                const notes = notesInput ? notesInput.value : '';
                const product = row.dataset.product || row.cells[1]?.innerText || '';
                const id = row.dataset.itemId || null;
                const soItemId = row.dataset.soItemId || null;

                if (status === 'picked' && pickedQty === 0) {
                    alert(`"${product}" is marked as Picked but has 0 quantity. Please update.`);
                    hasError = true;
                    return;
                }

                pickedItems.push({
                    id: id,
                    so_item_id: soItemId,
                    product: product,
                    picked_qty: pickedQty,
                    status: status,
                    notes: notes,
                    item_index: idx
                });
                totalPicked += pickedQty;
            });

            return { items: pickedItems, totalPicked: totalPicked, hasError: hasError };
        }

        function handleMarkAsGatheredSubmit(form, confirmMsg) {
            const collected = collectTablePickedItems();
            if (collected.hasError) return false;

            if (!confirm(confirmMsg)) return false;

            const hiddenInput = form.querySelector('#gatheredPickedItemsJson') || form.querySelector('input[name="picked_items_json"]');
            if (hiddenInput) {
                hiddenInput.value = JSON.stringify(collected.items);
            }
            return true;
        }

        function savePickedItemsInline() {
            const orderId = {{ $pickList->salesOrder?->id ?? 'null' }};
            const soNumber = '{{ $pickList->salesOrder?->so_number }}';
            const collected = collectTablePickedItems();

            if (collected.hasError) return;

            if (!confirm(`Save picked items for ${soNumber}?\n\nTotal picked: ${collected.totalPicked}`)) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                alert('Security token not found. Please refresh the page.');
                return;
            }

            const saveBtn = document.getElementById('savePickedBtn');
            const originalHTML = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="las la-spinner la-spin"></i> Saving...';

            const remarks = document.getElementById('pickListDetailsRemarks')?.value || '';

            fetch('/production/logistic/pick-list/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ order_id: orderId, so_number: soNumber, picked_items: collected.items, remarks: remarks })
            })
            .then(r => r.json())
            .then(data => {
                saveBtn.disabled = false;
                if (data.success) {
                    saveBtn.innerHTML = '<i class="las la-check-circle"></i> Saved!';
                    saveBtn.style.background = '#1a7a35';
                    setTimeout(() => {
                        saveBtn.innerHTML = originalHTML;
                        saveBtn.style.background = '';
                        window.location.reload();
                    }, 1800);
                } else {
                    saveBtn.innerHTML = originalHTML;
                    alert('Error: ' + (data.message || 'Failed to save picked items'));
                }
            })
            .catch(err => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalHTML;
                alert('Error saving: ' + err.message);
            });
        }

        function savePickListDetailsRemarks() {
            const remarks = document.getElementById('pickListDetailsRemarks').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('/production/logistic/pick-list/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({
                    order_id: {{ $pickList->salesOrder?->id ?? 'null' }},
                    so_number: '{{ $pickList->salesOrder?->so_number }}',
                    remarks: remarks
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Remarks saved successfully!');
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to save remarks'));
                }
            })
            .catch(err => alert('Error saving remarks: ' + err.message));
        }
    </script>
    @endpush
</x-app-layout>
