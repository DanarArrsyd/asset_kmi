<x-guest-layout title="Lupa Password">
    <header class="auth-head">
        <h1>Lupa Password</h1>
        <p>Masukkan email akun Anda. Kami kirimkan tautan untuk membuat password baru.</p>
    </header>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <x-input-label for="email" value="Email" :required="true" />
            <x-text-input id="email" type="email" name="email" :value="old('email')"
                          class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <x-primary-button class="btn--block">Kirim Tautan Reset</x-primary-button>

        <p class="auth-foot">
            <a class="auth-link" href="{{ route('login') }}">Kembali ke halaman masuk</a>
        </p>
    </form>
</x-guest-layout>
