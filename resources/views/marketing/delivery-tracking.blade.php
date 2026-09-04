<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Overview</h4>
                </div>
                <div class="card-body">
                    <p class="text-black mb-3">Track delivery status in real-time. Monitor driver locations and delivery progress.</p>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="text-white mb-2">Active Deliveries</h6>
                                    <h3 class="text-white mb-0">12</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="text-white mb-2">In Transit</h6>
                                    <h3 class="text-white mb-0">8</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="text-white mb-2">Delivered Today</h6>
                                    <h3 class="text-white mb-0">35</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="text-white mb-2">Delayed</h6>
                                    <h3 class="text-white mb-0">2</h3>
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
                    <h4 class="fs-20 mb-0 text-black">Active Deliveries</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover fs-14">
                            <thead>
                                <tr>
                                    <th>Sales Order</th>
                                    <th>Customer</th>
                                    <th>Driver</th>
                                    <th>Vehicle</th>
                                    <th>Current Location</th>
                                    <th>Status</th>
                                    <th>ETA</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SO-2026-001</td>
                                    <td>National Book Store</td>
                                    <td>John Doe</td>
                                    <td>Truck-001</td>
                                    <td>Manila</td>
                                    <td><span class="badge badge-info">In Transit</span></td>
                                    <td>30 mins</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">View Map</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SO-2026-002</td>
                                    <td>Pandayan Bookshop</td>
                                    <td>Jane Smith</td>
                                    <td>Van-002</td>
                                    <td>Quezon City</td>
                                    <td><span class="badge badge-success">Delivered</span></td>
                                    <td>-</td>
                                    <td>
                                        <a href="{{ route('marketing.delivery-receipt.list') }}" class="btn btn-sm btn-success">View DR</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>SO-2026-003</td>
                                    <td>PCBS</td>
                                    <td>Mike Johnson</td>
                                    <td>Truck-003</td>
                                    <td>Makati</td>
                                    <td><span class="badge badge-warning">Delayed</span></td>
                                    <td>1 hour</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning">Contact Driver</button>
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
