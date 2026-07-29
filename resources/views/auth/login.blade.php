<x-guest-layout>
    <header class="auth-head">
        <h1>Masuk</h1>
        <p>Gunakan akun kantor Anda.</p>
    </header>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <x-input-label for="email" value="Email" :required="true" />
            <x-text-input id="email" type="email" name="email" :value="old('email')"
                          class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                          required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="form-group">
            <x-input-label for="password" value="Password" :required="true" />
            <x-text-input id="password" type="password" name="password"
                          class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                          required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="form-group">
            <label for="remember_me" class="form-check">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Ingat saya</span>
            </label>
        </div>

        <x-primary-button class="btn--block">Masuk</x-primary-button>

        @if (Route::has('password.request'))
            <p class="auth-foot">
                <a class="auth-link" href="{{ route('password.request') }}">Lupa password?</a>
            </p>
        @endif
    </form>
</x-guest-layout>
