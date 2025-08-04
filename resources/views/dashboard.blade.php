@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard ')

@push('styles')
{{-- Font dan library animasi --}}
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        {{-- Header --}}
        <header class="header-container">
            <div class="header-greeting">
                <h1 class="greeting-text">
                    <span class="gradient-text">
                        @php
                            $hour = \Carbon\Carbon::now(new \DateTimeZone('Asia/Jakarta'))->hour;
                            $greeting = 'Malam';
                            if ($hour >= 4 && $hour < 11) { $greeting = 'Pagi'; }
                            elseif ($hour >= 11 && $hour < 15) { $greeting = 'Siang'; }
                            elseif ($hour >= 15 && $hour < 18) { $greeting = 'Sore'; }
                        @endphp
                        Selamat {{ $greeting }},
                    </span>
                    <span class="user-name">{{ Auth::user()->name ?? 'Pengguna' }}!</span>
                </h1>
            </div>
            <div class="header-actions">
                <div id="live-clock" class="live-clock"></div>

                {{-- Notifikasi --}}
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open; markNotificationsAsRead()" class="notification-button">
                        <i class="fas fa-bell"></i>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <span class="notification-badge">{{ Auth::user()->unreadNotifications->count() }}</span>
                        @else
                            <span class="notification-badge" style="display: none;">0</span>
                        @endif
                    </button>
                    <div x-show="open" x-cloak class="notification-dropdown">
                        <div class="dropdown-header">
                            <h4>Notifikasi</h4>
                        </div>
                        <div id="notification-list" class="dropdown-content">
                            @forelse(Auth::user()->notifications->take(10) as $notification)
                                <a href="#" class="notification-item">
                                    <p><b class="font-semibold">{{ $notification->data['sender_name'] ?? 'Sistem' }}</b> {{ $notification->data['message'] ?? 'telah mengirim notifikasi.' }}</p>
                                    <small class="text-slate-500 mt-1">{{ $notification->created_at->diffForHumans() }}</small>
                                </a>
                            @empty
                                <div id="notification-empty-message" class="dropdown-empty">Tidak ada notifikasi.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Profile Dropdown --}}
                <!-- @auth
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="profile-button">
                        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=8b5cf6&color=FFFFFF' }}" alt="{{ Auth::user()->name }}">
                    </button>
                    <div x-show="open" x-cloak class="profile-dropdown">
                        <div class="dropdown-header">
                            <p class="font-semibold">{{ Auth::user()->name }}</p>
                            <small class="truncate">{{ Auth::user()->email }}</small>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="dropdown-item">Profile</a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item-logout">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth
            </div> -->
        </header>

        {{-- Stats Grid --}}
        <div class="stats-grid">
            {{-- Kartu-kartu statistik Anda --}}
            <div class="stats-card" data-aos="fade-up">
                <div class="icon-wrapper bg-blue-100 text-blue-600"><i class="fas fa-tasks"></i></div>
                <p class="stats-label">Total Tugas</p>
                <p class="counter stats-value" data-target="{{ $totalTasks ?? 0 }}">0</p>
            </div>
            <div class="stats-card" data-aos="fade-up" data-aos-delay="100">
                <div class="icon-wrapper bg-green-100 text-green-600"><i class="fas fa-users"></i></div>
                <p class="stats-label">Total Anggota</p>
                <p class="counter stats-value" data-target="{{ $activeUsers ?? 0 }}">0</p>
            </div>
            <div class="stats-card" data-aos="fade-up" data-aos-delay="200">
                <div class="icon-wrapper bg-indigo-100 text-indigo-600"><i class="fas fa-check-circle"></i></div>
                <p class="stats-label">Selesai Hari Ini</p>
                <p class="counter stats-value" data-target="{{ $completedToday ?? 0 }}">0</p>
            </div>
            <div class="stats-card" data-aos="fade-up" data-aos-delay="300">
                <div class="icon-wrapper bg-amber-100 text-amber-600"><i class="fas fa-hourglass-half"></i></div>
                <p class="stats-label">Jatuh Tempo Hari Ini</p>
                <p class="counter stats-value" data-target="{{ $dueToday ?? 0 }}">0</p>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="main-content-grid">
            {{-- Tabel Tugas dan Aktivitas --}}
            <div class="content-card tasks-card" data-aos="fade-up" data-aos-delay="400">
                <div class="card-header">
                    <h3>Tugas Mendatang</h3>
                    <a href="{{ route('tasks.index') }}" class="btn-primary">Lihat Semua</a>
                </div>
                <div class="table-wrapper">
                    <table id="tasksTable">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0)">Tugas <span class="sort-icon"></span></th>
                                <th onclick="sortTable(1)">Ditugaskan Kepada <span class="sort-icon"></span></th>
                                <th onclick="sortTable(2)">Tanggal <span class="sort-icon"></span></th>
                                <th onclick="sortTable(3)">Status <span class="sort-icon"></span></th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingDeadlines as $task)
                            <tr data-due-date="{{ $task->due_date }}" data-status="{{ $task->status }}">
                                <td>
                                    <div class="font-semibold">{{ $task->title }}</div>
                                    <small class="text-slate-500">{{ $task->project->name ?? 'Proyek Pribadi' }}</small>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($task->assignedTo->name ?? 'N') }}&background=E0E7FF&color=3730A3&size=28&font-size=0.4&bold=true" alt="{{ $task->assignedTo->name ?? 'N/A' }}">
                                        <span>{{ $task->assignedTo->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($task->due_date)->isoFormat('D MMM YYYY') }}</td>
                                <td>
                                    <span class="status-badge status-{{ $task->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('tasks.show', $task->id) }}" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('tasks.edit', $task->id) }}" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-cell">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                    <p>Tidak ada tugas yang akan datang</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="content-card" data-aos="fade-up" data-aos-delay="500">
                <div class="card-header">
                    <h3>Aktivitas Terbaru</h3>
                </div>
                <div class="activity-feed">
                    @forelse($recentActivities as $activity)
                    <div class="activity-item">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($activity->assignedTo->name ?? 'S') }}&background=E0E7FF&color=3730A3&size=40&font-size=0.5" alt="{{ $activity->assignedTo->name ?? 'System' }}">
                        <div>
                            <p><span class="font-semibold">{{ $activity->assignedTo->name ?? 'System' }}</span> memperbarui tugas <span class="font-semibold">"{{ $activity->title }}"</span></p>
                            <small class="text-slate-500 mt-1">{{ $activity->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="empty-cell">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <p>Tidak ada aktivitas terbaru</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Semua CSS Anda yang sudah ada di sini... */
:root {
    --bg-color: #f8f9fc;
    --card-bg: rgba(255, 255, 255, 0.75);
    --text-primary: #111827;
    --text-secondary: #4b5563;
    --border-color: #e5e7eb;
    --primary-gradient: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
    --primary-color-start: #3b82f6;
    --primary-color-end: #8b5cf6;
    --shadow-sm: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
    --shadow-md: 0 10px 15px -3px rgb(0 0 0 / 0.07), 0 4px 6px -4px rgb(0 0 0 / 0.07);
}

body {
    background-color: var(--bg-color);
    font-family: 'Inter', sans-serif;
    color: var(--text-primary);
    background-image:
        radial-gradient(circle at 1% 1%, hsla(240, 82%, 93%, 0.5) 0px, transparent 40%),
        radial-gradient(circle at 99% 99%, hsla(300, 82%, 93%, 0.5) 0px, transparent 40%);
    background-attachment: fixed;
}
.dashboard-wrapper { backdrop-filter: blur(100px); }
.dashboard-container { min-height: 100vh; padding: 2rem; max-width: 1400px; margin: 0 auto; }
.relative { position: relative; }
[x-cloak] { display: none !important; }

/* Header */
.header-container { display: flex; flex-direction: column; justify-content: space-between; gap: 1rem; margin-bottom: 2.5rem; }
.header-greeting h1 { font-size: 2.5rem; font-weight: 900; line-height: 1.1; letter-spacing: -1.5px; }
.greeting-text { background: var(--primary-gradient); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
.user-name { display: block; color: var(--text-primary); }
.header-actions { display: flex; align-items: center; gap: 1rem; }
.live-clock { font-size: 0.875rem; font-weight: 600; color: var(--text-secondary); background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(4px); padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid var(--border-color); white-space: nowrap; }
.notification-button, .profile-button { position: relative; border: 1px solid var(--border-color); background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(4px); border-radius: 9999px; transition: color 0.2s; color: var(--text-secondary); cursor: pointer; }
.notification-button { width: 44px; height: 44px; font-size: 1.1rem; display: inline-flex; align-items: center; justify-content: center; }
.notification-badge { position: absolute; top: -5px; right: -5px; width: 1.25rem; height: 1.25rem; background-color: #ef4444; color: white; font-size: 0.75rem; font-weight: 600; border-radius: 9999px; display: flex; justify-content: center; align-items: center; border: 2px solid white; }
.profile-button img { width: 44px; height: 44px; border-radius: 9999px; object-fit: cover; }
.notification-button:hover, .profile-button:hover { color: var(--primary-color-start); }

/* Dropdowns */
.dropdown-header { padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); }
.dropdown-header h4 { font-weight: 600; color: var(--text-primary); }
.dropdown-header p { font-size: 0.875rem; line-height: 1.2; font-weight: 600; color: var(--text-primary); }
.dropdown-header small { font-size: 0.75rem; color: var(--text-secondary); }
.dropdown-content { max-height: 22rem; overflow-y: auto; }
.dropdown-item, .dropdown-item-logout { display: block; width: 100%; text-align: left; padding: 0.5rem 1rem; font-size: 0.875rem; color: var(--text-primary); }
.dropdown-item:hover { background-color: #f9fafb; }
.notification-item { display: block; padding: 0.75rem 1rem; border-bottom: 1px solid var(--border-color); }
.notification-item:last-child { border-bottom: none; }
.notification-item:hover { background-color: #f9fafb; }
.notification-item p { font-size: 0.875rem; color: var(--text-secondary); }
.notification-item b { color: var(--text-primary); }
.dropdown-item-logout { color: #ef4444; }
.dropdown-item-logout:hover { background-color: #fef2f2; }
.dropdown-divider { border-top: 1px solid var(--border-color); margin: 0.25rem 0; }
.dropdown-empty { padding: 2rem 1rem; text-align: center; font-size: 0.875rem; color: var(--text-secondary); }
.notification-dropdown, .profile-dropdown { position: absolute; top: 100%; right: 0; margin-top: 0.75rem; background-color: white; border-radius: 0.75rem; border: 1px solid var(--border-color); box-shadow: var(--shadow-md); z-index: 50; overflow: hidden; }
.notification-dropdown { width: 22rem; }
.profile-dropdown { width: 14rem; }

/* Stats Grid */
.stats-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 1.5rem; margin-bottom: 2rem; }
.stats-card { background-color: var(--card-bg); border-radius: 1rem; padding: 1.5rem; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); transition: all 0.3s ease; border-bottom: 3px solid transparent; }
.stats-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); border-bottom-color: var(--primary-color-start); }
.icon-wrapper { width: 3rem; height: 3rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.stats-label { margin-top: 0.75rem; font-size: 0.875rem; font-weight: 500; color: var(--text-secondary); }
.stats-value { font-size: 2rem; font-weight: 800; color: var(--text-primary); }

/* Main Content Grid */
.main-content-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 1.5rem; }
.content-card { background-color: var(--card-bg); border-radius: 1rem; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); overflow: hidden; }
.card-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); }
.card-header h3 { font-size: 1.125rem; font-weight: 600; color: var(--text-primary); }
.btn-primary { background-image: var(--primary-gradient); color: white; font-size: 0.875rem; font-weight: 500; padding: 0.6rem 1.2rem; border-radius: 0.5rem; transition: all 0.2s ease; border: none; box-shadow: 0 4px 15px -5px rgba(139, 92, 246, 0.5); cursor: pointer; }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px -5px rgba(139, 92, 246, 0.6); }

