<x-app-layout :title="'Driver Dashboard'" :sidebar="'production'">
    <div class="container-fluid">
        <!-- Driver Dashboard Overview -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-20 mb-0 text-black">Driver Dashboard Overview</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-black mb-3">View your assigned deliveries, track your progress, and update delivery status.</p>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6 class="text-white mb-2">Assigned Today</h6>
                                        <h3 class="text-white mb-0">5</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6 class="text-white mb-2">In Progress</h6>
                                        <h3 class="text-white mb-0">2</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6 class="text-white mb-2">Completed Today</h6>
                                        <h3 class="text-white mb-0">3</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h6 class="text-white mb-2">Pending</h6>
                                        <h3 class="text-white mb-0">1</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Deliveries -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-20 mb-0 text-black">My Deliveries</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover fs-14">
                                <thead>
                                    <tr>
                                        <th>Sales Order</th>
                                        <th>Customer</th>
                                        <th>Delivery Address</th>
                                        <th>Scheduled Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>SO-2026-001</td>
                                        <td>National Book Store</td>
                                        <td>123 Main St, Manila</td>
                                        <td>2026-01-22 09:00 AM</td>
                                        <td><span class="badge badge-primary">Assigned</span></td>
                                        <td>
                                            <a href="{{ route('production.dto.delivery-tracking') }}" class="btn btn-sm btn-primary">Start Delivery</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>SO-2026-002</td>
                                        <td>Pandayan Bookshop</td>
                                        <td>456 Quezon Ave, Quezon City</td>
                                        <td>2026-01-22 11:00 AM</td>
                                        <td><span class="badge badge-info">In Transit</span></td>
                                        <td>
                                            <a href="{{ route('production.dto.delivery-tracking') }}" class="btn btn-sm btn-info">Update Status</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>SO-2026-003</td>
                                        <td>PCBS</td>
                                        <td>789 Makati Ave, Makati</td>
                                        <td>2026-01-22 02:00 PM</td>
                                        <td><span class="badge badge-success">Delivered</span></td>
                                        <td>
                                            <a href="{{ route('production.logistic.delivery-receipt-list') }}" class="btn btn-sm btn-success">View Details</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link href="{{ asset('vendor/chartist/css/chartist.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    @endpush
</x-app-layout>
