<section class="panel panel--danger">
    <div class="panel__head">
        <div class="panel__head-text">
            <h2>Hapus Akun</h2>
            <p>
                Akun yang dihapus tidak bisa dikembalikan, termasuk riwayat stock opname
                yang pernah Anda catat. Hanya Super Admin yang bisa membuatkan akun baru.
            </p>
        </div>
    </div>

    <div class="panel__body">
        <button type="button" class="btn btn--danger" id="deleteAccountTrigger">
            Hapus Akun
        </button>
    </div>

    <dialog class="confirm-dialog" id="deleteAccountDialog">
        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <h2>Hapus akun Anda?</h2>
            <p>
                Tindakan ini permanen. Riwayat stock opname atas nama Anda ikut terhapus.
                Masukkan password untuk mengonfirmasi.
            </p>

            <div class="form-group">
                <x-input-label for="password" value="Password" class="sr-only" />
                <x-text-input id="password" name="password" type="password" placeholder="Password"
                              class="{{ $errors->userDeletion->has('password') ? 'is-invalid' : '' }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="form-row form-row--end">
                <button type="button" class="btn btn--secondary" id="deleteAccountCancel">Batal</button>
                <x-danger-button>Hapus Akun</x-danger-button>
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
