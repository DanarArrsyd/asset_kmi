<section>
    <h2 class="section-card__title">{{ __('Delete Account') }}</h2>
    <p class="section-card__desc">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>

    <button type="button" class="btn btn--danger" id="deleteAccountTrigger">
        {{ __('Delete Account') }}
    </button>

    <dialog class="confirm-dialog" id="deleteAccountDialog">
        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <h2>{{ __('Are you sure you want to delete your account?') }}</h2>
            <p>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>

            <div class="form-group">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-text-input id="password" name="password" type="password" placeholder="{{ __('Password') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="form-row" style="justify-content: flex-end; margin-top: var(--space-md);">
                <button type="button" class="btn btn--secondary" id="deleteAccountCancel">{{ __('Cancel') }}</button>
                <x-danger-button>{{ __('Delete Account') }}</x-danger-button>
            </div>
        </form>
    </dialog>
</section>

@if ($errors->userDeletion->isNotEmpty())
    <script>
        document.getElementById('deleteAccountDialog').showModal();
    </script>
@endif

@push('scripts')
<script>
    (function () {
        const dialog = document.getElementById('deleteAccountDialog');
        document.getElementById('deleteAccountTrigger')?.addEventListener('click', () => dialog.showModal());
        document.getElementById('deleteAccountCancel')?.addEventListener('click', () => dialog.close());
    })();
</script>
@endpush
