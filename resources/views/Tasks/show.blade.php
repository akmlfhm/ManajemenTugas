@extends('layouts.app')

@section('title', 'Detail Tugas')
@section('header', 'Detail Tugas')

@section('content')
<div class="content-section">
    <div class="section-header">
        <h2>{{ $task->title }}</h2>
        <a href="{{ route('tasks.index') }}" class="view-all" style="text-decoration: none;">Kembali ke Daftar</a>
    </div>
    
    <div class="task-details">
        {{-- Detail Tugas Utama --}}
        <div class="detail-item">
            <strong>Status:</strong>
            <span class="status-badge {{ str_replace('_', '-', $task->status) }}">
                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
            </span>
        </div>
        
        <div class="detail-item">
            <strong>Ditugaskan Kepada:</strong>
            <span>{{ $task->assignedTo->name ?? 'N/A' }}</span>
        </div>
        
        <div class="detail-item">
            <strong>Dibuat Oleh:</strong>
            <span>{{ $task->createdBy->name ?? 'N/A' }}</span>
        </div>
        
        <div class="detail-item">
            <strong>Tanggal Selesai:</strong>
            <span>
                @if($task->due_date)
                    {{ \Carbon\Carbon::parse($task->due_date)->isoFormat('dddd, D MMMM YYYY') }}
                @else
                    <em>Tidak ditentukan</em>
                @endif
            </span>
        </div>
        
        <div class="detail-item description">
            <strong>Deskripsi:</strong>
            <p>{{ $task->description ?? 'Tidak ada deskripsi.' }}</p>
        </div>
        
        <div class="detail-item">
            <strong>Dibuat Pada:</strong>
            <span>{{ $task->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</span>
        </div>
        
        <div class="detail-item">
            <strong>Diperbarui Pada:</strong>
            <span>{{ $task->updated_at->isoFormat('D MMMM YYYY, HH:mm') }}</span>
        </div>

        {{-- BAGIAN DETAIL PENGUMPULAN (HANYA TAMPIL JIKA ADA) --}}
        @if($task->submission_file_path)
        <div class="submission-details">
            <hr class="my-6">
            <h3 class="submission-title">Detail Pengumpulan</h3>
            
            <div class="detail-item">
                <strong>Dikumpulkan Pada:</strong>
                <span>
                    @if($task->submitted_at)
                        {{ \Carbon\Carbon::parse($task->submitted_at)->isoFormat('dddd, D MMMM YYYY, HH:mm') }}
                    @else
                        <em>N/A</em>
                    @endif
                </span>
            </div>
            
            <div class="detail-item">
                <strong>File Bukti:</strong>
                <a href="{{ asset('storage/' . $task->submission_file_path) }}" 
                   target="_blank" 
                   class="download-link">
                    <i class="fas fa-download mr-2"></i> Unduh Bukti
                </a>
            </div>
        </div>
        @endif

        {{-- Action Buttons --}}
        <div class="action-footer">
            @if(auth()->user()->isAdmin())
                {{-- Admin selalu bisa mengedit tugas --}}
                <a href="{{ route('tasks.edit', $task->id) }}" class="edit-btn">
                    <i class="fas fa-edit mr-2"></i> Edit Tugas
                </a>
                
                {{-- Tombol hapus untuk admin --}}
                <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" style="display: inline-block;" 
                      onsubmit="return confirm('Yakin ingin menghapus tugas ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-btn">
                        <i class="fas fa-trash mr-2"></i> Hapus Tugas
                    </button>
                </form>
                
            @elseif($task->status !== 'completed' && $task->assignedTo && $task->assignedTo->id === auth()->id())
                {{-- User hanya bisa mengumpulkan jika tugas belum selesai dan ditugaskan kepadanya --}}
                <a href="{{ route('tasks.edit', $task->id) }}" class="edit-btn">
                    <i class="fas fa-upload mr-2"></i> Kumpulkan Tugas
                </a>
            @endif
        </div>
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
        border-bottom: 3px solid #f8f9fa;
    }
    
    .section-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
    }
    
    .view-all {
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 6px;
        color: white !important;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .view-all:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }
    
    .task-details { 
        padding: 30px; 
    }
    
    .detail-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
        transition: background-color 0.3s ease;
    }
    
    .detail-item:hover {
        background-color: #f8f9fa;
        margin: 0 -15px;
        padding: 15px;
        border-radius: 8px;
    }
    
    .detail-item strong {
        font-weight: 600;
        width: 150px;
        flex-shrink: 0;
        color: #555;
    }
    
    .detail-item.description {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .detail-item.description p {
        margin-top: 8px;
        line-height: 1.6;
        color: #666;
    }
    
    /* Status Badge Styling */
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .status-badge.in-progress {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-badge.completed {
        background: #cce5ff;
        color: #004085;
        border: 1px solid #b3d7ff;
    }
    
    .submission-details { 
        margin-top: 30px; 
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #17a2b8;
    }
    
    .submission-title { 
        font-size: 1.25rem; 
        font-weight: 600; 
        margin-bottom: 15px;
        color: #17a2b8;
    }
    
    .download-link {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        padding: 10px 18px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3);
    }
    
    .download-link:hover { 
        background: linear-gradient(135deg, #138496 0%, #0f6674 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(23, 162, 184, 0.4);
    }
    
    .action-footer {
        margin-top: 30px;
        display: flex;
        gap: 15px;
        align-items: center;
    }
    
    .edit-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 3px 12px rgba(102, 126, 234, 0.3);
        display: inline-flex;
        align-items: center;
    }
    
    .edit-btn:hover { 
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }
    
    .delete-btn {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 3px 12px rgba(220, 53, 69, 0.3);
        display: inline-flex;
        align-items: center;
    }
    
    .delete-btn:hover {
        background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(220, 53, 69, 0.4);
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .section-header {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }
        
        .task-details {
            padding: 20px;
        }
        
        .detail-item {
            flex-direction: column;
            gap: 8px;
        }
        
        .detail-item strong {
            width: auto;
        }
        
        .action-footer {
            flex-direction: column;
            align-items: stretch;
        }
        
        .edit-btn, .delete-btn {
            text-align: center;
            justify-content: center;
        }
    }
</style>
@endsection