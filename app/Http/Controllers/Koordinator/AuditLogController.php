<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $module = $request->input('module');
        $action = $request->input('action');
        $timeframe = $request->input('timeframe', 'day');

        // Table Data Query
        $query = AuditLog::with(['user.mahasiswa', 'user.dosen'])
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%$search%");
                    })
                    ->orWhere('module', 'like', "%$search%")
                    ->orWhere('action', 'like', "%$search%");
                });
            })
            ->when($role, fn($q) => $q->where('role', $role))
            ->when($module, fn($q) => $q->where('module', $module))
            ->when($action, fn($q) => $q->where('action', 'like', "%$action%"))
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        $logs = $query->paginate(10);

        // Chart Data
        $chartData = $this->getChartData($timeframe);
        $donutData = $this->getDonutData($timeframe);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('koordinator.components.audit-log-table-rows', compact('logs'))->render(),
                'pagination' => (string) $logs->appends(request()->query())->links('vendor.pagination.custom'),
                'chartData' => $chartData,
                'donutData' => $donutData
            ]);
        }

        return view('koordinator.audit-log', compact('logs', 'chartData', 'donutData', 'timeframe'));
    }

    private function getChartData($timeframe)
    {
        $data = [];
        $labels = [];
        $now = now();

        switch ($timeframe) {
            case 'minute':
                for ($i = 59; $i >= 0; $i--) {
                    $t = $now->copy()->subMinutes($i);
                    $labels[] = $t->format('H:i');
                    $count = AuditLog::whereBetween('created_at', [
                        $t->copy()->startOfMinute()->toDateTimeString(),
                        $t->copy()->endOfMinute()->toDateTimeString()
                    ])->count();
                    $data[] = $count;
                }
                break;
            case 'hour':
                for ($i = 23; $i >= 0; $i--) {
                    $t = $now->copy()->subHours($i);
                    $labels[] = $t->format('H:00');
                    $count = AuditLog::whereBetween('created_at', [
                        $t->copy()->startOfHour()->toDateTimeString(),
                        $t->copy()->endOfHour()->toDateTimeString()
                    ])->count();
                    $data[] = $count;
                }
                break;
            case 'day':
                for ($i = 29; $i >= 0; $i--) {
                    $t = $now->copy()->subDays($i);
                    $labels[] = $t->format('d M');
                    $count = AuditLog::whereDate('created_at', $t->toDateString())->count();
                    $data[] = $count;
                }
                break;
            case 'month':
                for ($i = 11; $i >= 0; $i--) {
                    $t = $now->copy()->subMonths($i);
                    $labels[] = $t->format('M Y');
                    $count = AuditLog::whereBetween('created_at', [
                        $t->copy()->startOfMonth()->toDateTimeString(),
                        $t->copy()->endOfMonth()->toDateTimeString()
                    ])->count();
                    $data[] = $count;
                }
                break;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getDonutData($timeframe)
    {
        $activeUsers = AuditLog::whereNotNull('user_id')
            ->where('created_at', '>=', now()->subDay())
            ->distinct('user_id')
            ->count();
        
        $totalUsers = \App\Models\User::count() ?: 1;
        $percent = round(($activeUsers / $totalUsers) * 100);

        return [
            'active_percent' => $percent,
            'active_count' => $activeUsers,
            'total_count' => $totalUsers,
            'label' => "Sebanyak $activeUsers dari $totalUsers user aktif (Server Time: " . now()->format('H:i:s') . ")"
        ];
    }
}
