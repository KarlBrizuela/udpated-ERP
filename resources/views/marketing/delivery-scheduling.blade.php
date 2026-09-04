<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Overview</h4>
                </div>
                <div class="card-body">
                    <p class="text-black mb-3">Schedule and manage deliveries for packed orders. Assign drivers and vehicles to delivery routes.</p>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="text-white mb-2">Pending Scheduling</h6>
                                    <h3 class="text-white mb-0">15</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="text-white mb-2">Scheduled Today</h6>
                                    <h3 class="text-white mb-0">28</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="text-white mb-2">In Transit</h6>
                                    <h3 class="text-white mb-0">12</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="text-white mb-2">Completed Today</h6>
                                    <h3 class="text-white mb-0">35</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Delivery Schedule</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover fs-14">
                            <thead>
                                <tr>
                                    <th>Sales Order</th>
                                    <th>Customer</th>
                                    <th>Delivery Receipt</th>
                                    <th>Driver</th>
                                    <th>Vehicle</th>
                                    <th>Scheduled Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SO-2026-001</td>
                                    <td>National Book Store</td>
                                    <td>DR-2026-001</td>
                                    <td>John Doe</td>
                                    <td>Truck-001</td>
                                    <td>2026-01-22</td>
                                    <td><span class="badge badge-primary">Scheduled</span></td>
                                    <td>
                                        <a href="{{ route('marketing.delivery-tracking') }}" class="btn btn-sm btn-primary">Track</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SO-2026-002</td>
                                    <td>Pandayan Bookshop</td>
                                    <td>DR-2026-002</td>
                                    <td>Jane Smith</td>
                                    <td>Van-002</td>
                                    <td>2026-01-22</td>
                                    <td><span class="badge badge-info">In Transit</span></td>
                                    <td>
                                        <a href="{{ route('marketing.delivery-tracking') }}" class="btn btn-sm btn-info">View Status</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SO-2026-003</td>
                                    <td>PCBS</td>
                                    <td>DR-2026-003</td>
                                    <td>Mike Johnson</td>
                                    <td>Truck-003</td>
                                    <td>2026-01-21</td>
                                    <td><span class="badge badge-success">Delivered</span></td>
                                    <td>
                                        <a href="{{ route('marketing.delivery-receipt.list') }}" class="btn btn-sm btn-success">View DR</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
