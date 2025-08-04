<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AIController extends Controller
{
    /**
     * Process AI command from the chatbot.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processCommand(Request $request)
    {
        $command = strtolower($request->input('command', ''));

        // Command: "hapus user [nama]"
        if (Str::startsWith($command, 'hapus user ')) {
            $name = Str::after($command, 'hapus user ');
            $user = User::where('name', 'like', '%' . $name . '%')->first();

            if (!$user) {
                return response()->json(['reply' => "Maaf, pengguna dengan nama '{$name}' tidak ditemukan."]);
            }

            if ($user->id === Auth::id()) {
                return response()->json(['reply' => 'Anda tidak dapat menghapus akun Anda sendiri melalui AI.']);
            }

            $user->delete();
            return response()->json([
                'status' => 'success',
                'reply' => "Baik, pengguna '{$user->name}' telah berhasil dihapus. Halaman akan dimuat ulang."
            ]);
        }

        // Command: "jadikan [nama] admin"
        if (Str::startsWith($command, 'jadikan ')) {
            $parts = explode(' ', $command);
            $name = $parts[1] ?? '';
            $role = end($parts); // admin or mahasiswa

            if ($role !== 'admin' && $role !== 'mahasiswa') {
                return response()->json(['reply' => "Peran yang Anda maksud tidak valid. Gunakan 'admin' atau 'mahasiswa'."]);
            }

            $user = User::where('name', 'like', '%' . $name . '%')->first();

            if (!$user) {
                return response()->json(['reply' => "Maaf, pengguna dengan nama '{$name}' tidak ditemukan."]);
            }
            
            if ($user->id === Auth::id()) {
                return response()->json(['reply' => 'Anda tidak dapat mengubah role Anda sendiri melalui AI.']);
            }

            $role_id = ($role === 'admin') ? 1 : 2;
            $user->update(['role_id' => $role_id]);

            return response()->json([
                'status' => 'success',
                'reply' => "Siap! Role untuk '{$user->name}' telah diubah menjadi {$role}. Halaman akan dimuat ulang."
            ]);
        }

        // Default reply if command is not understood
        return response()->json(['reply' => 'Maaf, saya belum mengerti perintah itu. Coba perintah lain seperti "hapus user [nama]".']);
    }
}
