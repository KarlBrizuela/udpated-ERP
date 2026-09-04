<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .product-image-preview { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; }
        .category-badge { padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500; display: inline-block; }
        .category-books { background-color: #e3f2fd; color: #1976d2; }
        .category-non-books { background-color: #fff3e0; color: #f57c00; }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-20 mb-0 text-black">POS Products</h4>
                        <p class="mb-0 fs-12">Manage book prices and visibility on the POS system</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="posProductsTable" class="display" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Internal SKU</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $book)
                                <tr>
                                    <td>
                                        <img src="{{ $book->image ? '/storage/' . $book->image : asset('images/no-book-cover.svg') }}" class="product-image-preview">
                                    </td>
                                    <td>
                                        <strong>{{ $book->name }}</strong>
                                    </td>
                                    <td>
                                        @if($book->is_book)
                                            <span class="category-badge category-books">{{ $book->category ?? 'Books' }}</span>
                                        @else
                                            <span class="category-badge category-non-books">Non-Books</span>
                                        @endif
                                    </td>
                                    <td>₱{{ number_format($book->price, 2) }}</td>
                                    <td>#{{ $book->sku }}</td>
                                    <td>
                                        @if($book->is_active)
                                            <span class="badge light badge-success">Active</span>
                                        @else
                                            <span class="badge light badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1 edit-product-btn" 
                                               data-id="{{ $book->id }}"><i class="fas fa-pencil-alt"></i></a>
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

    @push('modals')
    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="editProductForm" class="modal-content">
                @csrf
                <input type="hidden" id="edit_product_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit POS Pricing & Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">POS Display Name</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">POS Category</label>
                            <select class="form-control" name="category" id="edit_category">
                                <option value="Books">Books</option>
                                <option value="Bibles">Bibles</option>
                                <option value="Prayer Books">Prayer Books</option>
                                <option value="Spiritual Reading">Spiritual Reading</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Selling Price</label>
                            <input type="number" class="form-control" name="price" id="edit_price" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check custom-checkbox check-xs mt-4">
                                <input type="checkbox" class="form-check-input" name="is_active" id="edit_is_active" value="1">
                                <label class="form-check-label" for="edit_is_active">Active on POS</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Book</button>
                </div>
            </form>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#posProductsTable').DataTable({
                pageLength: 25,
                responsive: true
            });

            const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));

            $('.edit-product-btn').on('click', function() {
                const id = $(this).data('id');
                $.get(`/marketing/book-list/${id}/edit-book`, function(data) {
                    $('#edit_product_id').val(data.id);
                    $('#edit_name').val(data.name);
                    
                    const categorySelect = $('#edit_category');
                    categorySelect.empty();
                    
                    if (!data.is_book) {
                        categorySelect.append('<option value="Non-Books">Non-Books</option>');
                        categorySelect.val('Non-Books');
                        categorySelect.prop('disabled', true);
                        if ($('#hidden_category').length === 0) {
                            $('#editProductForm').append('<input type="hidden" name="category" id="hidden_category" value="Non-Books">');
                        } else {
                            $('#hidden_category').val('Non-Books');
                        }
                    } else {
                        categorySelect.prop('disabled', false);
                        $('#hidden_category').remove();
                        categorySelect.append(`
                            <option value="Books">Books</option>
                            <option value="Bibles">Bibles</option>
                            <option value="Prayer Books">Prayer Books</option>
                            <option value="Spiritual Reading">Spiritual Reading</option>
                        `);
                        categorySelect.val(data.category || 'Books');
                    }
                    
                    $('#edit_price').val(data.price);
                    $('#edit_is_active').prop('checked', data.is_active ? true : false);
                    
                    // Add hidden SKU to maintain validation
                    let skuInput = $('#editProductForm').find('input[name="sku"]');
                    if(skuInput.length === 0) {
                        $('#editProductForm').append(`<input type="hidden" name="sku" value="${data.sku}">`);
                    } else {
                        skuInput.val(data.sku);
                    }
                    
                    editModal.show();
                });
            });

            $('#editProductForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit_product_id').val();
                $.ajax({
                    url: `/marketing/book-list/${id}/update-book`,
                    method: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        window.showAlert(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON ? (xhr.responseJSON.error || xhr.responseJSON.message) : 'Error occurred';
                        window.showAlert(msg, 'danger');
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
