<x-guest-layout title="Reset Password">
    <header class="auth-head">
        <h1>Password Baru</h1>
        <p>Buat password baru untuk akun Anda.</p>
    </header>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <x-input-label for="email" value="Email" :required="true" />
            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)"
                          class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                          required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="form-group">
            <x-input-label for="password" value="Password Baru" :required="true" />
            <x-text-input id="password" type="password" name="password"
                          class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="form-group">
            <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" :required="true" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <x-primary-button class="btn--block">Simpan Password</x-primary-button>
    </form>
</x-guest-layout>
