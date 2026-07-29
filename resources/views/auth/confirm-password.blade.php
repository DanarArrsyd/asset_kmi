<x-guest-layout title="Konfirmasi Password">
    <header class="auth-head">
        <h1>Konfirmasi Password</h1>
        <p>Bagian ini butuh konfirmasi ulang. Masukkan password Anda untuk melanjutkan.</p>
    </header>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="form-group">
            <x-input-label for="password" value="Password" :required="true" />
            <x-text-input id="password" type="password" name="password"
                          class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                          required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <x-primary-button class="btn--block">Konfirmasi</x-primary-button>
    </form>
</x-guest-layout>
