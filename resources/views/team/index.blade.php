@extends('layouts.app')

@section('title', 'Team Management')
@section('header', 'Team Management')

@section('content')

{{-- Menampilkan pesan sukses atau error --}}
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
        <div class="flex justify-between items-center">
            <h2>Daftar Semua Pengguna</h2>
            <button id="ai-summary-btn" class="ai-summary-btn">
                <i class="fas fa-wand-magic-sparkles"></i> Buat Ringkasan AI
            </button>
        </div>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role Saat Ini</th>
                    <th style="width: 250px;">Ubah Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="status-badge {{ $user->isAdmin() ? 'admin' : 'mahasiswa' }}">
                                {{ $user->isAdmin() ? 'Admin' : 'Mahasiswa' }}
                            </span>
                        </td>
                        <td>
                            @if (auth()->user()->id !== $user->id)
                                <form action="{{ route('team.updateRole', $user->id) }}" method="POST" class="role-form">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role_id">
                                        <option value="1" {{ $user->role_id == 1 ? 'selected' : '' }}>Admin</option>
                                        <option value="2" {{ $user->role_id == 2 ? 'selected' : '' }}>Mahasiswa</option>
                                    </select>
                                    <button type="submit" class="save-btn">Simpan</button>
                                </form>
                            @else
                                <span class="self-note">Anda tidak bisa mengubah role sendiri.</span>
                            @endif
                        </td>
                        <td>
                            @if (auth()->user()->id !== $user->id)
                                <form action="{{ route('team.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                     <tr>
                         <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada data pengguna.</td>
                     </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="padding: 20px;">
        {{ $users->links() }}
    </div>
</div>

<!-- AI Chatbot Elements -->
<div id="chatbot-container">
    <!-- Chatbot Toggle Button -->
    <button id="chatbot-toggle-btn" class="chatbot-toggle-btn">
        <i class="fas fa-robot icon-closed"></i>
        <i class="fas fa-times icon-opened" style="display: none;"></i>
    </button>

    <!-- Chatbot Window -->
    <div id="chatbot-window" class="chatbot-window" style="display: none;">
        <div class="chatbot-header">
            <h3>Asisten AI</h3>
            <p>Saya bisa membantu Anda. Coba ketik: "hapus user John Doe"</p>
        </div>
        <div id="chatbot-messages" class="chatbot-messages">
            <!-- Initial greeting from chatbot -->
            <div class="chat-message bot">
                Halo! Ada yang bisa saya bantu untuk manajemen tim hari ini?
            </div>
        </div>
        <div class="chatbot-input-area">
            <input type="text" id="chatbot-input" placeholder="Ketik perintah Anda...">
            <button id="chatbot-send-btn"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>
<!-- End AI Chatbot Elements -->

<style>
    .content-section {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .section-header {
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
        font-size: 14px;
        color: #666;
    }
    table tr:last-child td {
        border-bottom: none;
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-badge.admin {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    .status-badge.mahasiswa {
        background: #cce5ff;
        color: #004085;
        border: 1px solid #b3d7ff;
    }
    .role-form { display: flex; gap: 10px; align-items: center; }
    .role-form select { 
        padding: 8px; 
        border: 1px solid #ddd; 
        border-radius: 6px; 
        background-color: #f8f9fa;
    }
    .save-btn, .delete-btn, .ai-summary-btn {
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .save-btn {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
    }
    .save-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    .delete-btn {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }
    .delete-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }
    .ai-summary-btn {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    }
    .ai-summary-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
    }
    .self-note { font-style: italic; color: #6c757d; font-size: 12px; }
    .success-alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; color: #0f5132; background-color: #d1e7dd; border-color: #badbcc; }
    .error-alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; color: #842029; background-color: #f8d7da; border-color: #f5c2c7; }

    /* Chatbot Styles */
    #chatbot-container {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }
    .chatbot-toggle-btn {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        transition: transform 0.3s ease;
    }
    .chatbot-toggle-btn:hover {
        transform: scale(1.1);
    }
    .chatbot-window {
        width: 350px;
        height: 500px;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 5px 30px rgba(0,0,0,0.2);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        position: absolute;
        bottom: 80px;
        right: 0;
    }
    .chatbot-header {
        padding: 15px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-align: center;
    }
    .chatbot-header h3 { margin: 0; font-size: 1.2rem; }
    .chatbot-header p { margin: 5px 0 0; font-size: 0.8rem; opacity: 0.9; }

    .chatbot-messages {
        flex-grow: 1;
        padding: 15px;
        overflow-y: auto;
        background-color: #f4f7f9;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .chat-message {
        padding: 10px 15px;
        border-radius: 20px;
        max-width: 80%;
        line-height: 1.5;
    }
    .chat-message.user {
        background-color: #007bff;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 5px;
    }
    .chat-message.bot {
        background-color: #e9ecef;
        color: #333;
        align-self: flex-start;
        border-bottom-left-radius: 5px;
    }
    .chat-message.typing {
        font-style: italic;
        color: #888;
    }
    .chatbot-input-area {
        display: flex;
        padding: 10px;
        border-top: 1px solid #ddd;
    }
    #chatbot-input {
        flex-grow: 1;
        border: 1px solid #ccc;
        border-radius: 20px;
        padding: 10px 15px;
        outline: none;
    }
    #chatbot-send-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background-color: #007bff;
        color: white;
        margin-left: 10px;
        cursor: pointer;
        font-size: 16px;
    }
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatbotToggleBtn = document.getElementById('chatbot-toggle-btn');
    const chatbotWindow = document.getElementById('chatbot-window');
    const iconClosed = chatbotToggleBtn.querySelector('.icon-closed');
    const iconOpened = chatbotToggleBtn.querySelector('.icon-opened');
    const messagesContainer = document.getElementById('chatbot-messages');
    const inputField = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send-btn');

    // Toggle chatbot window
    chatbotToggleBtn.addEventListener('click', () => {
        const isHidden = chatbotWindow.style.display === 'none';
        chatbotWindow.style.display = isHidden ? 'flex' : 'none';
        iconClosed.style.display = isHidden ? 'none' : 'block';
        iconOpened.style.display = isHidden ? 'block' : 'none';
    });

    const sendMessage = async () => {
        const command = inputField.value.trim();
        if (command === '') return;

        // Display user message
        appendMessage(command, 'user');
        inputField.value = '';

        // Show typing indicator
        const typingIndicator = appendMessage('AI sedang mengetik...', 'bot typing');

        try {
            // Send command to backend
            const response = await fetch("{{ route('ai.command') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ command: command })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok.');
            }

            const data = await response.json();
            
            // Remove typing indicator and show bot reply
            typingIndicator.remove();
            appendMessage(data.reply, 'bot');

            // If command was successful, reload the page to see changes
            if(data.status === 'success') {
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }

        } catch (error) {
            console.error('Error:', error);
            typingIndicator.remove();
            appendMessage('Maaf, terjadi kesalahan saat memproses perintah Anda.', 'bot');
        }
    };

    const appendMessage = (text, type) => {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${type}`;
        messageDiv.textContent = text;
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight; // Auto-scroll
        return messageDiv;
    };

    sendBtn.addEventListener('click', sendMessage);
    inputField.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Add CSRF token meta tag if not present
    if (!document.querySelector('meta[name="csrf-token"]')) {
        let meta = document.createElement('meta');
        meta.name = "csrf-token";
        meta.content = "{{ csrf_token() }}";
        document.getElementsByTagName('head')[0].appendChild(meta);
    }
});
</script>
@endpush
