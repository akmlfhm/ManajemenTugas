@extends('layouts.app')

@section('header', 'Daftar Tugas')
@section('title', 'Daftar Tugas')

@section('content')

{{-- Bagian untuk menampilkan pesan sukses atau error --}}
@if (session('success'))
    <div class="success-alert" role="alert">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="error-alert" role="alert">
        {{ session('error') }}
    </div>
@endif

<div class="content-section">
    <div class="section-header">
        <h2>Semua Tugas</h2>
        @can('create', App\Models\Task::class)
            <a href="{{ route('tasks.create') }}" class="add-task-btn">
                <i class="fas fa-plus mr-2"></i> Tambah Tugas
            </a>
        @endcan
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Judul Tugas</th>
                    <th>Ditugaskan Kepada</th>
                    <th>Status</th>
                    <th class="text-center">Bukti</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                    <tr>
                        <td>{{ $task->title }}</td>
                        <td>{{ $task->assignedTo->name ?? 'Belum Ditugaskan' }}</td>
                        <td>
                            <span class="status-badge {{ str_replace('_', '-', $task->status) }}">
                                {{ str_replace('_', ' ', $task->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($task->submission_file_path)
                                <a href="{{ asset('storage/' . $task->submission_file_path) }}" target="_blank" title="Lihat Bukti">
                                    <i class="fas fa-check-circle text-green-500 fa-lg"></i>
                                </a>
                            @else
                                <i class="fas fa-times-circle text-gray-400 fa-lg" title="Belum Dikumpulkan"></i>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <a href="{{ route('tasks.show', $task->id) }}" class="action-btn view" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('tasks.edit', $task->id) }}" class="action-btn edit" title="Ubah/Kumpulkan">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                @can('delete', $task)
                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus tugas ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete" title="Hapus Tugas">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                     <tr>
                        <td colspan="5" class="text-center py-4">Tidak ada data tugas yang ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4">
        {{ $tasks->links() }}
    </div>
</div>

<style>
    .content-section {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .section-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    .add-task-btn {
        background: rgba(255, 255, 255, 0.2);
        padding: 10px 18px;
        border-radius: 8px;
        color: white !important;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }
    .add-task-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }
    .table-container {
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    table th, table td {
        padding: 15px 20px;
        text-align: left;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    table th {
        background: #f8f9fa;
        font-weight: 600;
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
    }
    .text-center { text-align: center; }
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: capitalize;
    }
    .status-badge.pending { background: #fff3cd; color: #856404; }
    .status-badge.in-progress { background: #cce5ff; color: #004085; }
    .status-badge.completed { background: #d4edda; color: #155724; }
    .action-buttons { display: inline-flex; gap: 10px; align-items: center; }
    .action-btn {
        width: 32px; height: 32px;
        border: none; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        color: white; cursor: pointer;
        transition: all 0.2s ease;
    }
    .action-btn:hover { transform: scale(1.1); }
    .action-btn.view { background-color: #17a2b8; }
    .action-btn.edit { background-color: #ffc107; }
    .action-btn.delete { background-color: #dc3545; }
    .success-alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
    .error-alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }
</style>
@endsection
