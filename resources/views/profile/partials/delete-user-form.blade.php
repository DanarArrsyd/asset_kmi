<section class="panel panel--form-sm panel--danger">
    <div class="panel__head">
        <div class="panel__head-text">
            <h2>{{ __('Delete Account') }}</h2>
            <p>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>
        </div>
    </div>

    <div class="panel__body">
        <button type="button" class="btn btn--danger" id="deleteAccountTrigger">
            {{ __('Delete Account') }}
        </button>
    </div>

    <dialog class="confirm-dialog" id="deleteAccountDialog">
        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <h2>{{ __('Are you sure you want to delete your account?') }}</h2>
            <p>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>

            <div class="form-group">
                <x-input-label for="password" :value="__('Password')" class="sr-only" />
                <x-text-input id="password" name="password" type="password" placeholder="{{ __('Password') }}"
                              class="{{ $errors->userDeletion->has('password') ? 'is-invalid' : '' }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="form-row form-row--end">
                <button type="button" class="btn btn--secondary" id="deleteAccountCancel">{{ __('Cancel') }}</button>
                <x-danger-button>{{ __('Delete Account') }}</x-danger-button>
            </div>
        </form>
    </dialog>
</section>

@push('scripts')
<script>
    (function () {
        const dialog = document.getElementById('deleteAccountDialog');

        document.getElementById('deleteAccountTrigger')?.addEventListener('click', () => dialog.showModal());
        document.getElementById('deleteAccountCancel')?.addEventListener('click', () => dialog.close());

        {{-- A failed password confirmation must reopen the dialog, otherwise the
             error renders where the user cannot see it. --}}
        @if ($errors->userDeletion->isNotEmpty())
            dialog.showModal();
        @endif
    })();
</script>
@endpush
