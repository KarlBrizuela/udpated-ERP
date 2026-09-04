<x-app-layout :title="'Order Fulfillment'" :sidebar="'production'">
    <div class="container-fluid">
        <!-- Order Fulfillment Overview -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-20 mb-0 text-black">Overview</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-black mb-3">Monitor and manage order fulfillment status from pick list completion to delivery scheduling.</p>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6 class="text-white mb-2">Pending Fulfillment</h6>
                                        <h3 class="text-white mb-0">24</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6 class="text-white mb-2">In Progress</h6>
                                        <h3 class="text-white mb-0">18</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6 class="text-white mb-2">Packed</h6>
                                        <h3 class="text-white mb-0">42</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h6 class="text-white mb-2">Ready for Delivery</h6>
                                        <h3 class="text-white mb-0">15</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Fulfillment Details -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="fs-20 mb-0 text-black">Order Fulfillment Status</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover fs-14">
                                <thead>
                                    <tr>
                                        <th>Sales Order</th>
                                        <th>Customer</th>
                                        <th>Pick List</th>
                                        <th>Status</th>
                                        <th>Packed Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>SO-2026-001</td>
                                        <td>National Book Store</td>
                                        <td>PL-2026-001</td>
                                        <td><span class="badge badge-primary">Pending</span></td>
                                        <td>-</td>
                                        <td>
                                            <a href="{{ route('production.logistic.pick-list-management') }}" class="btn btn-sm btn-primary">View Pick List</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>SO-2026-002</td>
                                        <td>Pandayan Bookshop</td>
                                        <td>PL-2026-002</td>
                                        <td><span class="badge badge-info">In Progress</span></td>
                                        <td>-</td>
                                        <td>
                                            <a href="{{ route('production.logistic.packing-scheduling') }}" class="btn btn-sm btn-info">Schedule Packing</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>SO-2026-003</td>
                                        <td>PCBS</td>
                                        <td>PL-2026-003</td>
                                        <td><span class="badge badge-success">Packed</span></td>
                                        <td>2026-01-20</td>
                                        <td>
                                            <!-- Note: Delivery Scheduling is commonly in DTO, assuming route exists or using placeholder -->
                                            <a href="#" class="btn btn-sm btn-success">Schedule Delivery</a>
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
