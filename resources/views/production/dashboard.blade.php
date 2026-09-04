<x-app-layout :title="$title ?? 'Production Division Dashboard'" :role="$role ?? 'User Role'" :sidebar="'production'">
    <!-- KPI Cards -->
    <div class="row">
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-primary card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700">{{ $stats['active_job_requests'] ?? 0 }}</h2>
                            <span class="fs-14">Active Job Requests</span>
                        </div>
                        <i class="las la-truck" style="font-size: 2rem; color: #ff0000;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-success card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700">{{ $stats['pending_purchase_orders'] ?? 0 }}</h2>
                            <span class="fs-14">Pending Purchase Orders</span>
                        </div>
                        <i class="las la-shopping-bag" style="font-size: 2rem; color: #68CF29;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-warning card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700">{{ $stats['active_printing_jobs'] ?? 0 }}</h2>
                            <span class="fs-14">Active Printing Jobs</span>
                        </div>
                        <i class="las la-print" style="font-size: 2rem; color: #FFAC30;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-info card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700">{{ $stats['pending_payment_requests'] ?? 0 }}</h2>
                            <span class="fs-14">Pending Payment Requests</span>
                        </div>
                        <i class="las la-money-bill-wave" style="font-size: 2rem; color: #51A6F5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Quick Access Modules -->
        <div class="col-xl-8 col-xxl-8">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Quick Access Modules</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- DTO Section -->
                        <div class="col-xl-6 col-lg-6 col-sm-6 mb-4">
                            <div class="card border" style="border-color: #e0e0e0 !important;">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="las la-truck" style="font-size: 3rem; color: #ff0000;"></i>
                                    </div>
                                    <h5 class="card-title text-black mb-3">DTO</h5>
                                    <p class="text-muted mb-3">Delivery & Transport Operations</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('production.dto.job-request-form') }}" class="btn btn-primary btn-sm">Job Request Form</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FORD Section -->
                        <div class="col-xl-6 col-lg-6 col-sm-6 mb-4">
                            <div class="card border" style="border-color: #e0e0e0 !important;">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="las la-warehouse" style="font-size: 3rem; color: #68CF29;"></i>
                                    </div>
                                    <h5 class="card-title text-black mb-3">FORD</h5>
                                    <p class="text-muted mb-3">Financial Operations & Resource Department</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('production.ford.payment-request') }}" class="btn btn-success btn-sm">Payment Request</a>
                                        <a href="{{ route('production.ford.purchase-order') }}" class="btn btn-outline-success btn-sm">Purchase Order</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Printing Services Section -->
                        <div class="col-xl-6 col-lg-6 col-sm-6 mb-4">
                            <div class="card border" style="border-color: #e0e0e0 !important;">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="las la-print" style="font-size: 3rem; color: #FFAC30;"></i>
                                    </div>
                                    <h5 class="card-title text-black mb-3">Printing Services</h5>
                                    <p class="text-muted mb-3">Printing & Production Services</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('production.printing.request-payment-to-printer') }}" class="btn btn-warning btn-sm">Request Payment to Printer</a>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Management Section -->
                        <div class="col-xl-6 col-lg-6 col-sm-6 mb-4">
                            <div class="card border" style="border-color: #e0e0e0 !important;">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="las la-boxes" style="font-size: 3rem; color: #51A6F5;"></i>
                                    </div>
                                    <h5 class="card-title text-black mb-3">Inventory Management</h5>
                                    <p class="text-muted mb-3">Stock & Asset Management</p>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('production.inventory.overview') }}" class="btn btn-info btn-sm">View Inventory</a>
                                        <a href="{{ route('production.inventory.add-stock') }}" class="btn btn-outline-info btn-sm">Add New Stock</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-xl-4 col-xxl-4">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Recent Activity</h4>
                    <div class="dropdown">
                        <a href="javascript:void(0)" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="las la-ellipsis-v"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="javascript:void(0);">View All</a>
                            <a class="dropdown-item" href="javascript:void(0);">Mark as Read</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        @forelse($recentActivities as $index => $act)
                        <div class="d-flex align-items-start {{ $index < count($recentActivities) - 1 ? 'mb-4' : '' }}">
                            <div class="activity-bx me-3">
                                <i class="{{ $act['icon'] }} text-{{ $act['color'] }}"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="fs-16 mb-1 text-black fw-bold">{{ $act['title'] }}</h6>
                                <p class="mb-0 text-muted">{{ $act['desc'] }}</p>
                                <span class="fs-12 text-muted">{{ $act['time'] }}</span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted small py-3">
                            No recent activities found.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
