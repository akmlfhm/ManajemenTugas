@extends('layouts.app')

@section('title', 'Edit Tugas')
@section('header', 'Edit Tugas')

@section('content')
<div class="content-section">
    <div class="section-header">
        <h2>Formulir Edit Tugas</h2>
        <a href="{{ route('tasks.index') }}" class="view-all" style="text-decoration: none;">Kembali ke Daftar</a>
    </div>
    <div style="padding: 20px;">
        <form action="{{ route('tasks.update', $task->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Method untuk update --}}
            
            <div class="form-group">
                <label for="title">Judul Tugas</label>
                {{-- Mengubah name="name" menjadi name="title" --}}
                <input type="text" id="title" name="title" value="{{ old('title', $task->title) }}" required>
                @error('title')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description">{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="assigned_to">Ditugaskan Kepada</label>
                <select id="assigned_to" name="assigned_to" required>
                    <option value="">Pilih Pengguna</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('assigned_to')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="due_date">Tanggal Selesai</label>
                <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '') }}">
                @error('due_date')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="submit-btn">Perbarui Tugas</button>
        </form>
    </div>
</div>

<style>
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
    .form-group input, .form-group textarea, .form-group select {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }
    .form-group textarea { min-height: 120px; resize: vertical; }
    .submit-btn {
        background: #667eea;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        transition: background 0.3s ease;
    }
    .submit-btn:hover { background: #764ba2; }
    .error-message { color: #dc3545; font-size: 12px; margin-top: 5px; }
</style>
@endsection
