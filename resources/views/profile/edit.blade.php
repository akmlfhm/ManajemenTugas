@extends('layouts.app')

@section('title', 'My Profile')
@section('header', 'My Profile')

@section('content')
<div class="space-y-6">
    {{-- Form untuk Informasi Profil --}}
    <div class="p-4 sm:p-8 bg-white shadow rounded-lg">
        <div class="max-w-xl">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        Informasi Profil
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Perbarui informasi profil dan alamat email akun Anda.
                    </p>
                </header>

                <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('patch')

                    {{-- Avatar --}}
                    <div>
                        <label for="avatar" class="block font-medium text-sm text-gray-700">Foto Profil</label>
                        <div class="mt-2 flex items-center gap-x-3">
                            <img class="h-20 w-20 rounded-full object-cover" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}" alt="Current profile photo">
                            <input id="avatar" name="avatar" type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                        </div>
                        @error('avatar')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block font-medium text-sm text-gray-700">Nama</label>
                        <input id="name" name="name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                        @error('name')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                        <input id="email" name="email" type="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="{{ old('email', $user->email) }}" required autocomplete="username" />
                        @error('email')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Simpan</button>

                        @if (session('status') === 'profile-updated')
                            <p class="text-sm text-gray-600">Tersimpan.</p>
                        @endif
                    </div>
                </form>
            </section>
        </div>
    </div>

    {{-- Form untuk Ubah Password --}}
    <div class="p-4 sm:p-8 bg-white shadow rounded-lg">
        <div class="max-w-xl">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        Ubah Password
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.
                    </p>
                </header>

                {{-- Arahkan ke route 'password.update' yang ada di routes/auth.php --}}
                <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password" class="block font-medium text-sm text-gray-700">Password Saat Ini</label>
                        <input id="current_password" name="current_password" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" autocomplete="current-password" />
                        @error('current_password', 'updatePassword')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block font-medium text-sm text-gray-700">Password Baru</label>
                        <input id="password" name="password" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" autocomplete="new-password" />
                        @error('password', 'updatePassword')
                            <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Konfirmasi Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" autocomplete="new-password" />
                        @error('password_confirmation', 'updatePassword')
                             <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Simpan</button>

                        @if (session('status') === 'password-updated')
                            <p class="text-sm text-gray-600">Tersimpan.</p>
                        @endif
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