/* Table */
.table-wrapper { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
table th, table td { padding: 1rem 1.5rem; text-align: left; font-size: 0.875rem; border-top: 1px solid var(--border-color); }
table th { font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer; user-select: none; border-top: none; }
table tbody tr:hover { background-color: rgba(243, 244, 246, 0.7); }
.user-cell { display: flex; align-items: center; gap: 0.5rem; }
.user-cell img { width: 28px; height: 28px; border-radius: 9999px; }
.status-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
.status-pending { background-color: #fef3c7; color: #92400e; }
.status-in_progress { background-color: #dbeafe; color: #1d4ed8; }
.status-completed { background-color: #d1fae5; color: #047857; }
.action-buttons { display: flex; justify-content: flex-end; gap: 0.25rem; }
.action-buttons a { color: var(--text-secondary); padding: 0.5rem; border-radius: 9999px; transition: all 0.2s ease; }
.action-buttons a:hover { color: var(--primary-color-start); background-color: #eff6ff; }
.empty-cell { text-align: center; padding: 3rem 1.5rem; color: var(--text-secondary); }
.empty-cell svg { margin: 0 auto 1rem; width: 3rem; height: 3rem; color: var(--border-color); }
.empty-cell p { font-size: 1rem; font-weight: 500; }

/* Activity Feed */
.activity-feed { padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem; }
.activity-item { display: flex; align-items: flex-start; gap: 0.75rem; }
.activity-item img { width: 40px; height: 40px; border-radius: 9999px; object-fit: cover; }
.activity-item p { font-size: 0.875rem; color: var(--text-primary); line-height: 1.5; }

/* Responsive Media Queries */
@media (min-width: 640px) { /* sm */
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (min-width: 1024px) { /* lg */
    .header-container { flex-direction: row; align-items: center; }
    .stats-grid { grid-template-columns: repeat(4, 1fr); }
    .main-content-grid { grid-template-columns: repeat(3, 1fr); }
    .tasks-card { grid-column: span 2 / span 2; }
}

/* Sorting Icon Logic */
.sort-icon { display: inline-block; width: 1rem; height: 1rem; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af'%3e%3cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: center; vertical-align: middle; margin-left: 0.25rem; opacity: 0.5; transition: opacity 0.2s ease; }
th:hover .sort-icon { opacity: 1; }
th.asc .sort-icon { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%233b82f6'%3e%3cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 15l7-7 7 7'/%3e%3c/svg%3e"); opacity: 1; }
th.desc .sort-icon { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%233b82f6'%3e%3cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3e%3c/svg%3e"); opacity: 1; }
</style>

<script>
// --- FUNGSI NOTIFIKASI OFFLINE ---

/**
 * Fungsi untuk memberitahu server bahwa notifikasi sudah dibaca.
 * Dipanggil saat ikon lonceng di-klik.
 */
async function markNotificationsAsRead() {
    let badge = document.querySelector('.notification-badge');
    
    // Hanya jalankan jika ada notifikasi yang belum dibaca
    if (badge && parseInt(badge.innerText) > 0) {
        try {
            await fetch('/notifications/mark-as-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });

            // Langsung sembunyikan badge di frontend untuk respons instan
            badge.innerText = '0';
            badge.style.display = 'none';

        } catch (error) {
            console.error('Gagal menandai notifikasi:', error);
        }
    }
}

/**
 * Fungsi ini mengambil notifikasi terbaru dari server secara berkala.
 */
async function fetchLatestNotifications() {
    try {
        const response = await fetch('/notifications/latest');
        if (!response.ok) throw new Error('Network response was not ok');
        const newNotifications = await response.json();

        if (newNotifications.length > 0) {
            // --- PERBAIKAN UTAMA ADA DI SINI ---
            // Panggil fungsi yang SET, bukan ADD
            setNotificationBadge(newNotifications.length);
            addNotificationsToDropdown(newNotifications);
        }
    } catch (error) {
        console.error('Gagal mengambil notifikasi:', error);
    }
}

/**
 * --- FUNGSI DIPERBAIKI ---
 * Fungsi ini MENGGANTI (SET) nilai badge, bukan menambahkannya.
 * @param {number} count - Jumlah total notifikasi baru.
 */
function setNotificationBadge(count) {
    let badge = document.querySelector('.notification-badge');
    if (badge) {
        badge.innerText = count;
        if (count > 0) {
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    }
}

/**
 * Fungsi untuk menambahkan item notifikasi baru ke dalam daftar dropdown.
 */
function addNotificationsToDropdown(notifications) {
    const listElement = document.getElementById('notification-list');
    const emptyMessage = document.getElementById('notification-empty-message');
    if (!listElement) return;
    if (emptyMessage) emptyMessage.style.display = 'none';

    notifications.forEach(notification => {
        // Cek agar tidak menambahkan notifikasi yang sudah ada di daftar
        if (!document.querySelector(`[data-notification-id="${notification.id}"]`)) {
            const notificationHtml = `
                <a href="#" class="notification-item" data-notification-id="${notification.id}">
                    <p><b class="font-semibold">${notification.data.sender_name ?? 'Sistem'}</b> ${notification.data.message}</p>
                    <small class="text-slate-500 mt-1">${notification.created_at_human}</small>
                </a>
            `;
            listElement.insertAdjacentHTML('afterbegin', notificationHtml);
        }
    });
}


// --- FUNGSI DASAR DASHBOARD (TIDAK BERUBAH) ---
function animateCounters() {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        let start = 0;
        const target = +counter.getAttribute('data-target');
        if (target === 0) return;
        const duration = 1500;
        const increment = target / (duration / 16);

        const updateCount = () => {
            start += increment;
            if (start < target) {
                counter.innerText = Math.ceil(start).toLocaleString('id-ID');
                requestAnimationFrame(updateCount);
            } else {
                counter.innerText = target.toLocaleString('id-ID');
            }
        };
        updateCount();
    });
}

function updateLiveClock() {
    const clockElement = document.getElementById('live-clock');
    if (!clockElement) return;
    const now = new Date();
    const options = { weekday: 'long', day: 'numeric', month: 'long' };
    const dateString = now.toLocaleDateString('id-ID', options);
    const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    clockElement.textContent = `${dateString}, ${timeString}`;
}

let currentSort = { column: null, direction: 'asc' };
function sortTable(columnIndex) {
    const table = document.getElementById('tasksTable');
    if (!table) return;
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const taskRows = rows.filter(row => row.hasAttribute('data-due-date'));
    const headers = table.querySelectorAll('thead th');

    if (currentSort.column === columnIndex) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.column = columnIndex;
        currentSort.direction = 'asc';
    }
    const direction = currentSort.direction === 'asc' ? 1 : -1;

    taskRows.sort((a, b) => {
        let valA, valB;
        if (columnIndex === 2) {
            valA = new Date(a.dataset.dueDate).getTime();
            valB = new Date(b.dataset.dueDate).getTime();
        } else {
            const cellA = a.querySelectorAll('td')[columnIndex];
            const cellB = b.querySelectorAll('td')[columnIndex];
            valA = cellA ? cellA.innerText.trim().toLowerCase() : '';
            valB = cellB ? cellB.innerText.trim().toLowerCase() : '';
        }
        if (valA < valB) return -1 * direction;
        if (valA > valB) return 1 * direction;
        return 0;
    });

    taskRows.forEach(row => tbody.appendChild(row));
    headers.forEach(th => th.classList.remove('asc', 'desc'));
    if (headers[columnIndex]) headers[columnIndex].classList.add(currentSort.direction);
}


// --- INISIALISASI HALAMAN ---
document.addEventListener('DOMContentLoaded', () => {
    if (typeof AOS !== 'undefined') {
        AOS.init({ once: true, duration: 600, easing: 'ease-out-quad', delay: 100 });
    }
    animateCounters();
    updateLiveClock();
    setInterval(updateLiveClock, 1000);

    // Mulai polling untuk notifikasi setiap 7 detik
    setInterval(fetchLatestNotifications, 7000);
});
</script>
@endsection