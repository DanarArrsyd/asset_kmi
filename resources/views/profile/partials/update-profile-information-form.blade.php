<section class="panel">
    <div class="panel__head">
        <div class="panel__head-text">
            <h2>Informasi Akun</h2>
            <p>Perbarui nama dan email yang dipakai akun ini.</p>
        </div>
    </div>

    <div class="panel__body">
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="form-grid">
                <div class="form-group">
                    <x-input-label for="name" value="Nama" :required="true" />
                    <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)"
                                  class="{{ $errors->has('name') ? 'is-invalid' : '' }}" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div class="form-group">
                    <x-input-label for="email" value="Email" :required="true" />
                    <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)"
                                  class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" />

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <p class="form-hint">
                            Email Anda belum terverifikasi.
                            <button form="send-verification" type="submit" class="auth-link">
                                Kirim ulang email verifikasi.
                            </button>
                        </p>

                        @if (session('status') === 'verification-link-sent')
                            <div class="form-status form-status--success">
                                Link verifikasi baru sudah dikirim ke email Anda.
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <div class="form-actions">
                <x-primary-button>Simpan</x-primary-button>

                @if (session('status') === 'profile-updated')
                    <span class="form-saved" role="status">Tersimpan.</span>
                @endif
            </div>
        </form>
    </div>
</section>
