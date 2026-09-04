<?php

namespace App\Http\Controllers;

use App\Models\Admin\MIS\CCTVReq;
use App\Models\Admin\MIS\MaterialReq;
use App\Models\Admin\MIS\MisQbRequest;
use App\Models\Admin\MIS\MisServiceRequest;
use App\Models\Admin\MIS\MisUndertimeRequest;
use Illuminate\Http\Request;
use App\Services\AccountingService;
use App\Models\JournalEntry;
use App\Models\EmployeeCashAdvance;
use App\Models\PettyCashVoucher;
use App\Models\PettyCashVoucherItem;
use App\Models\JournalEntryItem;
use App\Models\SalesOrder;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\JournalVoucherRequest;
use App\Models\JournalVoucherItem;
use App\Models\Customer;

class AdminFinanceController extends Controller
{
  protected $accounting;

  public function __construct(AccountingService $accounting)
  {
    $this->accounting = $accounting;
  }

  public function memoList()
  {
    $memos = collect();

    // 1. Journal Entries (including Check Vouchers)
    $journalEntries = JournalEntry::whereNotNull('memo')->with('creator')->get();
    foreach ($journalEntries as $entry) {
      $memos->push([
        'date' => $entry->date,
        'source' => $entry->entry_type ?? 'Journal Entry',
        'ref_no' => $entry->entry_no,
        'id' => $entry->id,
        'memo' => $entry->memo,
        'submitted_by' => $entry->creator->name ?? 'Unknown',
        'url' => ($entry->entry_type == 'CV') ? route('admin-finance.check-voucher.show', $entry->id) : route('accounting.journal.show', $entry->id),
      ]);
    }

    // 2. Journal Entry Items (some memos are on line items)
    $journalItems = JournalEntryItem::whereNotNull('memo')->with(['journalEntry.creator'])->get();
    foreach ($journalItems as $item) {
      if ($item->journalEntry) {
        $memos->push([
          'date' => $item->journalEntry->date,
          'source' => ($item->journalEntry->entry_type ?? 'Journal Entry') . ' (Item)',
          'ref_no' => $item->journalEntry->entry_no,
          'id' => $item->journalEntry->id,
          'memo' => $item->memo,
          'submitted_by' => $item->journalEntry->creator->name ?? 'Unknown',
          'url' => ($item->journalEntry->entry_type == 'CV') ? route('admin-finance.check-voucher.show', $item->journalEntry->id) : route('accounting.journal.show', $item->journalEntry->id),
        ]);
      }
    }

    // 3. Petty Cash Vouchers (Using items' particulars as memos)
    $pettyItems = PettyCashVoucherItem::with(['voucher.creator'])->get();
    foreach ($pettyItems as $item) {
      if ($item->voucher && $item->particulars) {
        $memos->push([
          'date' => $item->voucher->date,
          'source' => 'Petty Cash',
          'ref_no' => $item->voucher->pcv_number,
          'id' => $item->voucher->id,
          'memo' => $item->particulars,
          'submitted_by' => $item->voucher->creator->name ?? 'Unknown',
          'url' => route('admin-finance.petty-cash.show', $item->voucher->id),
        ]);
      }
    }

    // 4. Employee Cash Advances
    $cashAdvances = EmployeeCashAdvance::whereNotNull('purpose')->with('user')->get();
    foreach ($cashAdvances as $ca) {
      $memos->push([
        'date' => $ca->created_at,
        'source' => 'Cash Advance',
        'ref_no' => 'CA-' . str_pad($ca->id, 4, '0', STR_PAD_LEFT),
        'id' => $ca->id,
        'memo' => $ca->purpose,
        'submitted_by' => $ca->employee_name ?? ($ca->user->name ?? 'Unknown'),
        'url' => '#', // No specific show page found for CA, but often in approval queue
      ]);
    }

    // 5. MIS Requests
    $cctv = CCTVReq::whereNotNull('purpose')->with('user')->get();
    foreach ($cctv as $req) {
      $memos->push([
        'date' => $req->created_at,
        'source' => 'CCTV Request',
        'ref_no' => 'CCTV-' . str_pad($req->cctv_req_id, 4, '0', STR_PAD_LEFT),
        'id' => $req->cctv_req_id,
        'memo' => $req->purpose,
        'submitted_by' => $req->requested_by ?? ($req->user->name ?? 'Unknown'),
        'url' => route('admin-finance.mis.cctv-requests.show', $req->cctv_req_id),
      ]);
    }

    $materials = MaterialReq::whereNotNull('request_details')->with('user')->get();
    foreach ($materials as $req) {
      $memos->push([
        'date' => $req->created_at,
        'source' => 'Material Request',
        'ref_no' => 'MAT-' . str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT),
        'id' => $req->material_req_id,
        'memo' => $req->request_details,
        'submitted_by' => $req->requested_by ?? ($req->user->name ?? 'Unknown'),
        'url' => route('admin-finance.mis.material-requests.show', $req->material_req_id),
      ]);
    }

    $qbs = MisQbRequest::whereNotNull('customer_item_name')->with('user')->get();
    foreach ($qbs as $req) {
      $memos->push([
        'date' => $req->created_at,
        'source' => 'QB Request',
        'ref_no' => 'QB-' . str_pad($req->qb_req_id, 4, '0', STR_PAD_LEFT),
        'id' => $req->qb_req_id,
        'memo' => $req->customer_item_name,
        'submitted_by' => $req->user->name ?? 'Unknown',
        'url' => route('admin-finance.mis.qb-requests.show', $req->qb_req_id),
      ]);
    }

