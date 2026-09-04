<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        /* Modern Tabbed Form Styles */
        .product-modal-header-info {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .product-tab-container {
            display: flex;
            min-height: 520px;
        }

        .product-nav-tabs {
            width: 200px;
            border-right: 1px solid #dee2e6;
            background: #f1f1f1;
            padding-top: 1rem;
        }

        .product-nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            text-align: left;
            padding: 12px 15px;
            color: #444;
            font-weight: 500;
            font-size: 0.85rem;
            border-left: 4px solid transparent;
            transition: all 0.2s;
        }

        .product-nav-tabs .nav-link:hover {
            background: #e9e9e9;
        }

        .product-nav-tabs .nav-link.active {
            background: #fff;
            color: #ff0000;
            border-left-color: #ff0000;
            margin-right: -1px;
            font-weight: 700;
        }

        .product-tab-content {
            flex: 1;
            padding: 1.5rem;
            background: #fff;
            overflow-y: auto;
            max-height: 600px;
        }

        .form-row-custom {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            gap: 1rem;
        }

        .form-row-custom>label {
            width: 140px;
            text-align: right;
            font-size: 0.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0;
            text-transform: uppercase;
        }

        .form-row-custom .form-control-sm,
        .form-row-custom .form-select-sm {
            flex: 1;
            border-radius: 2px;
            border: 1px solid #ced4da;
        }

        .section-divider {
            border-bottom: 2px solid #f1f1f1;
            margin: 1.5rem 0 1rem;
            font-weight: 800;
            font-size: 0.8rem;
            color: #2c3e50;
            text-transform: uppercase;
            padding-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-divider::after {
            content: "";
            height: 1px;
            background: #eee;
            flex: 1;
        }

        .info-pane {
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 1rem;
            background: #fcfcfc;
            height: 100%;
        }

        .pane-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: #666;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            border-bottom: 1px solid #eee;
            padding-bottom: 2px;
        }

        .stat-box {
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            min-width: 80px;
            text-align: center;
        }

        /* Premium Image Upload Styles */
        .image-upload-wrapper {
            position: relative;
            width: 100%;
            max-width: 250px;
            margin: 0 auto;
        }

        .image-preview-premium {
            width: 100%;
            height: 250px;
            border-radius: 12px;
            overflow: hidden;
            background: #f8f9fa;
            border: 2px dashed #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .image-preview-premium:hover {
            border-color: #ff0000;
            background: #fffafa;
        }

        .image-preview-premium img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .image-preview-premium:hover img {
            transform: scale(1.05);
        }

        .upload-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 0, 0, 0.7);
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(2px);
        }

        .image-preview-premium:hover .upload-overlay {
            opacity: 1;
        }

        .upload-overlay i {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .upload-overlay span {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div class="d-flex align-items-center mt-3 mt-sm-0">
                        <select class="default-select style-1 me-3">
                            <option>Filter By Category</option>
                            <option>Bibles</option>
                            <option>Prayer Books</option>
                            <option>Spiritual Reading</option>
                            <option>Theology</option>
                            <option>Church History</option>
                            <option>Children's Books</option>
                        </select>
                        <a href="javascript:void(0);"
                            class="btn btn-primary rounded d-flex align-items-center" data-bs-toggle="modal"
                            data-bs-target="#addProductModal"
                            style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500;">
                            <i class="las la-plus"
                                style="font-size: 1rem; line-height: 1; margin: 0; padding: 0; background: transparent; border: none; box-shadow: none;"></i>
                            <span style="font-size: 0.875rem; white-space: nowrap;">Add New Book</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th style="width:50px;">
                                        <div class="form-check custom-checkboxcheckbox-success check-lg me-3">
                                            <input type="checkbox" class="form-check-input" id="checkAll">
                                            <label class="form-check-label" for="checkAll"></label>
                                        </div>
                                    </th>
                                    <th>Image</th>
                                    <th>SKU</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td>
                                        <div class="form-check custom-checkbox checkbox-success check-lg me-3">
                                            <input type="checkbox" class="form-check-input" id="check{{ $product->id }}">
                                            <label class="form-check-label" for="check{{ $product->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        @if($product->image)
                                            <img src="/storage/{{ $product->image }}" 
                                                 alt="" class="rounded" width="35" height="35" style="object-fit: cover;">
                                        @else
                                            <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                <i class="fas fa-book text-muted" style="font-size: 0.8rem;"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td><strong>#{{ $product->sku }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center"><span class="w-space-no">{{ $product->name }}</span></div>
                                    </td>
                                    <td>{{ $product->category ?? 'Uncategorized' }}</td>
                                    <td>₱{{ number_format($product->price, 2) }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fa fa-circle text-{{ $product->stock > 10 ? 'success' : 'warning' }} me-1"></i>
                                            {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1 edit-product-btn" 
                                               data-id="{{ $product->id }}"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-danger shadow btn-xs sharp delete-product-btn"
                                               data-id="{{ $product->id }}"><i class="fa fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No products found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <form id="addProductForm" class="modal-content" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="product_id" id="modal_product_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalTitle">Book Information - [New]</h5>
                    <div class="d-flex align-items-center">
                        <button type="submit" class="btn btn-primary btn-sm me-2">Save Product</button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <!-- Global Header Info -->
                <div class="product-modal-header-info">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="form-row-custom">
                                <label>ITEM NAME</label>
                                <input type="text" class="form-control form-control-sm" name="name" required>
                            </div>
                            <div class="form-row-custom">
                                <label>SKU / NUMBER</label>
                                <input type="text" class="form-control form-control-sm" name="sku" required>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex align-items-center gap-3">
                                <div class="form-row-custom mb-0">
                                    <label style="width: auto;">MFR PART NUMBER</label>
                                    <input type="text" class="form-control form-control-sm"
                                        style="width: 150px;" name="mfr_part_no">
                                </div>
                                <div class="form-check custom-checkbox check-xs mb-0 ms-auto">
                                    <input type="checkbox" class="form-check-input" name="is_active" value="1" checked>
                                    <label class="form-check-label small" for="itemInactive">Item is active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body p-0">
                    <div class="product-tab-container">
                        <!-- Vertical Tabs -->
                        <div class="nav flex-column nav-pills product-nav-tabs" id="product-tabs"
                            role="tablist">
                            <button class="nav-link active" id="tab-general-link" data-bs-toggle="pill"
                                data-bs-target="#tab-general" type="button" role="tab">General</button>
                            <button class="nav-link" id="tab-financials-link" data-bs-toggle="pill"
                                data-bs-target="#tab-financials" type="button" role="tab">Financials</button>
                            <button class="nav-link" id="tab-inventory-link" data-bs-toggle="pill"
                                data-bs-target="#tab-inventory" type="button" role="tab">Inventory Logic</button>
                            <button class="nav-link" id="tab-metadata-link" data-bs-toggle="pill"
                                data-bs-target="#tab-metadata" type="button" role="tab">Book Metadata</button>
                            <button class="nav-link" id="tab-image-link" data-bs-toggle="pill"
                                data-bs-target="#tab-image" type="button" role="tab">Book Cover</button>
                        </div>

                        <!-- Tab Content -->
                        <div class="tab-content product-tab-content" id="product-tabs-content">
                            <!-- General Tab -->
                            <div class="tab-pane fade show active" id="tab-general" role="tabpanel">
                                <div class="section-divider mt-0">IDENTIFICATION</div>
                                <div class="form-row-custom">
                                    <label>CATEGORY</label>
                                    <select class="form-select form-select-sm" name="category">
                                        <option value="">Uncategorized</option>
                                        <option>Bibles</option>
                                        <option>Prayer Books</option>
                                        <option>Spiritual Reading</option>
                                        <option>Seasonal</option>
                                    </select>
                                </div>
                                <div class="form-row-custom">
                                    <label>BARCODE NUMBER</label>
                                    <input type="text" class="form-control form-control-sm" name="barcode">
                                </div>

                                <div class="section-divider">UNIT OF MEASURE</div>
                                <div class="form-row-custom">
                                    <label>U/M SET</label>
                                    <select class="form-select form-select-sm" name="unit">
                                        <option value="pcs">Piece: pc</option>
                                        <option value="set">Set: set</option>
                                        <option value="box">Box: box</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Financials Tab -->
                            <div class="tab-pane fade" id="tab-financials" role="tabpanel">
                                <div class="row">
                                    <!-- Purchase Info -->
                                    <div class="col-md-6">
                                        <div class="info-pane">
                                            
                                            <div class="form-group mb-3">
                                                <label class="small fw-bold">PURCHASE DESCRIPTION</label>
                                                <textarea class="form-control form-control-sm" rows="3"
                                                    name="purchase_description"></textarea>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>COST</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">₱</span>
                                                    <input type="number" class="form-control" name="cost"
                                                        placeholder="0.00" step="0.01">
                                                </div>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>COGS ACCOUNT</label>
                                                <select class="form-select form-select-sm" name="cogs_account">
                                                    <option>Cost of Sales</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Sales Info -->
                                    <div class="col-md-6">
                                        <div class="info-pane">
                                            
                                            <div class="form-group mb-3">
                                                <label class="small fw-bold">SALES DESCRIPTION</label>
                                                <textarea class="form-control form-control-sm" rows="3"
                                                    name="sales_description"></textarea>
                                            </div>
                                            <div class="form-row-custom">
                                                <label>SALES PRICE</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">₱</span>
                                                    <input type="number" class="form-control" name="price"
                                                        placeholder="0.00" step="0.01">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Inventory Logic Tab -->
                            <div class="tab-pane fade" id="tab-inventory" role="tabpanel">
                                <div class="section-divider mt-0">INVENTORY ACCOUNTING</div>
                                <div class="form-row-custom">
                                    <label>ASSET ACCOUNT</label>
                                    <select class="form-select form-select-sm" name="asset_account">
                                        <option>Inventory Asset</option>
                                    </select>
                                </div>

                                <div class="section-divider">LEVELS & FORECASTING</div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="small fw-bold mb-1">INITIAL STOCK</label>
                                            <input type="number" class="form-control form-control-sm"
                                                name="stock" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="small fw-bold mb-1">REORDER POINT</label>
                                            <input type="number" class="form-control form-control-sm"
                                                name="reorder_point" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="small fw-bold mb-1">MAXIMUM LEVEL</label>
                                            <input type="number" class="form-control form-control-sm"
                                                name="max_stock" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Metadata Tab -->
                            <div class="tab-pane fade" id="tab-metadata" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>AUTHOR</label>
                                            <input type="text" class="form-control form-control-sm" name="author">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>COPYRIGHT</label>
                                            <input type="text" class="form-control form-control-sm" name="copyright">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>PUBLISHER</label>
                                            <input type="text" class="form-control form-control-sm" name="publisher">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>TYPE</label>
                                            <input type="text" class="form-control form-control-sm" name="book_type">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>SIZE (LXW) INCH</label>
                                            <input type="text" class="form-control form-control-sm" name="size" placeholder="e.g. 5.51x8.42">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>WEIGHT</label>
                                            <input type="text" class="form-control form-control-sm" name="weight">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>COVER TYPE</label>
                                            <input type="text" class="form-control form-control-sm" name="cover_type">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>NO. OF PAGES</label>
                                            <input type="number" class="form-control form-control-sm" name="pages">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-row-custom">
                                            <label>ROYALTY</label>
                                            <input type="text" class="form-control form-control-sm" name="royalty">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>ARTICLE</label>
                                            <input type="text" class="form-control form-control-sm" name="article">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>SUB-CATEGORY</label>
                                            <input type="text" class="form-control form-control-sm" name="sub_category">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>EMAIL</label>
                                            <input type="email" class="form-control form-control-sm" name="email">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>CONTACT NUMBER</label>
                                            <input type="text" class="form-control form-control-sm" name="contact_number">
                                        </div>
                                        <div class="form-row-custom">
                                            <label>ISBN</label>
                                            <input type="text" class="form-control form-control-sm" name="isbn">
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="tab-pane fade" id="tab-image" role="tabpanel">
                                <div class="section-divider mt-0">BOOK COVER VISUAL</div>
                                <div class="row align-items-center">
                                    <div class="col-md-12 text-center">
                                        <div class="image-upload-wrapper" onclick="document.getElementById('imageInput').click()">
                                            <div class="image-preview-premium">
                                                <img id="imagePreview" src="" alt="Preview" style="display: none;">
                                                <div id="noImageText" class="text-center text-muted">
                                                    <i class="fas fa-book-open d-block mb-2" style="font-size: 3rem; opacity: 0.3;"></i>
                                                    <span class="small">No Cover Uploaded</span>
                                                </div>
                                                <div class="upload-overlay">
                                                    <i class="las la-cloud-upload-alt"></i>
                                                    <span>Click to Upload</span>
                                                </div>
                                            </div>
                                            <input type="file" name="image_file" id="imageInput" accept="image/*" style="display:none;">
                                        </div>
                                        <p class="mt-3 small text-muted">A high-quality image makes your product look better in the POS.<br>Recommended: Square JPEG/PNG (Max 2MB)</p>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

            </form>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script>
        const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
        const form = document.getElementById('addProductForm');
        const modalTitle = document.getElementById('addProductModalTitle');
        const productIdInput = document.getElementById('modal_product_id');
        const imagePreview = document.getElementById('imagePreview');

        // Opening modal for NEW product
        document.querySelector('[data-bs-target="#addProductModal"]').addEventListener('click', function() {
            form.reset();
            productIdInput.value = '';
            modalTitle.innerText = 'Book Information - [New]';
            imagePreview.src = "";
            imagePreview.style.display = "none";
            document.getElementById('noImageText').style.display = "block";
        });

        // Opening modal for EDIT product
        document.querySelectorAll('.edit-product-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                fetch(`/marketing/products/${id}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        productIdInput.value = data.id;
                        modalTitle.innerText = `Book Information - [Edit: ${data.name}]`;
                        
                        // Fill form fields
                        form.querySelector('[name="name"]').value = data.name;
                        form.querySelector('[name="sku"]').value = data.sku;
                        form.querySelector('[name="mfr_part_no"]').value = data.mfr_part_no || '';
                        form.querySelector('[name="category"]').value = data.category || '';
                        form.querySelector('[name="barcode"]').value = data.barcode || '';
                        form.querySelector('[name="unit"]').value = data.unit || 'pcs';
                        form.querySelector('[name="purchase_description"]').value = data.purchase_description || '';
                        form.querySelector('[name="cost"]').value = data.cost;
                        form.querySelector('[name="cogs_account"]').value = data.cogs_account || '';
                        form.querySelector('[name="sales_description"]').value = data.sales_description || '';
                        form.querySelector('[name="price"]').value = data.price;
                        form.querySelector('[name="asset_account"]').value = data.asset_account || '';
                        form.querySelector('[name="stock"]').value = data.stock;
                        form.querySelector('[name="reorder_point"]').value = data.reorder_point;
                        form.querySelector('[name="max_stock"]').value = data.max_stock;
                        form.querySelector('[name="author"]').value = data.author || '';
                        form.querySelector('[name="publisher"]').value = data.publisher || '';
                        form.querySelector('[name="size"]').value = data.size || '';
                        form.querySelector('[name="pages"]').value = data.pages || '';
                        form.querySelector('[name="copyright"]').value = data.copyright || '';
                        form.querySelector('[name="book_type"]').value = data.book_type || '';
                        form.querySelector('[name="weight"]').value = data.weight || '';
                        form.querySelector('[name="cover_type"]').value = data.cover_type || '';
                        form.querySelector('[name="royalty"]').value = data.royalty || '';
                        form.querySelector('[name="article"]').value = data.article || '';
                        form.querySelector('[name="sub_category"]').value = data.sub_category || '';
                        form.querySelector('[name="email"]').value = data.email || '';
                        form.querySelector('[name="contact_number"]').value = data.contact_number || '';
                        form.querySelector('[name="isbn"]').value = data.isbn || '';
                        form.querySelector('[name="is_active"]').checked = !!data.is_active;

                        if (data.image) {
                            imagePreview.src = `/storage/${data.image}`;
                            imagePreview.style.display = "block";
                            document.getElementById('noImageText').style.display = "none";
                        } else {
                            imagePreview.src = "";
                            imagePreview.style.display = "none";
                            document.getElementById('noImageText').style.display = "block";
                        }

                        modal.show();
                    });
            });
        });

        // Image Preview logic
        document.getElementById('imageInput').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = "block";
                    document.getElementById('noImageText').style.display = "none";
                }
                reader.readAsDataURL(file);
            }
        });

        // Handle Submit (Create or Update)
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = productIdInput.value;
            const url = id ? `/marketing/products/${id}/update` : "{{ route('marketing.products.store') }}";
            const formData = new FormData(this);
            
            fetch(url, {
                method: 'POST', 
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                window.showAlert(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            })
            .catch(error => {
                console.error('Error:', error);
                window.showAlert('An error occurred. Please check console.', 'error');
            });
        });

        // Handle Delete
        document.querySelectorAll('.delete-product-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                window.showConfirm('Are you sure you want to delete this product?', function() {
                    fetch(`/marketing/products/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        window.showAlert(data.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        window.showAlert('An error occurred. Please check console.', 'error');
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
