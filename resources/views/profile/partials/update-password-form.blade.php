<section class="panel">
    <div class="panel__head">
        <div class="panel__head-text">
            <h2>Ubah Password</h2>
            <p>Pakai password yang panjang dan sulit ditebak agar akun tetap aman.</p>
        </div>
    </div>

    <div class="panel__body">
        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="form-grid">
                <div class="form-group form-field--full">
                    <x-input-label for="update_password_current_password" value="Password Saat Ini" :required="true" />
                    <x-text-input id="update_password_current_password" name="current_password" type="password"
                                  class="{{ $errors->updatePassword->has('current_password') ? 'is-invalid' : '' }}"
                                  autocomplete="current-password" />
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" />
                </div>

                <div class="form-group">
                    <x-input-label for="update_password_password" value="Password Baru" :required="true" />
                    <x-text-input id="update_password_password" name="password" type="password"
                                  class="{{ $errors->updatePassword->has('password') ? 'is-invalid' : '' }}"
                                  autocomplete="new-password" />
                    <x-input-error :messages="$errors->updatePassword->get('password')" />
                </div>

                <div class="form-group">
                    <x-input-label for="update_password_password_confirmation" value="Konfirmasi Password Baru" :required="true" />
                    <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                                  autocomplete="new-password" />
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" />
                </div>
            </div>

            <div class="form-actions">
                <x-primary-button>Simpan Password</x-primary-button>

                @if (session('status') === 'password-updated')
                    <span class="form-saved" role="status">Tersimpan.</span>
                @endif
            </div>
        </form>
    </div>
</section>
