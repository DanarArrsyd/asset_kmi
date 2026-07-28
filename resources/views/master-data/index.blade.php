@extends('layouts.main')

@php($activeMenu = $activeMenu ?? null)

@section('title', $pageTitle)

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => $pageTitle]]" />
@endsection

@section('content')
    <x-page-header :title="$pageTitle" lede="Master data — dipakai di form Asset.">
        @can('create', $modelClass)
            <button type="button" class="btn btn--primary" id="mdAddBtn">
                <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah {{ $pageTitle }}
            </button>
        @endcan
    </x-page-header>

    @if (session('status') === 'created')
        <div class="form-status form-status--success">{{ $pageTitle }} berhasil ditambahkan.</div>
    @elseif (session('status') === 'updated')
        <div class="form-status form-status--success">{{ $pageTitle }} berhasil diperbarui.</div>
    @elseif (session('status') === 'deleted')
        <div class="form-status form-status--success">{{ $pageTitle }} berhasil dihapus.</div>
    @elseif (session('error'))
        <div class="form-status form-status--error">{{ session('error') }}</div>
    @endif

    <x-table-toolbar :action="route($routeBase.'.index')"
                     placeholder="Cari {{ strtolower($pageTitle) }}..."
                     :export-url="route($routeBase.'.export', request()->query())" />

    <div class="panel">
        <div class="panel__body panel__body--flush">
            <div class="table-scroll">
                <table class="table">
                    <caption class="sr-only">Daftar {{ $pageTitle }}</caption>
                    <thead>
                        <tr>
                            <x-th-sort field="name" label="Nama" />
                            @if ($hasCode)
                                <x-th-sort field="code" label="Kode" />
                            @endif
                            <x-th-sort field="assets_count" label="Jumlah Asset" />
                            <th><span class="sr-only">Aksi</span></th>
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
                                <td class="cell-actions">
                                    <div class="row-actions">
                                        @can('update', $item)
                                            <button type="button" class="btn btn--secondary btn--icon md-edit-btn"
                                                    data-id="{{ $item->id }}"
                                                    data-name="{{ $item->name }}"
                                                    data-code="{{ $item->code ?? '' }}"
                                                    title="Edit {{ $item->name }}" aria-label="Edit {{ $item->name }}">
                                                <i class="bi bi-pencil" aria-hidden="true"></i>
                                            </button>
                                        @endcan
                                        @can('delete', $item)
                                            <button type="button" class="btn btn--secondary btn--icon md-delete-btn"
                                                    data-id="{{ $item->id }}"
                                                    data-name="{{ $item->name }}"
                                                    title="Hapus {{ $item->name }}" aria-label="Hapus {{ $item->name }}">
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $hasCode ? 4 : 3 }}" class="table-empty">Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel__foot">
            <x-pagination-bar :paginator="$items" :noun="strtolower($pageTitle)" />
        </div>
    </div>

    <dialog class="confirm-dialog" id="mdFormDialog">
        <form method="POST" id="mdForm">
            @csrf
            <input type="hidden" name="_method" id="mdMethod" value="">
            <input type="hidden" name="md_id" id="mdId" value="{{ old('md_id') }}">
            <h2 id="mdFormTitle">Tambah {{ $pageTitle }}</h2>

            <div class="form-group">
                <x-input-label for="md_name" value="Nama" :required="true" />
                <input type="text" id="md_name" name="name" value="{{ old('name') }}"
                       @class(['form-control', 'is-invalid' => $errors->has('name')]) required>
                <x-input-error :messages="$errors->get('name')" />
            </div>

            @if ($hasCode)
                <div class="form-group">
                    <x-input-label for="md_code" value="Kode" :required="true" />
                    <input type="text" id="md_code" name="code" value="{{ old('code') }}"
                           @class(['form-control', 'is-invalid' => $errors->has('code')]) required>
                    <x-input-error :messages="$errors->get('code')" />
                </div>
            @endif

            <div class="form-row form-row--end">
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
            <div class="form-row form-row--end">
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
            const entity = @json($pageTitle);
            const hasErrors = @json($errors->any());

            const formDialog = document.getElementById('mdFormDialog');
            const form = document.getElementById('mdForm');
            const methodField = document.getElementById('mdMethod');
            const idField = document.getElementById('mdId');
            const titleEl = document.getElementById('mdFormTitle');
            const nameField = document.getElementById('md_name');
            const codeField = document.getElementById('md_code');

            function openForm(item) {
                const isEdit = Boolean(item && item.id);

                form.action = isEdit ? routeUpdateBase.replace('__ID__', item.id) : routeStore;
                methodField.value = isEdit ? 'PUT' : '';
                idField.value = isEdit ? item.id : '';
                titleEl.textContent = (isEdit ? 'Edit ' : 'Tambah ') + entity;

                // On a validation bounce the server-rendered old() values are
                // already in the fields — don't clobber them.
                if (!item || !item.keepValues) {
                    nameField.value = item?.name ?? '';
                    if (codeField) codeField.value = item?.code ?? '';
                }

                formDialog.showModal();
                nameField.focus();
            }

            document.getElementById('mdAddBtn')?.addEventListener('click', () => openForm(null));

            document.querySelectorAll('.md-edit-btn').forEach((btn) => {
                btn.addEventListener('click', () => openForm({
                    id: btn.dataset.id,
                    name: btn.dataset.name,
                    code: btn.dataset.code,
                }));
            });

            document.getElementById('mdFormCancel')?.addEventListener('click', () => formDialog.close());

            // Validation failed on the last submit: reopen the dialog in the
            // mode it was in, with the user's input intact. Without this the
            // errors render into a dialog nobody can see.
            if (hasErrors) {
                openForm({ id: idField.value || null, keepValues: true });
            }

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
