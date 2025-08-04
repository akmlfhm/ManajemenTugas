<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Menampilkan halaman dashboard analytics.
     */
    public function index()
    {
        // 1. Data untuk grafik: Tugas Selesai per Hari (7 hari terakhir)
        $tasksCompletedPerDay = Task::where('status', 'completed')
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get([
                DB::raw('DATE(updated_at) as date'),
                DB::raw('COUNT(*) as count')
            ]);

        // Format data untuk Chart.js
        $completionLabels = $tasksCompletedPerDay->pluck('date')->map(function ($date) {
            return Carbon::parse($date)->format('D, M j');
        });
        $completionData = $tasksCompletedPerDay->pluck('count');


        // 2. Data untuk grafik: Distribusi Tugas berdasarkan Status
        $taskStatusDistribution = Task::groupBy('status')
            ->get([
                'status',
                DB::raw('COUNT(*) as count')
            ]);
        
        $statusLabels = $taskStatusDistribution->pluck('status')->map(function ($status) {
            return ucfirst(str_replace('_', ' ', $status));
        });
        $statusData = $taskStatusDistribution->pluck('count');


        // 3. Data untuk grafik: Kinerja Pengguna (Top 5 berdasarkan tugas selesai)
        $userPerformance = User::where('role_id', 2) // Hanya user biasa
            ->withCount(['assignedTasks' => function ($query) {
                $query->where('status', 'completed');
            }])
            ->orderBy('assigned_tasks_count', 'desc')
            ->take(5)
            ->get();
            
        $userLabels = $userPerformance->pluck('name');
        $userData = $userPerformance->pluck('assigned_tasks_count');


        return view('analytics.index', compact(
            'completionLabels', 'completionData',
            'statusLabels', 'statusData',
            'userLabels', 'userData'
        ));
    }
}
