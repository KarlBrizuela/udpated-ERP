<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Super admins see the main dashboard
        if ($user->isSuperAdmin()) {
            $sidebar = 'super-admin';
            
            // Special handling for Directors who might want the director sidebar
            if ($user->position === 'Director' || $user->division === 'All Divisions') {
                $sidebar = 'director';
            }
            
            // Fetch Dashboard Data
            $totalUsers = \App\Models\User::count();
            $activeUsers = \App\Models\User::where('status', true)->count();
            $pendingApprovals = \App\Models\User::where('status', false)->count(); 
            
            // Divisions (count distinct divisions)
            $divisionCount = \App\Models\User::distinct('division')->count('division'); 
            
            $rolesCount = \App\Models\Role::count();
            
            // Activity Logs - fetch latest
            $allActivityLogs = \App\Models\ActivityLog::with('user')->latest()->take(200)->get();
            $activityLogs = $allActivityLogs->take(8);
            $activityLogsCount = \App\Models\ActivityLog::count();
            
            // Division Summary - Get actual user counts per division
            $divisionSummary = \App\Models\User::selectRaw('division, COUNT(*) as user_count')
                ->whereNotNull('division')
                ->where('division', '!=', '')
                ->groupBy('division')
                ->get()
                ->map(function($item) use ($totalUsers) {
                    $percentage = $totalUsers > 0 ? round(($item->user_count / $totalUsers) * 100) : 0;
                    return [
                        'name' => $item->division,
                        'count' => $item->user_count,
                        'percentage' => $percentage
                    ];
                });
            
            // Module Activity - Get actual activity counts per module
            $moduleActivity = \App\Models\ActivityLog::selectRaw('module, COUNT(*) as action_count')
                ->whereNotNull('module')
                ->where('module', '!=', '')
                ->groupBy('module')
                ->orderBy('action_count', 'desc')
                ->get()
                ->map(function($item) use ($activityLogsCount) {
                    $percentage = $activityLogsCount > 0 ? round(($item->action_count / $activityLogsCount) * 100) : 0;
                    return [
                        'name' => $item->module,
                        'count' => $item->action_count,
                        'percentage' => $percentage
                    ];
                });
            
            // User Activity Trends - Get activity data for the last 7 days
            $userActivityTrends = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dayName = $date->format('D');
                
                // Count distinct active users who logged activities on this day
                $activeUsersCount = \App\Models\ActivityLog::whereDate('created_at', $date->toDateString())
                    ->distinct('user_id')
                    ->count('user_id');
                
                // Count login activities
                $loginCount = \App\Models\ActivityLog::whereDate('created_at', $date->toDateString())
                    ->where('action', 'LIKE', '%login%')
                    ->count();
                
                $userActivityTrends[] = [
                    'day' => $dayName,
                    'activeUsers' => $activeUsersCount,
                    'logins' => $loginCount
                ];
            }
            
            return view('dashboard', [
                'sidebar' => $sidebar,
                'totalUsers' => $totalUsers,
                'activeUsers' => $activeUsers,
                'pendingApprovals' => $pendingApprovals,
                'divisionCount' => $divisionCount,
                'rolesCount' => $rolesCount,
                'activityLogs' => $activityLogs,
                'allActivityLogs' => $allActivityLogs,
                'activityLogsCount' => $activityLogsCount,
                'divisionSummary' => $divisionSummary,
                'moduleActivity' => $moduleActivity,
                'userActivityTrends' => $userActivityTrends
            ]);
        }

        // Redirect users based on their specific permissions
        $firstRoute = $user->getFirstAvailableRoute();
        return redirect()->route($firstRoute);
    }

    public function marketing()
    {
        return view('marketing.dashboard');
    }

    public function adminFinance()
    {
        return view('admin-finance.dashboard');
    }

    public function production()
    {
        $activeJobRequests = \Illuminate\Support\Facades\Schema::hasTable('job_requests')
            ? \App\Models\JobRequest::where('status', '!=', 'Completed')->count()
            : 0;

        $pendingPurchaseOrders = \Illuminate\Support\Facades\Schema::hasTable('purchase_orders')
            ? \App\Models\PurchaseOrder::where('status', '!=', 'completed')->count()
            : 0;

        $activePrintingJobs = \Illuminate\Support\Facades\Schema::hasTable('production_costings')
            ? \App\Models\ProductionCosting::count()
            : 0;

        $pendingPaymentRequests = \Illuminate\Support\Facades\Schema::hasTable('payment_requests')
            ? \App\Models\PaymentRequest::where('status', 'like', 'pending%')->count()
            : 0;

        $recentActivities = \Illuminate\Support\Facades\Schema::hasTable('activity_logs')
            ? \App\Models\ActivityLog::with('user')
                ->latest()
                ->take(5)
                ->get()
                ->map(function($log) {
                    $actionLower = strtolower($log->action);
                    $icon = 'las la-bell';
                    $color = 'secondary';
                    
                    if (str_contains($actionLower, 'purchase') || str_contains($actionLower, 'po')) {
                        $icon = 'las la-shopping-cart';
                        $color = 'success';
                    } elseif (str_contains($actionLower, 'job') || str_contains($actionLower, 'reconsignment') || str_contains($actionLower, 'delivery')) {
                        $icon = 'las la-truck';
                        $color = 'primary';
                    } elseif (str_contains($actionLower, 'pick') || str_contains($actionLower, 'print')) {
                        $icon = 'las la-print';
                        $color = 'warning';
                    } elseif (str_contains($actionLower, 'payment') || str_contains($actionLower, 'debit') || str_contains($actionLower, 'invoice') || str_contains($actionLower, 'si')) {
                        $icon = 'las la-money-bill-wave';
                        $color = 'info';
                    }
                    
                    $details = '';
                    if (!empty($log->description) && !str_starts_with(trim($log->description), '{')) {
                        $details = $log->description;
                    } elseif (!empty($log->details)) {
                        $parsed = is_string($log->details) ? json_decode($log->details, true) : $log->details;
                        if (is_array($parsed)) {
                            if (isset($parsed['so_number'])) {
                                $details = "SO #" . $parsed['so_number'] . (isset($parsed['action']) ? " (" . $parsed['action'] . ")" : (isset($parsed['marked_by']) ? " by " . $parsed['marked_by'] : ""));
                            } elseif (isset($parsed['po_number'])) {
                                $details = "PO #" . $parsed['po_number'];
                            } elseif (isset($parsed['pick_list_number'])) {
                                $details = "Pick List: " . $parsed['pick_list_number'];
                            } elseif (isset($parsed['gathered_at'])) {
                                $details = "Gathered at: " . date('M d, Y H:i', strtotime($parsed['gathered_at']));
                            } elseif (isset($parsed['packed_by'])) {
                                $details = "Packed by " . $parsed['packed_by'] . (isset($parsed['boxes_count']) ? " ({$parsed['boxes_count']} box" . ($parsed['boxes_count'] > 1 ? "es" : "") . ")" : "");
                            } elseif (isset($parsed['marked_by'])) {
                                $details = "Marked by " . $parsed['marked_by'];
                            } elseif (isset($parsed['description'])) {
                                $details = $parsed['description'];
                            } else {
                                $parts = [];
                                foreach ($parsed as $k => $v) {
                                    if (is_string($v) || is_numeric($v)) {
                                        $parts[] = ucwords(str_replace('_', ' ', $k)) . ': ' . $v;
                                    }
                                }
                                $details = implode(', ', $parts);
                            }
                        } else {
                            $details = $log->details;
                        }
                    }

                    if (empty($details) || str_starts_with(trim($details), '{')) {
                        $details = $log->user->name ?? 'System Action';
                    }
                    
                    return [
                        'title' => $log->action,
                        'desc' => $details,
                        'time' => $log->created_at->diffForHumans(),
                        'icon' => $icon,
                        'color' => $color
                    ];
                })
            : collect();

        return view('production.dashboard', [
            'title' => 'Production Division Dashboard',
            'role' => auth()->user()->position,
            'sidebar' => 'production',
            'stats' => [
                'active_job_requests' => $activeJobRequests,
                'pending_purchase_orders' => $pendingPurchaseOrders,
                'active_printing_jobs' => $activePrintingJobs,
                'pending_payment_requests' => $pendingPaymentRequests,
            ],
            'recentActivities' => $recentActivities
        ]);
    }
}
