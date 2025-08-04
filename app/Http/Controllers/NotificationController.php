<?php

// 3. app/Http/Controllers/NotificationController.php
// Ganti seluruh isi file ini dengan kode di bawah.

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mengambil notifikasi yang belum dibaca dan mengirimkannya sebagai JSON.
     */
    public function getLatest()
    {
        $user = Auth::user();
        $notifications = $user->unreadNotifications;

        $response = [];

        if ($notifications->isNotEmpty()) {
            foreach ($notifications as $notification) {
                // Format data ini HARUS sesuai dengan yang diharapkan JavaScript
                $response[] = [
                    'id' => $notification->id,
                    'data' => $notification->data, // Mengirim semua data notifikasi
                    'created_at_human' => $notification->created_at->diffForHumans()
                ];
            }
            // --- PERBAIKAN: Baris ini dihapus agar tidak terjadi konflik ---
            // $user->unreadNotifications->markAsRead(); 
        }

        return response()->json($response);
    }

    /**
     * Tandai semua notifikasi yang belum dibaca sebagai sudah dibaca.
     * Method ini akan dipanggil oleh JavaScript saat lonceng di-klik.
     */
    public function markAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['status' => 'success']);
    }
}
