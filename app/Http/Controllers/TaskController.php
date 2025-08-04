<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

// --- TAMBAHAN ---
// Memanggil class notifikasi yang akan kita gunakan.
use App\Notifications\TugasDiberikan;
use App\Notifications\SubmissionReceived;
// --- AKHIR TAMBAHAN ---

class TaskController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Task::with('assignedTo', 'createdBy')->latest();
        // Jika pengguna bukan admin, hanya tampilkan tugas yang ditugaskan kepadanya
        if (!$user->isAdmin()) {
            $query->where('assigned_to', $user->id);
        }
        $tasks = $query->paginate(10);
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Task::class);
        $users = User::where('role_id', 2)->get(); // Asumsi role_id 2 adalah mahasiswa
        return view('tasks.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Task::class);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
            'assigned_to' => 'required|exists:users,id',
        ]);
        $validated['created_by'] = auth()->id();

        // Simpan tugas ke dalam variabel agar bisa digunakan untuk notifikasi
        $task = Task::create($validated);

        // --- TAMBAHAN: Kirim Notifikasi ke Mahasiswa ---
        $student = User::find($task->assigned_to);
        if ($student) {
            $student->notify(new TugasDiberikan($task));
        }
        // --- AKHIR TAMBAHAN ---

        return redirect()->route('tasks.index')->with('success', 'Tugas berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        // Jika admin, tampilkan form edit lengkap
        if (Auth::user()->isAdmin()) {
            $users = User::where('role_id', 2)->get();
            return view('tasks.edit', compact('task', 'users'));
        } else {
            // Jika mahasiswa, tampilkan form untuk mengumpulkan tugas
            return view('tasks.submit', compact('task'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'assigned_to' => 'required|exists:users,id',
        ]);
        $task->update($validated);
        return redirect()->route('tasks.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    /**
     * Metode untuk mahasiswa mengumpulkan tugas.
     */
    public function submit(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'submission_file' => 'required|file|mimes:pdf,jpg,jpeg,png,zip|max:10240',
        ]);

        if ($request->hasFile('submission_file')) {
            $file = $request->file('submission_file');

            try {
                $path = $file->store('submissions', 'public');

                if (!$path) {
                    return back()->with('error', 'Penyimpanan file gagal! Periksa izin folder storage/app/public.');
                }

                // Memperbarui database
                $task->submission_file_path = $path; // Asumsi Anda punya kolom ini
                $task->submitted_at = now();         // Asumsi Anda punya kolom ini
                $task->status = 'completed';
                $task->save();

                // --- TAMBAHAN: Kirim Notifikasi ke Dosen ---
                $lecturer = $task->createdBy; // Mengambil dosen dari relasi
                if ($lecturer) {
                    // Mengirimkan objek $task yang berisi semua info
                    $lecturer->notify(new SubmissionReceived($task));
                }
                // --- AKHIR TAMBAHAN ---

                return redirect()->route('tasks.index')->with('success', 'Tugas berhasil dikumpulkan!');

            } catch (\Exception $e) {
                Log::error("Error saat mengumpulkan tugas: " . $e->getMessage());
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Tidak ada file yang terdeteksi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Tugas berhasil dihapus.');
    }
}
