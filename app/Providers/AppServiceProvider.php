<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View; // 1. Import View Facade
use Illuminate\Support\Facades\Auth; // 2. Import Auth Facade
use App\Models\Task; // 3. Import Task Model
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Carbon::setLocale('id_ID');

        // 4. Berbagi data notifikasi ke semua view
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                
                // Ambil 5 notifikasi tugas terbaru yang belum dibaca untuk pengguna
                $notifications = Task::where('assigned_to', $user->id)
                                     // ->where('is_read', false) // Anda perlu menambahkan kolom 'is_read' di tabel tasks
                                     ->latest()
                                     ->take(5)
                                     ->get();

                $view->with('user_notifications', $notifications);
            }
        });
    }
}