    $services = MisServiceRequest::whereNotNull('nature_of_request')->with('user')->get();
    foreach ($services as $req) {
      $memos->push([
        'date' => $req->created_at,
        'source' => 'Service Request',
        'ref_no' => 'SRV-' . str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT),
        'id' => $req->service_req_id,
        'memo' => $req->nature_of_request,
        'submitted_by' => $req->requestor_name ?? ($req->user->name ?? 'Unknown'),
        'url' => route('admin-finance.mis.service-requests.show', $req->service_req_id),
      ]);
    }

    $undertimes = MisUndertimeRequest::whereNotNull('reason')->with('user')->get();
    foreach ($undertimes as $req) {
      $memos->push([
        'date' => $req->created_at,
        'source' => 'Undertime Request',
        'ref_no' => 'UND-' . str_pad($req->undertime_req_id, 4, '0', STR_PAD_LEFT),
        'id' => $req->undertime_req_id,
        'memo' => $req->reason,
        'submitted_by' => $req->employee_name ?? ($req->user->name ?? 'Unknown'),
        'url' => route('admin-finance.mis.undertime-requests.show', $req->undertime_req_id),
      ]);
    }

    // 6. Sales Orders
    $salesOrders = SalesOrder::whereNotNull('remarks')->with('preparedBy')->get();
    foreach ($salesOrders as $so) {
      $memos->push([
        'date' => $so->created_at,
        'source' => 'Sales Order',
        'ref_no' => $so->so_number,
        'id' => $so->id,
        'memo' => $so->remarks,
        'submitted_by' => $so->preparedBy->name ?? 'Unknown',
        'url' => route('admin-finance.sales-order.detail', $so->id),
      ]);
    }

    // 7. Journal Voucher Requests
    $jvMemos = JournalVoucherRequest::with('requestor')->latest()->get();
    foreach ($jvMemos as $req) {
        $memos->push([
            'date' => $req->date ?? $req->created_at,
            'source' => 'JV Request',
            'ref_no' => $req->jv_number,
            'id' => $req->id,
            'memo' => $req->reason ?? 'Initial Summary Compilation Request',
            'submitted_by' => $req->requestor->name ?? 'Unknown',
            'url' => route('admin-finance.credit-collection.jv-requests.show', $req->id),
        ]);
    }

    // Sort by date descending
    $memos = $memos->sortByDesc('date');

    return view('admin-finance.memo-list', [
      'title' => 'Memo List',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'memos' => $memos
    ]);
  }
  public function dashboard(\Illuminate\Http\Request $request)
  {
    // Determine period from query (daily, weekly, monthly, yearly)
    $period = $request->query('period', 'monthly');
    switch($period) {
      case 'daily':
        $start = \Carbon\Carbon::now()->startOfDay();
        $end = \Carbon\Carbon::now()->endOfDay();
        $periodLabel = 'Today';
        break;
      case 'weekly':
        $start = \Carbon\Carbon::now()->startOfWeek();
        $end = \Carbon\Carbon::now()->endOfWeek();
        $periodLabel = 'This Week';
        break;
      case 'yearly':
        $start = \Carbon\Carbon::now()->startOfYear();
        $end = \Carbon\Carbon::now()->endOfYear();
        $periodLabel = 'This Year';
        break;
      case 'monthly':
      default:
        $start = \Carbon\Carbon::now()->startOfMonth();
        $end = \Carbon\Carbon::now()->endOfMonth();
        $periodLabel = 'This Month';
        break;
    }

    // Financial summaries within the selected period
    $totalRevenue = \App\Models\SalesOrder::where(function($q) {
        $q->where('payment_status', 'paid')
          ->orWhereIn('type', ['paid', 'calculator_pos'])
          ->orWhere('status', 'completed');
      })
      ->where('status', '!=', 'cancelled')
      ->whereBetween('created_at', [$start, $end])
      ->sum('total_amount') ?: 0.00;

    $accountsReceivable = \App\Models\SalesOrder::where('payment_status', '!=', 'paid')
      ->where(function($q) {
          $q->whereNull('proof_of_payment')->orWhere('proof_of_payment', '');
      })
      ->whereNotIn('type', ['calculator_pos', 'ecom_direct'])
      ->where('status', '!=', 'cancelled')
      ->whereBetween('created_at', [$start, $end])
      ->sum('total_amount') ?: 0.00;

    // Expenses: sum debits for journal items whose account type is 'Expense' within period
    $totalExpenses = 0.00;
    try {
      if (\Illuminate\Support\Facades\Schema::hasTable('journal_entry_items') && \Illuminate\Support\Facades\Schema::hasTable('chart_of_accounts')) {
        $totalExpenses = (float) \App\Models\JournalEntryItem::whereHas('account', function($q){
          $q->where('type', 'Expense');
        })->whereBetween('created_at', [$start, $end])->sum('debit');
      }
    } catch (\Throwable $e) {
      \Log::warning('Dashboard totalExpenses calculation error: ' . $e->getMessage());
      $totalExpenses = 0.00;
    }

    $netProfit = $totalRevenue - $totalExpenses;

    // Admin & Finance: counts grouped by position (for sidebar summary)
    $deptCounts = \App\Models\User::select('position', \DB::raw('count(*) as cnt'))
      ->where('department', 'Admin & Finance')
      ->groupBy('position')
      ->get()
      ->keyBy('position');

    // Pending approvals: reflect the Departmental queue from gatherApprovalQueueItems()
    $user = auth()->user();
    $allPending = $this->gatherApprovalQueueItems($user);
    $userDept = strtolower($user->department ?? '');

    $filtered = array_filter($allPending, function($it) use ($userDept) {
      $dept = strtolower($it['department'] ?? '');
      if (empty($userDept)) return true;
      if (empty($dept)) return false;
      if (\Illuminate\Support\Str::contains($dept, $userDept) || \Illuminate\Support\Str::contains($userDept, $dept)) return true;
      // for Admin & Finance, include related departments and Payment Requests
      if (\Illuminate\Support\Str::contains($userDept, 'admin') || \Illuminate\Support\Str::contains($userDept, 'finance')) {
        if (\Illuminate\Support\Str::contains($dept, 'admin') || \Illuminate\Support\Str::contains($dept, 'finance') || \Illuminate\Support\Str::contains($dept, 'credit') || \Illuminate\Support\Str::contains($dept, 'account') || ($it['type'] ?? '') === 'Payment Request') return true;
      }
      return false;
    });

    $pending = collect($filtered)->map(function($it){
      return [
        'type' => $it['type'] ?? '',
        'submitted_by' => $it['submitted_by'] ?? '',
        'department' => $it['department'] ?? 'N/A',
        'amount' => isset($it['amount']) ? $it['amount'] : 0,
        'date' => $it['submitted_date'] ?? ($it['date'] ?? now()->toDateString()),
        'status' => $it['status'] ?? '',
        'id' => $it['id'] ?? null
      ];
    })->sortByDesc(function($row){
      return strtotime($row['date']);
    })->values()->take(6);

    // Build 7-point chart series (last 7 units according to period)
    $chartCategories = [];
    $chartRevenue = [];
    $chartExpenses = [];
    for ($i = 6; $i >= 0; $i--) {
      switch ($period) {
        case 'daily':
          $dt = \Carbon\Carbon::now()->subDays($i);
          $label = $dt->format('M j');
          $s = $dt->copy()->startOfDay();
          $e = $dt->copy()->endOfDay();
          break;
        case 'weekly':
          $dt = \Carbon\Carbon::now()->subWeeks($i);
          $label = 'W ' . $dt->weekOfYear;
          $s = $dt->copy()->startOfWeek();
          $e = $dt->copy()->endOfWeek();
          break;
        case 'yearly':
          $dt = \Carbon\Carbon::now()->subYears($i);
          $label = $dt->format('Y');
          $s = $dt->copy()->startOfYear();
          $e = $dt->copy()->endOfYear();
          break;
        case 'monthly':
        default:
          $dt = \Carbon\Carbon::now()->subMonths($i);
          $label = $dt->format('M');
          $s = $dt->copy()->startOfMonth();
          $e = $dt->copy()->endOfMonth();
          break;
      }

      $chartCategories[] = $label;

      $rev = \App\Models\SalesOrder::where('payment_status', 'paid')
        ->whereBetween('created_at', [$s, $e])
        ->sum('total_amount');

      $exp = \App\Models\JournalEntryItem::whereHas('account', function($q){
          $q->where('type', 'Expense');
        })->whereBetween('created_at', [$s, $e])->sum('debit');

      $chartRevenue[] = (float) $rev;
      $chartExpenses[] = (float) $exp;
    }

    // Admin & Finance division headcount summary
    $totalUsers = \App\Models\User::where('division', 'Admin & Finance Division')->count();
    $positionSummary = \App\Models\User::selectRaw('position, COUNT(*) as user_count')
        ->where('division', 'Admin & Finance Division')
        ->whereNotNull('position')
        ->where('position', '!=', '')
        ->groupBy('position')
        ->get()
        ->map(function($item) use ($totalUsers) {
            $percentage = $totalUsers > 0 ? round(($item->user_count / $totalUsers) * 100) : 0;
            return [
                'name' => $item->position,
                'count' => $item->user_count,
                'percentage' => $percentage
            ];
        });

    return view('admin-finance.dashboard', [
      'title' => 'Admin & Finance Dashboard',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'totalRevenue' => $totalRevenue,
      'accountsReceivable' => $accountsReceivable,
      'totalExpenses' => $totalExpenses,
      'netProfit' => $netProfit,
      'deptCounts' => $deptCounts,
      'pendingApprovals' => $pending,
      'period' => $period,
      'periodLabel' => $periodLabel,
      'chartCategories' => $chartCategories,
      'chartRevenue' => $chartRevenue,
      'chartExpenses' => $chartExpenses,
      'positionSummary' => $positionSummary,
      'totalDivisionUsers' => $totalUsers
    ]);
  }

  /**
   * Build unified approval queue items for a user (reusable)
   */
  private function gatherApprovalQueueItems($user)
  {
    $pos = $user->position;

    // --- CCTV Requests ---
    $cctvRequests = CCTVReq::with('user')
      ->whereIn('status', ['pending approval', 'Pending HR approval', 'Pending Final Approval'])
      ->orderBy('created_at', 'desc')
      ->get()
      ->filter(function ($request) use ($user) {
        return $request->canBeApprovedBy($user);
      });

    // --- Material Requests (only non-MIS/GSD) ---
    // Exclude MIS and GSD modules entirely from the unified approval queue
    $materialQuery = MaterialReq::with('user')->whereNotIn('module', ['GSD', 'MIS']);
    if ($pos === 'Super Admin') {
      $materialQuery->whereIn('status', [
        'pending approval', 'Pending Final Approval', 'forwarded to accounting', 'received',
        'pending_supervisor_approval', 'pending_admin_approval', 'pending_director_approval'
      ]);
    } else {
      $materialQuery->whereIn('status', [
        'pending approval', 'Pending Final Approval',
        'pending_supervisor_approval', 'pending_admin_approval', 'pending_director_approval'
      ]);
    }
    $materialRequests = $materialQuery->orderBy('created_at', 'desc')->get();
    if ($pos !== 'Super Admin') {
      $materialRequests = $materialRequests->filter(function ($request) use ($user) {
        return $request->canBeApprovedBy($user);
      });
    }

    // GSD material requests intentionally excluded from approval queue.

    // --- QB Requests ---
    $qbQuery = MisQbRequest::with(['user', 'items']);
    if (in_array($pos, ['Manager', 'MIS Supervisor'])) {
      $qbQuery->where('status', 'pending');
    } elseif (in_array($pos, ['HR Manager', 'HR Specialist', 'HR Staff'])) {
      $qbQuery->where('status', 'Pending HR approval');
    } elseif ($pos === 'Director') {
      $qbQuery->where('status', 'Pending Final Approval');
    } elseif ($pos === 'Super Admin') {
      $qbQuery->whereIn('status', ['pending', 'Pending HR approval', 'Pending Final Approval']);
    } else {
      $qbQuery->whereRaw('1 = 0');
    }
    $qbRequests = $qbQuery->orderBy('created_at', 'desc')->get();

    // --- Service Requests (MIS) ---
    $serviceQuery = MisServiceRequest::query()->where('module', '!=', 'GSD');
    if (in_array($pos, ['Manager', 'MIS Supervisor'])) {
      $serviceQuery->where('status', 'pending');
    } elseif (in_array($pos, ['HR Manager', 'HR Specialist', 'HR Staff'])) {
      $serviceQuery->where('status', 'Pending HR approval');
    } elseif ($pos === 'Director') {
      $serviceQuery->where('status', 'Pending Final Approval');
    } elseif ($pos === 'Super Admin') {
      $serviceQuery->whereIn('status', ['pending', 'Pending HR approval', 'Pending Final Approval']);
    } else {
      $serviceQuery->whereRaw('1 = 0');
    }
    $serviceRequests = $serviceQuery->orderBy('created_at', 'desc')->get();

    // --- GSD Service Requests ---
    $gsdServiceQuery = MisServiceRequest::query()->where('module', 'GSD');
    if (in_array($pos, ['Manager', 'MIS Supervisor'])) {
      $gsdServiceQuery->where('status', 'pending');
    } elseif (in_array($pos, ['HR Manager', 'HR Specialist', 'HR Staff'])) {
      $gsdServiceQuery->where('status', 'Pending HR approval');
    } elseif ($pos === 'Director') {
      $gsdServiceQuery->where('status', 'Pending Final Approval');
    } elseif ($pos === 'Super Admin') {
      $gsdServiceQuery->whereIn('status', ['pending', 'Pending HR approval', 'Pending Final Approval']);
    } else {
      $gsdServiceQuery->whereRaw('1 = 0');
    }
    $gsdServiceRequests = $gsdServiceQuery->orderBy('created_at', 'desc')->get();

    // --- Undertime Requests ---
    $undertimeQuery = MisUndertimeRequest::query();
    if (in_array($pos, ['Manager', 'MIS Supervisor'])) {
      $undertimeQuery->where('status', 'pending');
    } elseif (in_array($pos, ['HR Manager', 'HR Specialist', 'HR Staff'])) {
      $undertimeQuery->where('status', 'Pending HR approval');
    } elseif ($pos === 'Director') {
      $undertimeQuery->where('status', 'Pending Final Approval');
    } elseif ($pos === 'Super Admin') {
      $undertimeQuery->whereIn('status', ['pending', 'Pending HR approval', 'Pending Final Approval']);
    } else {
      $undertimeQuery->whereRaw('1 = 0');
    }
    $undertimeRequests = $undertimeQuery->orderBy('created_at', 'desc')->get();

    // Cash Advance Requests (Strict Role Filtering)
    $cashAdvanceQuery = \App\Models\EmployeeCashAdvance::query();
    $divisionMap = [
      'Admin & Finance Division' => 'Admin',
      'Marketing Division' => 'Marketing',
      'Production Division' => 'Production',
    ];
    $userMappedDivision = $divisionMap[$user->division] ?? $user->division;
    $isManager = str_contains($pos, 'Manager');
    $isAFManager = $isManager && (str_contains($user->division, 'Admin') || str_contains($user->division, 'Finance'));
    $isSuperAdmin = ($pos === 'Super Admin');
    if ($isAFManager) {
      $cashAdvanceQuery->where(function($q) use ($userMappedDivision) {
          $q->where(function($sub) use ($userMappedDivision) {
              $sub->where('status', 'pending_supervisor_approval')
                  ->where('department_source', $userMappedDivision);
          })->orWhere('status', 'pending_admin_approval');
      });
    } elseif ($isManager) {
      $cashAdvanceQuery->where('status', 'pending_supervisor_approval')
                       ->where('department_source', $userMappedDivision);
    } elseif ($pos === 'Director') {
      $cashAdvanceQuery->where('status', 'pending_director_approval');
    } elseif ($isSuperAdmin) {
      $cashAdvanceQuery->whereIn('status', ['pending_supervisor_approval', 'pending_admin_approval', 'pending_director_approval', 'approved', 'rejected']);
    } else {
      $cashAdvanceQuery->whereRaw('1 = 0');
    }
    $cashAdvanceRequests = $cashAdvanceQuery->orderBy('created_at', 'desc')->get();

    // JV Requests
    $jvPendingQuery = JournalVoucherRequest::with(['requestor', 'items']);
    if ($pos === 'Manager' || $pos === 'Super Admin') {
        $jvPendingQuery->whereIn('status', ['pending_accounting', 'pending_manager_approval', 'adjustment_prepared']);
    } else {
        $jvPendingQuery->where('status', 'pending_accounting');
    }
    $jvPending = $jvPendingQuery->orderBy('created_at', 'desc')->get();

    // Stock Transfers awaiting Accounting/Admin & Finance review
    $stockTransfers = StockTransfer::with(['fromSite', 'toSite', 'book', 'bookIndex.book', 'bookBundle', 'createdBy', 'approvedBy'])
      ->where('status', 'accounting_review')
      ->orderBy('created_at', 'desc')
      ->get()
      ->groupBy(function ($item) {
          return $item->batch_id ?: ('single_' . $item->id);
      })
      ->map(function ($items) {
          $first = $items->first();
          $first->batch_items = $items->map(function($i) {
              $unitPrice = (float) (
                  $i->bookIndex ? ($i->bookIndex->price ?: ($i->bookIndex->book?->price ?? 0))
                  : ($i->book ? $i->book->price 
                  : ($i->bookBundle ? $i->bookBundle->price : 0))
              );
              $barcode = $i->bookIndex ? ($i->bookIndex->barcode ?: ($i->bookIndex->nbs_barcode ?: $i->bookIndex->article))
                  : ($i->book ? ($i->book->barcode ?: ($i->book->isbn ?: $i->book->item_code))
                  : ($i->bookBundle ? $i->bookBundle->sku : ''));
              return [
                  'id'         => $i->id,
                  'name'       => $i->item_name,
                  'type'       => $i->item_type,
                  'quantity'   => $i->quantity,
                  'unit_price' => $unitPrice,
                  'barcode'    => (string) $barcode,
              ];
          })->values()->toArray();
          $first->total_quantity = $items->sum('quantity');
          $first->items_count = $items->count();
          return $first;
      })
      ->values()
      ->filter(function ($transfer) use ($user) {
        return $transfer->canBeReviewedByAccounting($user);
      });

    // --- Payment Requests ---
    $paymentRequestQuery = \App\Models\PaymentRequest::with(['requester', 'items']);
    if (str_contains(strtolower($pos), 'director')) {
        $paymentRequestQuery->where('status', 'pending_director_approval');
    } elseif ($pos === 'Super Admin') {
        $paymentRequestQuery->whereIn('status', ['pending_director_approval', 'pending_admin_finance_approval']);
    } else {
        $isAFManager = str_contains($pos, 'Manager') && 
                       (str_contains($user->division, 'Admin') || str_contains($user->division, 'Finance') || str_contains($user->department, 'Admin') || str_contains($user->department, 'Finance'));
        $isAdmin = str_contains($pos, 'Admin') || $pos === 'A&F Manager' || $isAFManager;
        $isFinance = str_contains($pos, 'Finance') || str_contains($pos, 'Accounting') || $pos === 'A&F Manager' || $isAFManager;
        
        if ($isAdmin && $isFinance) {
            $paymentRequestQuery->where('status', 'pending_admin_finance_approval')
                                ->where(function($q) {
                                    $q->whereNull('admin_approved_by')
                                      ->orWhereNull('finance_approved_by');
                                });
        } elseif ($isAdmin) {
            $paymentRequestQuery->where('status', 'pending_admin_finance_approval')
                                ->whereNull('admin_approved_by');
        } elseif ($isFinance) {
            $paymentRequestQuery->where('status', 'pending_admin_finance_approval')
                                ->whereNull('finance_approved_by');
        } else {
            $paymentRequestQuery->whereRaw('1 = 0');
        }
    }
    $pendingPaymentRequests = $paymentRequestQuery->orderBy('created_at', 'desc')->get();

    // --- Auto Debit Requests ---
    $autoDebitQuery = \App\Models\AutoDebit::with(['preparer']);
    if (str_contains(strtolower($pos), 'director')) {
        $autoDebitQuery->where('status', 'pending_director');
    } elseif ($pos === 'Super Admin') {
        $autoDebitQuery->whereIn('status', ['pending_director', 'pending_finance']);
    } else {
        $isAFManager = str_contains($pos, 'Manager') || str_contains($pos, 'Supervisor') || $pos === 'A&F Manager';
        if ($isAFManager) {
            $autoDebitQuery->where('status', 'pending_finance');
        } else {
            $autoDebitQuery->whereRaw('1 = 0');
        }
    }
    $pendingAutoDebits = $autoDebitQuery->orderBy('created_at', 'desc')->get();

    // Combine into a unified approval queue for the bottom card
    $pendingApprovals = [];
    foreach ($cctvRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'CCTV',
        'id' => $req->cctv_req_id,
        'reference_no' => 'CCTV-' . str_pad($req->cctv_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->requested_by,
        'submitted_date' => $req->created_at->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->purpose, 50),
        'full_description' => $req->purpose,
        'department' => $req->department,
        'status' => $req->status,
        'original' => $req
      ];
    }
    foreach ($materialRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'Material',
        'id' => $req->material_req_id,
        'reference_no' => 'MAT-' . str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->requested_by,
        'submitted_date' => \Carbon\Carbon::parse($req->request_date)->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->request_details, 50),
        'full_description' => $req->request_details,
        'department' => 'N/A',
        'status' => $req->status,
        'amount' => is_numeric($req->amount) ? (float)$req->amount : (float)0,
        'original' => $req
      ];
    }
    // GSD material requests intentionally excluded from the approval queue.
    foreach ($cashAdvanceRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'Cash Advance',
        'id' => $req->id,
        'reference_no' => 'CA-' . str_pad($req->id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->employee_name,
        'submitted_date' => $req->created_at->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->purpose, 50),
        'full_description' => $req->purpose,
        'department' => $req->department,
        'status' => $req->status,
        'amount' => is_numeric($req->amount) ? (float)$req->amount : (float)0,
        'original' => $req
      ];
    }

    // Freight Bills (include amounts)
    $freightQuery = \App\Models\FreightBill::with('customer')->orderBy('created_at', 'desc');
    $freightRequests = $freightQuery->get();
    foreach ($freightRequests as $r) {
      $pendingApprovals[] = [
        'type' => 'Freight Bill',
        'id' => $r->id,
        'reference_no' => 'FB-' . str_pad($r->id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $r->customer->customer_name ?? 'N/A',
        'submitted_date' => $r->bill_date ?? ($r->created_at->format('M. d, Y')),
        'description' => \Illuminate\Support\Str::limit($r->remarks ?? '', 50),
        'full_description' => $r->remarks ?? '',
        'department' => 'Logistics',
        'status' => $r->status,
        'amount' => is_numeric($r->amount) ? (float)$r->amount : (float)0,
        'original' => $r
      ];
    }
    foreach ($qbRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'QB',
        'id' => $req->qb_req_id,
        'reference_no' => 'QB-' . str_pad($req->qb_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->user->name ?? 'Unknown',
        'submitted_date' => $req->created_at->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->customer_item_name, 50),
        'full_description' => "Customer Item: " . $req->customer_item_name,
        'department' => 'N/A',
        'status' => $req->status,
        'original' => $req
      ];
    }
    foreach ($serviceRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'Service',
        'id' => $req->service_req_id,
        'reference_no' => 'SRV-' . str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->requestor_name,
        'submitted_date' => \Carbon\Carbon::parse($req->date)->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->nature_of_request, 50),
        'full_description' => $req->nature_of_request,
        'department' => 'N/A',
        'status' => $req->status,
        'original' => $req
      ];
    }
    foreach ($gsdServiceRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'GSD Service',
        'id' => $req->service_req_id,
        'reference_no' => 'GSD-SRV-' . str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->requestor_name,
        'submitted_date' => \Carbon\Carbon::parse($req->date)->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->nature_of_request, 50),
        'full_description' => $req->nature_of_request,
        'department' => 'GSD',
        'status' => $req->status,
        'original' => $req
      ];
    }
    foreach ($undertimeRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'Undertime',
        'id' => $req->undertime_req_id,
        'reference_no' => 'UND-' . str_pad($req->undertime_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_by' => $req->employee_name,
        'submitted_date' => \Carbon\Carbon::parse($req->date)->format('M. d, Y'),
        'description' => \Illuminate\Support\Str::limit($req->reason, 50),
        'full_description' => $req->reason,
        'department' => 'N/A',
        'status' => $req->status,
        'original' => $req
      ];
    }
    foreach ($jvPending as $req) {
      $pendingApprovals[] = [
        'type' => 'JV',
        'id' => $req->id,
        'reference_no' => $req->jv_number,
        'submitted_by' => $req->requestor->name ?? 'Unknown',
        'submitted_date' => $req->created_at->format('M. d, Y'),
        'description' => $req->reason ?? 'Initial Summary Compilation Request',
        'full_description' => "JV Number: " . $req->jv_number . " | Status: " . str_replace('_', ' ', $req->status),
        'department' => 'Credit & Collection',
        'status' => $req->status,
        'amount' => is_numeric($req->total_amount) ? (float)$req->total_amount : (float)$req->items->sum('amount'),
        'original' => $req
      ];
    }
    foreach ($stockTransfers as $transfer) {
      $pendingApprovals[] = [
        'type' => 'Stock Transfer',
        'id' => $transfer->id,
        'reference_no' => 'ST-' . str_pad($transfer->id, 5, '0', STR_PAD_LEFT),
        'submitted_by' => $transfer->createdBy->name ?? 'Unknown',
        'submitted_date' => $transfer->created_at->format('M. d, Y'),
        'description' => ($transfer->item_name ?? 'Unknown Item') . ' from ' . ($transfer->fromSite->name ?? 'N/A') . ' to ' . ($transfer->toSite->name ?? 'N/A'),
        'full_description' => 'Transfer ' . $transfer->quantity . ' unit(s) of ' . ($transfer->item_name ?? 'Unknown Item') . ' from ' . ($transfer->fromSite->name ?? 'N/A') . ' to ' . ($transfer->toSite->name ?? 'N/A') . '.',
        'department' => 'Admin & Finance',
        'status' => $transfer->status,
        'amount' => $transfer->quantity . ' units',
        'original' => $transfer
      ];
    }

    foreach ($pendingPaymentRequests as $req) {
      $pendingApprovals[] = [
        'type' => 'Payment Request',
        'id' => $req->id,
        'reference_no' => 'PR-' . str_pad($req->id, 5, '0', STR_PAD_LEFT),
        'submitted_by' => $req->requester->name ?? 'N/A',
        'submitted_date' => $req->created_at->format('M. d, Y'),
        'description' => 'Payment request to: ' . $req->payment_to . ' for: ' . ($req->payment_for ?? 'N/A'),
        'full_description' => 'Payment request to ' . $req->payment_to . ' for ' . ($req->payment_for ?? 'N/A') . '. PO#: ' . ($req->po_number ?? 'N/A'),
        'department' => 'FORD / Production',
        'status' => $req->status,
        'amount' => (float)$req->total_amount,
        'url' => route('payment-requests.show', $req->id),
        'original' => $req
      ];
    }

    foreach ($pendingAutoDebits as $req) {
      $pendingApprovals[] = [
        'type' => 'Auto Debit Letter',
        'id' => $req->id,
        'reference_no' => 'AD-' . str_pad($req->id, 5, '0', STR_PAD_LEFT),
        'submitted_by' => $req->preparer->name ?? 'N/A',
        'submitted_date' => $req->created_at->format('M. d, Y'),
        'description' => 'Auto debit for: ' . $req->item_reason . ' (' . $req->source_origin . ')',
        'full_description' => 'Auto debit letter for ' . $req->item_reason . ' (' . $req->source_origin . '). Scheduled Debit Date: ' . date('M. d, Y', strtotime($req->debit_date)),
        'department' => 'FORD',
        'status' => $req->status === 'pending_director' ? 'Pending Director Approval' : 'Pending Finance Approval',
        'amount' => (float)$req->amount,
        'url' => route('production.ford.auto-debit.show', $req->id),
        'original' => $req
      ];
    }

    // Sort by submitted date descending
    usort($pendingApprovals, function ($a, $b) {
      return strtotime($b['submitted_date']) - strtotime($a['submitted_date']);
    });

    return $pendingApprovals;
  }

  public function myRequests()
  {
    $cashAdvances = \App\Models\EmployeeCashAdvance::where('user_id', auth()->id())
      ->latest()
      ->get();
    $materialRequests = MaterialReq::where('user_id', auth()->id())
      ->latest()
      ->get();
    $cctvRequests = CCTVReq::where('user_id', auth()->id())
      ->latest()
      ->get();

    $mergedRequests = $cashAdvances->concat($materialRequests)->sortByDesc('created_at');

    return view('admin-finance.my-requests.index', [
      'title' => '',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'cashAdvances' => $mergedRequests,
      'cctvRequests' => $cctvRequests,
    ]);
  }

  public function approvalQueue()
  {
    $user = auth()->user();
    $pendingApprovals = $this->gatherApprovalQueueItems($user);

    // --- My Approvals (The unified list for the top card) ---
    $myApprovals = [];

    // 1. Sales Orders awaiting current user's approval (Finance Manager)
    $salesOrdersForMe = \App\Models\SalesOrder::with('customer', 'preparedBy')
        ->where(function($query) {
            $query->whereIn('status', ['pending_acct_approval', 'pending_si_approval', 'pending_dr_approval'])
                  ->where('type', '!=', 'ecom_direct');
        })
        ->latest()
        ->get();

    foreach ($salesOrdersForMe as $req) {
      $myApprovals[] = [
        'type' => 'Sales Order',
        'id' => $req->id,
        'reference_no' => $req->so_number,
        'customer_name' => $req->customer?->customer_name ?? ($req->customer_representative ?: 'N/A'),
        'submitted_by' => $req->preparedBy->name ?? 'Unknown',
        'submitted_date' => $req->created_at,
        'amount' => '₱' . number_format($req->total_amount, 2),
        'description' => 'Sales Order for ' . ($req->customer->customer_name ?? 'Unknown Customer'),
        'department' => 'Sales',
        'status' => $req->status,
        'url' => route('admin-finance.sales-order.detail', $req->id),
        'attachment' => $req->attachment,
        'original' => $req
      ];
    }

    // 2. Team Stock Transfers awaiting Admin & Finance approval
    $teamStockTransfers = \App\Models\TeamStockTransfer::with(['transferredByUser', 'items.book', 'items.bookIndex.book', 'items.bookBundle'])
        ->where('status', 'pending_af_approval')
        ->latest()
        ->get();

    foreach ($teamStockTransfers as $tt) {
      $myApprovals[] = [
        'type' => 'Team Stock Transfer',
        'id' => $tt->id,
        'reference_no' => $tt->transfer_number,
        'customer_name' => $tt->team_name,
        'submitted_by' => $tt->transferredByUser->name ?? 'Unknown',
        'submitted_date' => $tt->created_at,
        'amount' => $tt->items->sum('quantity') . ' pcs (' . $tt->team_name . ')',
        'description' => 'Team Stock Transfer for ' . $tt->team_name,
        'full_description' => 'Team Stock Transfer of ' . $tt->items->sum('quantity') . ' total items to ' . $tt->team_name,
        'department' => 'Marketing',
        'status' => 'pending_af_approval',
        'url' => '#',
        'attachment' => null,
        'original' => $tt
      ];
    }

    // 3. All MIS Requests awaiting current user's approval
    foreach ($pendingApprovals as $req) {
      $orig = $req['original'] ?? null;
      $attachment = null;
      if ($orig) {
        if (isset($orig->supporting_documents) && $orig->supporting_documents) {
          $attachment = $orig->supporting_documents;
        } elseif (isset($orig->attachment) && $orig->attachment) {
          $attachment = $orig->attachment;
        } elseif (isset($orig->documents) && $orig->documents) {
          $attachment = $orig->documents;
        }
      }

      $myApprovals[] = [
        'type' => $req['type'],
        'id' => $req['id'],
        'reference_no' => $req['reference_no'],
        'customer_name' => $req['customer_name'] ?? ($req['original']->customer?->customer_name ?? ($req['original']->customer_representative ?? 'N/A')),
        'submitted_by' => $req['submitted_by'],
        'submitted_date' => $req['original']->created_at,
        'amount' => $req['amount'] ?? 'N/A', // Use provided amount or default to N/A
        'description' => $req['description'],
        'full_description' => $req['full_description'] ?? $req['description'],
        'department' => $req['department'],
        'status' => $req['status'],
        'url' => $req['url'] ?? '#',
        'attachment' => $attachment,
        'original' => $req['original']
      ];
    }

    // Sort My Approvals by date descending
    usort($myApprovals, function ($a, $b) {
      return $b['submitted_date'] <=> $a['submitted_date'];
    });

    // Fetch My Submissions (Unified)
    $mySubmissions = [];

    // 1. Sales Orders
    $soSubmissions = \App\Models\SalesOrder::with('customer', 'preparedBy')
      ->where('prepared_by', auth()->id())
      ->latest()
      ->get();

    foreach ($soSubmissions as $so) {
      $mySubmissions[] = [
        'type' => 'Sales Order',
        'id' => $so->id,
        'reference_no' => $so->so_number,
        'customer_name' => $so->customer?->customer_name ?? ($so->customer_representative ?: 'N/A'),
        'submitted_date' => $so->created_at,
        'detail' => '₱' . number_format($so->total_amount, 2),
        'status' => $so->status,
        'url' => route('admin-finance.sales-order.detail', $so->id),
        'attachment' => $so->attachment,
        'original' => $so
      ];
    }

    // 2. MIS Requests (using user_id)
    $myCctv = CCTVReq::where('user_id', auth()->id())
      ->where('status', '!=', 'to submit')
      ->latest()
      ->get();
    foreach ($myCctv as $req) {
      $mySubmissions[] = [
        'type' => 'CCTV',
        'id' => $req->cctv_req_id,
        'reference_no' => 'CCTV-' . str_pad($req->cctv_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => \Illuminate\Support\Str::limit($req->purpose, 50),
        'status' => $req->status,
        'url' => '#',
        'attachment' => null,
        'original' => $req
      ];
    }

    $myMaterial = MaterialReq::where('user_id', auth()->id())
      ->where('status', '!=', 'to submit')
      ->latest()
      ->get();
    foreach ($myMaterial as $req) {
      $mySubmissions[] = [
        'type' => 'Material',
        'id' => $req->material_req_id,
        'reference_no' => 'MAT-' . str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => \Illuminate\Support\Str::limit($req->request_details, 50),
        'status' => $req->status,
        'url' => '#',
        'attachment' => null,
        'original' => $req
      ];
    }

    $myQb = MisQbRequest::with('items')->where('user_id', auth()->id())->latest()->get();
    foreach ($myQb as $req) {
      $mySubmissions[] = [
        'type' => 'QB',
        'id' => $req->qb_req_id,
        'reference_no' => 'QB-' . str_pad($req->qb_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => $req->customer_item_name,
        'status' => $req->status,
        'url' => '#',
        'attachment' => null,
        'original' => $req
      ];
    }

    $myService = MisServiceRequest::where('user_id', auth()->id())->latest()->get();
    foreach ($myService as $req) {
      $mySubmissions[] = [
        'type' => 'Service',
        'id' => $req->service_req_id,
        'reference_no' => 'SRV-' . str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => \Illuminate\Support\Str::limit($req->nature_of_request, 50),
        'status' => $req->status,
        'url' => '#',
        'attachment' => null,
        'original' => $req
      ];
    }

    $myUndertime = MisUndertimeRequest::where('user_id', auth()->id())->latest()->get();
    foreach ($myUndertime as $req) {
      $mySubmissions[] = [
        'type' => 'Undertime',
        'id' => $req->undertime_req_id,
        'reference_no' => 'UND-' . str_pad($req->undertime_req_id, 4, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => \Illuminate\Support\Str::limit($req->reason, 50),
        'status' => $req->status,
        'url' => '#',
        'attachment' => null,
        'original' => $req
      ];
    }

    // 5. Journal Voucher Requests
    $myJv = JournalVoucherRequest::where('requested_by', auth()->id())->latest()->get();
    foreach ($myJv as $req) {
      $mySubmissions[] = [
        'type' => 'JV Request',
        'id' => $req->id,
        'reference_no' => $req->jv_number,
        'submitted_date' => $req->created_at,
        'detail' => $req->reason ?? 'Initial Summary Compilation Request',
        'status' => $req->status,
        'url' => route('admin-finance.credit-collection.jv-requests.show', $req->id),
        'attachment' => $req->supporting_documents,
        'original' => $req
      ];
    }

    $myCashAdvances = \App\Models\EmployeeCashAdvance::where('user_id', auth()->id())->latest()->get();
    foreach ($myCashAdvances as $req) {
      $mySubmissions[] = [
        'type' => 'Cash Advance',
        'id' => $req->id,
        'reference_no' => 'CA-' . str_pad($req->id, 4, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => '₱' . number_format($req->amount, 2),
        'status' => $req->status,
        'url' => '#',
        'attachment' => null,
        'original' => $req
      ];
    }

    $myPaymentRequests = \App\Models\PaymentRequest::where('requester_id', auth()->id())->latest()->get();
    foreach ($myPaymentRequests as $req) {
      $mySubmissions[] = [
        'type' => 'Payment Request',
        'id' => $req->id,
        'reference_no' => 'PR-' . str_pad($req->id, 5, '0', STR_PAD_LEFT),
        'submitted_date' => $req->created_at,
        'detail' => 'PhP ' . number_format($req->total_amount, 2) . ' - ' . $req->payment_to,
        'status' => $req->status,
        'url' => route('payment-requests.show', $req->id),
        'attachment' => $req->attachment_path,
        'original' => $req
      ];
    }

    // Sort all submissions by date
    usort($mySubmissions, function ($a, $b) {
      return $b['submitted_date'] <=> $a['submitted_date'];
    });

    // Filter approved requests for the Approved tab
    // This includes user's own submissions that are approved AND items they personally approved
    $myApprovedSubmissions = array_filter($mySubmissions, function($item) {
      $approvedStatuses = ['completed', 'approved', 'received', 'forwarded to accounting', 'picking', 'delivered', 'ready_for_delivery'];
      return in_array($item['status'], $approvedStatuses);
    });

    $userApprovedEntries = [];
    $userId = auth()->id();

    // Fetch CCTV approved by user
    $cctvApproved = CCTVReq::where(function($q) use ($userId) {
        $q->where('approved_by_manager', $userId)
          ->orWhere('approved_by_hr', $userId)
          ->orWhere('approved_by_director', $userId);
    })->get();
    foreach ($cctvApproved as $req) {
        $userApprovedEntries[] = [
            'type' => 'CCTV',
            'id' => $req->cctv_req_id,
            'reference_no' => 'CCTV-' . str_pad($req->cctv_req_id, 4, '0', STR_PAD_LEFT),
            'submitted_by' => $req->requested_by,
            'submitted_date' => $req->created_at,
            'detail' => $req->purpose,
            'status' => $req->status,
            'url' => '#',
            'attachment' => null,
            'original' => $req
        ];
    }

    // Fetch Material approved by user
    $materialApproved = MaterialReq::where(function($q) use ($userId) {
        $q->where('approved_by_manager', $userId)
          ->orWhere('approved_by_admin', $userId)
          ->orWhere('approved_by_director', $userId);
    })->get();
    foreach ($materialApproved as $req) {
        $userApprovedEntries[] = [
            'type' => 'Material',
            'id' => $req->material_req_id,
            'reference_no' => 'MAT-' . str_pad($req->material_req_id, 4, '0', STR_PAD_LEFT),
            'submitted_by' => $req->requested_by,
            'submitted_date' => $req->created_at,
            'detail' => $req->request_details,
            'status' => $req->status,
            'url' => '#',
            'attachment' => null,
            'original' => $req
        ];
    }

    // Fetch MIS (QB, Service, Undertime)
    $qbApproved = MisQbRequest::with('user')->where('approved_by', $userId)->get();
    foreach ($qbApproved as $req) {
        $userApprovedEntries[] = [
            'type' => 'QB',
            'id' => $req->qb_req_id,
            'reference_no' => 'QB-' . str_pad($req->qb_req_id, 4, '0', STR_PAD_LEFT),
            'submitted_by' => $req->user->name ?? 'Unknown',
            'submitted_date' => $req->created_at,
            'detail' => $req->customer_item_name,
            'status' => $req->status,
            'url' => '#',
            'attachment' => null,
            'original' => $req
        ];
    }

    $serviceApproved = MisServiceRequest::where('approved_by', $userId)->get();
    foreach ($serviceApproved as $req) {
        $userApprovedEntries[] = [
            'type' => 'Service',
            'id' => $req->service_req_id,
            'reference_no' => 'SRV-' . str_pad($req->service_req_id, 4, '0', STR_PAD_LEFT),
            'submitted_by' => $req->requestor_name,
            'submitted_date' => $req->created_at,
            'detail' => $req->nature_of_request,
            'status' => $req->status,
            'url' => '#',
            'attachment' => null,
            'original' => $req
        ];
    }

    $undertimeApproved = MisUndertimeRequest::where('approved_by', $userId)->get();
    foreach ($undertimeApproved as $req) {
        $userApprovedEntries[] = [
            'type' => 'Undertime',
            'id' => $req->undertime_req_id,
            'reference_no' => 'UND-' . str_pad($req->undertime_req_id, 4, '0', STR_PAD_LEFT),
            'submitted_by' => $req->employee_name,
            'submitted_date' => $req->created_at,
            'detail' => $req->reason,
            'status' => $req->status,
            'url' => '#',
            'attachment' => null,
            'original' => $req
        ];
    }

    // Sales Orders approved by user
    $soApproved = \App\Models\SalesOrder::with('customer', 'preparedBy')->where(function($q) use ($userId) {
        $q->where('approved_by_acct', $userId)
          ->orWhere('signed_by_af_manager', $userId);
    })->get();
    foreach ($soApproved as $so) {
        $userApprovedEntries[] = [
            'type' => 'Sales Order',
            'id' => $so->id,
            'reference_no' => $so->so_number,
            'customer_name' => $so->customer?->customer_name ?? ($so->customer_representative ?: 'N/A'),
            'submitted_by' => $so->preparedBy->name ?? 'Unknown',
            'submitted_date' => $so->created_at,
            'detail' => '₱' . number_format($so->total_amount, 2),
            'status' => $so->status,
            'url' => route('admin-finance.sales-order.detail', $so->id),
            'attachment' => $so->attachment,
            'original' => $so
        ];
    }

    // Cash Advances approved by user
    $caApproved = \App\Models\EmployeeCashAdvance::where(function($q) use ($userId) {
        $q->where('approved_by_manager', $userId)
          ->orWhere('approved_by_admin', $userId)
          ->orWhere('approved_by_director', $userId);
    })->latest()->get();

    foreach ($caApproved as $req) {
        $userApprovedEntries[] = [
            'type' => 'Cash Advance',
            'id' => $req->id,
            'reference_no' => 'CA-' . str_pad($req->id, 4, '0', STR_PAD_LEFT),
            'submitted_by' => $req->employee_name,
            'submitted_date' => $req->created_at,
            'detail' => 'PhP ' . number_format($req->amount, 2) . ' - ' . \Illuminate\Support\Str::limit($req->purpose, 50),
            'status' => $req->status,
            'url' => '#',
            'attachment' => null,
            'original' => $req
        ];
    }

    // Merge: Personal Approval History FIRST, then own submissions
    // This ensures that even if I own a request, the "Approved" entry (with its full history)
    // takes precedence over my own submission record if it's not fully approved yet.
    $mergedApproved = array_merge($userApprovedEntries, $myApprovedSubmissions);
    $finalApproved = [];
    $seen = [];
    foreach ($mergedApproved as $item) {
        $key = $item['type'] . '_' . $item['id'];
        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $finalApproved[] = $item;
        }
    }

    // Sort by date descending (using unix timestamp for stability)
    usort($finalApproved, function ($a, $b) {
      $dateA = $a['submitted_date'] instanceof \Carbon\Carbon ? $a['submitted_date']->timestamp : strtotime($a['submitted_date']);
      $dateB = $b['submitted_date'] instanceof \Carbon\Carbon ? $b['submitted_date']->timestamp : strtotime($b['submitted_date']);
      return $dateB - $dateA;
    });

    return view('admin-finance.approval-queue', [
      'title' => 'Approval Queue',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'myApprovals' => $myApprovals,
      'pendingApprovals' => $pendingApprovals,
      'mySubmissions' => $mySubmissions,
      'myApprovedRequests' => $finalApproved,
      'salesOrders' => $salesOrdersForMe
    ]);
  }

  public function checkVoucherIndex()
  {
    $entries = JournalEntry::where('entry_type', 'CV')
                ->with('creator')
                ->latest()
                ->paginate(15);

    return view('admin-finance.check-voucher.list', [
      'title' => 'Check Vouchers',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'entries' => $entries
    ]);
  }

  public function showCheckVoucher($id)
  {
    $entry = JournalEntry::with(['items.account', 'creator'])->findOrFail($id);

    return view('admin-finance.check-voucher.show', [
      'title' => 'Check Voucher Details',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'entry' => $entry
    ]);
  }

public function checkVoucher()
{
  $vendors   = \App\Models\Vendor::where('status', 'active')->orderBy('vendor_name')->get(['id', 'vendor_name', 'vendor_code']);
  $suppliers = \App\Models\Supplier::orderBy('company_name')->get(['id', 'company_name', 'supplier_code']);
  $employees = \App\Models\User::whereNotNull('first_name')
                ->orderBy('first_name')
                ->select('id', 'first_name', 'last_name', 'employee_number')
                ->get()
                ->map(function($u) {
                    $u->full_name = trim($u->first_name . ' ' . $u->last_name);
                    return $u;
                });
  $accounts  = \App\Models\ChartOfAccount::where('name', 'not like', '%Inventory%')
                ->where('category', 'not like', '%Inventory%')
                ->orderBy('code')
                ->get();

  return view('admin-finance.check-voucher.create', [
    'title'     => 'Create Check Voucher',
    'role'      => 'Finance Manager',
    'sidebar'   => 'admin-finance',
    'vendors'   => $vendors,
    'suppliers' => $suppliers,
    'employees' => $employees,
    'accounts'  => $accounts,
  ]);
}

  public function storeCheckVoucher(Request $request)
  {
    $request->validate([
      'payee' => 'required',
      'check_no' => 'required',
      'date' => 'required|date',
    ]);

    // --- ACCOUNTING INTEGRATION ---
    $this->accounting->postCheckVoucherEntry([
      'payee' => $request->payee,
      'check_no' => $request->check_no,
      'date' => $request->date,
      'amount' => $request->amount ?? 0,
      'memo' => $request->memo,
      'items' => $request->items
    ]);

    return redirect()->route('admin-finance.check-voucher')->with('success', 'Check Voucher #' . $request->check_no . ' has been recorded and posted to the General Ledger.');
  }

  public function salesInvoice()
  {
    // 1. Get IDs of SalesOrders that already have an approved SalesInvoice
    $approvedSiSoIds = \App\Models\SalesInvoice::where('status', 'approved')->pluck('so_id')->filter()->toArray();

    // 2. Get SalesOrders pending SI prep/approval (NOT YET approved) ordered newest first
    $pendingOrders = \App\Models\SalesOrder::with('customer', 'preparedBy', 'siPreparedBy')
      ->where(function($q) use ($approvedSiSoIds) {
          $q->where(function($sq) {
              $sq->whereNull('signed_by_af_manager')
                 ->whereIn('status', ['pending_si_prep', 'pending_si_approval', 'si_created', 'ar_created', 'picking']);
          })->orWhere(function($sq) use ($approvedSiSoIds) {
              $sq->whereIn('status', ['pending_si_prep', 'pending_si_approval', 'si_created'])
                 ->whereNotIn('id', $approvedSiSoIds);
          });
      })
      ->orderBy('id', 'desc')
      ->get();

    $normalOrders = $pendingOrders->filter(function($order) {
        return $order->type !== 'ecom_direct';
    })->sortByDesc('id')->values();

    $ecomOrders = $pendingOrders->filter(function($order) {
        return $order->type === 'ecom_direct';
    })->sortByDesc('id')->values();

    // 3. Get all Completed / Finalized Sales Invoices ordered newest first
    $completedSIs = \App\Models\SalesInvoice::with('customer', 'salesOrder', 'createdBy')
      ->where('status', 'approved')
      ->orderBy('id', 'desc')
      ->get();

    // Ensure all signed/approved SalesOrders have a corresponding entry in $completedSIs (excluding complimentary orders and orders currently in pending SI queue)
    $existingSiSoIds = $completedSIs->pluck('so_id')->filter()->toArray();
    $pendingOrderIds = $pendingOrders->pluck('id')->toArray();
    $signedOrdersWithoutSI = \App\Models\SalesOrder::with('customer', 'preparedBy')
      ->where('type', '!=', 'complimentary')
      ->whereNotIn('id', $pendingOrderIds)
      ->where(function($q) {
          $q->whereNotNull('signed_by_af_manager')
            ->orWhereIn('status', ['ready_for_delivery', 'completed']);
      })
      ->whereNotIn('id', $existingSiSoIds)
      ->orderBy('id', 'desc')
      ->get();

    foreach ($signedOrdersWithoutSI as $so) {
        \App\Models\SalesInvoice::firstOrCreate(
            ['so_id' => $so->id],
            [
                'si_number' => 'SI-' . $so->so_number,
                'customer_id' => $so->customer_id,
                'customer_name' => $so->customer->customer_name ?? 'N/A',
                'total_amount' => $so->total_amount,
                'transaction_type' => $so->type . '_si',
                'payment_method' => $so->payment_method ?? 'cash',
                'status' => 'approved',
                'created_by' => $so->signed_by_af_manager ?? (auth()->id() ?? 1)
            ]
        );
    }

    if ($signedOrdersWithoutSI->count() > 0) {
        $completedSIs = \App\Models\SalesInvoice::with('customer', 'salesOrder', 'createdBy')
          ->where('status', 'approved')
          ->orderBy('id', 'desc')
          ->get();
    }

    // Split completed SIs into Normal Completed SIs and Completed E-com SIs
    $completedNormalSIs = $completedSIs->filter(function($si) {
        $type = $si->salesOrder->type ?? '';
        return $type !== 'ecom_direct' && !str_contains($si->transaction_type ?? '', 'ecom');
    })->sortByDesc('id')->values();

    $completedEcomSIs = $completedSIs->filter(function($si) {
        $type = $si->salesOrder->type ?? '';
        return $type === 'ecom_direct' || str_contains($si->transaction_type ?? '', 'ecom');
    })->sortByDesc('id')->values();

    $customers = \App\Models\Customer::orderBy('customer_name')->get();
    $userTeam = auth()->user()->sales_team ?? null;
    $mktController = app(\App\Http\Controllers\MarketingController::class);
    $products = $mktController->getUnifiedProducts($userTeam);
    $areaSalesStaff = \App\Models\User::where('department', 'Area Sales')->get();

    return view('admin-finance.accounting.sales-invoice', [
      'title' => 'Sales Invoice Management',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'normalOrders' => $normalOrders,
      'ecomOrders' => $ecomOrders,
      'completedSIs' => $completedNormalSIs,
      'completedEcomSIs' => $completedEcomSIs,
      'customers' => $customers,
      'products' => $products,
      'areaSalesStaff' => $areaSalesStaff,
      'userTeam' => $userTeam
    ]);
  }

  public function complimentaryReceiptIndex(Request $request)
  {
    $request->merge(['tab' => 'complimentary']);
    return $this->salesManagement($request);
  }

  public function prepareAR($id)
  {
    $order = \App\Models\SalesOrder::with(['customer', 'items.product', 'items.book', 'preparedBy'])->findOrFail($id);

    return view('admin-finance.accounting.prepare-ar', [
      'title' => 'Prepare Acknowledgement Receipt (Complimentary)',
      'role' => 'Accounting Staff',
      'sidebar' => 'admin-finance',
      'order' => $order
    ]);
  }

  public function storeAR(Request $request, $id)
  {
    $order = \App\Models\SalesOrder::with(['preparedBy', 'areaSalesStaff'])->findOrFail($id);

    $now = now();
    $order->update([
      'status' => 'ready_for_packing',
      'ar_prepared_by' => auth()->id(),
      'ar_prepared_at' => $now,
      'packing_data' => null,
      'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'Acknowledgement Receipt (Complimentary) Approved / Marked Complete by ' . auth()->user()->name
    ]);

    // Post expense journal entry (Complimentary & Donation Expense)
    $this->accounting->postComplimentaryEntry($order);

    $msg = 'Acknowledgement Receipt (Complimentary) for Order #' . $order->so_number . ' has been approved and sent to Packing.';

    return redirect()->route('admin-finance.accounting.complimentary-receipt')->with('success', $msg);
  }

  public function printAR($id)
  {
    $order = \App\Models\SalesOrder::with([
      'customer', 
      'items.product', 
      'items.book', 
      'items.bookIndex', 
      'items.bundle', 
      'preparedBy', 
      'arPreparedBy', 
      'signedBy',
      'acctApprovedBy',
      'mktApprovedBy'
    ])->findOrFail($id);

    return view('admin-finance.accounting.print-ar', [
      'title' => 'Acknowledgement Receipt (Complimentary) - ' . $order->so_number,
      'order' => $order
    ]);
  }

  public function prepareSalesInvoice($id)
  {
    $order = \App\Models\SalesOrder::with('customer', 'items.product', 'preparedBy')->findOrFail($id);

    $isExempt = in_array($order->type, ['ecom_direct', 'charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment', 'complimentary', 'cod']) || strtolower($order->transaction_type ?? '') === 'charge';

    if (!$isExempt && !$order->proof_of_payment) {
      return redirect()->back()->with('error', 'Cannot proceed. Sales Order #' . $order->so_number . ' does not have a Proof of Payment attached.');
    }

    return view('admin-finance.accounting.prepare-si', [
      'title' => 'Prepare Sales Invoice',
      'role' => 'Accounting Staff',
      'sidebar' => 'admin-finance',
      'order' => $order
    ]);
  }

  public function storeSalesInvoice(Request $request, $id)
  {
    $order = \App\Models\SalesOrder::findOrFail($id);

    if ($request->hasFile('proof_of_payment')) {
      $path = $request->file('proof_of_payment')->store('sales_orders', 'public');
      $order->proof_of_payment = $path;
      $order->save();
    }

    if ($request->filled('payment_method')) {
      $order->payment_method = strtolower($request->input('payment_method'));
      $order->save();
    }

    if ($request->filled('si_number')) {
      $order->si_number = trim($request->input('si_number'));
      $order->save();
    }

    $isExempt = in_array($order->type, ['ecom_direct', 'charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment', 'complimentary', 'cod']) || strtolower($order->transaction_type ?? '') === 'charge';

    if (!$isExempt && !$order->proof_of_payment) {
      return redirect()->route('admin-finance.accounting.sales-invoice')->with('error', 'Cannot proceed. Proof of Payment is required for Paid transactions before Sales Invoice can be generated.');
    }

    $isEcomDirect = $order->type === 'ecom_direct';
    $isConsignment = in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment']);

    if ($isEcomDirect) {
      $order->update([
        'status' => 'picking',
        'si_prepared_by' => auth()->id(),
        'si_prepared_at' => now(),
        'signed_by_af_manager' => auth()->id(),
        'signed_at' => now(),
        'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'SI Prepared and auto-signed by ' . auth()->user()->name
      ]);

      $siNumberVal = $order->si_number ?: ($request->input('si_number') ?: 'SI-' . $order->so_number);
      $si = \App\Models\SalesInvoice::firstOrCreate(
        ['so_id' => $order->id],
        [
          'si_number' => $siNumberVal,
          'customer_id' => $order->customer_id,
          'customer_name' => $order->customer->customer_name ?? 'N/A',
          'total_amount' => $order->total_amount,
          'transaction_type' => $order->type . '_si',
          'created_by' => auth()->id()
        ]
      );
      $si->update(['si_number' => $siNumberVal, 'status' => 'approved', 'posted_at' => now(), 'payment_method' => $order->payment_method ?? 'cash']);

      // --- ACCOUNTING INTEGRATION ---
      $this->accounting->postSalesOrderEntry($order);

      // Automatically create a pick list for E-Com Direct Invoice
      try {
        $order->load('items');
        if ($order->items && $order->items->count() > 0) {
          $pickList = \App\Models\PickList::create([
            'sales_order_id' => $order->id,
            'pick_list_number' => 'PL-' . $order->so_number . '-' . date('YmdHis'),
            'status' => 'in_progress',
            'prepared_by' => auth()->id(),
          ]);

          foreach ($order->items as $item) {
            \App\Models\PickListItem::create([
              'pick_list_id' => $pickList->id,
              'sales_order_item_id' => $item->id,
              'requested_qty' => $item->quantity,
              'picked_qty' => 0,
              'status' => 'pending'
            ]);
          }
          \Log::info('Automatically created pick list for E-com direct invoice on SI store: ' . $pickList->pick_list_number);
        }
      } catch (\Exception $e) {
        \Log::error('Failed to automatically create pick list for E-com direct invoice on SI store: ' . $e->getMessage());
      }

      return redirect()->route('admin-finance.accounting.sales-invoice')->with('success', 'Sales Invoice for #' . $order->so_number . ' has been prepared and finalized. Routed to pick list.');
    }

    // Consignment / Area Sales Consignment flow (Requires prep -> approval flow)
    $isAR = $order->type === 'area_sales_consignment';
    $isCR = $order->type === 'area_consignment';
    $isDC = $order->type === 'direct_consignment';

    if ($isAR || $isCR || $isDC) {
      // 1. Update Sales Order
      $order->update([
        'status' => 'pending_si_approval',
        'si_prepared_by' => auth()->id(),
        'si_prepared_at' => now(),
        'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'SI Prepared by ' . auth()->user()->name
      ]);

      // 2. Handle Sales Invoice
      $siNumberVal = $order->si_number ?: ($request->input('si_number') ?: 'SI-' . $order->so_number);
      $si = \App\Models\SalesInvoice::firstOrCreate(
        ['so_id' => $order->id],
        [
          'si_number' => $siNumberVal,
          'customer_id' => $order->customer_id,
          'customer_name' => $order->customer->customer_name ?? 'N/A',
          'total_amount' => $order->total_amount,
          'transaction_type' => $order->type . '_si',
          'created_by' => auth()->id()
        ]
      );
      
      $si->update([
        'si_number' => $siNumberVal,
        'status' => 'pending_approval',
        'payment_method' => $order->payment_method ?? 'cash'
      ]);

      // Send Notification to Director if status is "pending_si_approval"
      $director = \App\Models\User::where('position', 'Director')->first();
      if ($director) {
          try {
              $director->notify(new \App\Notifications\DirectorApprovalRequested($order, 'Sales Order'));
          } catch (\Exception $e) {
              \Log::error("Failed to send Sales Order SI approval notification: " . $e->getMessage());
          }
      }

      return redirect()->route('admin-finance.accounting.sales-invoice')->with('success', 'Sales Invoice for #' . $order->so_number . ' has been prepared and is waiting for Manager signature.');
    }

    // Normal flow
    $order->update([
      'status' => 'pending_si_approval',
      'si_prepared_by' => auth()->id(),
      'si_prepared_at' => now(),
      'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'SI Prepared by ' . auth()->user()->name
    ]);

    // Send Notification to Director if status is "pending_si_approval"
    $director = \App\Models\User::where('position', 'Director')->first();
    if ($director) {
        try {
            $director->notify(new \App\Notifications\DirectorApprovalRequested($order, 'Sales Order'));
        } catch (\Exception $e) {
            \Log::error("Failed to send Sales Order SI approval notification: " . $e->getMessage());
        }
    }

    return redirect()->route('admin-finance.accounting.sales-invoice')->with('success', 'Sales Invoice for #' . $order->so_number . ' has been prepared and is waiting for Manager signature.');
  }

  public function bulkFinalizeInvoices(Request $request)
  {
    $ids = $request->input('ids', []);
    $actionType = $request->input('action', 'auto');

    if (empty($ids)) {
      return response()->json(['success' => false, 'message' => 'No invoices selected.'], 400);
    }

    $processed = 0;
    $errors = [];

    // Run within a transaction for DB safety
    \DB::beginTransaction();
    try {
      foreach ($ids as $id) {
        $order = \App\Models\SalesOrder::findOrFail($id);

        $isExempt = in_array($order->type, ['ecom_direct', 'charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment', 'complimentary', 'cod']);

        if (!$isExempt && !$order->proof_of_payment) {
          $errors[] = "Order #{$order->so_number} is missing Proof of Payment.";
          continue;
        }

        $isEcomDirect = $order->type === 'ecom_direct';

        if ($isEcomDirect) {
          $order->update([
            'status' => 'picking',
            'si_prepared_by' => auth()->id(),
            'si_prepared_at' => now(),
            'signed_by_af_manager' => auth()->id(),
            'signed_at' => now(),
            'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'SI Prepared and auto-signed in bulk by ' . auth()->user()->name
          ]);

          // --- ACCOUNTING INTEGRATION ---
          $this->accounting->postSalesOrderEntry($order);

          // Automatically create a pick list for E-Com Direct Invoice
          $order->load('items');
          if ($order->items && $order->items->count() > 0) {
            $pickList = \App\Models\PickList::create([
              'sales_order_id' => $order->id,
              'pick_list_number' => 'PL-' . $order->so_number . '-' . date('YmdHis'),
              'status' => 'in_progress',
              'prepared_by' => auth()->id(),
            ]);

            foreach ($order->items as $item) {
              \App\Models\PickListItem::create([
                'pick_list_id' => $pickList->id,
                'sales_order_item_id' => $item->id,
                'requested_qty' => $item->quantity,
                'picked_qty' => 0,
                'status' => 'pending'
              ]);
            }
          }
        } else {
          if ($order->status === 'pending_si_prep' || $order->status === 'si_created' || $actionType === 'prepare') {
            $order->update([
              'status' => 'pending_si_approval',
              'si_prepared_by' => auth()->id(),
              'si_prepared_at' => now(),
              'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'SI Prepared in bulk by ' . auth()->user()->name
            ]);

            if (in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment'])) {
              \App\Models\SalesInvoice::where('so_id', $order->id)->where('status', 'draft')->update(['status' => 'pending_approval']);
            }

            // Send Notification to Director if status is "pending_si_approval"
            $director = \App\Models\User::where('position', 'Director')->first();
            if ($director) {
                try {
                    $director->notify(new \App\Notifications\DirectorApprovalRequested($order, 'Sales Order'));
                } catch (\Exception $e) {
                    \Log::error("Failed to send bulk Sales Order SI approval notification: " . $e->getMessage());
                }
            }
          } elseif ($order->status === 'pending_si_approval' || $actionType === 'sign') {
            $isCharge = $order->type === 'charge' || strtolower($order->transaction_type ?? '') === 'charge';
            $isTeamOrder = !empty($order->preparedBy?->sales_team)
                || !empty($order->areaSalesStaff?->sales_team)
                || str_starts_with($order->preparedBy?->email ?? '', 'marketing_team')
                || str_starts_with($order->areaSalesStaff?->email ?? '', 'marketing_team')
                || stripos($order->customer?->customer_type ?? '', 'TEAM') !== false
                || stripos($order->customer?->company_name ?? '', 'TEAM') !== false
                || stripos($order->customer?->customer_name ?? '', 'TEAM') !== false
                || str_contains($order->remarks ?? '', '[SITE: Team');

            if ($isTeamOrder || in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment'])) {
              $newStatus = 'completed';
            } elseif ($isCharge) {
              $newStatus = 'pending_dr_prep';
            } else {
              $newStatus = 'ready_for_delivery';
            }
            $order->update([
              'status' => $newStatus,
              'signed_by_af_manager' => auth()->id(),
              'signed_at' => now(),
              'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'SI Signed & Approved in bulk by ' . auth()->user()->name
            ]);

            if (in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment'])) {
              \App\Models\SalesInvoice::where('so_id', $order->id)->whereIn('status', ['draft', 'pending_approval'])->update(['status' => 'approved']);
            }

            // Accounting integration
            $this->accounting->postSalesOrderEntry($order);
          }
        }

        $processed++;
      }

      \DB::commit();

      $message = "Successfully processed {$processed} sales order(s).";
      if (!empty($errors)) {
        $message .= " Note: " . implode(' ', $errors);
      }

      return response()->json([
        'success' => true,
        'message' => $message,
        'processed' => $processed,
        'errors' => $errors
      ]);
    } catch (\Exception $e) {
      \DB::rollBack();
      \Log::error("Bulk invoice processing failed: " . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => 'An error occurred during bulk invoice processing: ' . $e->getMessage()
      ], 500);
    }
  }

  public function bulkSetPaid(Request $request)
  {
    $siIds = $request->input('si_ids', []);
    $soIds = $request->input('so_ids', []);

    if (empty($siIds) && empty($soIds)) {
      return response()->json(['success' => false, 'message' => 'No invoices selected.'], 400);
    }

    $processed = 0;
    \DB::beginTransaction();
    try {
      $invoices = \App\Models\SalesInvoice::with('salesOrder')->whereIn('id', $siIds)->get();
      $orderIds = $invoices->pluck('so_id')->filter()->merge($soIds)->unique()->toArray();
      $orders = \App\Models\SalesOrder::whereIn('id', $orderIds)->get();

      foreach ($orders as $so) {
        $remBal = (float) $so->remaining_balance;
        $totalAmount = (float) ($so->total_amount ?: $so->final_total);

        if ($remBal > 0 || $so->payment_status !== 'paid') {
          $payAmt = $remBal > 0 ? $remBal : $totalAmount;
          $pm = $so->payment_method ?: ($so->ecom_platform ? 'ecom_' . $so->ecom_platform : 'cash');

          $payment = \App\Models\Payment::create([
            'customer_id' => $so->customer_id,
            'sales_order_id' => $so->id,
            'amount' => $payAmt,
            'payment_method' => $pm,
            'payment_date' => now()->toDateString(),
            'status' => 'verified',
            'reference_number' => $so->so_number,
            'verified_by' => auth()->id(),
            'notes' => 'Bulk Set as Paid for E-Com Order #' . $so->so_number,
          ]);

          $so->update([
            'payment_status' => 'paid',
            'remarks' => ($so->remarks ? $so->remarks . ' | ' : '') . 'Marked as Paid in bulk by ' . (auth()->user()->name ?? 'System')
          ]);

          try {
            if (isset($this->accounting) && method_exists($this->accounting, 'postPaymentEntry')) {
              $this->accounting->postPaymentEntry($so, $payment, $pm, $payAmt);
            }
          } catch (\Exception $glEx) {
            \Log::warning("Bulk Set Paid GL entry skipped for SO #{$so->so_number}: " . $glEx->getMessage());
          }
        }
        $processed++;
      }

      foreach ($invoices as $inv) {
        if (!$inv->so_id) {
          $inv->update(['status' => 'approved']);
          $processed++;
        }
      }

      \DB::commit();

      if (class_exists('\App\Models\ActivityLog')) {
        \App\Models\ActivityLog::create([
          'user_id' => auth()->id() ?: 1,
          'action' => 'Bulk Payment Set as Paid',
          'description' => "Marked {$processed} E-com Sales Invoice(s) as Paid in bulk.",
          'affected_model' => 'SalesInvoice',
          'affected_model_id' => null,
          'ip_address' => $request->ip(),
        ]);
      }

      return response()->json([
        'success' => true,
        'message' => "Successfully marked {$processed} invoice(s) as Paid.",
        'processed' => $processed
      ]);
    } catch (\Exception $e) {
      \DB::rollBack();
      \Log::error("Bulk Set as Paid failed: " . $e->getMessage());

      return response()->json([
        'success' => false,
        'message' => 'An error occurred while marking invoices as paid: ' . $e->getMessage()
      ], 500);
    }
  }

  public function signSalesInvoice(Request $request, $id)
  {
    $order = \App\Models\SalesOrder::findOrFail($id);

    $isExempt = in_array($order->type, ['ecom_direct', 'charge', 'area_consignment', 'area_sales_consignment', 'direct_consignment', 'complimentary', 'cod']) || strtolower($order->transaction_type ?? '') === 'charge';

    if (!$isExempt && !$order->proof_of_payment) {
      return redirect()->back()->with('error', 'Cannot proceed. Proof of Payment is required for Paid transactions before Sales Invoice can be signed.');
    }

    if ($request->filled('si_number')) {
      $order->si_number = trim($request->input('si_number'));
      $order->save();
    }

    $isEcomDirect = $order->type === 'ecom_direct';
    $isAreaSalesConsignment = $order->type === 'area_sales_consignment';
    $isConsignment = $order->type === 'area_consignment';
    $isDirectConsignment = $order->type === 'direct_consignment';
    $isCharge = $order->type === 'charge' || strtolower($order->transaction_type ?? '') === 'charge';

    $isTeamOrder = !empty($order->preparedBy?->sales_team)
        || !empty($order->areaSalesStaff?->sales_team)
        || str_starts_with($order->preparedBy?->email ?? '', 'marketing_team')
        || str_starts_with($order->areaSalesStaff?->email ?? '', 'marketing_team')
        || stripos($order->customer?->customer_type ?? '', 'TEAM') !== false
        || stripos($order->customer?->company_name ?? '', 'TEAM') !== false
        || stripos($order->customer?->customer_name ?? '', 'TEAM') !== false
        || str_contains($order->remarks ?? '', '[SITE: Team');

    if ($isTeamOrder) {
      $newStatus = 'completed';
    } elseif ($isEcomDirect) {
      $newStatus = 'picking';
    } elseif ($isAreaSalesConsignment || $isConsignment || $isDirectConsignment) {
      $newStatus = 'completed';
    } elseif ($isCharge) {
      $newStatus = 'pending_dr_prep';
    } else {
      $newStatus = 'ready_for_delivery';
    }

    $order->update([
      'status' => $newStatus,
      'signed_by_af_manager' => auth()->id(),
      'signed_at' => now()
    ]);

    $siNumberVal = $order->si_number ?: ($request->input('si_number') ?: 'SI-' . $order->so_number);

    $si = \App\Models\SalesInvoice::firstOrCreate(
      ['so_id' => $order->id],
      [
        'si_number' => $siNumberVal,
        'customer_id' => $order->customer_id,
        'customer_name' => $order->customer->customer_name ?? 'N/A',
        'total_amount' => $order->total_amount,
        'transaction_type' => $order->type . '_si',
        'created_by' => auth()->id()
      ]
    );
    $si->update(['si_number' => $siNumberVal, 'status' => 'approved', 'posted_at' => now(), 'payment_method' => $order->payment_method ?? 'cash']);

    // --- ACCOUNTING INTEGRATION ---
    $this->accounting->postSalesOrderEntry($order);

    if ($isEcomDirect) {
      // Automatically create a pick list for E-Com Direct Invoice after signing SI
      try {
        $order->load('items');
        if ($order->items && $order->items->count() > 0) {
          $pickList = \App\Models\PickList::create([
            'sales_order_id' => $order->id,
            'pick_list_number' => 'PL-' . $order->so_number . '-' . date('YmdHis'),
            'status' => 'in_progress',
            'prepared_by' => auth()->id(),
          ]);

          foreach ($order->items as $item) {
            \App\Models\PickListItem::create([
              'pick_list_id' => $pickList->id,
              'sales_order_item_id' => $item->id,
              'requested_qty' => $item->quantity,
              'picked_qty' => 0,
              'status' => 'pending'
            ]);
          }
          \Log::info('Automatically created pick list for E-com direct invoice: ' . $pickList->pick_list_number);
        }
      } catch (\Exception $e) {
        \Log::error('Failed to automatically create pick list for E-com direct invoice: ' . $e->getMessage());
      }
    }

    $successMsg = 'Sales Invoice for #' . $order->so_number . ' has been signed by Admin & Finance Manager.';
    if ($isEcomDirect) {
      $successMsg .= ' Routed to pick list.';
    }
    return redirect()->back()->with('success', $successMsg);
  }

  public function updateSiNumber(Request $request, $id)
  {
    $request->validate([
      'si_number' => 'nullable|string|max:50'
    ]);

    $order = \App\Models\SalesOrder::findOrFail($id);
    $siNum = trim($request->input('si_number', ''));
    $order->si_number = $siNum ?: null;
    $order->save();

    if (!empty($siNum)) {
      \App\Models\SalesInvoice::updateOrCreate(
        ['so_id' => $order->id],
        [
          'so_number'        => $order->so_number,
          'si_number'        => $siNum,
          'customer_id'      => $order->customer_id,
          'customer_name'    => $order->customer?->customer_name ?? 'N/A',
          'transaction_type' => $order->type . '_si',
          'total_amount'     => $order->total_amount,
          'status'           => $order->signed_by_af_manager ? 'approved' : 'draft',
          'created_by'       => auth()->id(),
        ]
      );
    }

    return response()->json([
      'success' => true,
      'message' => 'SI Number updated successfully.',
      'si_number' => $order->si_number
    ]);
  }

  public function printSalesInvoice($id)
  {
    $order = \App\Models\SalesOrder::with(['customer', 'items.book', 'items.bundle', 'items.product', 'preparedBy', 'mktApprovedBy', 'prodApprovedBy', 'siPreparedBy', 'signedBy'])->findOrFail($id);

    return view('marketing.sales-orders.print-invoice', [
      'order' => $order
    ]);
  }

  public function printDeliveryReceipt($id)
  {
    $order = \App\Models\SalesOrder::with([
      'customer', 
      'items.book', 
      'items.bundle', 
      'items.product', 
      'items.bookIndex', 
      'preparedBy', 
      'mktApprovedBy', 
      'prodApprovedBy', 
      'drPreparedBy', 
      'drApprovedBy',
      'signedBy',
      'acctApprovedBy'
    ])->findOrFail($id);

    $deliveryReceipt = \App\Models\DeliveryReceipt::with('items')->where('so_id', $order->id)->latest()->first();

    return view('admin-finance.accounting.print-dr', [
      'order' => $order,
      'deliveryReceipt' => $deliveryReceipt
    ]);
  }

  public function revertSalesInvoiceToDR($id)
  {
    try {
      $order = \App\Models\SalesOrder::findOrFail($id);

      $isFromDR = $order->status === 'si_created'
          || !empty($order->dr_prepared_by)
          || !empty($order->dr_prepared_at)
          || !empty($order->dr_approved_by)
          || in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment']);

      if (!$isFromDR) {
          return redirect()->back()->with('error', "Sales Order #{$order->so_number} did not originate from Delivery Receipts (DR).");
      }

      // Determine where it should return:
      // If DR was already prepared (dr_prepared_at, dr_prepared_by, or dr_approved_by is set):
      // return to 'ready_for_delivery' (Completed DRs)
      // If DR was NOT yet prepared (still pending preparation):
      // return to 'pending_dr_prep' (Pending DR Prep)
      $hasPreparedDR = !empty($order->dr_prepared_at) || !empty($order->dr_prepared_by) || !empty($order->dr_approved_by);

      $targetStatus = $hasPreparedDR ? 'ready_for_delivery' : 'pending_dr_prep';
      $targetLocation = $hasPreparedDR ? 'Completed DRs' : 'Pending DR Prep';

      // 1. Clean up any unapproved / pending SalesInvoice and items
      $pendingSIs = \App\Models\SalesInvoice::where('so_id', $order->id)->where('status', '!=', 'approved')->get();
      foreach ($pendingSIs as $si) {
          $si->items()->delete();
          $si->delete();
      }

      // 2. Revert Sales Order status
      $order->update([
          'status'         => $targetStatus,
          'si_prepared_by' => null,
          'si_prepared_at' => null,
          'remarks'        => ($order->remarks ? $order->remarks . ' | ' : '') . 'Reverted back to DR by ' . (auth()->user()->name ?? 'User')
      ]);

      // 3. Log activity
      \App\Models\ActivityLog::create([
          'user_id'           => auth()->id(),
          'action'            => 'Reverted to DR',
          'description'       => "Sales Order {$order->so_number} reverted from Sales Invoice back to Delivery Receipt ({$targetLocation}).",
          'affected_model'    => 'SalesOrder',
          'affected_model_id' => $order->id,
          'ip_address'        => request()->ip(),
          'user_agent'        => request()->header('User-Agent')
      ]);

      return redirect()
          ->back()
          ->with('success', "Sales Order #{$order->so_number} has been reverted back to Delivery Receipts ({$targetLocation}) successfully.");
    } catch (\Exception $e) {
      \Log::error('Error reverting Sales Order to DR: ' . $e->getMessage());
      return redirect()->back()->with('error', 'Error reverting to DR: ' . $e->getMessage());
    }
  }

  public function bulkPrintSalesInvoice(\Illuminate\Http\Request $request)
  {
    $ids = $request->query('ids');
    if (!$ids) {
      return redirect()->back()->with('error', 'No orders selected for printing.');
    }

    $idsArray = explode(',', $ids);
    $orders = \App\Models\SalesOrder::with(['customer', 'items.book', 'items.bundle', 'items.product', 'preparedBy', 'mktApprovedBy', 'prodApprovedBy', 'siPreparedBy', 'signedBy'])
      ->whereIn('id', $idsArray)
      ->get();

    return view('admin-finance.accounting.bulk-print-si', [
      'orders' => $orders
    ]);
  }

  public function materialsRequisition()
  {
    $requisitions = \App\Models\AdminFinance\MaterialRequisition::with('user')->orderBy('created_at', 'desc')->get();

    return view('admin-finance.accounting.materials-requisition', [
      'title' => 'Materials/Supplies Requisition',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'requisitions' => $requisitions
    ]);
  }

  public function storeMaterialRequisition(Request $request)
  {
      $request->validate([
          'date' => 'required|date',
          'department' => 'required|string',
          'items' => 'required|array|min:1',
          'items.*.description' => 'required|string',
          'items.*.qty' => 'required|numeric|min:0'
      ]);

      try {
          \DB::beginTransaction();

          // Generate Requisition No.
          $latest = \App\Models\AdminFinance\MaterialRequisition::latest('id')->first();
          $nextId = $latest ? $latest->id + 1 : 1;
          $reqNo = 'REQ-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

          $requisition = \App\Models\AdminFinance\MaterialRequisition::create([
              'requisition_no' => $reqNo,
              'date' => $request->date,
              'department' => $request->department,
              'supplier' => $request->supplier,
              'po_number' => $request->po_number,
              'requested_by' => auth()->id(),
              'status' => 'pending'
          ]);

          foreach ($request->items as $item) {
              \App\Models\AdminFinance\MaterialRequisitionItem::create([
                  'material_requisition_id' => $requisition->id,
                  'qty' => $item['qty'],
                  'unit' => $item['unit'],
                  'description' => $item['description'],
                  'supplier1_price' => $item['supplier1_price'] ?? null,
                  'supplier2_price' => $item['supplier2_price'] ?? null,
                  'supplier3_price' => $item['supplier3_price'] ?? null,
              ]);
          }

          \DB::commit();

          return response()->json([
              'success' => true,
              'message' => "Requisition $reqNo has been successfully recorded.",
              'requisition' => [
                  'id' => $requisition->id,
                  'requisition_no' => $reqNo,
                  'date' => $request->date,
                  'requested_by' => auth()->user()->name,
                  'department' => $request->department,
                  'po_number' => $request->po_number,
                  'status' => 'pending'
              ]
          ]);

      } catch (\Exception $e) {
          \DB::rollBack();
          return response()->json([
              'success' => false,
              'message' => 'An error occurred while saving the requisition: ' . $e->getMessage()
          ], 500);
      }
  }

  public function showMaterialRequisition($id)
  {
      $requisition = \App\Models\AdminFinance\MaterialRequisition::with(['items', 'user'])->findOrFail($id);
      return response()->json([
          'success' => true,
          'requisition' => $requisition
      ]);
  }

  public function destroyMaterialRequisition($id)
  {
      try {
          $requisition = \App\Models\AdminFinance\MaterialRequisition::findOrFail($id);
          $requisition->delete();
          
          return response()->json([
              'success' => true,
              'message' => 'Material Requisition deleted successfully.'
          ]);
      } catch (\Exception $e) {
          return response()->json([
              'success' => false,
              'message' => 'An error occurred while deleting the requisition: ' . $e->getMessage()
          ], 500);
      }
  }

  public function materialRequestsIncoming(Request $request)
  {
    $allowedStatuses = ['forwarded to accounting', 'processing', 'received'];
    $query = MaterialReq::with(['user', 'manager', 'director']);

    // Apply Filters for "All Requests" tab and general view
    if ($request->filled('status')) {
        $status = $request->status;
        if ($status === 'pending') $status = 'forwarded to accounting';
        elseif ($status === 'completed') $status = 'received';
        $query->where('status', $status);
    } else {
        $query->whereIn('status', $allowedStatuses);
    }

    if ($request->filled('department')) {
        $dept = $request->department;
        $query->whereHas('user', function($q) use ($dept) {
            $q->where('department', 'like', '%' . $dept . '%');
        });
    }

    if ($request->filled('requested_by')) {
        $name = $request->requested_by;
        $query->where(function($q) use ($name) {
            $q->where('requested_by', 'like', '%' . $name . '%')
              ->orWhereHas('user', function($sq) use ($name) {
                  $sq->where('name', 'like', '%' . $name . '%');
              });
        });
    }

    if ($request->filled('date_range')) {
        $range = explode(' - ', $request->date_range);
        if (count($range) == 2) {
            $start = \Carbon\Carbon::parse($range[0])->startOfDay();
            $end = \Carbon\Carbon::parse($range[1])->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }
    }

    $allRequests = $query->latest()->get();
    
    // Maintain separate collections for the status-specific tabs (unfiltered by status but filtered by other criteria if desired)
    // For now, I'll keep the tabs consistent with the "All" view if filters are applied, 
    // OR just keep them as they were if the user expects them to always show "Pending Action" regardless of global filters.
    // Usually, global filters apply to the main view.
    
    // Exclude MIS and GSD module requests from the accounting approver queue
    $pendingRequests = MaterialReq::with(['user', 'manager', 'director'])
      ->where('status', 'forwarded to accounting')
      ->whereNotIn('module', ['MIS', 'GSD'])
      ->latest()->get();
    $processingRequests = MaterialReq::with(['user', 'manager', 'director'])->where('status', 'processing')->latest()->get();
    $completedRequests = MaterialReq::with(['user', 'manager', 'director'])->where('status', 'received')->latest()->get();

    // Get requests by department/division
    $directRequests = MaterialReq::with(['user', 'manager', 'director'])
        ->whereIn('status', $allowedStatuses)
        ->where(function($q) {
            $q->where('module', 'Direct')
              ->orWhereHas('user', function($sq) {
                  $sq->where('department', 'like', '%direct%');
              });
        })
        ->latest()->get();

    $gsdRequests = MaterialReq::with(['user', 'manager', 'director'])
        ->whereIn('status', $allowedStatuses)
        ->where(function($q) {
            $q->where('module', 'GSD')
              ->orWhereHas('user', function($sq) {
                  $sq->where('department', 'like', '%gsd%');
              });
        })
        ->latest()->get();

    $misRequests = MaterialReq::with(['user', 'manager', 'director'])
        ->whereIn('status', $allowedStatuses)
        ->where(function($q) {
            $q->where('module', 'MIS')
              ->orWhereHas('user', function($sq) {
                  $sq->where('department', 'like', '%mis%');
              });
        })
        ->latest()->get();

    return view('admin-finance.accounting.material-requests', [
      'title' => 'Material Requests',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'pendingRequests' => $pendingRequests,
      'processingRequests' => $processingRequests,
      'completedRequests' => $completedRequests,
      'allRequests' => $allRequests,
      'directRequests' => $directRequests,
      'gsdRequests' => $gsdRequests,
      'misRequests' => $misRequests
    ]);
  }

  public function expenseManagement()
  {
    return view('admin-finance.accounting.expense-management', [
      'title' => 'Expense Management',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance'
    ]);
  }

  public function createCashAdvance()
  {
    return view('admin-finance.accounting.cash-advance.cash-advance', [
      'title' => 'Cash Advance',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance'
    ]);
  }

  public function cashAdvanceLiquidation()
  {
    return view('admin-finance.expenses.cash-advance-liquidation', [
      'title' => 'Cash Advance Liquidation',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance'
    ]);
  }

  public function storeLiquidation(Request $request)
  {
    $request->validate([
      'employee_name' => 'required',
      'date' => 'required|date',
      'amount_advanced' => 'required|numeric',
      'total_expenses' => 'required|numeric',
      'expenses' => 'required|array',
    ]);

    // --- ACCOUNTING INTEGRATION ---
    $this->accounting->postLiquidationEntry([
      'date' => $request->date,
      'employee_name' => $request->employee_name,
      'reference' => $request->reference ?? 'CA-LIQ',
      'amount_liquidated' => $request->total_expenses, // Credit Receivable for the spent amount
      'expenses' => $request->expenses
    ]);

    return redirect()->back()->with('success', 'Liquidation for ' . $request->employee_name . ' has been recorded and expenses posted.');
  }

  public function billing()
  {
      $unpaidOrders = \App\Models\SalesOrder::with(['customer', 'payments'])
          ->whereNull('statement_of_account_id')
          ->where('type', '!=', 'complimentary')
          ->latest()
          ->get();

      $statements = \App\Models\StatementOfAccount::with('customer')->latest()->get();
      $freightBills = \App\Models\FreightBill::with('customer')->latest()->get();
      
      $jvRequests = \App\Models\JournalVoucherRequest::with(['requestor', 'items'])->latest()->get();
      $reconsignmentRequests = \App\Models\SalesOrder::with('customer')
          ->where('status', 'reconsignment_pending')
          ->latest()
          ->get();

      return view('admin-finance.credit-collection.billing', [
          'title' => 'Billing',
          'role' => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'unpaidOrders' => $unpaidOrders,
          'statements' => $statements,
          'freightBills' => $freightBills,
          'jvRequests' => $jvRequests,
          'reconsignmentRequests' => $reconsignmentRequests
      ]);
  }

  public function reconsignmentsList()
  {
      $reconsignmentRequests = \App\Models\SalesOrder::with(['customer', 'items.book', 'preparedBy'])
          ->where('status', 'reconsignment_pending')
          ->latest()
          ->get();

      return view('admin-finance.credit-collection.reconsignments', [
          'title' => 'Reconsignments',
          'role' => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'reconsignmentRequests' => $reconsignmentRequests
      ]);
  }

  public function approveReconsignment(Request $request, $id)
  {
      $order = \App\Models\SalesOrder::with(['items.book', 'customer'])->findOrFail($id);
      
      if ($order->status !== 'reconsignment_pending') {
          return redirect()->back()->with('error', 'Order is not in pending reconsignment status.');
      }

      // 1. Calculate return/reconsignment books for this request (remaining = sent_qty - picked_qty)
      $returnedBooks = [];
      foreach ($order->items as $item) {
          $alreadyPurchasedQty = \App\Models\SalesInvoiceItem::whereHas('invoice', function($query) use ($order) {
              $query->where('so_id', $order->id)->where('status', '!=', 'cancelled');
          })->where('book_id', $item->book_id)->sum('quantity');
          
          $pickedQty = max($alreadyPurchasedQty, (int)($item->customer_selected_qty ?? 0));
          $returnedQty = max(0, $item->quantity - $pickedQty);
          if ($returnedQty > 0) {
              $returnedBooks[] = [
                  'book_id' => $item->book_id,
                  'quantity' => $returnedQty,
                  'price' => $item->price,
                  'unit' => $item->unit ?? 'pcs',
                  'area' => $item->area ?? null,
                  'source_price_at_sale' => $item->source_price_at_sale ?? 0,
                  'book_name' => $item->book->name ?? 'Unknown Book'
              ];
          }
      }

      if (empty($returnedBooks)) {
          return redirect()->back()->with('error', 'No return books found for this reconsignment request.');
      }

      // Start Database Transaction
      \DB::beginTransaction();
      try {
          // 2. Generate a new unique SO number
          $baseSoNumber = $order->so_number;
          $newSoNumber = $baseSoNumber . '-R';
          $suffix = 1;
          while (\App\Models\SalesOrder::where('so_number', $newSoNumber)->exists()) {
              $newSoNumber = $baseSoNumber . '-R' . $suffix;
              $suffix++;
          }

          // 3. Create the new Sales Order (starts from the beginning of the approval cycle with no customer assigned for new cycle)
          $soData = [
              'customer_id' => null,
              'area_sales_staff_id' => $order->area_sales_staff_id,
              'so_number' => $newSoNumber,
              'type' => $order->type, // 'area_consignment'
              'terms' => $request->input('terms', $order->terms),
              'ref_number' => $order->ref_number,
              'status' => 'pending_mkt_approval', // needs marketing approval first
              'billing_address' => $order->billing_address,
              'shipping_address' => $order->shipping_address,
              'freight_option' => $order->freight_option,
              'freight_charges' => $order->freight_charges,
              'freight_notes' => $order->freight_notes,
              'prepared_by' => auth()->id(),
              'total_amount' => 0 // will update after items creation
          ];
          if (\Illuminate\Support\Facades\Schema::hasColumn('sales_orders', 'transaction_type')) {
              $soData['transaction_type'] = $order->transaction_type;
          }
          $newOrder = \App\Models\SalesOrder::create($soData);

          $newTotalAmount = 0;
          // 4. Create the new Sales Order Items and return items to stock
          foreach ($returnedBooks as $bookData) {
              $subtotal = $bookData['quantity'] * $bookData['price'];
              $newTotalAmount += $subtotal;

              \App\Models\SalesOrderItem::create([
                  'sales_order_id' => $newOrder->id,
                  'book_id' => $bookData['book_id'],
                  'quantity' => $bookData['quantity'],
                  'price' => $bookData['price'],
                  'subtotal' => $subtotal,
                  'unit' => $bookData['unit'],
                  'area' => $bookData['area'],
                  'source_price_at_sale' => $bookData['source_price_at_sale'],
              ]);

              // Increment book stock for returned/reconsigned items
              $book = \App\Models\Book::find($bookData['book_id']);
              if ($book) {
                  $book->stock += $bookData['quantity'];
                  $book->save();
                  
                  // Record Inventory Transaction
                  \App\Models\InventoryTransaction::create([
                      'book_id' => $book->id,
                      'type' => 'in',
                      'quantity' => $bookData['quantity'],
                      'location' => 'Main Warehouse',
                      'source' => 'Consignment Return',
                      'reference_number' => $order->so_number,
                      'unit_cost' => $book->cost ?? 0,
                      'total_cost' => $bookData['quantity'] * ($book->cost ?? 0),
                      'notes' => 'Returned from Area Consignment Sales Order #' . $order->so_number . ' via Reconsignment Approval',
                      'status' => 'completed',
                      'transaction_date' => now(),
                      'user_id' => auth()->id()
                  ]);
              }
          }

          // Update new Sales Order total amount and deduct stock
          $newOrder->update(['total_amount' => $newTotalAmount]);
          \App\Services\StockDeductionService::deductForSalesOrder($newOrder);

          // 5. Find the previous Delivery Receipt and close it
          $previousDr = \App\Models\DeliveryReceipt::where('so_id', $order->id)->first();
          if ($previousDr) {
              $previousDr->update(['status' => 'completed']);
          }

          // 6. Close the previous Sales Order
          $order->update(['status' => 'completed']);

          // 7. Log activity
          \App\Models\ActivityLog::create([
              'user_id' => auth()->id(),
              'action' => 'Reconsignment Approved',
              'description' => "Reconsignment request approved for Sales Order {$order->so_number}. Created new Sales Order {$newOrder->so_number} for returned books.",
              'affected_model' => 'SalesOrder',
              'affected_model_id' => $order->id,
          ]);

          \DB::commit();
          return redirect()->back()->with('success', "Reconsignment request approved. New Sales Order {$newOrder->so_number} created successfully and is now pending approval.");

      } catch (\Exception $e) {
          \DB::rollBack();
          \Log::error('Error approving reconsignment: ' . $e->getMessage());
          return redirect()->back()->with('error', 'Error approving reconsignment: ' . $e->getMessage());
      }
  }

  public function rejectReconsignment($id)
  {
      $order = \App\Models\SalesOrder::findOrFail($id);
      
      if ($order->status !== 'reconsignment_pending') {
          return redirect()->back()->with('error', 'Order is not in pending reconsignment status.');
      }

      // Reject: set back to si_created (meaning closed)
      $order->update(['status' => 'si_created']);

      // Log activity
      \App\Models\ActivityLog::create([
          'user_id' => auth()->id(),
          'action' => 'Reconsignment Rejected',
          'description' => "Reconsignment request rejected for Sales Order {$order->so_number}.",
          'affected_model' => 'SalesOrder',
          'affected_model_id' => $order->id,
      ]);

      return redirect()->back()->with('success', 'Reconsignment request rejected.');
  }

  public function showAccountStatement($id)
  {
    $soa = \App\Models\StatementOfAccount::with(['customer', 'items', 'salesOrders'])->findOrFail($id);
    return view('admin-finance.credit-collection.billing-show', [
      'title' => 'Statement Detail',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'soa' => $soa
    ]);
  }

  public function createAccountStatement($id)
  {
    $order = \App\Models\SalesOrder::with('customer')->findOrFail($id);
    return view('admin-finance.credit-collection.billing-create', [
      'title' => 'Prepare Statement',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'order' => $order,
      'mode' => 'create'
    ]);
  }

  public function createManualSOA()
  {
    $customers = \App\Models\Customer::orderBy('customer_name')->get();
    return view('admin-finance.credit-collection.billing-create', [
      'title' => 'Add SOA',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'customers' => $customers,
      'mode' => 'manual'
    ]);
  }

  public function editAccountStatement($id)
  {
    $soa = \App\Models\StatementOfAccount::with(['customer', 'salesOrders'])->findOrFail($id);
    return view('admin-finance.credit-collection.billing-create', [
      'title' => 'Edit Statement',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'soa' => $soa,
      'mode' => 'edit'
    ]);
  }

  public function createFreightBill()
  {
    return view('admin-finance.credit-collection.freight-billing-create', [
      'title' => 'Create New Freight Bill',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'customers' => \App\Models\Customer::all()
    ]);
  }

  public function storeAccountStatement(Request $request)
  {
      $request->validate([
          'soa_number' => 'required|unique:statement_of_accounts,soa_number',
          'customer_id' => 'required',
          'billing_period_start' => 'required|date',
          'billing_period_end' => 'required|date',
          'total_amount' => 'required|numeric',
          'sales_order_ids' => 'nullable|array',
          'items' => 'required|array',
          'status' => 'required'
      ]);

      $soa = \App\Models\StatementOfAccount::create([
          'soa_number' => $request->soa_number,
          'customer_id' => $request->customer_id,
          'contact_person' => $request->contact_person,
          'billing_address' => $request->billing_address,
          'billing_period_start' => $request->billing_period_start,
          'billing_period_end' => $request->billing_period_end,
          'total_amount' => $request->total_amount,
          'status' => $request->status
      ]);

      foreach ($request->items as $item) {
          \App\Models\StatementOfAccountItem::create([
              'statement_of_account_id' => $soa->id,
              'service' => $item['service'] ?? '',
              'description' => $item['description'] ?? '',
              'qty' => $item['qty'] ?? 1,
              'price' => $item['price'] ?? 0
          ]);
      }

      if (!empty($request->sales_order_ids)) {
          \App\Models\SalesOrder::whereIn('id', $request->sales_order_ids)
              ->update(['statement_of_account_id' => $soa->id]);
      }

      return redirect()->route('admin-finance.credit-collection.billing')->with('success', 'Statement of Account created successfully.');
  }

  public function storeFreightBill(Request $request)
  {
      $request->validate([
          'bill_number' => 'required|unique:freight_bills,bill_number',
          'customer_id' => 'required',
          'bill_date' => 'required|date',
          'carrier' => 'required',
          'amount' => 'required|numeric'
      ]);

      \App\Models\FreightBill::create([
          'bill_number' => $request->bill_number,
          'customer_id' => $request->customer_id,
          'bill_date' => $request->bill_date,
          'carrier' => $request->carrier,
          'amount' => $request->amount,
          'status' => 'draft'
      ]);

      return redirect()->route('admin-finance.credit-collection.billing')->with('success', 'Freight Bill created successfully.');
  }

  public function updateStatementStatus($id, Request $request)
  {
      $request->validate(['status' => 'required']);
      $soa = \App\Models\StatementOfAccount::findOrFail($id);
      $soa->update(['status' => $request->status]);

      return response()->json(['success' => true]);
  }

  public function updateFreightBillStatus($id, Request $request)
  {
      $request->validate(['status' => 'required']);
      $fb = \App\Models\FreightBill::findOrFail($id);
      $fb->update(['status' => $request->status]);

      return response()->json(['success' => true]);
  }

  public function destroyFreightBill($id)
  {
      $fb = \App\Models\FreightBill::findOrFail($id);
      $fb->delete();

      return response()->json(['success' => true]);
  }

  public function compileStatements(Request $request)
  {
      $ids = $request->input('ids');
      $date = $request->input('date') ? \Carbon\Carbon::parse($request->input('date'))->startOfDay() : now();
      \App\Models\StatementOfAccount::whereIn('id', $ids)->update([
          'status' => 'compiled',
          'created_at' => $date
      ]);
      return response()->json(['success' => true]);
  }

  public function compileFreightBills(Request $request)
  {
      $ids = $request->input('ids');
      $date = $request->input('date') ? \Carbon\Carbon::parse($request->input('date'))->startOfDay() : now();
      \App\Models\FreightBill::whereIn('id', $ids)->update([
          'status' => 'compiled',
          'bill_date' => $date,
          'created_at' => $date
      ]);
      return response()->json(['success' => true]);
  }

  public function reports(Request $request)
  {
      // --- 1. AR Aging Summary ---
      $arFilterGroup = $request->input('ar_group', 'customer_type');
      $arFilterValue = $request->input('ar_value', 'Team A');
      $arAsOfDate    = $request->input('ar_date', now()->toDateString());

      $arQuery = \App\Models\Customer::with(['salesOrders' => function($q) use ($arAsOfDate) {
          $q->where('payment_status', 'unpaid')
            ->where('status', '!=', 'cancelled')
            ->whereDate('created_at', '<=', $arAsOfDate);
      }]);

      if ($arFilterGroup == 'class') {
          $arQuery->where('class', $arFilterValue);
      } else {
          $arQuery->where('customer_type', $arFilterValue);
      }

      $arCustomers = $arQuery->get();
      $arTotalOpenBalance = 0;

      foreach ($arCustomers as $customer) {
          $customer->current = 0;
          $customer->days_1_30 = 0;
          $customer->days_31_60 = 0;
          $customer->days_61_90 = 0;
          $customer->over_90 = 0;
          $customer->total_ar = 0;

          foreach ($customer->salesOrders as $order) {
              $daysOld = \Carbon\Carbon::parse($order->created_at)->startOfDay()->diffInDays(\Carbon\Carbon::parse($arAsOfDate)->startOfDay());
              $amount = $order->total_amount;

              if ($daysOld <= 30) {
                  $customer->current += $amount;
              } elseif ($daysOld <= 60) {
                  $customer->days_1_30 += $amount;
              } elseif ($daysOld <= 90) {
                  $customer->days_31_60 += $amount;
              } elseif ($daysOld <= 120) {
                  $customer->days_61_90 += $amount;
              } else {
                  $customer->over_90 += $amount;
              }
              $customer->total_ar += $amount;
          }
          $arTotalOpenBalance += $customer->total_ar;
      }
      
      // Filter out customers with 0 balance
      $arCustomers = $arCustomers->filter(function($cust) {
          return $cust->total_ar > 0;
      });


      // --- 2. Sales by Customer Summary ---
      $salesType  = $request->input('sales_type', 'Team A');
      $salesStart = $request->input('sales_start', now()->startOfMonth()->toDateString());
      $salesEnd   = $request->input('sales_end', now()->endOfMonth()->toDateString());

      $salesOrders = \App\Models\SalesOrder::with(['customer', 'items.product'])
          ->whereHas('customer', function($q) use ($salesType) {
              $q->where('customer_type', $salesType);
          })
          ->whereBetween('created_at', [$salesStart . ' 00:00:00', $salesEnd . ' 23:59:59'])
          ->where('status', '!=', 'cancelled')
          ->get();

      foreach ($salesOrders as $order) {
          $freightAmount = 0;
          foreach ($order->items as $item) {
              $prodName = $item->product ? strtolower($item->product->title ?? $item->product->product_name ?? '') : strtolower($item->product_name ?? '');
              if (str_contains($prodName, 'delivery') || str_contains($prodName, 'freight') || str_contains($prodName, 'shipping')) {
                  $freightAmount += ($item->qty ?? $item->quantity ?? 1) * ($item->unit_price ?? $item->price ?? 0);
              }
          }
          $order->calculated_freight = $freightAmount;
          $order->net_sales = $order->total_amount - $order->tax_amount - $freightAmount;
      }


      // --- 3. Collection Report ---
      $collGroup = $request->input('coll_group', 'Teams');
      $collStart = $request->input('coll_start', now()->startOfMonth()->toDateString());
      $collEnd   = $request->input('coll_end', now()->endOfMonth()->toDateString());

      // Because the "Payment" model was just created, there might not be data in it yet.
      // But we will query it regardless.
      $paymentsQuery = \App\Models\Payment::with(['customer', 'salesOrder'])
          ->whereBetween('payment_date', [$collStart, $collEnd]);

      if ($collGroup == 'Ecom') {
          $paymentsQuery->whereHas('salesOrder', function($q) {
              $q->whereIn('platform', ['ECOM POS', 'Lazada', 'Shopee', 'Tiktok']);
          });
      } elseif ($collGroup == 'Events') {
          $paymentsQuery->whereHas('salesOrder', function($q) {
              $q->whereIn('platform', ['Events', 'Ads and Promo', 'Catholic Directory']);
          });
      } elseif ($collGroup == 'Booksale') {
          $paymentsQuery->whereHas('salesOrder', function($q) {
              $q->whereIn('platform', ['direct POS', 'Bookchains', 'direct sales']);
          });
      } else {
          // Assume Teams, filter by customer type if passed
          $teamsVal = $request->input('coll_team_val', 'Team A');
          $paymentsQuery->whereHas('customer', function($q) use ($teamsVal) {
              $q->where('customer_type', $teamsVal);
          });
      }

      $collections = $paymentsQuery->get();

      return view('admin-finance.credit-collection.reports', [
          'title'   => 'Credit & Collection Reports',
          'role'    => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'arCustomers' => $arCustomers,
          'arTotalOpenBalance' => $arTotalOpenBalance,
          'salesOrders' => $salesOrders,
          'collections' => $collections,
          'filters' => [
              'arGroup' => $arFilterGroup, 'arVal' => $arFilterValue, 'arDate' => $arAsOfDate,
              'salesType' => $salesType, 'salesStart' => $salesStart, 'salesEnd' => $salesEnd,
              'collGroup' => $collGroup, 'collStart' => $collStart, 'collEnd' => $collEnd
          ]
      ]);
  }

  public function invoice()
  {
    $invoices = \App\Models\SalesOrder::with(['customer', 'items.product'])
      ->whereIn('status', ['ready_for_delivery', 'completed', 'verified', 'draft', 'pending_si_prep', 'pending_dr_prep', 'pending_dr_approval', 'si_created'])
      ->latest()
      ->get();

    $products = \App\Models\Book::where('is_active', true)->orderBy('name')->get();

    $bookSaleTeamStocks = \App\Models\TeamStock::whereIn('team_name', ['Book Sale', 'Book Sales', 'Book Sale Warehouse'])
        ->get()
        ->keyBy(function($item) {
            return $item->book_id ?: ($item->book_index_id ?: ($item->book_bundle_id ?: ''));
        });

    $products->transform(function ($book) use ($bookSaleTeamStocks) {
        $ts = $bookSaleTeamStocks->get($book->id);
        $book->stock_booksale = $ts ? (int) $ts->quantity : 0;
        $book->stock_main = (int) ($book->stock ?? 0);
        return $book;
    });

    return view('admin-finance.credit-collection.invoice.invoice', [
      'title' => 'Invoice',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'invoices' => $invoices,
      'products' => $products
    ]);
  }

  public function finalizeInvoice(Request $request, $id)
  {
    $order = \App\Models\SalesOrder::findOrFail($id);

    // Start database transaction
    \DB::beginTransaction();
    try {
      // 1. Update Sales Order status to 'completed' and payment_status to 'paid'
      $order->update([
        'status' => 'completed',
        'payment_status' => 'paid',
        'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'Invoice finalized by ' . auth()->user()->name
      ]);

      // 2. Post accounting journal entries using AccountingService
      $this->accounting->postSalesOrderEntry($order);

      \DB::commit();
      
      return response()->json([
        'success' => true,
        'message' => 'Sales Invoice finalized, customer balance updated, and posted to Charts of Accounts successfully.'
      ]);

    } catch (\Exception $e) {
      \DB::rollBack();
      \Log::error('Error finalizing invoice: ' . $e->getMessage());
      return response()->json([
        'success' => false,
        'message' => 'Error finalizing invoice: ' . $e->getMessage()
      ], 500);
    }
  }

  public function ecomPayoutsIndex(Request $request)
  {
    $query = \App\Models\SalesOrder::with(['customer', 'preparedBy'])
      ->where('type', 'ecom_direct')
      ->whereIn('status', ['picking', 'ready_for_delivery', 'completed', 'verified']);

    if ($request->filled('platform')) {
      $query->where('ecom_platform', $request->platform);
    }

    if ($request->filled('date_from')) {
      $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
      $query->whereDate('created_at', '<=', $request->date_to);
    }

    $orders = $query->latest()->get();

    $dbPlatforms = \App\Models\SalesOrder::where('type', 'ecom_direct')
      ->whereNotNull('ecom_platform')
      ->pluck('ecom_platform')
      ->toArray();

    $platforms = collect(['Shopee', 'Lazada', 'TikTok'])
      ->merge($dbPlatforms)
      ->filter()
      ->unique(function ($p) {
        return strtolower($p);
      })
      ->values();

    return view('admin-finance.accounting.ecom-payouts.index', [
      'title'     => 'E-com Direct Payouts',
      'role'      => auth()->user()->position ?? 'Finance Manager',
      'sidebar'   => 'admin-finance',
      'orders'    => $orders,
      'platforms' => $platforms,
      'filters'   => [
        'platform'  => $request->platform,
        'date_from' => $request->date_from,
        'date_to'   => $request->date_to,
      ],
    ]);
  }

  public function ecomPayoutsToggle(Request $request, $id)
  {
    $order = \App\Models\SalesOrder::findOrFail($id);

    if ($order->type !== 'ecom_direct') {
      return redirect()->back()->with('error', 'This is not an E-com direct invoice.');
    }

    $newStatus = $order->ecom_payout_status === 'completed' ? 'pending' : 'completed';
    $order->update([
      'ecom_payout_status' => $newStatus,
      'remarks' => ($order->remarks ? $order->remarks . ' | ' : '') . 'E-com payout marked as ' . $newStatus . ' by ' . auth()->user()->name
    ]);

    return redirect()->back()->with('success', 'Payout status for Invoice #' . $order->so_number . ' updated to ' . ucfirst($newStatus) . '.');
  }

  public function reviewSalesOrder($id)
  {
    $order = \App\Models\SalesOrder::with('customer', 'items.product', 'preparedBy')->findOrFail($id);

    return view('admin-finance.sales-orders.review', [
      'title' => 'Review Sales Order',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'order' => $order
    ]);
  }

  public function uploadSalesOrderAttachment(Request $request, $id)
  {
    $request->validate([
      'attachment_type' => 'required|string|in:proof_of_payment,pick_list_attachment,attachment,order_list_attachment',
      'attachment_file' => 'required|file|max:10240',
    ]);

    $order = \App\Models\SalesOrder::findOrFail($id);
    $type = $request->input('attachment_type');
    $file = $request->file('attachment_file');

    $folder = $type === 'proof_of_payment' ? 'sales_orders/proof_of_payments' : 'sales_orders/attachments';
    $path = $file->store($folder, 'public');

    $order->$type = $path;
    $order->save();

    return redirect()->back()->with('success', 'Attachment uploaded successfully.');
  }

  public function updatePaymentMethod(Request $request, $id)
  {
    $order = \App\Models\SalesOrder::findOrFail($id);
    $paymentMethod = strtolower($request->input('payment_method', 'cash'));
    $order->payment_method = $paymentMethod;
    $order->save();

    \App\Models\SalesInvoice::where('so_id', $order->id)->update(['payment_method' => $paymentMethod]);

    return response()->json(['success' => true, 'message' => 'Payment method updated to ' . strtoupper($paymentMethod)]);
  }

  public function approveSalesOrder(Request $request, $id)
  {
    $order = \App\Models\SalesOrder::with('items')->findOrFail($id);

    if ($order->type === 'ecom_direct') {
      return redirect()->back()->with('error', 'E-com direct invoices do not require accounting approval.');
    }
    
    \Log::info('Processing approval for SO #' . $order->so_number . ' with ' . $order->items->count() . ' items');
    

    // Determine if order belongs to Team A, B, or C (or user assigned to a sales team)
    $userTeam = null;
    if ($order->area_sales_staff_id) {
        $staff = \App\Models\User::find($order->area_sales_staff_id);
        if ($staff && !empty($staff->sales_team)) {
            $userTeam = trim($staff->sales_team);
        }
    }
    if (empty($userTeam) && $order->preparedBy && !empty($order->preparedBy->sales_team)) {
        $userTeam = trim($order->preparedBy->sales_team);
    }
    if (empty($userTeam) && auth()->check() && !empty(auth()->user()->sales_team)) {
        $userTeam = trim(auth()->user()->sales_team);
    }
    if (empty($userTeam) && (str_starts_with($order->preparedBy?->email ?? '', 'marketing_team') || str_starts_with($order->areaSalesStaff?->email ?? '', 'marketing_team'))) {
        $userTeam = 'Team ABC';
    }
    if (empty($userTeam) && ($order->customer && stripos($order->customer->customer_type ?? '', 'TEAM') !== false)) {
        $userTeam = trim($order->customer->customer_type);
    }
    if (empty($userTeam) && str_contains($order->remarks ?? '', '[SITE: Team')) {
        $userTeam = 'Team ABC';
    }

    $isTeamUser = !empty($userTeam);
    $isConsignment = in_array($order->type, ['area_consignment', 'area_sales_consignment', 'direct_consignment']) || $order->transaction_type === 'consignment';

    if ($isTeamUser) {
        // Team A, B, C: SO will NOT go through picklist and packing!
        $nextStatus = $isConsignment ? 'pending_dr_prep' : 'pending_si_prep';
    } else {
        $nextStatus = 'picking';
    }

    $updateData = [
      'status' => $nextStatus,
      'approved_by_acct' => auth()->id(),
      'acct_approved_at' => now()
    ];

    if ($request->filled('remarks')) {
      $userTitle = auth()->user()->name . ' (Admin/Finance)';
      $updateData['remarks'] = trim(($order->remarks ? $order->remarks . "\n" : '') . '[' . $userTitle . ']: ' . $request->remarks);
    }

    // Update Sales Order status
    $order->update($updateData);

    if ($isTeamUser) {
        $msg = $isConsignment 
            ? 'Sales Order #' . $order->so_number . ' approved! Directly routed to Delivery Receipt (DR) Preparation (Bypassed picklist & packing).'
            : 'Sales Order #' . $order->so_number . ' approved! Directly routed to Sales Invoice (SI) Preparation (Bypassed picklist & packing).';
        return redirect()->route('admin-finance.approval-queue')->with('success', $msg);
    }

    // Automatically create a pick list after accounting approval for non-team orders
    try {
      $order->load('items');
      // Check if SO has items
      if (!$order->items || $order->items->count() === 0) {
        \Log::warning('SO #' . $order->so_number . ' has NO items - cannot create pick list');
        return redirect()->route('admin-finance.approval-queue')->with('warning', 'Sales Order #' . $order->so_number . ' approved but has no items.');
      }

      $pickList = \App\Models\PickList::create([
        'sales_order_id' => $order->id,
        'pick_list_number' => 'PL-' . $order->so_number . '-' . date('YmdHis'),
        'status' => 'in_progress',
        'prepared_by' => auth()->id(),
      ]);

      \Log::info('Created pick list: ' . $pickList->pick_list_number);

      // Create pick list items from sales order items
      foreach ($order->items as $item) {
        \App\Models\PickListItem::create([
          'pick_list_id' => $pickList->id,
          'sales_order_item_id' => $item->id,
          'requested_qty' => $item->quantity,
          'picked_qty' => 0,
          'status' => 'pending',
        ]);
      }
      
      \Log::info('Successfully created pick list with ' . $order->items->count() . ' items');
      
    } catch (\Exception $e) {
      \Log::error('Failed to create pick list for SO #' . $order->so_number . ': ' . $e->getMessage());
    }

    return redirect()->route('admin-finance.approval-queue')->with('success', 'Sales Order #' . $order->so_number . ' has been approved and sent to Logistics for picking.');
  }

  public function rejectSalesOrder(Request $request, $id)
  {
    $order = \App\Models\SalesOrder::findOrFail($id);
    $userTitle = auth()->user()->name . ' (Admin/Finance Rejection)';
    $remarksText = $request->remarks ? $request->remarks : 'Rejected by Admin/Finance';
    $newRemarks = trim(($order->remarks ? $order->remarks . "\n" : '') . '[' . $userTitle . ']: ' . $remarksText);
    $order->update([
      'status' => 'cancelled',
      'remarks' => $newRemarks
    ]);

    \App\Services\StockDeductionService::restoreForSalesOrder($order, 'Admin/Finance Rejection');

    return redirect()->route('admin-finance.approval-queue')->with('warning', 'Sales Order #' . $order->so_number . ' has been rejected.');
  }

  public function jobOrders(Request $request)
  {
    $user = auth()->user();
    $pos = $user->position;
    $status = $request->query('status', 'all');
    if ($status === 'approved') {
        $status = 'on_hold';
    }
    
    if ($status === 'all') {
        $statuses = ['to submit', 'pending approval', 'Pending HR approval', 'Pending Final Approval', 'approved', 'ongoing', 'on_hold', 'completed', 'forwarded to accounting', 'processing', 'received'];
    } elseif ($status === 'on_hold') {
        $statuses = ['on_hold', 'approved'];
    } else {
        $statuses = [$status];
    }

    // --- CCTV Requests ---
    $cctvQuery = CCTVReq::query();
    if ($pos === 'Director') {
      $cctvQuery->where('status', '!=', 'Pending Final Approval');
    }
    $cctvRequests = $cctvQuery->whereIn('status', $statuses)->orderBy('created_at', 'desc')->get();
    $materialRequests = MaterialReq::where(function($q) {
        $q->where('module', 'MIS')
          ->orWhereHas('user', function($sq) {
              $sq->where('department', 'like', '%mis%');
          });
    })
    ->whereIn('status', $statuses)
    ->orderBy('created_at', 'desc')
    ->get();
    $qbRequests = MisQbRequest::with('items')->whereIn('status', $statuses)->orderBy('created_at', 'desc')->get();
    $undertimeRequests = MisUndertimeRequest::whereIn('status', $statuses)->orderBy('created_at', 'desc')->get();
    
    // Service Requests - Filter by MIS department
    $serviceRequests = MisServiceRequest::where('department', 'MIS')
        ->whereIn('status', $statuses)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('admin-finance.mis.job-orders', [
      'title' => 'Job Orders',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'cctvRequests' => $cctvRequests,
      'materialRequests' => $materialRequests,
      'qbRequests' => $qbRequests,
      'undertimeRequests' => $undertimeRequests,
      'serviceRequests' => $serviceRequests,
      'currentStatus' => $status
    ]);
  }

  public function misUpdateJobOrderStatus(Request $request, $type, $id)
  {
    $validated = $request->validate([
      'status' => 'required|in:on_hold,ongoing,completed'
    ]);

    if ($type === 'cctv') {
      $order = CCTVReq::findOrFail($id);
    } elseif ($type === 'material') {
      $order = MaterialReq::findOrFail($id);
    } elseif ($type === 'qb') {
      $order = MisQbRequest::findOrFail($id);
    } elseif ($type === 'undertime') {
      $order = MisUndertimeRequest::findOrFail($id);
    } elseif ($type === 'service') {
      $order = MisServiceRequest::findOrFail($id);
    } else {
      abort(404);
    }

    $data = ['status' => $validated['status']];

    if ($validated['status'] === 'completed') {
      $data['completed_by'] = auth()->id();
      $data['completed_at'] = now();
    }

    $order->update($data);

    return redirect()->back()->with('success', 'Job Order status updated to ' . ucfirst(str_replace('_', ' ', $validated['status'])) . '.');
  }

  public function hrJobOrders(Request $request)
  {
    $status = $request->query('status', 'all');
    
    if ($status === 'all') {
        $statuses = ['to submit', 'pending approval', 'Pending HR approval', 'Pending Final Approval', 'approved', 'ongoing', 'on_hold', 'completed'];
    } else {
        $statuses = [$status];
    }
    
    $cctvRequests = CCTVReq::whereIn('status', $statuses)->orderBy('created_at', 'desc')->get();
    
    // Service Requests - Filter by HR department
    $serviceRequests = MisServiceRequest::where('department', 'HR')
        ->whereIn('status', $statuses)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('admin-finance.hr.job-orders', [
      'title' => 'Job Orders',
      'role' => 'Finance Manager',
      'sidebar' => 'admin-finance',
      'cctvRequests' => $cctvRequests,
      'serviceRequests' => $serviceRequests,
      'currentStatus' => $status
    ]);
  }

  // GSD Job Orders Method (New)
  // GSD Job Orders Method (New)
  public function gsdJobOrders(Request $request)
  {
    $status = $request->query('status', 'all');
    
    if ($status === 'all') {
        $statuses = ['to submit', 'pending approval', 'Pending HR approval', 'Pending Final Approval', 'approved', 'ongoing', 'on_hold', 'completed', 'forwarded to accounting', 'processing', 'received'];
    } else {
        $statuses = [$status];
    }
    
    // Material Requests - apply status filter
    $materialRequests = MaterialReq::where(function($q) {
        $q->where('module', 'GSD')
          ->orWhereHas('user', function($sq) {
              $sq->where('department', 'like', '%gsd%');
          });
    })
    ->whereIn('status', $statuses)
    ->orderBy('created_at', 'desc')
    ->get();
    
    // Service Requests - Filter by GSD department
    $serviceRequests = MisServiceRequest::where(function ($query) {
            $query->where('module', 'GSD')
                ->orWhere('department', 'GSD');
        })
        ->with('approver')
        ->whereIn('status', $statuses)
        ->orderBy('updated_at', 'desc')
        ->get();

    return view('admin-finance.gsd.job-orders', [
      'title' => 'GSD Job Orders',
      'role' => auth()->user()->position,
      'sidebar' => 'admin-finance',
      'materialRequests' => $materialRequests,
      'serviceRequests' => $serviceRequests,
      'currentStatus' => $status
    ]);
  }

  // GSD Update Job Order Status (New)
  public function gsdUpdateJobOrderStatus(Request $request, $type, $id)
  {
      $validated = $request->validate([
          'status' => 'required|in:on_hold,ongoing,completed'
      ]);

      if ($type == 'material') {
          $order = MaterialReq::findOrFail($id);
      } elseif ($type == 'qb') {
          $order = MisQbRequest::findOrFail($id);
      } elseif ($type == 'undertime') {
          $order = MisUndertimeRequest::findOrFail($id);
      } elseif ($type == 'service') {
          $order = MisServiceRequest::findOrFail($id);
      } else {
          abort(404);
      }

      $data = ['status' => $validated['status']];
      
      if ($validated['status'] === 'completed') {
          $data['completed_by'] = auth()->id();
          $data['completed_at'] = now();
      }

      $order->update($data);

      return redirect()->back()->with('success', 'Job Order status updated to ' . ucfirst(str_replace('_', ' ', $validated['status'])) . '.');
  }

  public function jvRequests()
  {
      $requests = JournalVoucherRequest::with(['requestor', 'items'])->latest()->get();
      return view('admin-finance.credit-collection.jv-requests.index', [
          'title' => 'Journal Voucher Requests',
          'role' => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'requests' => $requests
      ]);
  }

  // Create Service Request Form
  public function createServiceRequest()
  {
      abort_unless(auth()->user()->hasPermission('admin_finance.service_requests'), 403);

      return view('admin-finance.service-requests.create', [
          'title' => 'Create Service Request',
          'role' => auth()->user()->position,
          'sidebar' => 'admin-finance'
      ]);
  }

  // Store Service Request
  public function storeServiceRequest(Request $request)
  {
      abort_unless(auth()->user()->hasPermission('admin_finance.service_requests'), 403);

      $validated = $request->validate([
          'department' => 'required|in:GSD,MIS,HR,DTO',
          'requestor_name' => 'required|string|max:255',
          'date' => 'required|date',
          'nature_of_request' => 'required|string'
      ]);

      MisServiceRequest::create([
          'user_id' => auth()->id(),
          'module' => $validated['department'] === 'GSD' ? 'GSD' : 'MIS',
          'requestor_name' => $validated['requestor_name'],
          'date' => $validated['date'],
          'nature_of_request' => $validated['nature_of_request'],
          'department' => $validated['department'],
          'status' => 'to submit'
      ]);

      $departmentRoutes = [
          'GSD' => 'admin-finance.gsd.job-orders',
          'MIS' => 'admin-finance.mis.job-orders',
          'HR' => 'admin-finance.hr.job-orders',
          'DTO' => 'production.dto.job-request-form',
      ];

      $user = auth()->user();
      $canAccessProduction = $user->isSuperAdmin()
          || $user->division === 'All Divisions'
          || str_contains($user->division ?? '', 'Production')
          || $user->divisions()->where('division', 'Production Division')->exists();

      $redirectRoute = $validated['department'] === 'DTO' && ! $canAccessProduction
          ? 'admin-finance.service-requests.create'
          : $departmentRoutes[$validated['department']];

      return redirect()->route($redirectRoute)
          ->with('success', 'Service Request created successfully! View it in the ' . $validated['department'] . ' job orders.');
  }

  public function createJvRequest()
  {
      return view('admin-finance.credit-collection.jv-requests.create', [
          'title' => 'New JV Request',
          'role' => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'customers' => \App\Models\Customer::all()
      ]);
  }

  public function storeJvRequest(Request $request)
  {
      $request->validate([
          'items' => 'required|array|min:1'
      ]);

      \DB::beginTransaction();
      try {
          $latest = JournalVoucherRequest::latest('id')->first();
          $nextNo = $latest ? (int)$latest->jv_number + 1 : 11082; 

          $jvRequest = JournalVoucherRequest::create([
              'jv_number' => (string)$nextNo,
              'date' => now(), 
              'requested_by' => auth()->id(),
              'reason' => $request->reason ?? null,
              'category' => $request->category ?? 'Account Statement',
              'documents' => 'Summary Report',
              'supporting_documents' => null,
              'status' => 'pending_accounting'
          ]);

          $total = 0;
          foreach ($request->items as $item) {
              JournalVoucherItem::create([
                  'jv_request_id' => $jvRequest->id,
                  'reference_no' => $item['reference_no'],
                  'customer_name' => $item['customer_name'],
                  'customer_id' => $item['customer_id'] ?? null,
                  'amount' => $item['amount'],
                  'remarks' => $item['remarks'] ?? 'QB Entry',
                  'type' => $item['type'] ?? 'item'
              ]);
              $total += $item['amount'];
          }

          $jvRequest->update(['total_amount' => $total]);

          \DB::commit();
          // Redirect back to Billing page and open JV Summary tab for a smoother UX
          return redirect()->route('admin-finance.credit-collection.billing', ['tab' => 'jv'])->with('success', "JV Request #$nextNo created successfully.");

      } catch (\Exception $e) {
          \DB::rollBack();
          return back()->with('error', 'Error creating JV Request: ' . $e->getMessage());
      }
  }

  public function showJvRequest($id)
  {
      $request = JournalVoucherRequest::with(['items', 'requestor', 'approver'])->findOrFail($id);
      return view('admin-finance.credit-collection.jv-requests.show', [
          'title' => 'JV Request Details',
          'role' => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'request' => $request
      ]);
  }

  public function approveJvRequest($id)
  {
      $request = JournalVoucherRequest::with('items')->findOrFail($id);
      $updateData = [
          // Mark as accounting-verified while keeping approval metadata
          'status' => 'accounting_verified',
          'approved_by' => auth()->id(),
          'accounting_remarks' => 'Verified by Accounting'
      ];
      if (\Illuminate\Support\Facades\Schema::hasColumn('journal_voucher_requests', 'approved_at')) {
          $updateData['approved_at'] = now();
      }
      $request->update($updateData);

      // Create and post Journal Entry from this JV Request
      try {
        $entry = $this->accounting->postJournalVoucherRequest($request);
        $msg = "Compilation #{$request->jv_number} verified by Accounting and posted as Journal Entry " . ($entry->entry_no ?? 'N/A') . ".";
      } catch (\Exception $e) {
        // Log exception and notify user, but don't break approval flow
        report($e);
        $msg = "Compilation #{$request->jv_number} approved by Accounting, but posting failed: " . $e->getMessage();
      }

      return redirect()->back()->with('success', $msg);
  }

  public function rejectJvRequest($id)
  {
      $request = JournalVoucherRequest::findOrFail($id);
      $updateData = [
          'status' => 'rejected',
      ];
      if (\Illuminate\Support\Facades\Schema::hasColumn('journal_voucher_requests', 'rejected_by')) {
          $updateData['rejected_by'] = auth()->id();
      }
      if (\Illuminate\Support\Facades\Schema::hasColumn('journal_voucher_requests', 'rejected_at')) {
          $updateData['rejected_at'] = now();
      }
      $request->update($updateData);

      return redirect()->back()->with('warning', "JV Request #{$request->jv_number} has been rejected.");
  }

  public function managerApproveJvRequest($id)
  {
      $jvRequest = JournalVoucherRequest::with('items')->findOrFail($id);
      
      $jvRequest->update([
          'status' => 'posted',
          'manager_approved_by' => auth()->id(),
          'manager_approved_at' => now(),
      ]);

      $entryNo = 'JV-' . $jvRequest->jv_number;
      $entry = \App\Models\JournalEntry::where('entry_no', $entryNo)->first();

      if (!$entry) {
          $entry = \App\Models\JournalEntry::create([
              'entry_no' => $entryNo,
              'entry_type' => 'JV',
              'date' => $jvRequest->date,
              'reference' => 'JV #' . $jvRequest->jv_number,
              'memo' => $jvRequest->reason ?? 'Journal Voucher Request Approval',
              'currency' => 'PHP',
              'exchange_rate' => 1.0,
              'created_by' => auth()->id(),
              'status' => 'posted'
          ]);

          $arAccount = \App\Models\ChartOfAccount::where('code', '1200')->first();
          if (!$arAccount) {
              $arAccount = \App\Models\ChartOfAccount::where('name', 'like', '%Receivable%')->first()
                  ?? \App\Models\ChartOfAccount::where('type', 'Asset')->first();
          }

          if ($arAccount) {
              foreach ($jvRequest->items as $item) {
                  \App\Models\JournalEntryItem::create([
                      'journal_entry_id' => $entry->id,
                      'chart_of_account_id' => $arAccount->id,
                      'memo' => $item->customer_name . ' - ' . $item->reference_no,
                      'debit' => $item->amount,
                      'credit' => 0,
                  ]);
              }
          }
      }

      return redirect()->route('admin-finance.credit-collection.billing')->with('success', 'JV Request approved and posted to General Journal.');
  }

  public function printJvRequest($id)
  {
      $request = JournalVoucherRequest::with(['items', 'requestor', 'approver'])->findOrFail($id);
      return view('admin-finance.credit-collection.jv-requests.print', [
          'request' => $request
      ]);
  }

  public function printSummaryRequest($id)
  {
      $request = JournalVoucherRequest::with(['items', 'requestor', 'approver'])->findOrFail($id);
      return view('admin-finance.credit-collection.jv-requests.print-summary', [
          'request' => $request
      ]);
  }

  public function prepareAdjustmentRequest($id)
  {
      $request = JournalVoucherRequest::findOrFail($id);
      return view('admin-finance.credit-collection.jv-requests.prepare-adjustment', [
          'title' => 'Prepare Adjustment Form',
          'role' => 'Finance Manager',
          'sidebar' => 'admin-finance',
          'request' => $request
      ]);
  }

  public function updateAdjustmentRequest(Request $request, $id)
  {
      $jvRequest = JournalVoucherRequest::findOrFail($id);

      $request->validate([
          'date' => 'required|date',
          'client_name' => 'nullable|string|max:255',
          'category' => 'required|string',
          'reason' => 'required|string',
          'documents' => 'required|string',
          'remarks' => 'nullable|string',
          'supporting_documents' => 'nullable|file|mimes:pdf,jpg,png,zip|max:5120'
      ]);

      $data = $request->only(['date', 'client_name', 'category', 'reason', 'documents']);
      $data['accounting_remarks'] = $request->remarks ?? null;

      // Preserve existing path by default
      $docPath = $jvRequest->supporting_documents;

      if ($request->hasFile('supporting_documents')) {
          // store new file
          $docPath = $request->file('supporting_documents')->store('jv_supporting', 'public');
      }

      // Handle explicit removal request from UI
      if ($request->has('remove_supporting') && $request->remove_supporting) {
          if ($jvRequest->supporting_documents) {
              $old = storage_path('app/public/' . $jvRequest->supporting_documents);
              if (file_exists($old)) {
                  @unlink($old);
              }
          }
          $docPath = null;
      }

      $data['supporting_documents'] = $docPath;
      $data['status'] = 'pending_manager_approval';
      $jvRequest->update($data);

      return redirect()->route('admin-finance.credit-collection.billing')->with('success', 'Adjustment request submitted for Manager approval.');
  }
    /**
     * Stream/download the supporting documents file stored in storage/app/public
     */
    public function downloadSupportingDocuments($id)
    {
        $jvRequest = JournalVoucherRequest::findOrFail($id);
        if (!$jvRequest->supporting_documents) {
            return redirect()->back()->with('error', 'No supporting documents available for download.');
        }

        $relative = $jvRequest->supporting_documents; // e.g. jv_supporting/xyz.pdf
        $path = storage_path('app/public/' . $relative);
        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Supporting document file not found on server.');
        }

        return response()->download($path, basename($path));
    }

    private function getAccountBalanceAndTrack($codeOrKeyword, $type, &$trackedIds)
    {
        $account = \App\Models\ChartOfAccount::where('code', $codeOrKeyword)
            ->orWhere('name', 'like', "%{$codeOrKeyword}%")
            ->first();

        if (!$account) {
            return 0.00;
        }

        $trackedIds[] = $account->id;

        $debitSum = $account->journalEntryItems()->sum('debit');
        $creditSum = $account->journalEntryItems()->sum('credit');

        if ($account->type === 'Asset' || $account->type === 'Expense') {
            return $debitSum - $creditSum;
        } else {
            return $creditSum - $debitSum;
        }
    }

    public function chartOfAccounts(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting.chart_of_accounts') && !$user->hasPermission('admin_finance.accounting')) {
            abort(403, 'Unauthorized action.');
        }

        $mainTab = $request->query('main_tab', 'crud');
        if (!in_array($mainTab, ['crud', 'cards', 'account_groups'])) {
            $mainTab = 'crud';
        }

        $tab = $request->query('tab', 'assets');
        if (!in_array($tab, ['assets', 'liabilities', 'equity', 'income', 'expenses'])) {
            $tab = 'assets';
        }

        // --- Fetch all accounts for CRUD Data Table ---
        $searchQuery = trim($request->query('search', ''));
        $categoryFilter = $request->query('category', '');

        $accountsQuery = \App\Models\ChartOfAccount::with('accountGroup');
        if (!empty($searchQuery)) {
            $accountsQuery->where(function($q) use ($searchQuery) {
                $q->where('code', 'like', "%{$searchQuery}%")
                  ->orWhere('name', 'like', "%{$searchQuery}%")
                  ->orWhere('category', 'like', "%{$searchQuery}%")
                  ->orWhere('type', 'like', "%{$searchQuery}%");
            });
        }
        if (!empty($categoryFilter)) {
            $accountsQuery->where('type', $categoryFilter);
        }
        $allAccounts = $accountsQuery->orderBy('code', 'asc')->paginate(10)->withQueryString();

        // --- Fetch unposted sales orders (POS, SO, E-Com, Area Sales, etc.) ---
        $postedOrderNumbers = \App\Models\JournalEntry::pluck('reference')->filter()->toArray();

        $unpostedOrders = \App\Models\SalesOrder::where('status', '!=', 'cancelled')
            ->whereNotIn('so_number', $postedOrderNumbers)
            ->get();

        $unpostedLazada = $unpostedOrders->where('ecom_platform', 'lazada')->sum('total_amount');
        $unpostedShopee = $unpostedOrders->where('ecom_platform', 'shopee')->sum('total_amount');
        $unpostedTiktok = $unpostedOrders->where('ecom_platform', 'tiktok')->sum('total_amount');
        $unpostedFacebook = $unpostedOrders->where('ecom_platform', 'facebook')->sum('total_amount');
        $unpostedCob = $unpostedOrders->filter(function($o) {
            return $o->type === 'website_direct' || in_array($o->ecom_platform, ['website', 'cob']);
        })->sum('total_amount');
        $unpostedExport = $unpostedOrders->where('transaction_subtype', 'foreign')->sum('total_amount');
        
        $unpostedPos = $unpostedOrders->where('type', 'calculator_pos')->sum('total_amount');
        $unpostedSo = $unpostedOrders->filter(function($o) {
            return !in_array($o->type, ['calculator_pos', 'area_consignment', 'area_sales_consignment', 'website_direct']) 
                && empty($o->ecom_platform)
                && $o->transaction_subtype !== 'foreign';
        })->sum('total_amount');
        $unpostedEcomDirect = $unpostedOrders->where('type', 'ecom_direct')->sum('total_amount');

        $unpostedCash = $unpostedOrders->filter(function($o) {
            return strtolower($o->payment_method ?? '') === 'cash' || empty($o->payment_method);
        })->sum('total_amount');

        $unpostedEwallet = $unpostedOrders->filter(function($o) {
            return in_array(strtolower($o->payment_method ?? ''), ['gcash', 'paymaya', 'qr_ph', 'ewallet', 'qrph']);
        })->sum('total_amount');

        $unpostedBank = $unpostedOrders->filter(function($o) {
            return in_array(strtolower($o->payment_method ?? ''), ['bank', 'check', 'bank_transfer', 'cheque']);
        })->sum('total_amount');

        $unpostedCard = $unpostedOrders->filter(function($o) {
            return in_array(strtolower($o->payment_method ?? ''), ['card', 'credit_card', 'debit_card']);
        })->sum('total_amount');

        $unpostedArea = $unpostedOrders->filter(function($o) {
            return in_array($o->type, ['area_consignment', 'area_sales_consignment']);
        })->sum('total_amount');
        
        $unpostedDirect = $unpostedOrders->filter(function($o) {
            return !in_array($o->ecom_platform, ['lazada', 'shopee', 'tiktok', 'facebook', 'website', 'cob'])
                && !in_array($o->type, ['area_consignment', 'area_sales_consignment', 'website_direct'])
                && $o->transaction_subtype !== 'foreign';
        })->sum('total_amount');
        
        $unpostedBookSales = $unpostedOrders->sum('total_amount');

        $trackedIds = [];

        $companyBankAccounts = \App\Models\CompanyBankAccount::latest()->get();
        if ($companyBankAccounts->isEmpty()) {
            \App\Models\CompanyBankAccount::create([
                'account_code' => '1000',
                'bank_name' => 'BDO Unibank',
                'account_name' => 'Claretian Communications Foundation Inc.',
                'account_number' => '0012-3456-7890',
                'account_type' => 'Checking',
                'currency' => 'PHP',
                'opening_balance' => 250000.00,
                'current_balance' => 250000.00,
                'status' => 'Active',
                'notes' => 'Primary operational & clearing account'
            ]);

            \App\Models\CompanyBankAccount::create([
                'account_code' => '1006',
                'bank_name' => 'BPI (Bank of the Philippine Islands)',
                'account_name' => 'CCFI Operating Account',
                'account_number' => '0987-6543-2100',
                'account_type' => 'Savings',
                'currency' => 'PHP',
                'opening_balance' => 150000.00,
                'current_balance' => 150000.00,
                'status' => 'Active',
                'notes' => 'Secondary operational account'
            ]);

            \App\Models\CompanyBankAccount::create([
                'account_code' => '1020',
                'bank_name' => 'GCash / Merchant E-Wallet',
                'account_name' => 'Claretian Digital Collections',
                'account_number' => '0917-888-9999',
                'account_type' => 'E-Wallet',
                'currency' => 'PHP',
                'opening_balance' => 50000.00,
                'current_balance' => 50000.00,
                'status' => 'Active',
                'notes' => 'Merchant collections & mobile QR'
            ]);
            $companyBankAccounts = \App\Models\CompanyBankAccount::latest()->get();
        }

        $bankBalances = [];
        foreach ($companyBankAccounts as $acct) {
            $bankBalances[$acct->account_code] = $this->getAccountBalanceAndTrack($acct->account_code, 'Asset', $trackedIds);
        }

        // Also track standard codes in case they are selected but not in company bank accounts table yet
        $this->getAccountBalanceAndTrack('1000', 'Asset', $trackedIds);
        $this->getAccountBalanceAndTrack('1006', 'Asset', $trackedIds);
        $this->getAccountBalanceAndTrack('1020', 'Asset', $trackedIds);
        $this->getAccountBalanceAndTrack('1025', 'Asset', $trackedIds);

        $accountDetails = \App\Models\ChartOfAccount::all()->keyBy('code');

        $balances = [
            // Assets
            'cash_on_hand' => ($this->getAccountBalanceAndTrack('Cash on Hand', 'Asset', $trackedIds) + $this->getAccountBalanceAndTrack('1040', 'Asset', $trackedIds)) + (\App\Models\SalesInvoice::sum('total_amount') + \App\Models\Payment::sum('amount') + \App\Models\SalesOrder::where('status', '!=', 'cancelled')->where(function($q) { $q->where('payment_status', 'paid')->orWhere(function($sub) { $sub->whereNotNull('proof_of_payment')->where('proof_of_payment', '!=', ''); })->orWhere('type', 'calculator_pos'); })->sum('total_amount')),
            'petty_cash' => $this->getAccountBalanceAndTrack('1015', 'Asset', $trackedIds) + \App\Models\PettyCashVoucher::withSum('items', 'amount')->get()->sum('items_sum_amount'),
            'bank_accounts' => 0.00,
            'bank_balances' => $bankBalances,
            'receivables' => $this->getAccountBalanceAndTrack('1200', 'Asset', $trackedIds) + (\App\Models\SalesOrder::where('status', '!=', 'cancelled')->where('payment_status', '!=', 'paid')->where(function($q) { $q->whereNull('proof_of_payment')->orWhere('proof_of_payment', ''); })->whereNotIn('type', ['calculator_pos', 'ecom_direct'])->sum('total_amount') + \App\Models\StatementOfAccount::where('status', '!=', 'paid')->sum('total_amount')),
            'inventory_raw_materials' => max(0, $this->getAccountBalanceAndTrack('1320', 'Asset', $trackedIds)),
            'inventory_work_in_progress' => max(0, $this->getAccountBalanceAndTrack('1330', 'Asset', $trackedIds)),
            'inventory_finished_goods' => $this->getAccountBalanceAndTrack('1300', 'Asset', $trackedIds) + \App\Models\Book::sum(\DB::raw('stock * cost')),
            'fixed_assets' => ($this->getAccountBalanceAndTrack('1600', 'Asset', $trackedIds) + $this->getAccountBalanceAndTrack('1050', 'Asset', $trackedIds)),
            'investments' => $this->getAccountBalanceAndTrack('1700', 'Asset', $trackedIds),
            'deposits' => ($this->getAccountBalanceAndTrack('1800', 'Asset', $trackedIds) + $this->getAccountBalanceAndTrack('1070', 'Asset', $trackedIds)),

            // Liabilities
            'suppliers' => $this->getAccountBalanceAndTrack('2000', 'Liability', $trackedIds) + \App\Models\PurchaseOrder::sum('total_amount'),
            'payables' => $this->getAccountBalanceAndTrack('2200', 'Liability', $trackedIds),
            'loans' => $this->getAccountBalanceAndTrack('2300', 'Liability', $trackedIds),
            'taxes' => $this->getAccountBalanceAndTrack('2100', 'Liability', $trackedIds),
            'government_contributions' => $this->getAccountBalanceAndTrack('2400', 'Liability', $trackedIds),
            'customer_deposits' => $this->getAccountBalanceAndTrack('2500', 'Liability', $trackedIds),
            'unearned_revenue' => $this->getAccountBalanceAndTrack('2600', 'Liability', $trackedIds),

            // Equity
            'capital' => $this->getAccountBalanceAndTrack('3100', 'Equity', $trackedIds),
            'retained_earnings' => $this->getAccountBalanceAndTrack('3000', 'Equity', $trackedIds),
            'current_year_income' => $this->getAccountBalanceAndTrack('3200', 'Equity', $trackedIds) + (\App\Models\SalesInvoice::sum('total_amount') + $unpostedBookSales),

            // Income (Publishing)
            'pub_book_sales' => $this->getAccountBalanceAndTrack('4000', 'Income', $trackedIds) + $unpostedBookSales,
            'pub_royalties' => $this->getAccountBalanceAndTrack('4020', 'Income', $trackedIds),
            'pub_rights_income' => $this->getAccountBalanceAndTrack('4030', 'Income', $trackedIds),
            'pub_licensing' => $this->getAccountBalanceAndTrack('4040', 'Income', $trackedIds),
            'pub_ebooks' => $this->getAccountBalanceAndTrack('4060', 'Income', $trackedIds),

            // Income (Printing)
            'print_income' => $this->getAccountBalanceAndTrack('4300', 'Income', $trackedIds),
            'print_layout' => $this->getAccountBalanceAndTrack('4310', 'Income', $trackedIds),
            'print_design' => $this->getAccountBalanceAndTrack('4320', 'Income', $trackedIds),
            'print_binding' => $this->getAccountBalanceAndTrack('4330', 'Income', $trackedIds),
            'print_lamination' => $this->getAccountBalanceAndTrack('4340', 'Income', $trackedIds),

            // Income (Marketing)
            'mkt_pos_sales' => $unpostedPos,
            'mkt_so_sales' => $unpostedSo,
            'mkt_ecom_direct' => $unpostedEcomDirect,
            'mkt_pay_cash' => $unpostedCash,
            'mkt_pay_ewallet' => $unpostedEwallet,
            'mkt_pay_bank' => $unpostedBank,
            'mkt_pay_card' => $unpostedCard,
            'mkt_direct_sales' => $this->getAccountBalanceAndTrack('4430', 'Income', $trackedIds) + $unpostedDirect,
            'mkt_area_sales' => $this->getAccountBalanceAndTrack('4440', 'Income', $trackedIds) + $unpostedArea,
            'mkt_cob_sales' => $this->getAccountBalanceAndTrack('4450', 'Income', $trackedIds) + $unpostedCob,
            'mkt_lazada' => $this->getAccountBalanceAndTrack('4460', 'Income', $trackedIds) + $unpostedLazada,
            'mkt_shopee' => $this->getAccountBalanceAndTrack('4470', 'Income', $trackedIds) + $unpostedShopee,
            'mkt_tiktok' => $this->getAccountBalanceAndTrack('4480', 'Income', $trackedIds) + $unpostedTiktok,
            'mkt_facebook' => $this->getAccountBalanceAndTrack('4490', 'Income', $trackedIds) + $unpostedFacebook,
            'mkt_wholesale' => $this->getAccountBalanceAndTrack('4500', 'Income', $trackedIds),
            'mkt_export' => $this->getAccountBalanceAndTrack('4510', 'Income', $trackedIds) + $unpostedExport,
            'mkt_claret_media' => $this->getAccountBalanceAndTrack('4520', 'Income', $trackedIds),

            // Income (Other)
            'oth_donations' => $this->getAccountBalanceAndTrack('4700', 'Income', $trackedIds),
            'oth_grants' => $this->getAccountBalanceAndTrack('4710', 'Income', $trackedIds),
            'oth_investments' => $this->getAccountBalanceAndTrack('4720', 'Income', $trackedIds),
            'oth_interest_income' => $this->getAccountBalanceAndTrack('4730', 'Income', $trackedIds),
            'oth_rental_income' => $this->getAccountBalanceAndTrack('4740', 'Income', $trackedIds),

            // Expenses
            'exp_fixed_assets' => $this->getAccountBalanceAndTrack('5510', 'Expense', $trackedIds) + (float) \App\Models\ProductionFixedAsset::sum('purchase_price'),
            'exp_supplies' => $this->getAccountBalanceAndTrack('5140', 'Expense', $trackedIds) + (float) \App\Models\OfficeSupply::sum(\DB::raw('item_price * items_stock')),
            'exp_operational' => $this->getAccountBalanceAndTrack('5105', 'Expense', $trackedIds) + \App\Models\Expense::sum('amount'),
            'exp_cogs' => $this->getAccountBalanceAndTrack('5010', 'Expense', $trackedIds) + $this->getAccountBalanceAndTrack('5020', 'Expense', $trackedIds) + $this->getAccountBalanceAndTrack('5030', 'Expense', $trackedIds) + $this->getAccountBalanceAndTrack('5040', 'Expense', $trackedIds) + $this->getAccountBalanceAndTrack('5050', 'Expense', $trackedIds) + $this->getAccountBalanceAndTrack('5060', 'Expense', $trackedIds) + $this->getAccountBalanceAndTrack('5070', 'Expense', $trackedIds) + $this->getAccountBalanceAndTrack('5080', 'Expense', $trackedIds),
            'exp_payroll' => $this->getAccountBalanceAndTrack('5110', 'Expense', $trackedIds) + $this->getAccountBalanceAndTrack('5120', 'Expense', $trackedIds),
            'exp_utilities' => $this->getAccountBalanceAndTrack('5130', 'Expense', $trackedIds) + $this->getAccountBalanceAndTrack('5520', 'Expense', $trackedIds),
            'exp_marketing' => $this->getAccountBalanceAndTrack('5210', 'Expense', $trackedIds) + $this->getAccountBalanceAndTrack('5220', 'Expense', $trackedIds) + $this->getAccountBalanceAndTrack('5230', 'Expense', $trackedIds) + $this->getAccountBalanceAndTrack('5240', 'Expense', $trackedIds),
            'exp_petty_cash' => \App\Models\PettyCashVoucher::withSum('items', 'amount')->get()->sum('items_sum_amount'),
        ];

        // Also fetch any dynamic/uncategorized accounts from the database that don't match our pre-defined names
        $typeFilter = '';
        if ($tab === 'assets') {
            $typeFilter = 'Asset';
        } elseif ($tab === 'liabilities') {
            $typeFilter = 'Liability';
        } elseif ($tab === 'equity') {
            $typeFilter = 'Equity';
        } elseif ($tab === 'income') {
            $typeFilter = 'Income';
        } elseif ($tab === 'expenses') {
            $typeFilter = 'Expense';
        }

        $uncategorizedAccounts = \App\Models\ChartOfAccount::where('type', $typeFilter)
            ->whereNotIn('id', $trackedIds)
            ->whereNull('account_group_id')
            ->get()
            ->map(function($acc) use ($typeFilter) {
                $debit = $acc->journalEntryItems()->sum('debit');
                $credit = $acc->journalEntryItems()->sum('credit');
                if ($typeFilter === 'Asset' || $typeFilter === 'Expense') {
                    $acc->balance = $debit - $credit;
                } else {
                    $acc->balance = $credit - $debit;
                }

                if (stripos($acc->name, 'supplies') !== false) {
                    $acc->balance = max($acc->balance, (float) \App\Models\OfficeSupply::sum(\DB::raw('item_price * items_stock')));
                }

                return $acc;
            });

        // Query operational transaction details for interactive modals
        $salesInvoices = \App\Models\SalesOrder::with('customer')
            ->where('status', '!=', 'cancelled')
            ->where(function($q) {
                $q->where('payment_status', 'paid')
                  ->orWhere(function($sub) {
                      $sub->whereNotNull('proof_of_payment')->where('proof_of_payment', '!=', '');
                  })
                  ->orWhere('type', 'calculator_pos');
            })
            ->latest()
            ->get()
            ->map(function($so) {
                return (object)[
                    'si_number' => $so->so_number,
                    'customer_name' => $so->customer->customer_name ?? ($so->customer->company_name ?? 'N/A'),
                    'total_amount' => $so->total_amount,
                    'status' => 'Paid',
                    'created_at' => $so->created_at,
                ];
            });

        // Fetch General Journal items hitting the Cash on Hand accounts
        $cashOnHandAccountIds = \App\Models\ChartOfAccount::where('name', 'like', '%Cash on Hand%')->orWhereIn('code', ['1010', '1040'])->pluck('id');
        $journalItems = \App\Models\JournalEntryItem::with(['journalEntry'])
            ->whereIn('chart_of_account_id', $cashOnHandAccountIds)
            ->whereHas('journalEntry', function($q) {
                $q->where('status', 'posted');
            })
            ->get()
            ->map(function($item) {
                // Asset account: Debit increases it (+), Credit decreases it (-)
                $amount = (float)$item->debit - (float)$item->credit;
                
                return (object)[
                    'si_number' => $item->journalEntry->entry_no,
                    'customer_name' => $item->memo ?? $item->journalEntry->reference ?? 'General Journal Entry',
                    'total_amount' => $amount,
                    'status' => 'Posted',
                    'created_at' => \Carbon\Carbon::parse($item->journalEntry->date),
                ];
            });

        // Merge and sort by date descending
        $salesInvoices = $salesInvoices->concat($journalItems)->sortByDesc('created_at');
        $pettyCashVouchers = \App\Models\PettyCashVoucher::withSum('items', 'amount')->latest()->get();
        $unpaidReceivables = \App\Models\SalesOrder::with('customer')
            ->where('status', '!=', 'cancelled')
            ->where('payment_status', '!=', 'paid')
            ->where(function($q) {
                $q->whereNull('proof_of_payment')->orWhere('proof_of_payment', '');
            })
            ->whereNotIn('type', ['calculator_pos', 'ecom_direct'])
            ->latest()
            ->get()
            ->map(function($so) {
                return (object)[
                    'soa_number' => $so->so_number,
                    'customer_name' => $so->customer->customer_name ?? ($so->customer->company_name ?? 'N/A'),
                    'type' => 'Sales Order',
                    'status' => 'Unpaid',
                    'total_amount' => $so->total_amount,
                    'created_at' => $so->created_at,
                ];
            });

        $soaList = \App\Models\StatementOfAccount::with('customer')->latest()->get()->map(function($soa) {
            $rawStatus = strtolower($soa->status ?? '');
            $displayStatus = ($rawStatus === 'paid') ? 'Paid' : 'Unpaid';

            return (object)[
                'soa_number' => $soa->soa_number,
                'customer_name' => $soa->customer->customer_name ?? ($soa->customer->company_name ?? 'N/A'),
                'type' => 'Statement of Account',
                'status' => $displayStatus,
                'total_amount' => $soa->total_amount,
                'created_at' => $soa->created_at,
            ];
        });

        $statementOfAccounts = $unpaidReceivables->concat($soaList);
        $books = \App\Models\Book::select('name', 'stock', 'cost')->where('stock', '>', 0)->get();
        $purchaseOrders = \Illuminate\Support\Facades\Schema::hasTable('purchase_orders') ? \App\Models\PurchaseOrder::select('po_number', 'total_amount', 'status', 'created_at')->latest()->get() : collect();
        // $companyBankAccounts is loaded at the top of the method

        $officeSuppliesList = \App\Models\OfficeSupply::latest()->get();
        $expensesRecordsList = \App\Models\Expense::with(['department', 'addedBy'])->latest()->get();
        $fixedAssetsRecordsList = \App\Models\ProductionFixedAsset::latest()->get();

        $allAccountGroups = \App\Models\AccountGroup::withCount('accounts')->orderBy('name')->get();

        $categoryAccounts = \App\Models\ChartOfAccount::with('accountGroup')
            ->where('type', $typeFilter)
            ->orderBy('code')
            ->get()
            ->map(function($acc) use ($typeFilter) {
                $acc->calculated_balance = $this->calculateAccountLiveBalance($acc, $typeFilter);
                return $acc;
            });

        $categoryAccountGroups = \App\Models\AccountGroup::where('type', $typeFilter)
            ->with(['accounts' => function($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('name')
            ->get()
            ->map(function($group) use ($typeFilter) {
                $totalBalance = 0;
                foreach ($group->accounts as $acc) {
                    $totalBalance += $this->calculateAccountLiveBalance($acc, $typeFilter);
                }
                $group->calculated_balance = $totalBalance;
                return $group;
            });

        return view('admin-finance.accounting.chart-of-accounts', [
            'title' => 'Chart of Accounts - ' . ($mainTab === 'crud' ? 'Management' : ($mainTab === 'account_groups' ? 'Account Groups' : ucfirst($tab))),
            'role' => $user->position,
            'sidebar' => 'admin-finance',
            'mainTab' => $mainTab,
            'tab' => $tab,
            'allAccounts' => $allAccounts,
            'categoryAccounts' => $categoryAccounts,
            'allAccountGroups' => $allAccountGroups,
            'categoryAccountGroups' => $categoryAccountGroups,
            'balances' => $balances,
            'uncategorizedAccounts' => $uncategorizedAccounts,
            'salesInvoices' => $salesInvoices,
            'pettyCashVouchers' => $pettyCashVouchers,
            'statementOfAccounts' => $statementOfAccounts,
            'books' => $books,
            'purchaseOrders' => $purchaseOrders,
            'companyBankAccounts' => $companyBankAccounts,
            'officeSuppliesList' => $officeSuppliesList,
            'expensesRecordsList' => $expensesRecordsList,
            'fixedAssetsRecordsList' => $fixedAssetsRecordsList,
            'accountDetails' => $accountDetails,
        ]);
    }

    private function calculateAccountLiveBalance($acc, $typeFilter = null)
    {
        $type = $typeFilter ?? $acc->type;
        $debit = $acc->journalEntryItems()->whereHas('journalEntry', function($q) { $q->where('status', 'posted'); })->sum('debit');
        $credit = $acc->journalEntryItems()->whereHas('journalEntry', function($q) { $q->where('status', 'posted'); })->sum('credit');
        
        $pendingPpSum = \App\Models\ClientPaymentPostingItem::where('chart_of_account_id', $acc->id)
            ->whereHas('posting', function($q) { $q->where('status', 'pending'); })
            ->sum('amount');

        $glBalance = ($type === 'Asset' || $type === 'Expense') ? ($debit - $credit) : ($credit - $debit);

        // Check if there is a matching Company Bank Account for this Chart of Account
        $cbaBalance = 0.00;
        if (\Illuminate\Support\Facades\Schema::hasTable('company_bank_accounts')) {
            $cba = \App\Models\CompanyBankAccount::where('account_code', $acc->code)->first();
            if (!$cba) {
                // Try matching by first word of name if BDO/BPI/GCash/Bank/Union
                $firstWord = explode(' ', trim($acc->name))[0];
                if (strlen($firstWord) >= 3 && in_array(strtoupper($firstWord), ['BDO', 'BPI', 'GCASH', 'BANK', 'UNION'])) {
                    $cba = \App\Models\CompanyBankAccount::where('bank_name', 'like', "%{$firstWord}%")->first();
                }
            }
            if ($cba) {
                $cbaBalance = (float) $cba->current_balance;
            }
        }

        return max(0, $glBalance + $pendingPpSum + $cbaBalance);
    }

    public function storeChartOfAccount(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting.chart_of_accounts') && !$user->hasPermission('admin_finance.accounting')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:chart_of_accounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:Asset,Liability,Equity,Income,Expense',
            'category' => 'nullable|string|max:255',
            'account_group_id' => 'nullable|exists:account_groups,id',
            'normal_balance' => 'nullable|in:Debit,Credit',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        if (empty($validated['normal_balance'])) {
            $validated['normal_balance'] = in_array($validated['type'], ['Asset', 'Expense']) ? 'Debit' : 'Credit';
        }

        $account = \App\Models\ChartOfAccount::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Chart of Account created successfully.', 'account' => $account]);
        }

        return redirect()->back()->with('success', 'Chart of Account created successfully.');
    }

    public function updateChartOfAccount(\Illuminate\Http\Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting.chart_of_accounts') && !$user->hasPermission('admin_finance.accounting')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        $account = \App\Models\ChartOfAccount::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:chart_of_accounts,code,' . $id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:Asset,Liability,Equity,Income,Expense',
            'category' => 'nullable|string|max:255',
            'account_group_id' => 'nullable|exists:account_groups,id',
            'normal_balance' => 'nullable|in:Debit,Credit',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        if (empty($validated['normal_balance'])) {
            $validated['normal_balance'] = in_array($validated['type'], ['Asset', 'Expense']) ? 'Debit' : 'Credit';
        }

        $account->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Chart of Account updated successfully.', 'account' => $account]);
        }

        return redirect()->back()->with('success', 'Chart of Account updated successfully.');
    }

    public function storeAccountGroup(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting.chart_of_accounts') && !$user->hasPermission('admin_finance.accounting')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Asset,Liability,Equity,Income,Expense',
            'description' => 'nullable|string',
        ]);

        $group = \App\Models\AccountGroup::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Account Group created successfully.', 'group' => $group]);
        }

        return redirect()->back()->with('success', 'Account Group created successfully.');
    }

    public function updateAccountGroup(\Illuminate\Http\Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting.chart_of_accounts') && !$user->hasPermission('admin_finance.accounting')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        $group = \App\Models\AccountGroup::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Asset,Liability,Equity,Income,Expense',
            'description' => 'nullable|string',
        ]);

        $group->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Account Group updated successfully.', 'group' => $group]);
        }

        return redirect()->back()->with('success', 'Account Group updated successfully.');
    }

    public function destroyAccountGroup(\Illuminate\Http\Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting.chart_of_accounts') && !$user->hasPermission('admin_finance.accounting')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        $group = \App\Models\AccountGroup::findOrFail($id);
        $group->accounts()->update(['account_group_id' => null]);
        $group->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Account Group deleted successfully.']);
        }

        return redirect()->back()->with('success', 'Account Group deleted successfully.');
    }

    public function getAccountGroupAccounts(\Illuminate\Http\Request $request, $id)
    {
        $group = \App\Models\AccountGroup::with(['accounts' => function($q) {
            $q->orderBy('code');
        }])->findOrFail($id);

        $accounts = $group->accounts->map(function($acc) {
            $balance = $this->calculateAccountLiveBalance($acc);

            return [
                'id' => $acc->id,
                'code' => $acc->code,
                'name' => $acc->name,
                'type' => $acc->type,
                'category' => $acc->category,
                'balance' => number_format($balance, 2),
                'raw_balance' => $balance,
                'is_active' => (bool)$acc->is_active,
            ];
        });

        return response()->json([
            'success' => true,
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
                'type' => $group->type,
                'description' => $group->description,
                'total_balance' => number_format($accounts->sum('raw_balance'), 2),
            ],
            'accounts' => $accounts,
        ]);
    }

    public function destroyChartOfAccount(\Illuminate\Http\Request $request, $id)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting.chart_of_accounts') && !$user->hasPermission('admin_finance.accounting')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Unauthorized action.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        $account = \App\Models\ChartOfAccount::findOrFail($id);

        if ($account->journalEntryItems()->exists()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Cannot delete account because it has recorded journal entries.'], 422);
            }
            return redirect()->back()->with('error', 'Cannot delete account because it has recorded journal entries.');
        }

        $account->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Chart of Account deleted successfully.']);
        }

        return redirect()->back()->with('success', 'Chart of Account deleted successfully.');
    }

    public function toggleAccountStatus(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting.chart_of_accounts') && !$user->hasPermission('admin_finance.accounting')) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $type = $request->input('type', 'coa');
        $id = $request->input('id');

        if ($type === 'bank') {
            $bank = \App\Models\CompanyBankAccount::find($id);
            if (!$bank) {
                return response()->json(['error' => 'Bank account not found.'], 404);
            }
            $bank->status = ($bank->status === 'Active') ? 'Inactive' : 'Active';
            $bank->save();

            // Also find and toggle the corresponding Chart of Account if it exists by account_code
            $coa = \App\Models\ChartOfAccount::where('code', $bank->account_code)->first();
            if ($coa) {
                $coa->is_active = ($bank->status === 'Active') ? 1 : 0;
                $coa->save();
            }

            return response()->json([
                'success' => true,
                'is_active' => ($bank->status === 'Active'),
                'message' => 'Bank account status updated to ' . $bank->status . '.'
            ]);
        } else {
            $account = \App\Models\ChartOfAccount::find($id);
            if (!$account) {
                return response()->json(['error' => 'Account not found.'], 404);
            }
            $account->is_active = $account->is_active ? 0 : 1;
            $account->save();

            // Also find and toggle the corresponding Company Bank Account status if it's a bank
            if ($account->category === 'Cash & Bank') {
                $bank = \App\Models\CompanyBankAccount::where('account_code', $account->code)->first();
                if ($bank) {
                    $bank->status = $account->is_active ? 'Active' : 'Inactive';
                    $bank->save();
                }
            }

            return response()->json([
                'success' => true,
                'is_active' => $account->is_active,
                'message' => 'Account "' . $account->name . '" status updated to ' . ($account->is_active ? 'Active' : 'Inactive') . '.'
            ]);
        }
    }

    public function getAccountLedger($id)
    {
        $account = \App\Models\ChartOfAccount::findOrFail($id);

        // 1. General Journal Entry Items
        $items = \App\Models\JournalEntryItem::with(['journalEntry'])
            ->where('chart_of_account_id', $id)
            ->whereHas('journalEntry', function($q) {
                $q->where('status', 'posted');
            })
            ->get()
            ->map(function($item) {
                return [
                    'date' => $item->journalEntry ? date('M d, Y', strtotime($item->journalEntry->date)) : 'N/A',
                    'ref_no' => $item->journalEntry ? ($item->journalEntry->reference ?: $item->journalEntry->entry_no) : '—',
                    'memo' => $item->memo ?: ($item->journalEntry ? $item->journalEntry->description : 'Journal Entry'),
                    'debit' => (float)$item->debit,
                    'credit' => (float)$item->credit,
                ];
            });

        // 2. Client Payment Postings (Only pending items to avoid double-counting posted GL items)
        $paymentPostings = \App\Models\ClientPaymentPostingItem::with(['posting', 'customer'])
            ->where('chart_of_account_id', $id)
            ->whereHas('posting', function($q) {
                $q->where('status', 'pending');
            })
            ->get()
            ->map(function($item) {
                return [
                    'date' => $item->posting ? date('M d, Y', strtotime($item->posting->date)) : ($item->payment_date ? date('M d, Y', strtotime($item->payment_date)) : 'N/A'),
                    'ref_no' => $item->receipt_no ?: ($item->invoice_no ?: ($item->reference_no ?: 'PP-' . str_pad($item->posting_id, 5, '0', STR_PAD_LEFT))),
                    'memo' => '[Pending] ' . ($item->customer ? $item->customer->customer_name : 'Client Payment') . ' (' . ucfirst($item->payment_method ?? 'Payment') . ')',
                    'debit' => (float)$item->amount,
                    'credit' => 0.00,
                ];
            });

        $operationalItems = collect();

        // 3. Operational Sources matching specific Account Codes or Categories
        if (stripos($account->name, 'cash on hand') !== false || in_array($account->code, ['1010', '1040'])) {
            // Sales Invoices / POS Transactions
            $salesInvoices = \App\Models\SalesOrder::with('customer')
                ->where('status', '!=', 'cancelled')
                ->where(function($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhereNotNull('proof_of_payment')
                      ->orWhere('type', 'calculator_pos');
                })
                ->latest()
                ->take(50)
                ->get()
                ->map(function($so) {
                    return [
                        'date' => $so->created_at ? $so->created_at->format('M d, Y') : 'N/A',
                        'ref_no' => $so->so_number,
                        'memo' => 'Sales Invoice - ' . ($so->customer->customer_name ?? 'Walk-in / POS'),
                        'debit' => (float)$so->total_amount,
                        'credit' => 0.00,
                    ];
                });
            $operationalItems = $operationalItems->concat($salesInvoices);
        }

        if ($account->code === '1015' || stripos($account->name, 'petty cash') !== false) {
            // Petty Cash Vouchers
            $pcvs = \App\Models\PettyCashVoucher::withSum('items', 'amount')->latest()->take(50)->get()->map(function($pcv) {
                return [
                    'date' => $pcv->date ? date('M d, Y', strtotime($pcv->date)) : 'N/A',
                    'ref_no' => $pcv->pcv_number,
                    'memo' => 'Petty Cash Voucher - Payee: ' . ($pcv->pay_to ?? 'N/A'),
                    'debit' => (float)$pcv->items_sum_amount,
                    'credit' => 0.00,
                ];
            });
            $operationalItems = $operationalItems->concat($pcvs);
        }

        if (stripos($account->name, 'supplies') !== false) {
            // Office Supplies Valuation
            $supplies = \App\Models\OfficeSupply::latest()->take(50)->get()->map(function($sup) {
                return [
                    'date' => $sup->created_at ? $sup->created_at->format('M d, Y') : 'N/A',
                    'ref_no' => 'SUPPLY-' . $sup->id,
                    'memo' => 'Office Supply Item: ' . $sup->item_name . ' (' . $sup->items_stock . ' pcs)',
                    'debit' => (float)($sup->item_price * $sup->items_stock),
                    'credit' => 0.00,
                ];
            });
            $operationalItems = $operationalItems->concat($supplies);
        }

        if (in_array($account->code, ['1600', '5510']) || stripos($account->name, 'fixed asset') !== false) {
            // Fixed Assets
            $assets = \App\Models\ProductionFixedAsset::latest()->take(50)->get()->map(function($ast) {
                return [
                    'date' => $ast->purchase_date ? $ast->purchase_date->format('M d, Y') : 'N/A',
                    'ref_no' => $ast->asset_code,
                    'memo' => 'Fixed Asset: ' . $ast->name . ' (' . ($ast->category ?? 'Asset') . ')',
                    'debit' => (float)$ast->purchase_price,
                    'credit' => 0.00,
                ];
            });
            $operationalItems = $operationalItems->concat($assets);
        }

        $allTransactions = $items->concat($paymentPostings)->concat($operationalItems)->sortByDesc('date')->values();

        $totalDebit = (float)$allTransactions->sum('debit');
        $totalCredit = (float)$allTransactions->sum('credit');
        $isAssetOrExpense = in_array($account->type, ['Asset', 'Expense']);
        $computedBalance = $isAssetOrExpense ? ($totalDebit - $totalCredit) : ($totalCredit - $totalDebit);

        return response()->json([
            'success' => true,
            'account' => [
                'id' => $account->id,
                'code' => $account->code,
                'name' => $account->name,
                'type' => $account->type,
                'category' => $account->category,
                'balance' => (float)$computedBalance,
            ],
            'transactions' => $allTransactions
        ]);
    }

    public function salesManagement(Request $request)
    {
        $tab = $request->query('tab', 'bookstore');
        $user = auth()->user();

        // 1. Bookstore data
        $bookstoreDailySales = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->whereDate('created_at', today())
            ->sum('total_amount') ?: 0.00;
        
        $bookstoreCashSales = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->where('payment_method', 'cash')
            ->sum('total_amount') ?: 0.00;
            
        $bookstoreGcashSales = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->whereIn('payment_method', ['gcash', 'qr_ph', 'ewallet', 'GCash'])
            ->sum('total_amount') ?: 0.00;
            
        $bookstoreCardSales = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->whereIn('payment_method', ['credit_card', 'card', 'Credit Card'])
            ->sum('total_amount') ?: 0.00;
            
        $bookstoreChargeSales = \App\Models\SalesOrder::where('type', 'charge')
            ->sum('total_amount') ?: 0.00;

        $bookstoreDiscountSales = \App\Models\SalesOrder::where('status', '!=', 'cancelled')
            ->where('discount_amount', '>', 0)
            ->sum('discount_amount') ?: 0.00;

        // Bookstore Detail Lists
        $bookstoreDailyOrders = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->latest()
            ->get();
        
        $bookstoreCashOrders = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->where('payment_method', 'cash')
            ->latest()
            ->get();

        $bookstoreGcashOrders = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->whereIn('payment_method', ['gcash', 'qr_ph', 'ewallet', 'GCash'])
            ->latest()
            ->get();

        $bookstoreCardOrders = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
            ->whereIn('payment_method', ['credit_card', 'card', 'Credit Card'])
            ->latest()
            ->get();

        $bookstoreChargeOrders = \App\Models\SalesOrder::where('type', 'charge')
            ->latest()
            ->get();

        $bookstoreDiscountOrders = \App\Models\SalesOrder::where('status', '!=', 'cancelled')
            ->where('discount_amount', '>', 0)
            ->with(['customer', 'preparedBy'])
            ->latest()
            ->get();

        // 2. E-Commerce Platform Data
        $getEcomQuery = function($pName, $typeFallback = null) {
            return \App\Models\SalesOrder::where(function($q) use ($pName, $typeFallback) {
                $q->where('platform', $pName)
                  ->orWhere('ecom_platform', $pName);
                if ($typeFallback) {
                    $q->orWhere('type', $typeFallback);
                }
            });
        };

        $ecomWebsiteSales  = (clone $getEcomQuery('website', 'direct_invoice_website'))->sum('total_amount') ?: 0.00;
        $ecomShopeeSales   = (clone $getEcomQuery('shopee'))->sum('total_amount') ?: 0.00;
        $ecomLazadaSales   = (clone $getEcomQuery('lazada'))->sum('total_amount') ?: 0.00;
        $ecomFacebookSales = (clone $getEcomQuery('facebook'))->sum('total_amount') ?: 0.00;
        $ecomTiktokSales   = (clone $getEcomQuery('tiktok'))->sum('total_amount') ?: 0.00;

        $ecomWebsiteOrders  = (clone $getEcomQuery('website', 'direct_invoice_website'))->with(['customer', 'preparedBy'])->latest()->get();
        $ecomShopeeOrders   = (clone $getEcomQuery('shopee'))->with(['customer', 'preparedBy'])->latest()->get();
        $ecomLazadaOrders   = (clone $getEcomQuery('lazada'))->with(['customer', 'preparedBy'])->latest()->get();
        $ecomFacebookOrders = (clone $getEcomQuery('facebook'))->with(['customer', 'preparedBy'])->latest()->get();
        $ecomTiktokOrders   = (clone $getEcomQuery('tiktok'))->with(['customer', 'preparedBy'])->latest()->get();

        // 3. Area Sales Data
        $areaRepSales = \App\Models\SalesOrder::where(function($q) {
            $q->whereNotNull('area_sales_staff_id')
              ->orWhere('type', 'area_sales_consignment');
        })->sum('total_amount') ?: 0.00;

        $areaOrders = \App\Models\SalesOrder::where(function($q) {
            $q->whereNotNull('area_sales_staff_id')
              ->orWhere('type', 'area_sales_consignment');
        })
        ->with(['createdBy', 'preparedBy', 'areaSalesStaff', 'customer'])
        ->latest()
        ->get();

        // 4. Complimentary Receipt Data
        $complimentaryOrders = \App\Models\SalesOrder::where('type', 'complimentary')
            ->with(['customer', 'preparedBy', 'items.product', 'items.book'])
            ->latest()
            ->get();

        $complimentaryTotalValuation = 0;
        foreach ($complimentaryOrders as $cOrd) {
            foreach ($cOrd->items as $cItem) {
                $cost = ($cItem->book && $cItem->book->cost > 0) ? $cItem->book->cost : ($cItem->unit_price > 0 ? $cItem->unit_price : 0);
                $complimentaryTotalValuation += ($cost * $cItem->quantity);
            }
        }
        if ($complimentaryTotalValuation <= 0 && $complimentaryOrders->count() > 0) {
            $complimentaryTotalValuation = $complimentaryOrders->sum('total_amount');
        }

        $pendingComplimentaryOrders = $complimentaryOrders->filter(function($ord) {
            return is_null($ord->ar_prepared_at) && in_array($ord->status, ['picking', 'pending_ar_prep']);
        });

        $issuedComplimentaryOrders = $complimentaryOrders->filter(function($ord) {
            return !is_null($ord->ar_prepared_at);
        });

        // 5. MIBF POS Data
        $mibfQuery = \App\Models\SalesOrder::where('status', 'completed')
            ->where(function($q) {
                $q->where('ecom_platform', 'MIBF')
                  ->orWhere('platform', 'MIBF');
            });

        $mibfDailySales = (clone $mibfQuery)->whereDate('created_at', today())->sum('total_amount') ?: 0.00;
        $mibfCashSales = (clone $mibfQuery)->where('payment_method', 'cash')->sum('total_amount') ?: 0.00;
        $mibfGcashSales = (clone $mibfQuery)->whereIn('payment_method', ['gcash', 'GCash'])->sum('total_amount') ?: 0.00;
        $mibfMayaSales = (clone $mibfQuery)->whereIn('payment_method', ['maya', 'paymaya', 'PayMaya'])->sum('total_amount') ?: 0.00;
        $mibfCardSales = (clone $mibfQuery)->whereIn('payment_method', ['card', 'credit_card'])->sum('total_amount') ?: 0.00;
        $mibfCheckSales = (clone $mibfQuery)->where('payment_method', 'check')->sum('total_amount') ?: 0.00;
        $mibfBankSales = (clone $mibfQuery)->whereIn('payment_method', ['bank', 'bank_transfer'])->sum('total_amount') ?: 0.00;

        $mibfDailyOrders = (clone $mibfQuery)->whereDate('created_at', today())->with(['customer', 'preparedBy'])->latest()->get();
        $mibfCashOrders = (clone $mibfQuery)->where('payment_method', 'cash')->with(['customer', 'preparedBy'])->latest()->get();
        $mibfGcashOrders = (clone $mibfQuery)->whereIn('payment_method', ['gcash', 'GCash'])->with(['customer', 'preparedBy'])->latest()->get();
        $mibfMayaOrders = (clone $mibfQuery)->whereIn('payment_method', ['maya', 'paymaya', 'PayMaya'])->with(['customer', 'preparedBy'])->latest()->get();
        $mibfCardOrders = (clone $mibfQuery)->whereIn('payment_method', ['card', 'credit_card'])->with(['customer', 'preparedBy'])->latest()->get();
        $mibfCheckOrders = (clone $mibfQuery)->where('payment_method', 'check')->with(['customer', 'preparedBy'])->latest()->get();
        $mibfBankOrders = (clone $mibfQuery)->whereIn('payment_method', ['bank', 'bank_transfer'])->with(['customer', 'preparedBy'])->latest()->get();

        return view('admin-finance.accounting.sales-management', [
            'title' => 'Sales Management - ' . ($tab === 'mibf' ? 'MIBF POS' : ucfirst($tab)),
            'role' => $user ? $user->position : 'Staff',
            'sidebar' => 'admin-finance',
            'tab' => $tab,
            
            // Bookstore metrics
            'bookstoreDailySales' => $bookstoreDailySales,
            'bookstoreCashSales' => $bookstoreCashSales,
            'bookstoreGcashSales' => $bookstoreGcashSales,
            'bookstoreCardSales' => $bookstoreCardSales,
            'bookstoreChargeSales' => $bookstoreChargeSales,
            'bookstoreDiscountSales' => $bookstoreDiscountSales,
            
            // Bookstore lists
            'bookstoreDailyOrders' => $bookstoreDailyOrders,
            'bookstoreCashOrders' => $bookstoreCashOrders,
            'bookstoreGcashOrders' => $bookstoreGcashOrders,
            'bookstoreCardOrders' => $bookstoreCardOrders,
            'bookstoreChargeOrders' => $bookstoreChargeOrders,
            'bookstoreDiscountOrders' => $bookstoreDiscountOrders,
            
            // Ecom metrics
            'ecomWebsiteSales' => $ecomWebsiteSales,
            'ecomShopeeSales' => $ecomShopeeSales,
            'ecomLazadaSales' => $ecomLazadaSales,
            'ecomFacebookSales' => $ecomFacebookSales,
            'ecomTiktokSales' => $ecomTiktokSales,
            
            // Ecom lists
            'ecomWebsiteOrders' => $ecomWebsiteOrders,
            'ecomShopeeOrders' => $ecomShopeeOrders,
            'ecomLazadaOrders' => $ecomLazadaOrders,
            'ecomFacebookOrders' => $ecomFacebookOrders,
            'ecomTiktokOrders' => $ecomTiktokOrders,

            // Area sales
            'areaRepSales' => $areaRepSales,
            'areaOrders' => $areaOrders,

            // Complimentary Receipts
            'complimentaryOrders' => $complimentaryOrders,
            'complimentaryTotalValuation' => $complimentaryTotalValuation,
            'pendingComplimentaryOrders' => $pendingComplimentaryOrders,
            'issuedComplimentaryOrders' => $issuedComplimentaryOrders,

            // MIBF POS metrics
            'mibfDailySales' => $mibfDailySales,
            'mibfCashSales' => $mibfCashSales,
            'mibfGcashSales' => $mibfGcashSales,
            'mibfMayaSales' => $mibfMayaSales,
            'mibfCardSales' => $mibfCardSales,
            'mibfCheckSales' => $mibfCheckSales,
            'mibfBankSales' => $mibfBankSales,

            // MIBF POS lists
            'mibfDailyOrders' => $mibfDailyOrders,
            'mibfCashOrders' => $mibfCashOrders,
            'mibfGcashOrders' => $mibfGcashOrders,
            'mibfMayaOrders' => $mibfMayaOrders,
            'mibfCardOrders' => $mibfCardOrders,
            'mibfCheckOrders' => $mibfCheckOrders,
            'mibfBankOrders' => $mibfBankOrders,
        ]);
    }

    public function accountsReceivable(Request $request)
    {
        $user = auth()->user();
        
        $query = \App\Models\Customer::query();

        // Search text filter (client name search)
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', '%' . $search . '%')
                  ->orWhere('company_name', 'like', '%' . $search . '%');
            });
        }

        // Credit rating filter
        if ($rating = $request->input('credit_rating')) {
            if ($rating === 'AAA') {
                $query->where('credit_limit', '>=', 200000);
            } elseif ($rating === 'AA') {
                $query->where('credit_limit', '>=', 100000)->where('credit_limit', '<', 200000);
            } elseif ($rating === 'A') {
                $query->where('credit_limit', '<', 100000);
            }
        }

        // Payment terms filter
        if ($terms = $request->input('payment_terms')) {
            $query->where('payment_terms', $terms);
        }

        $dbCustomers = $query->orderBy('customer_name')->paginate(10);
        $customersList = collect();

        foreach ($dbCustomers as $cust) {
            $allOrders = \App\Models\SalesOrder::where('customer_id', $cust->customer_id)
                ->with(['areaSalesStaff', 'preparedBy'])
                ->latest()
                ->get();

            $invoices = $allOrders->map(function($so) {
                $paidFromDb = \App\Models\Payment::where('sales_order_id', $so->id)->sum('amount') ?: 0.00;
                $hasProof = !empty($so->proof_of_payment);
                $isAutoPaidType = in_array($so->type, ['calculator_pos', 'ecom_direct']);
                
                $isPaid = $so->payment_status === 'paid' || $hasProof || $isAutoPaidType || ($paidFromDb >= $so->total_amount && $so->total_amount > 0);
                $paid = $isPaid ? $so->total_amount : $paidFromDb;
                $rem = max(0, $so->total_amount - $paid);
                
                $status = 'Unpaid';
                if ($isPaid) {
                    $status = 'Paid';
                } elseif ($paid > 0) {
                    $status = 'Partially Paid';
                }

                $so->computed_is_paid = $isPaid;
                $so->computed_paid_amount = $paid;
                $so->computed_remaining = $rem;
                $so->computed_status = $status;

                $salesRep = 'N/A';
                if ($so->areaSalesStaff) {
                    $salesRep = $so->areaSalesStaff->name;
                } elseif ($so->preparedBy) {
                    $salesRep = $so->preparedBy->name;
                } elseif ($so->type === 'calculator_pos') {
                    $salesRep = 'Admin - POS';
                }

                return (object)[
                    'so_number' => $so->so_number,
                    'date' => $so->created_at ? $so->created_at->format('M d, Y') : 'N/A',
                    'sales_rep' => $salesRep,
                    'total_amount' => $so->total_amount,
                    'paid_amount' => $paid,
                    'remaining_balance' => $rem,
                    'status' => $status,
                ];
            });

            $unpaidOrders = $allOrders->filter(function($so) {
                return !($so->computed_is_paid ?? false);
            });
            $outstanding = $unpaidOrders->sum('total_amount') ?: 0.00;

            $customersList->push((object)[
                'customer_id' => $cust->customer_id,
                'customer_name' => $cust->customer_name,
                'company_name' => $cust->company_name ?: $cust->customer_name,
                'account_number' => $cust->account_number ?: 'ACC-' . str_pad($cust->customer_id, 4, '0', STR_PAD_LEFT),
                'credit_limit' => $cust->credit_limit ?: 0.00,
                'payment_terms' => $cust->payment_terms ?: 'Due on receipt',
                'outstanding_balance' => $outstanding,
                'credit_rating' => $cust->credit_limit >= 200000 ? 'AAA' : ($cust->credit_limit >= 100000 ? 'AA' : 'A'),
                'rep' => $cust->rep,
                'sales_rep' => $cust->rep === 'CLE' ? 'Xavier Almocera' : ($cust->rep === 'MKT' ? 'Kerwin Morfe' : 'N/A'),
                'main_phone' => $cust->mobile ?: ($cust->main_phone ?: 'N/A'),
                'main_email' => $cust->main_email ?: 'N/A',
                'billing_address' => $cust->billing_address ?: 'N/A',
                'interest_rate' => 1.5,
                'overdue_amount' => 0.00,
                'bad_debts' => 0.00,
                'accrued_interest' => 0.00,
                'invoices' => $invoices,
            ]);
        }

        $dbCustomers->setCollection($customersList);

        $paymentTermsList = \App\Models\Customer::whereNotNull('payment_terms')
            ->where('payment_terms', '!=', '')
            ->distinct()
            ->pluck('payment_terms');

        return view('admin-finance.accounting.accounts-receivable', [
            'title' => 'Accounts Receivable Ledger',
            'role' => $user ? $user->position : 'Staff',
            'sidebar' => 'admin-finance',
            'customers' => $dbCustomers,
            'paymentTermsList' => $paymentTermsList,
        ]);
    }

    public function updateCustomerRep(Request $request, $id)
    {
        $customer = \App\Models\Customer::findOrFail($id);
        
        $request->validate([
            'rep' => 'nullable|string|in:CLE,MKT'
        ]);

        $customer->rep = $request->rep ?: null;
        $customer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Sales Representative updated successfully.'
        ]);
    }

    public function accountsPayable(Request $request)
    {
        $user = auth()->user();

        $categories = [
            'Paper Suppliers',
            'Ink Suppliers',
            'Freight',
            'Utilities',
            'Outside Printers',
            'Government',
            'Professional Services',
        ];

        $selectedCategory = $request->query('category');
        $search = $request->query('search');

        $query = \App\Models\Supplier::with(['purchaseOrders', 'receivingReports', 'invoices', 'payments']);

        if ($selectedCategory && $selectedCategory !== 'All') {
            $query->where('category', $selectedCategory);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('supplier_code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('company_name')->paginate(10);

        // Calculate summary metrics across all suppliers
        $allInvoices = \App\Models\SupplierInvoice::with('supplier')->get();
        $allPayments = \App\Models\SupplierPayment::with(['supplier', 'invoice'])->get();
        $allPOs = \App\Models\PurchaseOrder::with('supplier')->get();
        $allRRs = \App\Models\ReceivingReport::with(['supplier', 'purchaseOrder'])->get();

        $totalApBalance = 0;
        $totalOverdueAp = 0;
        $totalWithheldTax = $allPayments->sum('withholding_tax_amount') + $allInvoices->sum('withholding_tax_amount');

        $today = \Carbon\Carbon::today();

        foreach ($allInvoices as $inv) {
            $balance = max(0, $inv->total_amount - $inv->amount_paid);
            if ($balance > 0) {
                $totalApBalance += $balance;
                if (\Carbon\Carbon::parse($inv->due_date)->lt($today)) {
                    $totalOverdueAp += $balance;
                    $inv->is_overdue = true;
                } else {
                    $inv->is_overdue = false;
                }
            } else {
                $inv->is_overdue = false;
            }
        }

        // Generate 1099 / Expanded Withholding Tax (EWT) Report data per supplier
        $ewtReports = collect();
        $allSuppliers = \App\Models\Supplier::all();
        foreach ($allSuppliers as $supp) {
            $suppInvoices = $allInvoices->where('supplier_id', $supp->id);
            $suppPayments = $allPayments->where('supplier_id', $supp->id);

            $grossBilled = $suppInvoices->sum('subtotal');
            $taxWithheld = $suppPayments->sum('withholding_tax_amount') ?: $suppInvoices->sum('withholding_tax_amount');
            $totalPaid = $suppPayments->sum('amount_paid');

            $ewtReports->push((object)[
                'supplier_id' => $supp->id,
                'supplier_code' => $supp->supplier_code,
                'company_name' => $supp->company_name,
                'category' => $supp->category,
                'tin' => $supp->tin ?: 'N/A',
                'tax_rate' => $supp->tax_rate ?: 1.00,
                'gross_amount' => $grossBilled,
                'tax_withheld' => $taxWithheld,
                'total_paid' => $totalPaid,
            ]);
        }

        return view('admin-finance.accounting.accounts-payable', [
            'title' => 'Accounts Payable Ledger',
            'role' => $user ? $user->position : 'Staff',
            'sidebar' => 'admin-finance',
            'suppliers' => $suppliers,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory ?: 'All',
            'purchaseOrders' => $allPOs,
            'receivingReports' => $allRRs,
            'invoices' => $allInvoices,
            'payments' => $allPayments,
            'ewtReports' => $ewtReports,
            'metrics' => [
                'total_ap_balance' => $totalApBalance,
                'total_overdue_ap' => $totalOverdueAp,
                'total_withheld_tax' => $totalWithheldTax,
                'active_suppliers_count' => $suppliers->where('status', 'active')->count(),
            ],
        ]);
    }

    public function storeSupplier(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'category' => 'required|string',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'tin' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'terms' => 'nullable|string|max:100',
        ]);

        $code = 'SUP-' . strtoupper(\Str::random(5));

        \App\Models\Supplier::create([
            'supplier_code' => $code,
            'company_name' => $request->company_name,
            'category' => $request->category,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'tin' => $request->tin,
            'address' => $request->address,
            'tax_rate' => $request->tax_rate ?: 1.00,
            'terms' => $request->terms ?: '30 Days',
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'Supplier created successfully!');
    }

    public function storeSupplierInvoice(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|unique:supplier_invoices,invoice_number',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'withholding_tax_rate' => 'nullable|numeric|min:0',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'receiving_report_id' => 'nullable|exists:receiving_reports,id',
            'notes' => 'nullable|string',
        ]);

        $supplier = \App\Models\Supplier::findOrFail($request->supplier_id);
        $subtotal = (float) $request->subtotal;
        $taxRate = $request->has('withholding_tax_rate') && $request->withholding_tax_rate !== null ? (float) $request->withholding_tax_rate : (float) ($supplier->tax_rate ?: 1.00);
        $withholdingTaxAmount = round(($subtotal * $taxRate) / 100, 2);
        $taxAmount = round($subtotal * 0.12, 2); // Standard VAT 12% if applicable
        $totalAmount = ($subtotal + $taxAmount) - $withholdingTaxAmount;

        \App\Models\SupplierInvoice::create([
            'invoice_number' => $request->invoice_number,
            'supplier_id' => $supplier->id,
            'purchase_order_id' => $request->purchase_order_id,
            'receiving_report_id' => $request->receiving_report_id,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'withholding_tax_rate' => $taxRate,
            'withholding_tax_amount' => $withholdingTaxAmount,
            'total_amount' => $totalAmount,
            'amount_paid' => 0.00,
            'status' => 'unpaid',
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Supplier Invoice recorded successfully!');
    }

    public function storeSupplierPayment(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_invoice_id' => 'nullable|exists:supplier_invoices,id',
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $payNum = 'PAY-' . date('Ym') . '-' . rand(1000, 9999);
        $amountPaid = (float) $request->amount_paid;

        $invoice = null;
        $withholdingTaxAmount = 0.00;

        if ($request->supplier_invoice_id) {
            $invoice = \App\Models\SupplierInvoice::findOrFail($request->supplier_invoice_id);
            $withholdingTaxAmount = $invoice->withholding_tax_amount;

            $newPaid = $invoice->amount_paid + $amountPaid;
            $invoice->amount_paid = min($invoice->total_amount, $newPaid);
            if ($invoice->amount_paid >= $invoice->total_amount) {
                $invoice->status = 'paid';
            } else {
                $invoice->status = 'partially_paid';
            }
            $invoice->save();
        }

        \App\Models\SupplierPayment::create([
            'payment_number' => $payNum,
            'supplier_id' => $request->supplier_id,
            'supplier_invoice_id' => $request->supplier_invoice_id,
            'payment_date' => $request->payment_date,
            'amount_paid' => $amountPaid,
            'withholding_tax_amount' => $withholdingTaxAmount,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'status' => 'completed',
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Payment recorded successfully!');
    }

    public function updateSupplier(Request $request, $id)
    {
        $supplier = \App\Models\Supplier::findOrFail($id);
        
        $request->validate([
            'company_name' => 'required|string|max:255',
            'category' => 'required|string',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'tin' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'terms' => 'nullable|string|max:100',
            'status' => 'required|string',
        ]);

        $supplier->update([
            'company_name' => $request->company_name,
            'category' => $request->category,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'tin' => $request->tin,
            'address' => $request->address,
            'tax_rate' => $request->tax_rate ?: 1.00,
            'terms' => $request->terms ?: '30 Days',
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Supplier updated successfully!');
    }

    public function destroySupplier($id)
    {
        $supplier = \App\Models\Supplier::findOrFail($id);
        $supplier->delete();
        return redirect()->back()->with('success', 'Supplier deleted successfully!');
    }

    public function updateSupplierInvoice(Request $request, $id)
    {
        $invoice = \App\Models\SupplierInvoice::findOrFail($id);
        
        $request->validate([
            'invoice_number' => 'required|string|unique:supplier_invoices,invoice_number,' . $id,
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'subtotal' => 'required|numeric|min:0',
            'withholding_tax_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $subtotal = (float) $request->subtotal;
        $taxRate = $request->has('withholding_tax_rate') && $request->withholding_tax_rate !== null ? (float) $request->withholding_tax_rate : (float) ($invoice->supplier->tax_rate ?: 1.00);
        $withholdingTaxAmount = round(($subtotal * $taxRate) / 100, 2);
        $taxAmount = round($subtotal * 0.12, 2);
        $totalAmount = ($subtotal + $taxAmount) - $withholdingTaxAmount;

        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'withholding_tax_rate' => $taxRate,
            'withholding_tax_amount' => $withholdingTaxAmount,
            'total_amount' => $totalAmount,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Supplier invoice updated successfully!');
    }

    public function destroySupplierInvoice($id)
    {
        $invoice = \App\Models\SupplierInvoice::findOrFail($id);
        $invoice->delete();
        return redirect()->back()->with('success', 'Supplier invoice deleted successfully!');
    }

    public function destroySupplierPayment($id)
    {
        $payment = \App\Models\SupplierPayment::findOrFail($id);
        $payment->delete();
        return redirect()->back()->with('success', 'Supplier payment deleted successfully!');
    }

    public function investments(Request $request)
    {
        $types = [
            'Time Deposits',
            'Stocks',
            'Mutual Funds',
            'Bonds',
            'Money Market',
        ];

        $selectedType = $request->query('type', 'All');
        $search = $request->query('search');

        $query = \App\Models\Investment::with('transactions');

        if ($selectedType && $selectedType !== 'All') {
            $query->where('type', $selectedType);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('portfolio_code', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        $investments = $query->latest('acquisition_date')->get();

        // Recalculate dynamic returns and ROI for all items
        foreach ($investments as $inv) {
            $inv->recalculatePerformance();
            $inv->save();
        }

        $totalPrincipal = $investments->sum('principal_amount');
        $totalCurrentVal = $investments->sum('current_value');
        $totalDividendsAll = $investments->sum('total_dividends');
        $totalInterestAll = $investments->sum('total_interest');
        $totalReturnAll = $investments->sum('total_return');

        $overallRoiPct = $totalPrincipal > 0 ? round(($totalReturnAll / $totalPrincipal) * 100, 2) : 0.00;

        return view('admin-finance.accounting.investments', [
            'title' => 'Investments Module',
            'role' => 'Finance Manager',
            'sidebar' => 'admin-finance',
            'types' => $types,
            'selectedType' => $selectedType,
            'search' => $search,
            'investments' => $investments,
            'metrics' => [
                'total_principal' => $totalPrincipal,
                'total_current_val' => $totalCurrentVal,
                'total_current_value' => $totalCurrentVal,
                'total_dividends' => $totalDividendsAll,
                'total_interest' => $totalInterestAll,
                'total_return' => $totalReturnAll,
                'overall_roi_pct' => $overallRoiPct,
                'total_items_count' => $investments->count(),
            ],
        ]);
    }

    public function showInvestment($id)
    {
        $investment = \App\Models\Investment::with('transactions')->findOrFail($id);
        $investment->recalculatePerformance();
        $investment->save();

        return view('admin-finance.accounting.investment-detail', [
            'title' => 'Investment Profile: ' . $investment->name,
            'role' => 'Finance Manager',
            'sidebar' => 'admin-finance',
            'investment' => $investment,
        ]);
    }

    public function storeInvestment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'institution' => 'required|string|max:255',
            'principal_amount' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'acquisition_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $code = 'INV-' . date('Ym') . '-' . rand(100, 999);

        $investment = new \App\Models\Investment($request->all());
        $investment->portfolio_code = $code;
        $investment->interest_rate = $request->interest_rate ?: 0.00;
        $investment->recalculatePerformance();
        $investment->save();

        return redirect()->route('admin-finance.investments.index')
            ->with('success', "Investment asset '{$investment->name}' added successfully!");
    }

    public function destroyInvestment($id)
    {
        $investment = \App\Models\Investment::findOrFail($id);
        $name = $investment->name;
        $investment->delete();

        return redirect()->route('admin-finance.investments.index')
            ->with('success', "Investment asset '{$name}' has been deleted successfully.");
    }

    public function storeInvestmentTransaction(Request $request)
    {
        $request->validate([
            'investment_id' => 'required|exists:investments,id',
            'transaction_type' => 'required|string', // Dividend, Interest, Valuation Update
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'reference_no' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $tx = \App\Models\InvestmentTransaction::create($request->all());

        $investment = \App\Models\Investment::findOrFail($request->investment_id);

        if ($request->transaction_type === 'Valuation Update') {
            $investment->current_value = $request->amount;
        }

        $investment->recalculatePerformance();
        $investment->save();

        return redirect()->back()->with('success', "{$request->transaction_type} payout recorded successfully!");
    }

    public function donations(Request $request)
    {
        $filterTabs = [
            'All',
            'Donor Database',
            'Cash Donations',
            'Book Donations',
            'Equipment Donations',
            'Restricted Funds',
            'Projects Supported',
            'Acknowledgement Receipts',
            'Tax Documentation',
            'Reports by Donor',
            'Recurring Donors',
            'Campaign Tracking',
        ];

        $selectedTab = $request->query('tab', 'All');
        $search = $request->query('search');

        // Fetch Donors & Campaigns for dropdowns
        $donors = \App\Models\Donor::with('donations')->latest()->get();
        $campaigns = \App\Models\DonationCampaign::with('donations')->latest()->get();

        foreach ($donors as $dnr) {
            $dnr->recalculateTotals();
            $dnr->save();
        }

        foreach ($campaigns as $cmp) {
            $cmp->recalculateRaised();
            $cmp->save();
        }

        $query = \App\Models\Donation::with(['donor', 'campaign']);

        // Apply Tab Filter
        if ($selectedTab === 'Donor Database') {
            // Handled in view
        } elseif ($selectedTab === 'Cash Donations') {
            $query->where('donation_type', 'Cash');
        } elseif ($selectedTab === 'Book Donations') {
            $query->where('donation_type', 'Books');
        } elseif ($selectedTab === 'Equipment Donations') {
            $query->where('donation_type', 'Equipment');
        } elseif ($selectedTab === 'Restricted Funds') {
            $query->where('is_restricted', true);
        } elseif ($selectedTab === 'Projects Supported') {
            $query->whereNotNull('project_supported');
        } elseif ($selectedTab === 'Acknowledgement Receipts') {
            $query->whereNotNull('receipt_number');
        } elseif ($selectedTab === 'Tax Documentation') {
            $query->where('tax_doc_issued', true);
        } elseif ($selectedTab === 'Recurring Donors') {
            $query->whereHas('donor', function($q) {
                $q->where('is_recurring', true);
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('donation_no', 'like', "%{$search}%")
                  ->orWhere('project_supported', 'like', "%{$search}%")
                  ->orWhere('restricted_fund_purpose', 'like', "%{$search}%")
                  ->orWhereHas('donor', function($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $donations = $query->latest('donation_date')->get();

        $totalCashRaised = \App\Models\Donation::where('donation_type', 'Cash')->sum('amount');
        $totalInKindCount = \App\Models\Donation::whereIn('donation_type', ['Books', 'Equipment'])->count();
        $activeDonorsCount = \App\Models\Donor::where('status', 'Active')->count();
        $totalCampaignRaised = \App\Models\DonationCampaign::sum('raised_amount');

        return view('admin-finance.accounting.donations', [
            'title' => 'Donations Module',
            'role' => 'Finance Manager',
            'sidebar' => 'admin-finance',
            'filterTabs' => $filterTabs,
            'selectedTab' => $selectedTab,
            'search' => $search,
            'donations' => $donations,
            'donors' => $donors,
            'campaigns' => $campaigns,
            'metrics' => [
                'total_cash_raised' => $totalCashRaised,
                'total_in_kind_count' => $totalInKindCount,
                'active_donors_count' => $activeDonorsCount,
                'total_campaign_raised' => $totalCampaignRaised,
                'total_donations_count' => \App\Models\Donation::count(),
            ],
        ]);
    }

    public function showDonation($id)
    {
        $donation = \App\Models\Donation::with(['donor', 'campaign'])->findOrFail($id);

        return view('admin-finance.accounting.donation-detail', [
            'title' => 'Donation Receipt: ' . $donation->donation_no,
            'role' => 'Finance Manager',
            'sidebar' => 'admin-finance',
            'donation' => $donation,
        ]);
    }

    public function storeDonor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'is_recurring' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $code = 'DNR-' . date('Ym') . '-' . rand(1000, 9999);

        $donor = new \App\Models\Donor($request->all());
        $donor->donor_code = $code;
        $donor->is_recurring = $request->has('is_recurring') ? true : false;
        $donor->save();

        return redirect()->back()->with('success', "Donor '{$donor->name}' registered successfully!");
    }

    public function updateDonor(Request $request, $id)
    {
        $donor = \App\Models\Donor::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:100',
        ]);

        $donor->update($request->only(['name', 'type', 'email', 'phone']));

        return redirect()->back()->with('success', "Donor '{$donor->name}' updated successfully!");
    }

    public function destroyDonor($id)
    {
        $donor = \App\Models\Donor::findOrFail($id);
        $name = $donor->name;
        $donor->delete();

        return redirect()->back()->with('success', "Donor '{$name}' has been deleted successfully.");
    }

    public function storeDonation(Request $request)
    {
        $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'campaign_id' => 'nullable|exists:donation_campaigns,id',
            'donation_type' => 'required|string', // Cash, Books, Equipment
            'amount' => 'required|numeric|min:0',
            'item_description' => 'nullable|string',
            'is_restricted' => 'nullable|boolean',
            'restricted_fund_purpose' => 'nullable|string',
            'project_supported' => 'nullable|string',
            'donation_date' => 'required|date',
            'tax_doc_issued' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $no = 'DON-' . date('Ym') . '-' . rand(1000, 9999);
        $receipt = 'AR-DON-' . date('Ym') . '-' . rand(100, 999);
        $taxCert = $request->has('tax_doc_issued') ? 'TAX-CERT-' . date('Ym') . '-' . rand(1000, 9999) : null;

        $donation = new \App\Models\Donation($request->all());
        $donation->donation_no = $no;
        $donation->receipt_number = $receipt;
        $donation->is_restricted = $request->has('is_restricted') ? true : false;
        $donation->tax_doc_issued = $request->has('tax_doc_issued') ? true : false;
        $donation->tax_cert_number = $taxCert;
        $donation->save();

        // Recalculate Donor & Campaign totals
        $donor = \App\Models\Donor::find($request->donor_id);
        if ($donor) {
            $donor->recalculateTotals()->save();
        }

        if ($request->campaign_id) {
            $campaign = \App\Models\DonationCampaign::find($request->campaign_id);
            if ($campaign) {
                $campaign->recalculateRaised()->save();
            }
        }

        return redirect()->route('admin-finance.donations.index')
            ->with('success', "Donation {$no} recorded and Acknowledgement Receipt {$receipt} issued!");
    }

    public function destroyDonation($id)
    {
        $donation = \App\Models\Donation::findOrFail($id);
        $no = $donation->donation_no;
        $donation->delete();

        return redirect()->route('admin-finance.donations.index')
            ->with('success', "Donation record '{$no}' has been deleted successfully.");
    }

    public function storeDonationCampaign(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $code = 'CMP-' . date('Ym') . '-' . rand(1000, 9999);

        $cmp = new \App\Models\DonationCampaign($request->all());
        $cmp->campaign_code = $code;
        $cmp->save();

        return redirect()->back()->with('success', "Fundraising campaign '{$cmp->title}' created successfully!");
    }

    public function budgeting(Request $request)
    {
        $divisions = [
            'Production' => [
                'Pre-Press Department',
                'Press & Printing Department',
                'Post-Press & Binding Department',
                'Logistics & Warehouse Department',
                'Quality Assurance Department',
            ],
            'Sales & Marketing' => [
                'Area Sales Department',
                'Bookstore Department',
                'E-Commerce Department',
                'Wholesale & Direct Sales Department',
                'Ads & Promo Department',
            ],
            'Admin & Finance' => [
                'Accounting & Treasury Department',
                'Credit & Collection Department',
                'General Services (GSD) Department',
                'MIS & IT Department',
                'HR & Personnel Department',
            ],
            'Executive' => [
                'Board & Executive Management',
                'Legal & Compliance Department',
                'Strategic Planning Department',
            ],
        ];

        $selectedDivision = $request->query('division', 'All');
        $fiscalYear = $request->query('year', date('Y'));
        $search = $request->query('search');

        $query = \App\Models\DepartmentBudget::with('lineItems')->where('fiscal_year', $fiscalYear);

        if ($selectedDivision && $selectedDivision !== 'All') {
            $query->where('division', $selectedDivision);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('department', 'like', "%{$search}%")
                  ->orWhere('budget_code', 'like', "%{$search}%")
                  ->orWhere('division', 'like', "%{$search}%");
            });
        }

        $budgets = $query->get();

        foreach ($budgets as $b) {
            $b->recalculateMetrics();
            $b->save();
        }

        $totalAllocated = $budgets->sum('allocated_budget');
        $totalActual = $budgets->sum('actual_spend');
        $totalVariance = $totalAllocated - $totalActual;
        $overallPctUsed = $totalAllocated > 0 ? round(($totalActual / $totalAllocated) * 100, 2) : 0.00;
        $totalForecast = $budgets->sum('forecasted_spend');

        return view('admin-finance.accounting.budgeting', [
            'title' => 'Budgeting Module',
            'role' => 'Finance Manager',
            'sidebar' => 'admin-finance',
            'divisions' => $divisions,
            'selectedDivision' => $selectedDivision,
            'fiscalYear' => $fiscalYear,
            'search' => $search,
            'budgets' => $budgets,
            'metrics' => [
                'total_allocated' => $totalAllocated,
                'total_actual' => $totalActual,
                'total_variance' => $totalVariance,
                'overall_pct_used' => $overallPctUsed,
                'total_forecast' => $totalForecast,
                'total_departments_count' => $budgets->count(),
            ],
        ]);
    }

    public function showBudget($id)
    {
        $budget = \App\Models\DepartmentBudget::with('lineItems')->findOrFail($id);
        $budget->recalculateMetrics();
        $budget->save();

        return view('admin-finance.accounting.budget-detail', [
            'title' => 'Department Budget: ' . $budget->department,
            'role' => 'Finance Manager',
            'sidebar' => 'admin-finance',
            'budget' => $budget,
        ]);
    }

    public function storeDepartmentBudget(Request $request)
    {
        $request->validate([
            'fiscal_year' => 'required|integer|min:2020|max:2050',
            'division' => 'required|string',
            'department' => 'required|string',
            'allocated_budget' => 'required|numeric|min:0',
            'actual_spend' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $divCode = strtoupper(substr(str_replace([' ', '&'], '', $request->division), 0, 4));
        $code = 'BDG-' . $request->fiscal_year . '-' . $divCode . '-' . rand(1000, 9999);

        $budget = \App\Models\DepartmentBudget::firstOrNew([
            'fiscal_year' => $request->fiscal_year,
            'division' => $request->division,
            'department' => $request->department,
        ]);

        $budget->budget_code = $budget->exists ? $budget->budget_code : $code;
        $budget->allocated_budget = $request->allocated_budget;
        $budget->actual_spend = $request->actual_spend ?: 0.00;
        $budget->notes = $request->notes;
        $budget->recalculateMetrics();
        $budget->save();

        return redirect()->route('admin-finance.budgeting.index')
            ->with('success', "Annual Budget for '{$budget->department}' saved successfully!");
    }

    public function destroyDepartmentBudget($id)
    {
        $budget = \App\Models\DepartmentBudget::findOrFail($id);
        $dept = $budget->department;
        $budget->delete();

        return redirect()->route('admin-finance.budgeting.index')
            ->with('success', "Department budget for '{$dept}' has been deleted successfully.");
    }

    public function storeBudgetLineItem(Request $request)
    {
        $request->validate([
            'department_budget_id' => 'required|exists:department_budgets,id',
            'account_category' => 'required|string|max:255',
            'allocated_amount' => 'required|numeric|min:0',
            'actual_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $item = new \App\Models\BudgetLineItem($request->all());
        $item->recalculateVariance();
        $item->save();

        $budget = \App\Models\DepartmentBudget::findOrFail($request->department_budget_id);
        $budget->recalculateMetrics();
        $budget->save();

        return redirect()->back()->with('success', "Line item expense category '{$item->account_category}' recorded!");
    }

    public function cashManagement(Request $request)
    {
        // 0. Ensure default institutional bank accounts exist
        if (\App\Models\CompanyBankAccount::count() === 0) {
            \App\Models\CompanyBankAccount::create([
                'account_code' => '1000',
                'bank_name' => 'BDO Unibank',
                'account_name' => 'Claretian Communications Foundation Inc.',
                'account_number' => '0012-3456-7890',
                'account_type' => 'Checking',
                'currency' => 'PHP',
                'opening_balance' => 250000.00,
                'current_balance' => 250000.00,
                'status' => 'Active',
                'notes' => 'Primary operational & clearing account'
            ]);

            \App\Models\CompanyBankAccount::create([
                'account_code' => '1006',
                'bank_name' => 'BPI (Bank of the Philippine Islands)',
                'account_name' => 'CCFI Operating Account',
                'account_number' => '0987-6543-2100',
                'account_type' => 'Savings',
                'currency' => 'PHP',
                'opening_balance' => 150000.00,
                'current_balance' => 150000.00,
                'status' => 'Active',
                'notes' => 'Secondary operational account'
            ]);

            \App\Models\CompanyBankAccount::create([
                'account_code' => '1020',
                'bank_name' => 'GCash / Merchant E-Wallet',
                'account_name' => 'Claretian Digital Collections',
                'account_number' => '0917-888-9999',
                'account_type' => 'E-Wallet',
                'currency' => 'PHP',
                'opening_balance' => 50000.00,
                'current_balance' => 50000.00,
                'status' => 'Active',
                'notes' => 'Merchant collections & mobile QR'
            ]);
        }

        // 1. Sync paid Sales Orders & Payments to Cash Transactions if none exist yet
        if (\App\Models\CashTransaction::count() === 0) {
            $primaryBank = \App\Models\CompanyBankAccount::first();
            $bankId = $primaryBank ? $primaryBank->id : null;

            // Inflows from Sales Orders
            $salesOrders = \App\Models\SalesOrder::whereIn('type', ['paid', 'calculator_pos'])
                ->orWhere('status', 'completed')
                ->latest()
                ->take(50)
                ->get();

            foreach ($salesOrders as $so) {
                \App\Models\CashTransaction::create([
                    'transaction_no' => 'CTX-IN-' . str_pad($so->id, 5, '0', STR_PAD_LEFT),
                    'bank_account_id' => $bankId,
                    'transaction_type' => 'Deposit',
                    'category' => 'Inflow',
                    'amount' => $so->total_amount,
                    'reference_no' => $so->so_number ?: ('SO-' . $so->id),
                    'payee_or_payer' => $so->customer ? ($so->customer->customer_name ?? $so->customer->company_name) : 'Walk-in Cash Customer',
                    'transaction_date' => $so->created_at ? $so->created_at->format('Y-m-d') : date('Y-m-d'),
                    'status' => 'Cleared',
                    'notes' => 'Customer Sales Order revenue collection (' . ucfirst($so->payment_method ?: 'cash') . ')'
                ]);
            }

            // Outflows from Supplier Payments
            $supplierPayments = \App\Models\Payment::with(['supplier', 'invoice'])->latest()->take(20)->get();
            foreach ($supplierPayments as $sp) {
                \App\Models\CashTransaction::create([
                    'transaction_no' => 'CTX-OUT-' . str_pad($sp->id, 5, '0', STR_PAD_LEFT),
                    'bank_account_id' => $bankId,
                    'transaction_type' => 'Check Issuance',
                    'category' => 'Outflow',
                    'amount' => $sp->amount_paid,
                    'reference_no' => $sp->reference_number ?: ('PAY-' . $sp->id),
                    'payee_or_payer' => $sp->supplier ? $sp->supplier->company_name : 'Supplier Vendor',
                    'transaction_date' => $sp->payment_date ? date('Y-m-d', strtotime($sp->payment_date)) : date('Y-m-d'),
                    'status' => 'Cleared',
                    'notes' => 'Supplier invoice settlement disbursement'
                ]);
            }
        }

        // 2. Ensure Petty Cash Vouchers are created & synced into Cash Transactions
        if (\App\Models\PettyCashVoucher::count() === 0) {
            $pcv1 = \App\Models\PettyCashVoucher::create([
                'pcv_number' => 'PCV-2026-001',
                'type' => 'Store & Office Supplies',
                'date' => date('Y-m-d', strtotime('-2 days')),
                'pay_to' => 'Juan Dela Cruz (Store Custodian)',
                'status' => 'completed',
                'created_by' => auth()->id() ?: 1,
            ]);
            $pcv1->items()->create(['description' => 'Office stationeries & receipt paper rolls', 'amount' => 1250.00]);

            $pcv2 = \App\Models\PettyCashVoucher::create([
                'pcv_number' => 'PCV-2026-002',
                'type' => 'Local Transportation & Freight',
                'date' => date('Y-m-d', strtotime('-1 days')),
                'pay_to' => 'Maria Santos (Courier Transport)',
                'status' => 'completed',
                'created_by' => auth()->id() ?: 1,
            ]);
            $pcv2->items()->create(['description' => 'Local messenger & document dispatch fee', 'amount' => 850.00]);
        }

        $primaryBank = \App\Models\CompanyBankAccount::first();
        $bankId = $primaryBank ? $primaryBank->id : null;

        $pettyVouchers = \App\Models\PettyCashVoucher::with('items')->get();
        foreach ($pettyVouchers as $pcv) {
            $txNo = 'CTX-PCV-' . str_pad($pcv->id, 5, '0', STR_PAD_LEFT);
            $amount = $pcv->items->sum('amount') ?: 0.00;

            \App\Models\CashTransaction::firstOrCreate(
                ['transaction_no' => $txNo],
                [
                    'bank_account_id' => $bankId,
                    'transaction_type' => 'Petty Cash',
                    'category' => 'Outflow',
                    'amount' => $amount,
                    'reference_no' => $pcv->pcv_number ?: ('PCV-' . $pcv->id),
                    'payee_or_payer' => $pcv->pay_to ?: 'Petty Cash Custodian',
                    'transaction_date' => $pcv->date ? date('Y-m-d', strtotime($pcv->date)) : date('Y-m-d'),
                    'status' => 'Cleared',
                    'notes' => 'Petty Cash Voucher Disbursement (' . ($pcv->type ?: 'Expense') . ')'
                ]
            );
        }

        $filterTabs = [
            'Cash Position',
            'Bank Accounts',
            'Cash Flow',
            'Petty Cash',
            'Check Issuance',
            'Deposits',
            'Transfers',
            'Bank Reconciliation',
            'Projected Cash',
        ];

        $selectedTab = $request->query('tab', 'Cash Position');
        $search = $request->query('search');

        $allBankAccounts = \App\Models\CompanyBankAccount::with('transactions')->get();

        foreach ($allBankAccounts as $acct) {
            $acct->recalculateBalance();
            $acct->save();
        }

        $bankAccountsQuery = \App\Models\CompanyBankAccount::with('transactions');
        if ($search) {
            $bankAccountsQuery->where(function($q) use ($search) {
                $q->where('bank_name', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhere('account_code', 'like', "%{$search}%");
            });
        }
        $bankAccounts = $bankAccountsQuery->paginate(10)->withQueryString();

        $query = \App\Models\CashTransaction::with(['bankAccount', 'destinationBankAccount']);

        if ($selectedTab === 'Check Issuance') {
            $query->where('transaction_type', 'Check Issuance');
        } elseif ($selectedTab === 'Deposits') {
            $query->where('transaction_type', 'Deposit');
        } elseif ($selectedTab === 'Transfers') {
            $query->where('transaction_type', 'Transfer');
        } elseif ($selectedTab === 'Bank Reconciliation') {
            $query->where('transaction_type', 'Reconciliation');
        } elseif ($selectedTab === 'Petty Cash') {
            $query->where('transaction_type', 'Petty Cash');
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('transaction_no', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('payee_or_payer', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest('transaction_date')->paginate(10)->withQueryString();

        $totalCashPosition = $allBankAccounts->sum('current_balance');
        $totalInflows = \App\Models\CashTransaction::where('category', 'Inflow')->where('status', '!=', 'Cancelled')->sum('amount');
        $totalOutflows = \App\Models\CashTransaction::where('category', 'Outflow')->where('status', '!=', 'Cancelled')->sum('amount');
        $netCashFlow = $totalInflows - $totalOutflows;

        // Cash projection estimates (Mock/Dynamic based on cash position + pending AR/AP estimates)
        $projected30Days = $totalCashPosition + ($totalCashPosition * 0.12);

        return view('admin-finance.accounting.cash-management', [
            'title' => 'Cash Management Module',
            'role' => 'Finance Manager',
            'sidebar' => 'admin-finance',
            'filterTabs' => $filterTabs,
            'selectedTab' => $selectedTab,
            'search' => $search,
            'bankAccounts' => $bankAccounts,
            'allBankAccounts' => $allBankAccounts,
            'transactions' => $transactions,
            'metrics' => [
                'total_cash_position' => $totalCashPosition,
                'total_inflows' => $totalInflows,
                'total_outflows' => $totalOutflows,
                'net_cash_flow' => $netCashFlow,
                'projected_30_days' => $projected30Days,
                'active_accounts_count' => $allBankAccounts->where('status', 'Active')->count(),
            ],
        ]);
    }

    public function showCashManagementAccount(Request $request, $id)
    {
        $account = \App\Models\CompanyBankAccount::with('transactions')->findOrFail($id);
        $account->recalculateBalance();
        $account->save();

        $query = \App\Models\CashTransaction::where('bank_account_id', $id);

        $search = $request->query('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('transaction_no', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('payee_or_payer', 'like', "%{$search}%")
                  ->orWhere('transaction_type', 'like', "%{$search}%");
            });
        }

        $transactions = $query->latest('transaction_date')->paginate(10)->withQueryString();

        return view('admin-finance.accounting.cash-management-detail', [
            'title' => 'Bank Account Statement: ' . $account->bank_name,
            'role' => 'Finance Manager',
            'sidebar' => 'admin-finance',
            'account' => $account,
            'transactions' => $transactions,
            'search' => $search,
        ]);
    }

    public function storeCompanyBankAccount(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_type' => 'required|string',
            'currency' => 'required|string',
            'opening_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $bankCode = strtoupper(substr(str_replace(' ', '', $request->bank_name), 0, 4));
        $code = 'BANK-' . $bankCode . '-' . rand(100, 999);

        $acct = new \App\Models\CompanyBankAccount($request->all());
        $acct->account_code = $code;
        $acct->current_balance = $request->opening_balance;
        $acct->save();

        return redirect()->back()->with('success', "Bank Account '{$acct->bank_name} - {$acct->account_number}' registered successfully!");
    }

    public function updateCompanyBankAccount(Request $request, $id)
    {
        $acct = \App\Models\CompanyBankAccount::findOrFail($id);
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_type' => 'required|string',
            'opening_balance' => 'required|numeric|min:0',
            'status' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $acct->update($request->only([
            'bank_name',
            'account_name',
            'account_number',
            'account_type',
            'opening_balance',
            'status',
            'notes',
        ]));

        $acct->recalculateBalance()->save();

        return redirect()->back()->with('success', "Bank Account '{$acct->bank_name}' updated successfully!");
    }

    public function destroyCompanyBankAccount($id)
    {
        $acct = \App\Models\CompanyBankAccount::findOrFail($id);
        $name = $acct->bank_name;
        $acct->delete();

        return redirect()->back()->with('success', "Bank Account '{$name}' has been deleted successfully.");
    }

    public function storeCashTransaction(Request $request)
    {
        $request->validate([
            'bank_account_id' => 'required|exists:company_bank_accounts,id',
            'to_bank_account_id' => 'nullable|exists:company_bank_accounts,id',
            'transaction_type' => 'required|string', // Deposit, Check Issuance, Transfer, Reconciliation, Petty Cash
            'category' => 'required|string', // Inflow, Outflow, Transfer
            'amount' => 'required|numeric|min:0',
            'reference_no' => 'nullable|string|max:255',
            'payee_or_payer' => 'nullable|string|max:255',
            'transaction_date' => 'required|date',
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $no = 'CSH-' . date('Ym') . '-' . rand(1000, 9999);

        $tx = new \App\Models\CashTransaction($request->all());
        $tx->transaction_no = $no;
        $tx->save();

        // Recalculate source bank account balance
        $src = \App\Models\CompanyBankAccount::find($request->bank_account_id);
        if ($src) {
            $src->recalculateBalance()->save();
        }

        // Recalculate destination bank account balance if transfer
        if ($request->to_bank_account_id) {
            $dst = \App\Models\CompanyBankAccount::find($request->to_bank_account_id);
            if ($dst) {
                $dst->recalculateBalance()->save();
            }
        }

        return redirect()->back()->with('success', "{$request->transaction_type} transaction '{$no}' recorded successfully!");
    }

    public function financialReports(Request $request)
    {
        $reportsList = [
            'Balance Sheet',
            'Income Statement',
            'Cash Flow',
            'Trial Balance',
            'General Ledger',
            'Subsidiary Ledgers',
            'Sales Reports',
            'Expense Reports',
            'Production Costing',
            // 'Department Reports',
            'Profit by Product',
            'Profit by Customer',
            'Profit by Sales Channel',
            'Profit by Salesperson',
        ];

        $selectedReport = $request->query('report', 'Balance Sheet');
        $startDate = $request->query('start_date', date('Y-01-01'));
        $endDate = $request->query('end_date', date('Y-12-31'));

        // Aggregated live financial metrics from database
        $liveAr = \App\Models\SalesOrder::where('payment_status', '!=', 'paid')
            ->where(function($q) {
                $q->whereNull('proof_of_payment')->orWhere('proof_of_payment', '');
            })
            ->whereNotIn('type', ['paid', 'calculator_pos', 'ecom_direct'])
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount') ?: 0.00;

        $liveAp = \Illuminate\Support\Facades\Schema::hasTable('purchase_orders') ? (\App\Models\PurchaseOrder::where('status', '!=', 'completed')->sum('total_amount') ?: 0.00) : 0.00;
        $liveBookInventory = \Illuminate\Support\Facades\Schema::hasTable('books') ? (\App\Models\Book::sum(\DB::raw('stock * cost')) ?: 0.00) : 0.00;
        $liveExpenses = \Illuminate\Support\Facades\Schema::hasTable('expenses') ? (\App\Models\Expense::sum('amount') ?: 0.00) : 0.00;
        $liveWht = \App\Models\SalesOrder::sum('withholding_tax_amount') ?: 0.00;
        $liveFixedAssets = \Illuminate\Support\Facades\Schema::hasTable('production_fixed_assets')
            ? (\App\Models\ProductionFixedAsset::sum('purchase_price') ?: 0.00)
            : 0.00;

        $bankBalances = \Illuminate\Support\Facades\Schema::hasTable('company_bank_accounts') ? (\App\Models\CompanyBankAccount::sum('current_balance') ?: 0.00) : 0.00;
        $journalCash = (\App\Models\JournalEntryItem::whereHas('account', function($q) {
            $q->where('name', 'like', '%Cash%')->orWhereIn('code', ['1000', '1010']);
        })->sum('debit') - \App\Models\JournalEntryItem::whereHas('account', function($q) {
            $q->where('name', 'like', '%Cash%')->orWhereIn('code', ['1000', '1010']);
        })->sum('credit')) ?: 0.00;
        $totalCash = max(0, $bankBalances + $journalCash);

        $totalAssets = $totalCash + $liveAr + $liveBookInventory + \App\Models\Investment::sum('current_value') + $liveFixedAssets;
        $totalRevenue = \App\Models\SalesOrder::whereIn('status', ['ready_for_delivery', 'completed'])->sum('total_amount') + \App\Models\CashTransaction::where('category', 'Inflow')->sum('amount');
        $totalExpenses = $liveExpenses + \App\Models\CashTransaction::where('category', 'Outflow')->sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

        // Dynamic report data containers
        $reportData = [];
        $totalSalesSum = 0.00;
        $totalExpenseSum = 0.00;

        if ($selectedReport === 'Balance Sheet') {
            $getDynamicAccountLabel = function($code, $fallbackName) {
                $acc = \App\Models\ChartOfAccount::where('code', $code)
                    ->orWhere('name', 'like', '%' . explode(' ', $fallbackName)[0] . '%')
                    ->first();
                if ($acc) {
                    return $acc->code . ' - ' . $acc->name;
                }
                return $code . ' - ' . $fallbackName;
            };

            // Dynamically load Account Groups for Asset
            $assetGroups = \App\Models\AccountGroup::where('type', 'Asset')->with(['accounts' => function($q) {
                $q->where('is_active', true);
            }])->get();

            $currentAssetsList = [];
            foreach ($assetGroups as $grp) {
                if ($grp->accounts->count() > 0) {
                    $grpTotal = 0;
                    $subAccs = [];
                    foreach ($grp->accounts as $acc) {
                        $bal = $this->calculateAccountLiveBalance($acc, 'Asset');
                        $grpTotal += $bal;
                        $subAccs[] = [
                            'code' => $acc->code,
                            'name' => $acc->name,
                            'amount' => $bal,
                        ];
                    }
                    $currentAssetsList[] = [
                        'is_group' => true,
                        'group_name' => $grp->name,
                        'amount' => $grpTotal,
                        'accounts' => $subAccs,
                    ];
                }
            }

            if (empty($currentAssetsList)) {
                $cashAcc = \App\Models\ChartOfAccount::where('name', 'like', '%Cash on Hand%')->first();
                $cashCode = $cashAcc ? $cashAcc->code : '1010';
                $currentAssetsList[] = ['is_group' => false, 'account' => $getDynamicAccountLabel($cashCode, 'Cash & Bank Balances'), 'amount' => $totalCash];
            }
            $currentAssetsList[] = ['is_group' => false, 'account' => $getDynamicAccountLabel('1200', 'Accounts Receivable'), 'amount' => $liveAr];
            $currentAssetsList[] = ['is_group' => false, 'account' => $getDynamicAccountLabel('1300', 'Production Master Inventory'), 'amount' => $liveBookInventory];
            $currentAssetsList[] = ['is_group' => false, 'account' => $getDynamicAccountLabel('1040', 'Short-term Time Deposits'), 'amount' => \App\Models\Investment::where('type', 'Time Deposits')->sum('current_value')];

            // Liabilities Groups & Standalone
            $liabGroups = \App\Models\AccountGroup::where('type', 'Liability')->with(['accounts' => function($q) {
                $q->where('is_active', true);
            }])->get();

            $liabilitiesList = [];
            foreach ($liabGroups as $grp) {
                if ($grp->accounts->count() > 0) {
                    $grpTotal = 0;
                    $subAccs = [];
                    foreach ($grp->accounts as $acc) {
                        $bal = $this->calculateAccountLiveBalance($acc, 'Liability');
                        $grpTotal += $bal;
                        $subAccs[] = [
                            'code' => $acc->code,
                            'name' => $acc->name,
                            'amount' => $bal,
                        ];
                    }
                    $liabilitiesList[] = [
                        'is_group' => true,
                        'group_name' => $grp->name,
                        'amount' => $grpTotal,
                        'accounts' => $subAccs,
                    ];
                }
            }

            $liabilitiesList[] = ['is_group' => false, 'account' => $getDynamicAccountLabel('2000', 'Accounts Payable (Suppliers)'), 'amount' => $liveAp];
            $liabilitiesList[] = ['is_group' => false, 'account' => $getDynamicAccountLabel('2020', 'Accrued Operating Expenses'), 'amount' => $liveExpenses];
            $liabilitiesList[] = ['is_group' => false, 'account' => $getDynamicAccountLabel('2100', 'Withholding Tax Payable'), 'amount' => $liveWht];

            // Equity Groups & Standalone
            $equityGroups = \App\Models\AccountGroup::where('type', 'Equity')->with(['accounts' => function($q) {
                $q->where('is_active', true);
            }])->get();

            $equityList = [];
            foreach ($equityGroups as $grp) {
                if ($grp->accounts->count() > 0) {
                    $grpTotal = 0;
                    $subAccs = [];
                    foreach ($grp->accounts as $acc) {
                        $bal = $this->calculateAccountLiveBalance($acc, 'Equity');
                        $grpTotal += $bal;
                        $subAccs[] = [
                            'code' => $acc->code,
                            'name' => $acc->name,
                            'amount' => $bal,
                        ];
                    }
                    $equityList[] = [
                        'is_group' => true,
                        'group_name' => $grp->name,
                        'amount' => $grpTotal,
                        'accounts' => $subAccs,
                    ];
                }
            }

            $liabilitiesTotal = $liveAp + $liveExpenses + $liveWht;
            $equityList[] = ['is_group' => false, 'account' => $getDynamicAccountLabel('3000', 'Capital & Retained Earnings'), 'amount' => max(0, $totalAssets - $liabilitiesTotal)];

            $reportData = [
                'current_assets' => $currentAssetsList,
                'non_current_assets' => [
                    ['is_group' => false, 'account' => $getDynamicAccountLabel('1600', 'Production Fixed Machinery'), 'amount' => $liveFixedAssets],
                    ['is_group' => false, 'account' => $getDynamicAccountLabel('1700', 'Long-term Investments & Bonds'), 'amount' => \App\Models\Investment::whereIn('type', ['Bonds', 'Stocks', 'Mutual Funds'])->sum('current_value')],
                ],
                'liabilities' => $liabilitiesList,
                'equity' => $equityList,
            ];
        } elseif ($selectedReport === 'Income Statement') {
            $salesFilter = function($q) {
                $q->where(function($sub) {
                    $sub->whereNotNull('sales_orders.proof_of_payment')->where('sales_orders.proof_of_payment', '!=', '')
                       ->orWhere('sales_orders.type', 'ecom_direct')
                       ->orWhere('sales_orders.type', 'calculator_pos')
                       ->orWhere('sales_orders.payment_method', 'cash');
                });
            };

            $query = \App\Models\SalesOrder::leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                ->where($salesFilter)
                ->whereBetween(\DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at)'), [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            $bookstoreRev = (float) (clone $query)->where('sales_orders.type', 'calculator_pos')->sum(\DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount)')) ?: 0.00;
            $areaRev = (float) (clone $query)->whereIn('sales_orders.type', ['area_consignment', 'area_sales_consignment'])->sum(\DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount)')) ?: 0.00;
            $ecomRev = (float) (clone $query)->where('sales_orders.type', 'ecom_direct')->sum(\DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount)')) ?: 0.00;
            $otherRev = (float) (clone $query)->whereNotIn('sales_orders.type', ['calculator_pos', 'area_consignment', 'area_sales_consignment', 'ecom_direct'])->sum(\DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount)')) ?: 0.00;

            // Dynamic Operating Expenses resolution via ChartOfAccount & Expenses table
            $expenseAccounts = \App\Models\ChartOfAccount::where('type', 'Expense')
                ->where('is_active', 1)
                ->orderBy('code')
                ->get();

            $realExpenses = \Illuminate\Support\Facades\Schema::hasTable('expenses')
                ? \App\Models\Expense::whereBetween('expense_date', [$startDate, $endDate])->get()
                : collect();

            $operatingExpenses = [];
            $mappedExpenseTotal = 0.00;

            foreach ($expenseAccounts as $expAcc) {
                $jeAmount = (float) \App\Models\JournalEntryItem::where('chart_of_account_id', $expAcc->id)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('date', [$startDate, $endDate])
                          ->where('status', 'posted');
                    })->sum('debit') ?: 0.00;

                $liveAmount = 0.00;
                $nameLower = strtolower($expAcc->name);
                foreach ($realExpenses as $exp) {
                    $titleLower = strtolower($exp->title);
                    if (str_contains($titleLower, $nameLower) || str_contains($nameLower, $titleLower)) {
                        $liveAmount += (float) $exp->amount;
                    }
                }

                $totalExpAmount = max($jeAmount, $liveAmount);
                if ($totalExpAmount > 0) {
                    $operatingExpenses[] = [
                        'is_group' => false,
                        'category' => $expAcc->code . ' - ' . $expAcc->name,
                        'amount' => $totalExpAmount
                    ];
                    $mappedExpenseTotal += $totalExpAmount;
                }
            }

            $totalRealExpensesSum = (float) $realExpenses->sum('amount');
            if ($totalRealExpensesSum > $mappedExpenseTotal) {
                $unmappedAmount = $totalRealExpensesSum - $mappedExpenseTotal;
                $operatingExpenses[] = [
                    'is_group' => false,
                    'category' => '5999 - Other General & Administrative Expenses',
                    'amount' => $unmappedAmount
                ];
            }

            if (empty($operatingExpenses)) {
                $operatingExpenses[] = ['is_group' => false, 'category' => '5100 - Operating Expenses', 'amount' => $totalRealExpensesSum];
            }

            $getRevLabel = function($code, $fallbackName) {
                $acc = \App\Models\ChartOfAccount::where('code', $code)
                    ->orWhere('name', 'like', '%' . explode(' ', $fallbackName)[0] . '%')
                    ->first();
                return $acc ? ($acc->code . ' - ' . $acc->name) : ($code . ' - ' . $fallbackName);
            };

            $reportData = [
                'revenue' => [
                    ['category' => $getRevLabel('4000', 'Bookstore Sales Revenue (POS)'), 'amount' => $bookstoreRev],
                    ['category' => $getRevLabel('4010', 'Area Sales Revenue (Consignment)'), 'amount' => $areaRev],
                    ['category' => $getRevLabel('4020', 'E-Commerce Website Sales'), 'amount' => $ecomRev],
                    ['category' => $getRevLabel('4030', 'Wholesale & Institution Direct Sales'), 'amount' => $otherRev],
                ],
                'cogs' => [
                    ['category' => $getRevLabel('5000', 'Direct Cost of Sales & Production'), 'amount' => 0.00],
                ],
                'operating_expenses' => $operatingExpenses,
            ];
        } elseif ($selectedReport === 'Cash Flow') {
            $salesFilter = function($q) {
                $q->where(function($sub) {
                    $sub->whereNotNull('sales_orders.proof_of_payment')->where('sales_orders.proof_of_payment', '!=', '')
                       ->orWhere('sales_orders.type', 'ecom_direct')
                       ->orWhere('sales_orders.type', 'calculator_pos')
                       ->orWhere('sales_orders.payment_method', 'cash');
                });
            };

            $cashReceipts = (float) \App\Models\SalesOrder::leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                ->where($salesFilter)
                ->whereBetween(\DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at)'), [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->sum(\DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount)')) ?: 0.00;

            $cashPaidExpenses = \Illuminate\Support\Facades\Schema::hasTable('expenses')
                ? (float) \App\Models\Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount')
                : 0.00;

            $fixedAssetPurchases = \Illuminate\Support\Facades\Schema::hasTable('production_fixed_assets')
                ? (float) \App\Models\ProductionFixedAsset::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->sum('purchase_price')
                : 0.00;

            $netChange = $cashReceipts - $cashPaidExpenses - $fixedAssetPurchases;
            $beginningCash = max(0, $totalCash - $netChange);
            $endingCash = $beginningCash + $netChange;

            $getCashFlowLabel = function($code, $keyword, $fallbackName) {
                $acc = \App\Models\ChartOfAccount::where('code', $code)
                    ->orWhere('name', 'like', "%{$keyword}%")
                    ->first();
                return $acc ? ($acc->code . ' - ' . $acc->name) : $fallbackName;
            };

            $opReceiptLabel = $getCashFlowLabel('1200', 'Receivable', 'Cash Receipts from Customers (Sales & AR)');
            $opExpenseLabel = $getCashFlowLabel('5100', 'Expense', 'Cash Paid for Operating Expenses');
            $invAssetLabel = $getCashFlowLabel('1600', 'Fixed Asset', 'Purchase of Fixed Assets & Machinery');
            $finCapLabel = $getCashFlowLabel('3000', 'Capital', 'Capital & Loan Financing Transactions');

            $reportData = [
                'operating' => [
                    ['category' => $opReceiptLabel, 'amount' => $cashReceipts],
                    ['category' => $opExpenseLabel, 'amount' => -$cashPaidExpenses],
                ],
                'investing' => [
                    ['category' => $invAssetLabel, 'amount' => -$fixedAssetPurchases],
                ],
                'financing' => [
                    ['category' => $finCapLabel, 'amount' => 0.00],
                ],
                'summary' => [
                    'net_change' => $netChange,
                    'beginning' => $beginningCash,
                    'ending' => $endingCash,
                ]
            ];
        } elseif ($selectedReport === 'Sales Reports') {
            $salesFilter = function($q) {
                $q->where(function($sub) {
                    $sub->whereNotNull('sales_orders.proof_of_payment')->where('sales_orders.proof_of_payment', '!=', '')
                       ->orWhere('sales_orders.type', 'ecom_direct')
                       ->orWhere('sales_orders.type', 'calculator_pos')
                       ->orWhere('sales_orders.payment_method', 'cash');
                });
            };

            $query = \App\Models\SalesOrder::leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                ->where($salesFilter)
                ->whereBetween(\DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at)'), [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            $totalSalesSum = (float) (clone $query)->sum(\DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount)')) ?: 0.00;

            $reportData = \App\Models\SalesOrder::leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                ->select(
                    'sales_orders.id',
                    'sales_orders.so_number',
                    'sales_orders.customer_id',
                    'sales_orders.type',
                    'sales_orders.status',
                    \DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at) as effective_date'),
                    \DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount) as effective_amount')
                )
                ->with('customer')
                ->where($salesFilter)
                ->whereBetween(\DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at)'), [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->orderBy('effective_date', 'desc')
                ->paginate(10)
                ->withQueryString();
        } elseif ($selectedReport === 'Expense Reports') {
            if (\Illuminate\Support\Facades\Schema::hasTable('expenses')) {
                $query = \App\Models\Expense::whereBetween('expense_date', [$startDate, $endDate]);
                $totalExpenseSum = (float) (clone $query)->sum('amount') ?: 0.00;
                $reportData = $query->with('department')
                    ->orderBy('expense_date', 'desc')
                    ->paginate(10)
                    ->withQueryString();
            } else {
                $reportData = collect();
            }
        } elseif ($selectedReport === 'Profit by Product') {
            $reportData = \App\Models\Book::select('id', 'name', 'sku', 'price', 'cost')
                ->paginate(10)
                ->through(function($bk) use ($startDate, $endDate) {
                    $salesItems = \App\Models\SalesOrderItem::where('book_id', $bk->id)
                        ->whereHas('order', function($q) use ($startDate, $endDate) {
                            $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                              ->where('status', '!=', 'cancelled');
                        })->get();

                    $salesQty = $salesItems->sum('quantity') ?: 0;
                    $rev = $salesItems->sum('subtotal') ?: 0.00;
                    $cogs = $salesQty * ($bk->cost ?? 0.00);
                    $profit = $rev - $cogs;
                    $margin = $rev > 0 ? round(($profit / $rev) * 100, 1) : 0.0;
                    
                    return [
                        'sku' => $bk->sku ?: 'SKU-PUB',
                        'name' => $bk->name,
                        'sales_qty' => $salesQty,
                        'revenue' => $rev,
                        'cogs' => $cogs,
                        'profit' => $profit,
                        'margin_pct' => $margin,
                    ];
                })
                ->withQueryString();
        } elseif ($selectedReport === 'Profit by Sales Channel') {
            $salesFilter = function($q) {
                $q->where(function($sub) {
                    $sub->whereNotNull('sales_orders.proof_of_payment')->where('sales_orders.proof_of_payment', '!=', '')
                       ->orWhere('sales_orders.type', 'ecom_direct')
                       ->orWhere('sales_orders.type', 'calculator_pos')
                       ->orWhere('sales_orders.payment_method', 'cash');
                });
            };

            $query = \App\Models\SalesOrder::leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                ->where($salesFilter)
                ->whereBetween(\DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at)'), [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

            $posRev = (float) (clone $query)->where('sales_orders.type', 'calculator_pos')->sum(\DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount)')) ?: 0.00;
            $soRev = (float) (clone $query)->whereNotIn('sales_orders.type', ['calculator_pos', 'area_consignment', 'area_sales_consignment', 'ecom_direct'])->sum(\DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount)')) ?: 0.00;
            $nbsRev = (float) (clone $query)->whereIn('sales_orders.type', ['area_consignment', 'area_sales_consignment'])->sum(\DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount)')) ?: 0.00;
            $ecomRev = (float) (clone $query)->where('sales_orders.type', 'ecom_direct')->sum(\DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount)')) ?: 0.00;

            $reportData = [
                ['channel' => 'POS', 'revenue' => $posRev, 'expenses' => 0.00, 'profit' => $posRev, 'margin' => $posRev > 0 ? 100.0 : 0.0],
                ['channel' => 'SO', 'revenue' => $soRev, 'expenses' => 0.00, 'profit' => $soRev, 'margin' => $soRev > 0 ? 100.0 : 0.0],
                ['channel' => 'NBS', 'revenue' => $nbsRev, 'expenses' => 0.00, 'profit' => $nbsRev, 'margin' => $nbsRev > 0 ? 100.0 : 0.0],
                ['channel' => 'E-Com', 'revenue' => $ecomRev, 'expenses' => 0.00, 'profit' => $ecomRev, 'margin' => $ecomRev > 0 ? 100.0 : 0.0],
            ];
        } elseif ($selectedReport === 'Profit by Customer') {
            $reportData = \App\Models\Customer::orderBy('customer_name')
                ->paginate(10)
                ->through(function($cust) {
                    $orders = \App\Models\SalesOrder::where('customer_id', $cust->customer_id)
                        ->where('status', '!=', 'cancelled')
                        ->get();
                    $rev = $orders->sum('total_amount') ?: 0.00;
                    return [
                        'customer' => $cust->customer_name,
                        'type' => $cust->company_name ?: 'Individual',
                        'revenue' => $rev,
                        'cost' => 0.00,
                        'net_profit' => $rev,
                    ];
                })
                ->withQueryString();
        } elseif ($selectedReport === 'Profit by Salesperson') {
            $salesFilter = function($q) {
                $q->where(function($sub) {
                    $sub->whereNotNull('sales_orders.proof_of_payment')->where('sales_orders.proof_of_payment', '!=', '')
                       ->orWhere('sales_orders.type', 'ecom_direct')
                       ->orWhere('sales_orders.type', 'calculator_pos')
                       ->orWhere('sales_orders.payment_method', 'cash');
                });
            };

            $orders = \App\Models\SalesOrder::leftJoin('sales_invoices', 'sales_orders.id', '=', 'sales_invoices.so_id')
                ->select(
                    'sales_orders.prepared_by',
                    'sales_orders.area_sales_staff_id',
                    \DB::raw('COALESCE(sales_invoices.total_amount, sales_orders.total_amount) as amount')
                )
                ->where($salesFilter)
                ->whereBetween(\DB::raw('COALESCE(sales_invoices.created_at, sales_orders.created_at)'), [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->get();

            $userSales = [];

            foreach ($orders as $ord) {
                $amount = (float) $ord->amount;
                $creatorId = $ord->prepared_by;
                $staffId = $ord->area_sales_staff_id;

                if ($staffId) {
                    $userSales[$staffId] = ($userSales[$staffId] ?? 0.00) + $amount;
                }
                if ($creatorId && $creatorId != $staffId) {
                    $userSales[$creatorId] = ($userSales[$creatorId] ?? 0.00) + $amount;
                }
            }

            $userIds = array_keys($userSales);
            $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');

            $salespersonCollection = collect($userSales)->map(function($salesAmount, $userId) use ($users) {
                $user = $users->get($userId);
                $name = $user ? $user->name : 'System / Guest';
                $territory = $user ? ($user->department ?: 'Direct Sales') : 'Direct Sales';

                return [
                    'salesperson' => $name,
                    'territory' => $territory,
                    'quota' => 0.00,
                    'achieved' => $salesAmount,
                    'net_margin' => $salesAmount,
                ];
            })->sortByDesc('achieved')->values();

            $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
            $perPage = 10;
            $currentPageItems = $salespersonCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $reportData = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentPageItems,
                $salespersonCollection->count(),
                $perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
            $reportData->withQueryString();
        } elseif ($selectedReport === 'Trial Balance') {
            $accountQuery = \App\Models\ChartOfAccount::where('is_active', 1);
            if (\Illuminate\Support\Facades\Schema::hasColumn('chart_of_accounts', 'is_postable')) {
                $accountQuery->where('is_postable', 1);
            }
            $accounts = $accountQuery
                ->orderByRaw("
                    CASE type
                        WHEN 'Asset' THEN 1
                        WHEN 'Liability' THEN 2
                        WHEN 'Equity' THEN 3
                        WHEN 'Income' THEN 4
                        WHEN 'Expense' THEN 5
                        ELSE 6
                    END,
                    display_order,
                    code
                ")
                ->get();

            $trialBalanceData = [];
            $totalDebitsSum = 0.00;
            $totalCreditsSum = 0.00;

            foreach ($accounts as $acc) {
                $journalDebits = (float) \App\Models\JournalEntryItem::where('chart_of_account_id', $acc->id)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('date', [$startDate, $endDate])
                          ->where('status', 'posted');
                    })->sum('debit') ?: 0.00;

                $journalCredits = (float) \App\Models\JournalEntryItem::where('chart_of_account_id', $acc->id)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('date', [$startDate, $endDate])
                          ->where('status', 'posted');
                    })->sum('credit') ?: 0.00;

                $liveAdd = 0.00;
                if (str_contains(strtolower($acc->name), 'cash') || in_array($acc->code, ['1000', '1010'])) {
                    $liveAdd = $totalCash;
                } elseif (str_contains(strtolower($acc->name), 'receivable') || $acc->code === '1200') {
                    $liveAdd = $liveAr;
                } elseif (str_contains(strtolower($acc->name), 'inventory') || in_array($acc->code, ['1030', '1300'])) {
                    $liveAdd = $liveBookInventory;
                } elseif (str_contains(strtolower($acc->name), 'payable') || in_array($acc->code, ['2000', '2010'])) {
                    $liveAdd = $liveAp;
                } elseif ($acc->code === '2020' || str_contains(strtolower($acc->name), 'operating expenses')) {
                    $liveAdd = $liveExpenses;
                }

                $rawDebit = $journalDebits;
                $rawCredit = $journalCredits;

                if ($liveAdd > 0 && $rawDebit == 0 && $rawCredit == 0) {
                    if (in_array($acc->type, ['Asset', 'Expense'])) {
                        $rawDebit = $liveAdd;
                    } else {
                        $rawCredit = $liveAdd;
                    }
                }

                $netAmount = $rawDebit - $rawCredit;
                $debitBalance = 0.00;
                $creditBalance = 0.00;

                if (in_array($acc->type, ['Asset', 'Expense'])) {
                    if ($netAmount >= 0) {
                        $debitBalance = $netAmount;
                    } else {
                        $creditBalance = abs($netAmount);
                    }
                } else {
                    if ($netAmount <= 0) {
                        $creditBalance = abs($netAmount);
                    } else {
                        $debitBalance = $netAmount;
                    }
                }

                $trialBalanceData[] = [
                    'code' => $acc->code,
                    'name' => $acc->name,
                    'type' => $acc->type,
                    'category' => $acc->category,
                    'debit' => $debitBalance,
                    'credit' => $creditBalance,
                ];
                $totalDebitsSum += $debitBalance;
                $totalCreditsSum += $creditBalance;
            }

            $trialBalanceCollection = collect($trialBalanceData);
            $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
            $perPage = 15;
            $currentPageItems = $trialBalanceCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $paginatedAccounts = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentPageItems,
                $trialBalanceCollection->count(),
                $perPage,
                $currentPage,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
            $paginatedAccounts->withQueryString();

            $reportData = [
                'accounts' => $paginatedAccounts,
                'total_debits' => $totalDebitsSum,
                'total_credits' => $totalCreditsSum,
                'is_balanced' => abs($totalDebitsSum - $totalCreditsSum) < 0.01,
            ];
        } elseif ($selectedReport === 'General Ledger') {
            // Fetch active, postable Asset, Liability, and Equity accounts
            $glAccounts = \App\Models\ChartOfAccount::where('is_active', 1)
                ->where('is_postable', 1)
                ->whereIn('type', ['Asset', 'Liability', 'Equity'])
                ->orderByRaw("
                    CASE type
                        WHEN 'Asset' THEN 1
                        WHEN 'Liability' THEN 2
                        WHEN 'Equity' THEN 3
                        ELSE 4
                    END,
                    display_order,
                    code
                ")
                ->get();

            $glAccountId = $request->query('account_id');
            if (!$glAccountId && $glAccounts->isNotEmpty()) {
                $glAccountId = $glAccounts->first()->id;
            }

            $selectedGlAccount = $glAccountId ? \App\Models\ChartOfAccount::find($glAccountId) : null;

            $totalDebits = 0.00;
            $totalCredits = 0.00;

            if ($selectedGlAccount) {
                $totalDebits = (float) \App\Models\JournalEntryItem::where('chart_of_account_id', $selectedGlAccount->id)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('date', [$startDate, $endDate])
                          ->where('status', 'posted');
                    })
                    ->sum('debit') ?: 0.00;

                $totalCredits = (float) \App\Models\JournalEntryItem::where('chart_of_account_id', $selectedGlAccount->id)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('date', [$startDate, $endDate])
                          ->where('status', 'posted');
                    })
                    ->sum('credit') ?: 0.00;

                $paginatedQuery = \App\Models\JournalEntryItem::where('chart_of_account_id', $selectedGlAccount->id)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('date', [$startDate, $endDate])
                          ->where('status', 'posted');
                    })
                    ->join('journal_entries', 'journal_entry_items.journal_entry_id', '=', 'journal_entries.id')
                    ->orderBy('journal_entries.date', 'desc')
                    ->orderBy('journal_entries.id', 'desc')
                    ->orderBy('journal_entry_items.id', 'desc')
                    ->select('journal_entry_items.*')
                    ->with(['journalEntry']);

                $items = $paginatedQuery->paginate(15)->withQueryString();
            } else {
                $items = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            }

            $reportData = [
                'accounts' => $glAccounts,
                'selected_account' => $selectedGlAccount,
                'items' => $items,
                'total_debits' => $totalDebits,
                'total_credits' => $totalCredits,
            ];
        } elseif ($selectedReport === 'Subsidiary Ledgers') {
            // Fetch active accounts under Income and Expense categories
            $subsidiaryAccounts = \App\Models\ChartOfAccount::where('is_active', 1)
                ->whereIn('type', ['Income', 'Expense'])
                ->orderByRaw("
                    CASE type
                        WHEN 'Income' THEN 1
                        WHEN 'Expense' THEN 2
                        ELSE 3
                    END,
                    display_order,
                    code
                ")
                ->get();

            $subAccountId = $request->query('account_id');
            if (!$subAccountId && $subsidiaryAccounts->isNotEmpty()) {
                $subAccountId = $subsidiaryAccounts->first()->id;
            }

            $selectedSubAccount = $subAccountId ? \App\Models\ChartOfAccount::find($subAccountId) : null;

            $totalIncomeDebits = 0;
            $totalIncomeCredits = 0;
            $totalExpenseDebits = 0;
            $totalExpenseCredits = 0;

            // Calculate Period Aggregates for all Income accounts
            $incomeAccountIds = $subsidiaryAccounts->where('type', 'Income')->pluck('id')->toArray();
            if (!empty($incomeAccountIds)) {
                $totalIncomeDebits = (float) \App\Models\JournalEntryItem::whereIn('chart_of_account_id', $incomeAccountIds)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('date', [$startDate, $endDate])
                          ->where('status', 'posted');
                    })->sum('debit') ?: 0.00;

                $totalIncomeCredits = (float) \App\Models\JournalEntryItem::whereIn('chart_of_account_id', $incomeAccountIds)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('date', [$startDate, $endDate])
                          ->where('status', 'posted');
                    })->sum('credit') ?: 0.00;
            }

            // Calculate Period Aggregates for all Expense accounts
            $expenseAccountIds = $subsidiaryAccounts->where('type', 'Expense')->pluck('id')->toArray();
            if (!empty($expenseAccountIds)) {
                $totalExpenseDebits = (float) \App\Models\JournalEntryItem::whereIn('chart_of_account_id', $expenseAccountIds)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('date', [$startDate, $endDate])
                          ->where('status', 'posted');
                    })->sum('debit') ?: 0.00;

                $totalExpenseCredits = (float) \App\Models\JournalEntryItem::whereIn('chart_of_account_id', $expenseAccountIds)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('date', [$startDate, $endDate])
                          ->where('status', 'posted');
                    })->sum('credit') ?: 0.00;
            }

            if ($selectedSubAccount) {
                $paginatedQuery = \App\Models\JournalEntryItem::where('chart_of_account_id', $selectedSubAccount->id)
                    ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('date', [$startDate, $endDate])
                          ->where('status', 'posted');
                    })
                    ->join('journal_entries', 'journal_entry_items.journal_entry_id', '=', 'journal_entries.id')
                    ->orderBy('journal_entries.date', 'desc')
                    ->orderBy('journal_entries.id', 'desc')
                    ->orderBy('journal_entry_items.id', 'desc')
                    ->select('journal_entry_items.*')
                    ->with(['journalEntry']);

                $items = $paginatedQuery->paginate(15)->withQueryString();
            } else {
                $items = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            }

            $reportData = [
                'accounts' => $subsidiaryAccounts,
                'selected_account' => $selectedSubAccount,
                'items' => $items,
                'total_income_debits' => $totalIncomeDebits,
                'total_income_credits' => $totalIncomeCredits,
                'total_expense_debits' => $totalExpenseDebits,
                'total_expense_credits' => $totalExpenseCredits,
            ];
        } elseif ($selectedReport === 'Production Costing') {
            $search = $request->query('search');
            $costingsQuery = \App\Models\ProductionCosting::with('book');

            if ($search) {
                $costingsQuery->where(function($q) use ($search) {
                    $q->where('job_title', 'like', "%{$search}%")
                      ->orWhere('job_number', 'like', "%{$search}%");
                });
            }

            $allCostings = $costingsQuery->latest()->get();
            $paginatedCostings = $costingsQuery->latest()->paginate(15)->withQueryString();

            $reportData = [
                'items' => $paginatedCostings,
                'total_cogs_sum' => $allCostings->sum('total_cogs'),
                'avg_unit_cogs' => $allCostings->count() > 0 ? $allCostings->avg('unit_cogs') : 0.00,
                'total_qty_produced' => $allCostings->sum('quantity_produced'),
                'active_jobs_count' => $allCostings->count(),
            ];
        }

        return view('admin-finance.accounting.financial-reports', [
            'title' => 'Financial Reports Module',
            'role' => 'Finance Manager',
            'sidebar' => 'admin-finance',
            'reportsList' => $reportsList,
            'selectedReport' => $selectedReport,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reportData' => $reportData,
            'totalSalesSum' => $totalSalesSum,
            'totalExpenseSum' => $totalExpenseSum,
            'metrics' => [
                'total_assets' => $totalAssets,
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'net_profit' => $netProfit,
            ],
        ]);
    }

    /**
     * Approve Team Stock Transfer by Admin & Finance (moves to Production approval queue)
     */
    public function approveTeamStockTransferByAdminFinance(Request $request, $id)
    {
        $transfer = \App\Models\TeamStockTransfer::findOrFail($id);
        if ($transfer->status !== 'pending_af_approval') {
            return redirect()->back()->with('error', 'This transfer is not pending Admin & Finance approval.');
        }

        $remarks = $request->input('approval_remarks') ?: ($request->input('remarks') ?: null);
        $updateData = [
            'status' => 'pending_prod_approval',
            'approved_by_af' => auth()->id(),
        ];
        if ($remarks) {
            $existingRemarks = $transfer->remarks;
            $updateData['remarks'] = $existingRemarks ? ($existingRemarks . "\n[Admin & Finance]: " . $remarks) : ('[Admin & Finance]: ' . $remarks);
        }

        $transfer->update($updateData);

        return redirect()->back()->with('success', 'Team Stock Transfer #' . $transfer->transfer_number . ' approved by Admin & Finance! Moved to Production Approval Queue.');
    }

    /**
     * Reject Team Stock Transfer by Admin & Finance
     */
    public function rejectTeamStockTransferByAdminFinance(Request $request, $id)
    {
        $transfer = \App\Models\TeamStockTransfer::findOrFail($id);
        if ($transfer->status !== 'pending_af_approval') {
            return redirect()->back()->with('error', 'This transfer is not pending Admin & Finance approval.');
        }

        $reason = $request->input('rejection_reason');
        $existingRemarks = $transfer->remarks;
        $transfer->update([
            'status' => 'rejected',
            'remarks' => $existingRemarks ? ($existingRemarks . "\n[Admin & Finance Rejection]: " . $reason) : ('[Admin & Finance Rejection]: ' . $reason),
        ]);

        return redirect()->back()->with('success', 'Team Stock Transfer #' . $transfer->transfer_number . ' has been rejected.');
    }

    /**
     * Production Costing Accounting View (Accounting Reports Tab)
     */
    public function productionCosting(Request $request)
    {
        $search = $request->query('search');
        $query = \App\Models\ProductionCosting::with('book');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('job_title', 'like', "%{$search}%")
                  ->orWhere('job_number', 'like', "%{$search}%");
            });
        }

        $allCostings = $query->latest()->get();
        $costings = $query->latest()->paginate(10)->withQueryString();
        $books = \App\Models\Book::orderBy('name')->get();

        return view('admin-finance.accounting.production-costing', [
            'title' => 'Production Costing & COGS Accounting',
            'role' => auth()->user() ? auth()->user()->position : 'Finance Manager',
            'sidebar' => 'admin-finance',
            'costings' => $costings,
            'books' => $books,
            'metrics' => [
                'total_cogs' => $allCostings->sum('total_cogs'),
                'avg_unit_cogs' => $allCostings->count() > 0 ? $allCostings->avg('unit_cogs') : 0.00,
                'total_qty_produced' => $allCostings->sum('quantity_produced'),
                'active_jobs_count' => $allCostings->count(),
            ],
        ]);
    }

    /**
     * Show Production Costing Sheet (Accounting Reports View)
     */
    public function showProductionCosting($id)
    {
        $costing = \App\Models\ProductionCosting::with('book')->findOrFail($id);

        return view('admin-finance.accounting.production-costing-show', [
            'title' => 'Production Costing Sheet',
            'role' => auth()->user() ? auth()->user()->position : 'Finance Manager',
            'sidebar' => 'admin-finance',
            'costing' => $costing,
        ]);
    }
}




