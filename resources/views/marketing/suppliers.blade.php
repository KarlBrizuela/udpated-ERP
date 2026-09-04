<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .dataTables_wrapper { font-size: 14px; }
        #supplierTable thead th { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; padding: 12px 15px; font-weight: 600; }
        #supplierTable tbody td { padding: 12px 15px; border-bottom: 1px solid #dee2e6; vertical-align: middle; }
    </style>
    @endpush

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex">
                    <div>
                        <h4 class="fs-20 mb-0 text-black">Suppliers</h4>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-sm-0">
                        <button type="button" class="btn btn-primary rounded d-flex align-items-center" style="background: #ff0000; border: none;" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                            <i class="las la-plus me-2"></i>
                            <span>Add New Supplier</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="supplierTable" class="display" style="width: 100%">
                            <thead>
                                <tr>
                                    <th>Supplier ID</th>
                                    <th>Company Name</th>
                                    <th>Contact Person</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suppliers as $supplier)
                                <tr>
                                    <td><strong>#{{ $supplier->supplier_code }}</strong></td>
                                    <td>{{ $supplier->company_name }}</td>
                                    <td>{{ $supplier->contact_person }}</td>
                                    <td>{{ $supplier->email }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>
                                        <span class="badge light badge-{{ $supplier->status == 'active' ? 'success' : 'danger' }}">
                                            {{ ucfirst($supplier->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="javascript:void(0);" class="btn btn-primary shadow btn-xs sharp me-1 view-supplier" data-id="{{ $supplier->id }}"><i class="fas fa-eye"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-warning shadow btn-xs sharp me-1 edit-supplier" data-id="{{ $supplier->id }}"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="javascript:void(0);" class="btn btn-danger shadow btn-xs sharp delete-supplier" data-id="{{ $supplier->id }}"><i class="fa fa-trash"></i></a>
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
    <!-- Add/Edit Supplier Modal -->
    <div class="modal fade" id="addSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add New Supplier</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="supplierForm">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="id" id="supplierId">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-black font-w600">Supplier Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="supplier_code" id="supplier_code" placeholder="e.g. SUP-001" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-black font-w600">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="company_name" id="company_name" placeholder="" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-black font-w600">Contact Person</label>
                                <input type="text" class="form-control" name="contact_person" id="contact_person" placeholder="">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-black font-w600">Email Address</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-black font-w600">Phone Number</label>
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-black font-w600">Status</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" style="background: #ff0000; border: none;">Save Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Supplier Modal -->
    <div class="modal fade" id="viewSupplierModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View Supplier Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row" id="viewSupplierDetails">
                        <div class="col-md-6 mb-3">
                            <label class="text-black font-w600">Supplier ID:</label>
                            <p class="mb-0" id="v-supplier_code"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-black font-w600">Company Name:</label>
                            <p class="mb-0" id="v-company_name"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-black font-w600">Contact Person:</label>
                            <p class="mb-0" id="v-contact_person"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-black font-w600">Email Address:</label>
                            <p class="mb-0" id="v-email"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-black font-w600">Phone Number:</label>
                            <p class="mb-0" id="v-phone"></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-black font-w600">Status:</label>
                            <p class="mb-0" id="v-status"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endpush

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            const table = $('#supplierTable').DataTable({
                pageLength: 25,
                responsive: true
            });

            // Handle Add Button
            $('[data-bs-target="#addSupplierModal"]').on('click', function() {
                $('#modalTitle').text('Add New Supplier');
                $('#supplierForm')[0].reset();
                $('#formMethod').val('POST');
                $('#supplierId').val('');
            });

            // Handle Supplier Form Submit
            $('#supplierForm').on('submit', function(e) {
                e.preventDefault();
                const id = $('#supplierId').val();
                const url = id ? `/suppliers/${id}` : '/suppliers';
                const method = $('#formMethod').val();

                $.ajax({
                    url: url,
                    type: 'POST', // Always POST for Laravel with _method
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#addSupplierModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';
                        for (let field in errors) {
                            errorMsg += errors[field][0] + '\n';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg || 'Something went wrong!'
                        });
                    }
                });
            });

            // Handle View Button
            $(document).on('click', '.view-supplier', function() {
                const id = $(this).data('id');
                $.get(`/suppliers/${id}`, function(data) {
                    $('#v-supplier_code').text('#' + data.supplier_code);
                    $('#v-company_name').text(data.company_name);
                    $('#v-contact_person').text(data.contact_person || 'N/A');
                    $('#v-email').text(data.email || 'N/A');
                    $('#v-phone').text(data.phone || 'N/A');
                    $('#v-status').html(`<span class="badge light badge-${data.status === 'active' ? 'success' : 'danger'}">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span>`);
                    $('#viewSupplierModal').modal('show');
                });
            });

            // Handle Edit Button
            $(document).on('click', '.edit-supplier', function() {
                const id = $(this).data('id');
                $.get(`/suppliers/${id}/edit`, function(data) {
                    $('#modalTitle').text('Edit Supplier');
                    $('#supplierId').val(data.id);
                    $('#supplier_code').val(data.supplier_code);
                    $('#company_name').val(data.company_name);
                    $('#contact_person').val(data.contact_person);
                    $('#email').val(data.email);
                    $('#phone').val(data.phone);
                    $('#status').val(data.status);
                    $('#formMethod').val('PUT');
                    $('#addSupplierModal').modal('show');
                });
            });

            // Handle Delete Button
            $(document).on('click', '.delete-supplier', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ff0000',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/suppliers/${id}`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
