<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <style>
        .consignment-tabs-container {
            background: #f8f9fa;
            padding: 10px 10px 0 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .nav-tabs.modern-tabs {
            border-bottom: none;
            gap: 10px;
        }
        .nav-tabs.modern-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-size: 13px;
            letter-spacing: 0.5px;
            padding: 12px 25px;
            border-radius: 10px 10px 0 0;
            transition: all 0.3s ease;
            position: relative;
            background: transparent;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .nav-tabs.modern-tabs .nav-link i {
            font-size: 16px;
            opacity: 0.7;
        }
        .nav-tabs.modern-tabs .nav-link:hover {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.05);
        }
        .nav-tabs.modern-tabs .nav-link.active {
            color: #dc3545;
            background: #fff;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.05);
        }
        .nav-tabs.modern-tabs .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: #dc3545;
            border-radius: 3px 3px 0 0;
        }
        .nav-tabs.modern-tabs .nav-link.active i {
            color: #dc3545;
            opacity: 1;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-hand-holding-usd me-2 text-primary"></i>Consignment Management</h5>
                            <p class="text-muted small mb-0">Manage third-party book owners, assign markups, and track settlements.</p>
                        </div>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <!-- Search Form -->
                            <form action="{{ route('marketing.consignment.index') }}" method="GET" class="d-flex align-items-center gap-1">
                                <div style="width: 220px; height: 32px; display: flex; align-items: center; border: 1px solid #ced4da; border-radius: 4px; background-color: #fff; padding: 0 10px; box-sizing: border-box;">
                                    <span class="fas fa-search text-muted me-2" style="font-size: 0.85rem; line-height: 1;"></span>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Search owner, book..." value="{{ request('search') }}" 
                                           style="border: none !important; background: transparent !important; padding: 0 !important; height: 100%; font-size: 0.82rem; color: #333; outline: none !important; box-shadow: none !important;">
                                    @if(request('search'))
                                        <a href="{{ route('marketing.consignment.index') }}" class="text-muted d-inline-flex align-items-center ms-1" title="Clear search" style="text-decoration: none;">
                                            <span class="fas fa-times-circle" style="color: #999; font-size: 0.9rem; cursor: pointer;"></span>
                                        </a>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-sm btn-danger text-white rounded d-inline-flex align-items-center justify-content-center gap-1" style="height: 32px; padding: 0 12px; font-size: 0.8rem; background-color: #D9251C; border: none;">
                                    <i class="fas fa-search" style="font-size: 0.8rem;"></i>
                                    <span>Search</span>
                                </button>
                            </form>

                            <button class="btn btn-primary btn-sm px-3 shadow-sm d-inline-flex align-items-center justify-content-center" style="height: 32px; font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#addOwnerModal">
                                <i class="fas fa-plus-circle me-1"></i> Add New Owner
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <!-- Enhanced Navigation Tabs -->
                        <div class="consignment-tabs-container">
                            <ul class="nav nav-tabs modern-tabs" id="consignmentTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-bold" id="owners-tab" data-bs-toggle="tab" data-bs-target="#owners" type="button" role="tab">
                                        <i class="fas fa-users"></i> OWNER REGISTRY
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold" id="books-tab" data-bs-toggle="tab" data-bs-target="#books" type="button" role="tab">
                                        <i class="fas fa-book"></i> CONSIGNMENT BOOKS
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold" id="settlements-tab" data-bs-toggle="tab" data-bs-target="#settlements" type="button" role="tab">
                                        <i class="fas fa-file-invoice-dollar"></i> PAYABLES
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                                        <i class="fas fa-history"></i> HISTORY
                                    </button>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content p-4" id="consignmentTabsContent">
                            <!-- Owners Tab -->
                            <div class="tab-pane fade show active" id="owners" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle border">
                                        <thead class="table-light text-uppercase small fw-bold">
                                            <tr>
                                                <th>Owner Name</th>
                                                <th>Contact Person</th>
                                                <th>Email/Phone</th>
                                                <th>Books Count</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($owners as $owner)
                                            <tr>
                                                <td class="fw-bold">{{ $owner->name }}</td>
                                                <td>{{ $owner->contact_person ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="small">{{ $owner->email }}</div>
                                                    <div class="small text-muted">{{ $owner->phone }}</div>
                                                </td>
                                                <td><span class="badge bg-info rounded-pill px-3">{{ $owner->books_count }} books</span></td>
                                                <td class="text-end">
                                                    <button class="btn btn-outline-secondary btn-sm edit-owner-btn" data-id="{{ $owner->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm delete-owner-btn" data-id="{{ $owner->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                                    <p>No consignment owners registered yet.</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Books Tab -->
                            <div class="tab-pane fade" id="books" role="tabpanel">
                                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
                                    <i class="fas fa-info-circle me-3 fa-lg"></i>
                                    <div>
                                        Only books classified as <strong>"Consignment"</strong> in the Master Registry appear here.
                                    </div>
                                </div>
                                <div class="table-responsive mt-3">
                                    <table class="table table-hover align-middle border" id="booksTable">
                                        <thead class="table-light text-uppercase small fw-bold">
                                            <tr>
                                                <th>Book Details</th>
                                                <th>Owner / Supplier</th>
                                                <th>Source Price</th>
                                                <th>Markup</th>
                                                <th>Selling Price</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($books as $book)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $book->name }}</div>
                                                    <div class="small text-muted">SKU: {{ $book->sku }}</div>
                                                </td>
                                                <td>
                                                    @if($book->consignmentOwner)
                                                        <span class="text-primary fw-bold"><i class="fas fa-user-check me-1"></i>{{ $book->consignmentOwner->name }}</span>
                                                    @else
                                                        <span class="text-warning small italic"><i class="fas fa-exclamation-triangle me-1"></i>Unassigned</span>
                                                    @endif
                                                </td>
                                                <td class="fw-bold">₱{{ number_format($book->source_price, 2) }}</td>
                                                <td class="text-success fw-bold">+₱{{ number_format($book->markup_amount, 2) }}</td>
                                                <td class="bg-light fw-bold text-dark">₱{{ number_format($book->price, 2) }}</td>
                                                <td class="text-end">
                                                    <button class="btn btn-primary btn-sm assign-book-btn" 
                                                            data-id="{{ $book->id }}"
                                                            data-name="{{ $book->name }}"
                                                            data-owner-id="{{ $book->consignment_owner_id }}"
                                                            data-source="{{ $book->source_price }}"
                                                            data-markup="{{ $book->markup_amount }}">
                                                        <i class="fas fa-cog me-1"></i> Config
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-5 text-muted">
                                                    <p>No books with classification "Consignment" found.</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Settlements Tab -->
                            <div class="tab-pane fade" id="settlements" role="tabpanel">
                                <div class="row mb-4">
                                    @php $hasPending = false; @endphp
                                    @foreach($settlements as $s)
                                        @if($s['total_owed'] > 0)
                                            @php $hasPending = true; @endphp
                                            <div class="col-md-4 mb-3">
                                                <div class="card border shadow-none h-100">
                                                    <div class="card-body">
                                                        <h6 class="fw-bold mb-3 text-uppercase text-muted small">{{ $s['owner']->name }}</h6>
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="text-muted">Total Books Sold:</span>
                                                            <span class="fw-bold">{{ $s['total_sold_qty'] }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <span class="text-muted">Total Owed (Source):</span>
                                                            <h5 class="fw-bold text-danger mb-0">₱{{ number_format($s['total_owed'], 2) }}</h5>
                                                        </div>
                                                        <hr>
                                                        <button class="btn btn-outline-danger w-100 btn-sm fw-bold settle-btn" 
                                                                data-id="{{ $s['owner']->id }}"
                                                                data-name="{{ $s['owner']->name }}"
                                                                data-amount="{{ $s['total_owed'] }}">
                                                            <i class="fas fa-check-double me-2"></i> MARK AS SETTLED
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    @if(!$hasPending)
                                        <div class="col-12 text-center py-5">
                                            <i class="fas fa-check-circle fa-4x text-success mb-3 opacity-25"></i>
                                            <h5>All accounts are settled!</h5>
                                            <p class="text-muted">No pending payables for any consignment owner.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- History Tab -->
                            <div class="tab-pane fade" id="history" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle border">
                                        <thead class="table-light text-uppercase small fw-bold">
                                            <tr>
                                                <th>Date</th>
                                                <th>Owner</th>
                                                <th>Books Settled</th>
                                                <th>Total Amount</th>
                                                <th class="text-end">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($history as $h)
                                            <tr>
                                                <td>{{ $h->settled_at->format('M d, Y h:i A') }}</td>
                                                <td class="fw-bold">{{ $h->owner->name }}</td>
                                                <td>{{ $h->total_qty }} units</td>
                                                <td class="fw-bold text-success">₱{{ number_format($h->amount, 2) }}</td>
                                                <td class="text-end">
                                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i> Paid</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <p>No settlement history found.</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Add/Edit Owner Modal -->
    <div class="modal fade" id="addOwnerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="ownerModalTitle">Add Consignment Owner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="ownerForm">
                    <input type="hidden" name="owner_id" id="modal_owner_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Owner Name / Company</label>
                            <input type="text" class="form-control" name="name" required placeholder="e.g. Acme Publishing">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Contact Person</label>
                            <input type="text" class="form-control" name="contact_person" placeholder="e.g. John Doe">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Phone</label>
                                <input type="text" class="form-control" name="phone">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Email</label>
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Address</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Account Number</label>
                            <input type="text" class="form-control" name="account_number" placeholder="Internal tracking number">
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light btn-sm px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4">Save Owner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Config Book Consignment Modal -->
    <div class="modal fade" id="configBookModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Consignment Configuration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="configBookForm">
                    <input type="hidden" id="config_book_id">
                    <div class="modal-body text-center py-4 px-5">
                        <i class="fas fa-cog fa-3x text-primary mb-3 opacity-50"></i>
                        <h6 id="config_book_name" class="fw-bold mb-4"></h6>
                        
                        <div class="mb-3 text-start">
                            <label class="form-label small fw-bold">Assign Owner</label>
                            <select class="form-select" id="config_owner_id" required>
                                <option value="" disabled selected>Select an owner...</option>
                                @foreach($allOwners ?? $owners as $owner)
                                    <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3 text-start">
                                <label class="form-label small fw-bold">Source Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" step="0.01" class="form-control price-input" id="config_source_price" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 text-start">
                                <label class="form-label small fw-bold">Your Markup</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" step="0.01" class="form-control price-input" id="config_markup_amount" required>
                                </div>
                            </div>
                        </div>

                        <div class="bg-light p-3 rounded text-center">
                            <div class="small text-muted mb-1 text-uppercase">Selling Price</div>
                            <h3 class="fw-bold text-dark mb-0">₱ <span id="total_price_preview">0.00</span></h3>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success btn-sm px-4">Update Configuration</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center p-4">
                    <div id="notificationIcon" class="mb-3"></div>
                    <h5 id="notificationTitle" class="fw-bold mb-2"></h5>
                    <p id="notificationMessage" class="text-muted small mb-3"></p>
                    <button type="button" class="btn btn-primary btn-sm w-100" data-bs-dismiss="modal" id="notificationBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-question-circle fa-3x text-warning"></i>
                    </div>
                    <h5 id="confirmTitle" class="fw-bold mb-2">Are you sure?</h5>
                    <p id="confirmMessage" class="text-muted small mb-4"></p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light btn-sm flex-fill" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger btn-sm flex-fill" id="confirmBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const ownerModal = new bootstrap.Modal(document.getElementById('addOwnerModal'));
        const configModal = new bootstrap.Modal(document.getElementById('configBookModal'));
        const notifyModal = new bootstrap.Modal(document.getElementById('notificationModal'));
        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmModal'));

        function showAlert(title, message, type = 'success', reload = false) {
            const iconDiv = document.getElementById('notificationIcon');
            const titleEl = document.getElementById('notificationTitle');
            const msgEl = document.getElementById('notificationMessage');
            const btn = document.getElementById('notificationBtn');

            titleEl.innerText = title;
            msgEl.innerText = message;
            
            if (type === 'success') {
                iconDiv.innerHTML = '<i class="fas fa-check-circle fa-3x text-success"></i>';
                btn.className = 'btn btn-success btn-sm w-100';
            } else {
                iconDiv.innerHTML = '<i class="fas fa-exclamation-circle fa-3x text-danger"></i>';
                btn.className = 'btn btn-danger btn-sm w-100';
            }

            if (reload) {
                document.getElementById('notificationModal').addEventListener('hidden.bs.modal', () => location.reload(), { once: true });
            }

            notifyModal.show();
        }

        function showConfirm(message, callback) {
            document.getElementById('confirmMessage').innerText = message;
            const btn = document.getElementById('confirmBtn');
            
            // Remove previous listeners to avoid multiple triggers
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            
            newBtn.addEventListener('click', function() {
                confirmationModal.hide();
                callback();
            });
            
            confirmationModal.show();
        }

        // Handle Owner Form Submission
        document.getElementById('ownerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('modal_owner_id').value;
            const url = id ? `/marketing/consignment/owners/${id}` : "{{ route('marketing.consignment.owners.store') }}";
            const data = Object.fromEntries(new FormData(this).entries());

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ ...data, _method: id ? 'PUT' : 'POST' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.message) {
                    ownerModal.hide();
                    showAlert('Success', data.message, 'success', true);
                } else {
                    showAlert('Error', data.error || 'Something went wrong', 'error');
                }
            });
        });

        // Edit Owner
        document.querySelectorAll('.edit-owner-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`/marketing/consignment/owners/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('modal_owner_id').value = data.id;
                        document.getElementById('ownerModalTitle').innerText = 'Edit Consignment Owner';
                        const form = document.getElementById('ownerForm');
                        form.querySelector('[name="name"]').value = data.name;
                        form.querySelector('[name="contact_person"]').value = data.contact_person || '';
                        form.querySelector('[name="phone"]').value = data.phone || '';
                        form.querySelector('[name="email"]').value = data.email || '';
                        form.querySelector('[name="address"]').value = data.address || '';
                        form.querySelector('[name="account_number"]').value = data.account_number || '';
                        ownerModal.show();
                    });
            });
        });

        // Assign Book Logic
        document.querySelectorAll('.assign-book-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const d = this.dataset;
                document.getElementById('config_book_id').value = d.id;
                document.getElementById('config_book_name').innerText = d.name;
                document.getElementById('config_owner_id').value = d.ownerId || '';
                document.getElementById('config_source_price').value = d.source;
                document.getElementById('config_markup_amount').value = d.markup;
                updatePricePreview();
                configModal.show();
            });
        });

        function updatePricePreview() {
            const source = parseFloat(document.getElementById('config_source_price').value) || 0;
            const markup = parseFloat(document.getElementById('config_markup_amount').value) || 0;
            document.getElementById('total_price_preview').innerText = (source + markup).toLocaleString(undefined, {minimumFractionDigits: 2});
        }

        document.querySelectorAll('.price-input').forEach(input => {
            input.addEventListener('input', updatePricePreview);
        });

        document.getElementById('configBookForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('config_book_id').value;
            const data = {
                consignment_owner_id: document.getElementById('config_owner_id').value,
                source_price: document.getElementById('config_source_price').value,
                markup_amount: document.getElementById('config_markup_amount').value
            };

            fetch(`/marketing/consignment/books/${id}/update`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(data => {
                configModal.hide();
                showAlert('Updated', data.message, 'success', true);
            });
        });

        // Delete Owner
        document.querySelectorAll('.delete-owner-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                showConfirm('Are you sure you want to delete this owner? This action cannot be undone.', function() {
                    fetch(`/marketing/consignment/owners/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) showAlert('Error', data.error, 'error');
                        else showAlert('Deleted', 'Owner deleted successfully', 'success', true);
                    });
                });
            });
        });

        // Settle Logic
        document.querySelectorAll('.settle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const amount = parseFloat(this.dataset.amount).toLocaleString(undefined, {minimumFractionDigits: 2});

                showConfirm(`Are you sure you want to mark ₱${amount} as settled for ${name}?`, function() {
                    fetch(`/marketing/consignment/owners/${id}/settle`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.error) showAlert('Error', data.error, 'error');
                        else showAlert('Settled', data.message, 'success', true);
                    });
                });
            });
        });

        // Reset modal on close
        document.getElementById('addOwnerModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('ownerForm').reset();
            document.getElementById('modal_owner_id').value = '';
            document.getElementById('ownerModalTitle').innerText = 'Add Consignment Owner';
        });
    </script>
    @endpush
</x-app-layout>
