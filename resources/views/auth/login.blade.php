<x-guest-layout>
    {{--
        Kode ini telah disederhanakan agar sesuai dengan layout guest.blade.php yang baru.
        Layout guest sekarang menangani wadah kartu, jadi kita hanya perlu menampilkan form di sini.
    --}}

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Alamat Email -->
        <div>
            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
            {{-- Mengubah warna focus border dan ring agar sesuai dengan tombol --}}
            <input id="email" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-[#5c89e3] focus:ring focus:ring-[#5c89e3] focus:ring-opacity-50" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            @error('email')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
            {{-- Mengubah warna focus border dan ring agar sesuai dengan tombol --}}
            <input id="password" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-[#5c89e3] focus:ring focus:ring-[#5c89e3] focus:ring-opacity-50"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            @error('password')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Ingat Saya -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                {{-- Mengubah warna checkbox dan focus ring agar sesuai --}}
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 shadow-sm focus:ring-[#5c89e3]" name="remember" style="color: #5c89e3;">
                <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            {{-- Link untuk registrasi --}}
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#5c89e3]" href="{{ route('register') }}">
                Belum punya akun?
            </a>

            {{-- Tombol Login yang sudah diubah warnanya --}}
            <button type="submit"
    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 transition ease-in-out duration-150"
    style="background-color: #2563eb;">
    MASUK
</button>

        </div>
    </form>
</x-guest-layout>
