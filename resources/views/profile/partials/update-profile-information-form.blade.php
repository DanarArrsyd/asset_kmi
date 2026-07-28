<section class="panel panel--form-sm">
    <div class="panel__head">
        <div class="panel__head-text">
            <h2>{{ __('Profile Information') }}</h2>
            <p>{{ __("Update your account's profile information and email address.") }}</p>
        </div>
    </div>

    <div class="panel__body">
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="form-group">
                <x-input-label for="name" :value="__('Name')" :required="true" />
                <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)"
                              class="{{ $errors->has('name') ? 'is-invalid' : '' }}" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" />
            </div>

            <div class="form-group">
                <x-input-label for="email" :value="__('Email')" :required="true" />
                <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)"
                              class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" />

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <p class="form-hint">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" type="submit" class="auth-link">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <div class="form-status form-status--success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </div>
                    @endif
                @endif
            </div>

            <div class="form-actions">
                <x-primary-button>{{ __('Save') }}</x-primary-button>

                @if (session('status') === 'profile-updated')
                    <span class="form-saved" role="status">{{ __('Saved.') }}</span>
                @endif
            </div>
        </form>
    </div>
</section>
