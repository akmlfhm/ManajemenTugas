<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        

        $user = auth()->user();
        
        // Variabel ini akan diisi berdasarkan role user
        $totalTasks = 0;
        $pendingTasks = 0;
        $completedToday = 0;
        $dueToday = 0;
        $upcomingDeadlines = [];
        $recentActivities = [];

        // Menghitung jumlah anggota (user dengan role_id 2)
        // Variabel ini dinamai $activeUsers agar sesuai dengan view yang sudah ada
        $activeUsers = User::where('role_id', 2)->count();

        if ($user->isAdmin()) {
            // Data untuk Admin
            $totalTasks = Task::count(); // Menghitung semua tugas
            $pendingTasks = Task::where('status', 'pending')->count();
            $completedToday = Task::where('status', 'completed')
                ->whereDate('updated_at', today())
                ->count();
            $dueToday = Task::whereDate('due_date', today())->count();
            
            $upcomingDeadlines = Task::with(['assignedTo'])
                ->whereIn('status', ['pending', 'in_progress'])
                ->orderBy('due_date', 'asc')
                ->take(5) // Mengambil 5 tugas terdekat
                ->get();
                
            $recentActivities = Task::with(['assignedTo'])
                ->orderBy('updated_at', 'desc')
                ->take(5) // Mengambil 5 aktivitas terbaru
                ->get();
        } else {
            // Data untuk User biasa
            $totalTasks = Task::where('assigned_to', $user->id)->count(); // Menghitung tugas milik user
            $pendingTasks = Task::where('assigned_to', $user->id)
                ->where('status', 'pending')
                ->count();
            $completedToday = Task::where('assigned_to', $user->id)
                ->where('status', 'completed')
                ->whereDate('updated_at', today())
                ->count();
            $dueToday = Task::where('assigned_to', $user->id)
                ->whereDate('due_date', today())
                ->count();
            
            $upcomingDeadlines = Task::with(['assignedTo'])
                ->where('assigned_to', $user->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->orderBy('due_date', 'asc')
                ->take(5)
                ->get();
                
            $recentActivities = Task::with(['assignedTo'])
                ->where('assigned_to', $user->id)
                ->orderBy('updated_at', 'desc')
                ->take(5)
                ->get();
        }

        // Mengirim semua variabel yang dibutuhkan oleh view
        return view('dashboard', compact(
            'totalTasks',
            'pendingTasks',
            'completedToday',
            'dueToday',
            'activeUsers',
            'upcomingDeadlines',
            'recentActivities'
        ));
    }
}
