<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * Menampilkan halaman manajemen tim.
     */
    public function index()
    {
        // Mengambil semua user, diurutkan agar admin (jika ada) atau user yang sedang login di atas
        $users = User::orderBy('id')->paginate(15);
        return view('team.index', compact('users'));
    }

    /**
     * Memperbarui role seorang pengguna.
     */
    public function updateRole(Request $request, User $user)
    {
        // Validasi input
        $request->validate([
            'role_id' => 'required|in:1,2', // Memastikan role_id hanya 1 (Admin) atau 2 (User)
        ]);

        // Mencegah admin mengubah role mereka sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('team.index')->with('error', 'Anda tidak dapat mengubah role Anda sendiri.');
        }

        $user->update([
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('team.index')->with('success', 'Role pengguna berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna dari sistem.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Mencegah admin menghapus akunnya sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('team.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Simpan nama user untuk pesan notifikasi sebelum dihapus
        $userName = $user->name;
        
        // Hapus user
        $user->delete();

        // Redirect kembali ke halaman tim dengan pesan sukses
        return redirect()->route('team.index')->with('success', "Pengguna '{$userName}' telah berhasil dihapus.");
    }
}
