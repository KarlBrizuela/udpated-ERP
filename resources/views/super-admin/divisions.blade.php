<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="row">
        <div class="col-xl-12 col-xxl-12">
            <div class="card">
                <div class="card-header border-0 d-block d-sm-flex" style="padding-bottom: 0.5rem;">
                    <div>
                        <h2 class="mb-0 text-black" style="font-size: 2.5rem; font-weight: 700;">{{ $title }}</h2>
                    </div>
                    <div class="d-flex align-items-center mt-3 mt-sm-0">
                        <a href="javascript:void(0);"
                            class="btn btn-primary rounded d-flex align-items-center" data-bs-toggle="modal"
                            data-bs-target="#addDivisionModal"
                            style="gap: 0.5rem; padding: 0.5rem 1rem; height: 38px; min-height: 38px; line-height: 1.5; box-sizing: border-box; border: none; background: #ff0000; color: #ffffff; font-weight: 500;">
                            <i class="las la-plus"
                                style="font-size: 1rem; line-height: 1; margin: 0; padding: 0; background: transparent; border: none; box-shadow: none;"></i>
                            <span style="font-size: 0.875rem; white-space: nowrap;">Add New Division</span>
                        </a>
                    </div>
                </div>
                <div class="card-body" style="padding-top: 0.5rem;">
                    <div class="row">
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0 text-black">Admin & Finance Division</h5>
                                        <div class="dropdown">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-light"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#editDivisionModal">Edit</a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#viewDepartmentsModal">View
                                                    Departments</a>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted mb-3">Financial management, accounting, credit and
                                        collection, HR, IT</p>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="text-muted d-block">Departments</span>
                                            <strong class="text-black">5</strong>
                                        </div>
                                        <div>
                                            <span class="text-muted d-block">Users</span>
                                            <strong class="text-black">52</strong>
                                        </div>
                                        <div>
                                            <span class="text-muted d-block">Status</span>
                                            <span class="badge light badge-success">Active</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0 text-black">Marketing Division</h5>
                                        <div class="dropdown">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-light"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#editDivisionModal">Edit</a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#viewDepartmentsModal">View
                                                    Departments</a>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted mb-3">Sales operations, customer management, area
                                        sales, direct sales, e-commerce</p>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="text-muted d-block">Departments</span>
                                            <strong class="text-black">4</strong>
                                        </div>
                                        <div>
                                            <span class="text-muted d-block">Users</span>
                                            <strong class="text-black">68</strong>
                                        </div>
                                        <div>
                                            <span class="text-muted d-block">Status</span>
                                            <span class="badge light badge-success">Active</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0 text-black">Production Division</h5>
                                        <div class="dropdown">
                                            <a href="javascript:void(0);" class="btn btn-sm btn-light"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#editDivisionModal">Edit</a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#viewDepartmentsModal">View
                                                    Departments</a>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-muted mb-3">Inventory management, order fulfillment,
                                        delivery, printing services</p>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="text-muted d-block">Departments</span>
                                            <strong class="text-black">3</strong>
                                        </div>
                                        <div>
                                            <span class="text-muted d-block">Users</span>
                                            <strong class="text-black">36</strong>
                                        </div>
                                        <div>
                                            <span class="text-muted d-block">Status</span>
                                            <span class="badge light badge-success">Active</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
    <!-- Add Division Modal -->
    <div class="modal fade" id="addDivisionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Division</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group mb-3">
                            <label class="form-label">Division Name</label>
                            <input type="text" class="form-control" placeholder="e.g. Operations Division">
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3"
                                placeholder="Division description and responsibilities"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Create Division</button>
                </div>
            </div>
        </div>
    </div>
    @endpush
</x-app-layout>
