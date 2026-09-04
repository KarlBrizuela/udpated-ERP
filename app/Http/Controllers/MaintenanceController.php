<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
    public function showClearData()
    {
        $user = auth()->user();
        if (!$user || $user->position !== 'Super Admin') {
            abort(403);
        }

        return view('admin.maintenance.clear-data', [
            'title' => 'Maintenance',
            'role' => $user->position,
            'sidebar' => 'super-admin'
        ]);
    }

    public function clearData(Request $request)
    {
        $user = auth()->user();
        if (!$user || $user->position !== 'Super Admin') {
            abort(403);
        }

        $request->validate([
            'confirm' => 'required|in:1'
        ]);

        $excluded = [
            'users',
            'book_categories',
            'migrations',
            // preserve assigned divisions used by Super Admin Roles & Permissions UI
            'division_user'
        ];

        DB::beginTransaction();
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            $rows = DB::select('SHOW TABLES');
            $key = null;
            if (!empty($rows)) {
                $first = (array) $rows[0];
                $keys = array_keys($first);
                $key = $keys[0] ?? null;
            }
            foreach ($rows as $r) {
                $row = (array)$r;
                $table = $key ? ($row[$key] ?? null) : null;
                if (!$table) continue;
                if (in_array($table, $excluded)) continue;
                if (strpos($table, 'failed_jobs') !== false) continue;
                DB::table($table)->truncate();
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::commit();

            Log::info('Maintenance: cleared DB tables', ['by_user_id' => $user->id]);
            return redirect()->back()->with('success', 'Database cleared (users and book categories preserved).');
        } catch (\Exception $e) {
            // Only attempt rollback if a transaction is active
            try {
                $pdo = DB::getPdo();
                if (method_exists($pdo, 'inTransaction') && $pdo->inTransaction()) {
                    DB::rollBack();
                }
            } catch (\Throwable $ex) {
                // ignore errors during rollback attempt
            }

            // Ensure FK checks are restored
            try { DB::statement('SET FOREIGN_KEY_CHECKS=1'); } catch (\Throwable $__) {}

            Log::error('Maintenance: clear DB failed', ['error' => $e->getMessage(), 'by_user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to clear database: ' . $e->getMessage());
        }
    }
}
