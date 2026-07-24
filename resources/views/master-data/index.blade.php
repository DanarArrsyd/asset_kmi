@extends('layouts.main')

@php($activeMenu = $activeMenu ?? null)

@section('title', $pageTitle)

@section('breadcrumb')
    <span>Home</span> / <span class="is-current">{{ $pageTitle }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $pageTitle }}</h1>
            <p>Master data — dipakai di form Asset.</p>
        </div>
        @can('create', $modelClass)
            <button type="button" class="btn btn--primary" id="mdAddBtn">
                <i class="bi bi-plus-lg"></i> Tambah {{ $pageTitle }}
            </button>
        @endcan
    </div>

    @if (session('status') === 'created')
        <div class="form-status form-status--success">{{ $pageTitle }} berhasil ditambahkan.</div>
    @elseif (session('status') === 'updated')
        <div class="form-status form-status--success">{{ $pageTitle }} berhasil diperbarui.</div>
    @elseif (session('status') === 'deleted')
        <div class="form-status form-status--success">{{ $pageTitle }} berhasil dihapus.</div>
    @elseif (session('error'))
        <div class="form-status" style="color: var(--color-danger); background: oklch(57.7% 0.215 27.3 / 0.12);">{{ session('error') }}</div>
    @endif

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    @if ($hasCode)
                        <th>Kode</th>
                    @endif
                    <th>Jumlah Asset</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        @if ($hasCode)
                            <td class="cell-muted">{{ $item->code }}</td>
                        @endif
                        <td class="cell-muted">{{ $item->assets_count }}</td>
                        <td>
                            <div class="row-actions">
                                @can('update', $item)
                                    <button type="button" class="btn btn--secondary md-edit-btn"
                                        data-id="{{ $item->id }}"
                                        data-name="{{ $item->name }}"
                                        data-code="{{ $item->code ?? '' }}"
                                        title="Edit"><i class="bi bi-pencil"></i></button>
                                @endcan
                                @can('delete', $item)
                                    <button type="button" class="btn btn--secondary md-delete-btn"
                                        data-id="{{ $item->id }}"
                                        data-name="{{ $item->name }}"
                                        title="Hapus"><i class="bi bi-trash"></i></button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="table-empty">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <dialog class="confirm-dialog" id="mdFormDialog">
        <form method="POST" id="mdForm">
            @csrf
            <input type="hidden" name="_method" id="mdMethod" value="">
            <h2 id="mdFormTitle">Tambah {{ $pageTitle }}</h2>

            <div class="form-group">
                <x-input-label for="md_name" value="Nama" :required="true" />
                <input type="text" id="md_name" name="name" class="form-control" required>
            </div>

            @if ($hasCode)
                <div class="form-group">
                    <x-input-label for="md_code" value="Kode" :required="true" />
                    <input type="text" id="md_code" name="code" class="form-control" required>
                </div>
            @endif

            <div class="form-row" style="justify-content: flex-end;">
                <button type="button" class="btn btn--secondary" id="mdFormCancel">Batal</button>
                <button type="submit" class="btn btn--primary">Simpan</button>
            </div>
        </form>
    </dialog>

    <dialog class="confirm-dialog" id="mdDeleteDialog">
        <form method="POST" id="mdDeleteForm">
            @csrf
            @method('DELETE')
            <h2>Hapus <span id="mdDeleteName"></span>?</h2>
            <p>Data ini akan dihapus permanen.</p>
            <div class="form-row" style="justify-content: flex-end;">
                <button type="button" class="btn btn--secondary" id="mdDeleteCancel">Batal</button>
                <button type="submit" class="btn btn--danger">Hapus</button>
            </div>
        </form>
    </dialog>

    @push('scripts')
    <script>
        (function () {
            const routeStore = @json(route($routeBase.'.store'));
            const routeUpdateBase = @json(route($routeBase.'.update', ['id' => '__ID__']));
            const routeDestroyBase = @json(route($routeBase.'.destroy', ['id' => '__ID__']));

            const formDialog = document.getElementById('mdFormDialog');
            const form = document.getElementById('mdForm');
            const methodField = document.getElementById('mdMethod');
            const titleEl = document.getElementById('mdFormTitle');
            const nameField = document.getElementById('md_name');
            const codeField = document.getElementById('md_code');

            document.getElementById('mdAddBtn')?.addEventListener('click', () => {
                form.action = routeStore;
                methodField.value = '';
                titleEl.textContent = 'Tambah {{ $pageTitle }}';
                nameField.value = '';
                if (codeField) codeField.value = '';
                formDialog.showModal();
            });

            document.querySelectorAll('.md-edit-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    form.action = routeUpdateBase.replace('__ID__', btn.dataset.id);
                    methodField.value = 'PUT';
                    titleEl.textContent = 'Edit {{ $pageTitle }}';
                    nameField.value = btn.dataset.name;
                    if (codeField) codeField.value = btn.dataset.code;
                    formDialog.showModal();
                });
            });

            document.getElementById('mdFormCancel')?.addEventListener('click', () => formDialog.close());

            const deleteDialog = document.getElementById('mdDeleteDialog');
            const deleteForm = document.getElementById('mdDeleteForm');
            const deleteNameEl = document.getElementById('mdDeleteName');

            document.querySelectorAll('.md-delete-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    deleteForm.action = routeDestroyBase.replace('__ID__', btn.dataset.id);
                    deleteNameEl.textContent = btn.dataset.name;
                    deleteDialog.showModal();
                });
            });

            document.getElementById('mdDeleteCancel')?.addEventListener('click', () => deleteDialog.close());
        })();
    </script>
    @endpush
@endsection
