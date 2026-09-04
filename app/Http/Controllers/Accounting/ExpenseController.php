<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Department;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     */
    public function index(Request $request)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.expenses')) {
            abort(403, 'Unauthorized.');
        }

        $search = $request->input('search');
        $deptId = $request->input('dept_id');

        $expensesQuery = Expense::with(['department', 'addedBy'])
            ->latest();

        if ($search) {
            $expensesQuery->where('title', 'like', '%' . $search . '%');
        }

        if ($deptId) {
            $expensesQuery->where('department_id', $deptId);
        }

        $expenses = $expensesQuery->paginate(15);

        // Fetch departments for dropdown
        $departments = Department::orderBy('dept_name', 'asc')->get();
        if ($departments->isEmpty()) {
            $defaultDepts = [
                'Accounting & Finance',
                'Sales & Marketing',
                'Production & Printing',
                'Logistics & Warehouse',
                'Human Resources',
                'Executive Office',
                'General Services (GSD)',
                'IT & MIS',
                'Bookstore Operations',
            ];
            foreach ($defaultDepts as $name) {
                Department::firstOrCreate(['dept_name' => $name]);
            }
            $departments = Department::orderBy('dept_name', 'asc')->get();
        }

        return view('admin-finance.accounting.expenses.index', [
            'title' => 'Expenses Management',
            'role' => $user->position,
            'sidebar' => 'admin-finance',
            'expenses' => $expenses,
            'departments' => $departments,
            'search' => $search,
            'dept_id' => $deptId
        ]);
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.expenses')) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,dept_id',
            'notes' => 'nullable|string',
        ]);

        Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'department_id' => $request->department_id,
            'added_by' => $user->id,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin-finance.accounting.expenses.index')
            ->with('success', 'Expense record created successfully.');
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, $id)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.expenses')) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'department_id' => 'nullable|exists:departments,dept_id',
            'notes' => 'nullable|string',
        ]);

        $expense = Expense::findOrFail($id);
        $expense->update([
            'title' => $request->title,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'department_id' => $request->department_id,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin-finance.accounting.expenses.index')
            ->with('success', 'Expense record updated successfully.');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy($id)
    {
        // Permission check
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('admin_finance.accounting') && !$user->hasPermission('admin_finance.accounting.expenses')) {
            abort(403, 'Unauthorized.');
        }

        $expense = Expense::findOrFail($id);
        $expense->delete();

        return redirect()->route('admin-finance.accounting.expenses.index')
            ->with('success', 'Expense record deleted successfully.');
    }
}
