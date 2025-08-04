@extends('layouts.app')

@section('title', 'Kumpulkan Tugas')
@section('header', 'Kumpulkan Tugas')

@section('content')
<div class="content-section">
    <div class="section-header">
        <h2>{{ $task->title }}</h2>
        <a href="{{ route('tasks.index') }}" class="view-all" style="text-decoration: none;">Kembali</a>
    </div>
    <div style="padding: 20px;">
        {{-- Detail Tugas (Read-only) --}}
        <div class="task-details">
            <p><strong>Ditugaskan oleh:</strong> {{ $task->createdBy->name ?? 'N/A' }}</p>
            <p><strong>Jatuh Tempo:</strong> {{ \Carbon\Carbon::parse($task->due_date)->isoFormat('D MMMM YYYY') }}</p>
            <div class="description">
                <strong>Deskripsi:</strong>
                <p>{{ $task->description ?? 'Tidak ada deskripsi.' }}</p>
            </div>
        </div>

        <hr class="my-6">

        {{-- Form Pengumpulan --}}
        <form action="{{ route('tasks.submit', $task->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="submission_file">Upload Bukti Pengumpulan</label>
                <input type="file" id="submission_file" name="submission_file" required>
                <p class="field-hint">Format yang didukung: PDF, JPG, PNG, ZIP (Maks: 10MB)</p>
                @error('submission_file')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="submit-btn">Kumpulkan Tugas</button>
        </form>
    </div>
</div>

<style>
    .task-details { margin-bottom: 20px; }
    .task-details p { margin-bottom: 5px; }
    .task-details .description { margin-top: 15px; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
    .form-group input[type="file"] {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        width: 100%;
    }
    .field-hint { font-size: 12px; color: #6c757d; margin-top: 5px; }
    .submit-btn {
        background: #28a745;
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        transition: background 0.3s ease;
    }
    .submit-btn:hover { background: #218838; }
    .error-message { color: #dc3545; font-size: 12px; margin-top: 5px; }
</style>
@endsection
