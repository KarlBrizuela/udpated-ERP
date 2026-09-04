<x-app-layout :title="'Super Admin Dashboard'" :role="'Super Admin'" :sidebar="'super-admin'">
    <!-- Add Project -->
    <div class="row">
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-primary card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700">{{ $totalUsers ?? 0 }}</h2>
                            <span class="fs-14">Total Users</span>
                        </div>
                        <i class="las la-users" style="font-size: 2rem; color: #ff0000;"></i>
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
                            <h2 class="num-text text-black font-w700">{{ $activeUsers ?? 0 }}</h2>
                            <span class="fs-14">Active Users</span>
                        </div>
                        <i class="las la-user-check" style="font-size: 2rem; color: #68CF29;"></i>
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
                            <h2 class="num-text text-black font-w700">{{ $pendingApprovals ?? 0 }}</h2>
                            <span class="fs-14">Pending Approvals</span>
                        </div>
                        <i class="las la-clipboard-list" style="font-size: 2rem; color: #FFAC30;"></i>
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
                            <h2 class="num-text text-black font-w700">{{ $divisionCount ?? 3 }}</h2>
                            <span class="fs-14">Divisions</span>
                        </div>
                        <i class="las la-building" style="font-size: 2rem; color: #51A6F5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-danger card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700">{{ $rolesCount ?? 0 }}</h2>
                            <span class="fs-14">Roles</span>
                        </div>
                        <i class="las la-user-shield" style="font-size: 2rem; color: #FF3D57;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-secondary card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700">{{ $activityLogsCount ?? 0 }}</h2>
                            <span class="fs-14">Activity Logs</span>
                        </div>
                        <i class="las la-history" style="font-size: 2rem; color: #6c757d;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-dark card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700">99.8%</h2>
                            <span class="fs-14">System Uptime</span>
                        </div>
                        <i class="las la-server" style="font-size: 2rem; color: #212529;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card card-bd">
                <div class="bg-primary card-border"></div>
                <div class="card-body box-style">
                    <div class="media align-items-center">
                        <div class="media-body me-3">
                            <h2 class="num-text text-black font-w700">89</h2>
                            <span class="fs-14">Active Sessions</span>
                        </div>
                        <i class="las la-sign-in-alt" style="font-size: 2rem; color: #ff0000;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-8 col-xxl-8">
            <div class="card" style="min-height: 500px; display: flex; flex-direction: column;">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">User Activity Trends</h4>
                </div>
                <div class="card-body" style="flex: 1; min-height: 450px; display: flex; flex-direction: column; padding: 1.5rem;">
                    <div id="userActivityChart" style="flex: 1; min-height: 400px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-xxl-4">
            <div class="card" style="min-height: 500px; display: flex; flex-direction: column;">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Division Summary</h4>
                </div>
                <div class="card-body" style="flex: 1;">
                    @php
                        $colors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-info', 'bg-danger', 'bg-secondary'];
                        $colorIndex = 0;
                    @endphp
                    @forelse($divisionSummary ?? [] as $division)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-black font-w600">{{ $division['name'] }}</span>
                            <span class="text-black font-w600">{{ $division['count'] }} users</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $colors[$colorIndex++ % count($colors)] }}" role="progressbar" style="width: {{ $division['percentage'] }}%" aria-valuenow="{{ $division['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">No division data available</div>
                    @endforelse
                    @if(isset($divisionSummary) && $divisionSummary->count() > 0)
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between">
                            <span class="text-black font-w600 font-w700">Total Users</span>
                            <span class="text-black font-w600 font-w700">{{ $totalUsers ?? 0 }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-8 col-xxl-8">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h4 class="fs-20 mb-0 text-black">Recent Activity Logs</h4>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#activityLogsModal">View All</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th><strong>Timestamp</strong></th>
                                    <th><strong>User</strong></th>
                                    <th><strong>Action</strong></th>
                                    <th><strong>Module</strong></th>
                                    <th><strong>Status</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activityLogs ?? [] as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $log->user->name ?? 'System' }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->module ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($log->status) {
                                                'success' => 'badge-success',
                                                'warning' => 'badge-warning',
                                                'critical' => 'badge-danger',
                                                'info' => 'badge-info',
                                                default => 'badge-secondary'
                                            };
                                        @endphp
                                        <span class="badge light {{ $badgeClass }}">{{ ucfirst($log->status) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No activity logs found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-xxl-4">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <h4 class="fs-20 mb-0 text-black">Module Activity</h4>
                </div>
                <div class="card-body">
                    @php
                        $moduleColors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-info', 'bg-danger', 'bg-secondary'];
                        $moduleColorIndex = 0;
                        $totalActions = $moduleActivity->sum('count') ?? 0;
                    @endphp
                    @forelse($moduleActivity ?? [] as $module)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-black font-w600">{{ $module['name'] }}</span>
                            <span class="text-black font-w600">{{ $module['count'] }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar {{ $moduleColors[$moduleColorIndex++ % count($moduleColors)] }}" role="progressbar" style="width: {{ $module['percentage'] }}%" aria-valuenow="{{ $module['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">No module activity data available</div>
                    @endforelse
                    @if(isset($moduleActivity) && $moduleActivity->count() > 0)
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between">
                            <span class="text-black font-w600 font-w700">Total Actions</span>
                            <span class="text-black font-w600 font-w700">{{ $totalActions }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (window.ApexCharts) {
                    const chartElement = document.getElementById('userActivityChart');
                    if (chartElement) {
                        const containerHeight = chartElement.offsetHeight || 400;
                        
                        @php
                            $chartData = isset($userActivityTrends) ? $userActivityTrends : [];
                            $categories = array_column($chartData, 'day');
                            $activeUsersData = array_column($chartData, 'activeUsers');
                            $loginsData = array_column($chartData, 'logins');
                        @endphp
                        
                        const chart = new ApexCharts(chartElement, {
                            series: [{
                                name: 'Active Users',
                                data: {!! json_encode($activeUsersData) !!}
                            }, {
                                name: 'New Logins',
                                data: {!! json_encode($loginsData) !!}
                            }],
                            chart: {
                                height: containerHeight,
                                type: 'area',
                                toolbar: {
                                    show: false
                                },
                                zoom: {
                                    enabled: false
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                curve: 'smooth',
                                width: 2
                            },
                            legend: {
                                show: true,
                                position: 'top',
                                horizontalAlign: 'right'
                            },
                            colors: ['#ff0000', '#51A6F5'],
                            xaxis: {
                                categories: {!! json_encode($categories) !!},
                                labels: {
                                    style: {
                                        colors: '#333',
                                        fontSize: '12px',
                                        fontWeight: 500
                                    }
                                }
                            },
                            grid: {
                                borderColor: '#e0e0e0',
                            },
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    shadeIntensity: 1,
                                    opacityFrom: 0.7,
                                    opacityTo: 0.3,
                                    stops: [0, 90, 100]
                                }
                            }
                        });
                        chart.render();
                    }
                }
            }, 1000);
        });
    </script>
    @endpush

    @push('modals')
    <!-- Activity Logs Modal -->
    <div class="modal fade" id="activityLogsModal" tabindex="-1" aria-labelledby="activityLogsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="activityLogsModalLabel">All Activity Logs (Recent 200)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-responsive-md mb-0">
                            <thead class="thead-light" style="position: sticky; top: 0; z-index: 1; background: #fff;">
                                <tr>
                                    <th><strong>Timestamp</strong></th>
                                    <th><strong>User</strong></th>
                                    <th><strong>Action</strong></th>
                                    <th><strong>Module</strong></th>
                                    <th><strong>Status</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allActivityLogs ?? [] as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $log->user->name ?? 'System' }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->module ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match($log->status) {
                                                'success' => 'badge-success',
                                                'warning' => 'badge-warning',
                                                'critical' => 'badge-danger',
                                                'info' => 'badge-info',
                                                default => 'badge-secondary'
                                            };
                                        @endphp
                                        <span class="badge light {{ $badgeClass }}">{{ ucfirst($log->status) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No activity logs found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endpush
</x-app-layout>
