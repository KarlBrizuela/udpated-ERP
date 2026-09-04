<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Overview</h4>
                </div>
                <div class="card-body">
                    <p class="text-black mb-3">Manage packing operations and schedule deliveries for completed pick lists.</p>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="text-white mb-2">Awaiting Packing</h6>
                                    <h3 class="text-white mb-0">18</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="text-white mb-2">Packing In Progress</h6>
                                    <h3 class="text-white mb-0">12</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="text-white mb-2">Packed & Ready</h6>
                                    <h3 class="text-white mb-0">35</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="text-white mb-2">Scheduled</h6>
                                    <h3 class="text-white mb-0">28</h3>
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
                    <h4 class="fs-20 mb-0 text-black">Packing Schedule</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover fs-14">
                            <thead>
                                <tr>
                                    <th>Sales Order</th>
                                    <th>Customer</th>
                                    <th>Pick List</th>
                                    <th>Packing Status</th>
                                    <th>Scheduled Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SO-2026-001</td>
                                    <td>National Book Store</td>
                                    <td>PL-2026-001</td>
                                    <td><span class="badge badge-primary">Awaiting Packing</span></td>
                                    <td>-</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">Start Packing</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SO-2026-002</td>
                                    <td>Pandayan Bookshop</td>
                                    <td>PL-2026-002</td>
                                    <td><span class="badge badge-info">Packing In Progress</span></td>
                                    <td>2026-01-22</td>
                                    <td>
                                        <a href="{{ route('marketing.delivery-receipt') }}" class="btn btn-sm btn-info">Create DR</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SO-2026-003</td>
                                    <td>PCBS</td>
                                    <td>PL-2026-003</td>
                                    <td><span class="badge badge-success">Packed & Ready</span></td>
                                    <td>2026-01-23</td>
                                    <td>
                                        <a href="{{ route('marketing.delivery-scheduling') }}" class="btn btn-sm btn-success">Schedule Delivery</a>
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
