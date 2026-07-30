@extends('layouts.main')

@php($activeMenu = 'users')

@section('title', 'Pengguna')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Pengguna']]" />
@endsection

@section('content')
    <x-page-header title="Pengguna" lede="Kelola akun & role akses.">
        <a href="{{ route('users.create') }}" class="btn btn--primary">
            <i class="bi bi-plus-lg" aria-hidden="true"></i> Tambah User
        </a>
    </x-page-header>

    @if (session('status') === 'created')
        <div class="form-status form-status--success">Pengguna berhasil ditambahkan.</div>
    @elseif (session('status') === 'updated')
        <div class="form-status form-status--success">Pengguna berhasil diperbarui.</div>
    @elseif (session('status') === 'deleted')
        <div class="form-status form-status--success">Pengguna berhasil dihapus.</div>
    @elseif (session('error'))
        <div class="form-status form-status--error">{{ session('error') }}</div>
    @endif

    <x-table-toolbar :action="route('users.index')"
                     placeholder="Cari nama / email..."
                     :export-url="route('users.export', request()->query())">
        <label class="sr-only" for="filterRole">Role</label>
        <select id="filterRole" name="role" class="form-control">
            <option value="">Semua Role</option>
            @foreach ($roles as $role)
                <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
            @endforeach
        </select>
    </x-table-toolbar>

    <div class="panel">
        <div class="panel__body panel__body--flush">
            <div class="table-scroll">
                <table class="table">
                    <caption class="sr-only">Daftar pengguna</caption>
                    <thead>
                        <tr>
                            <x-th-sort field="name" label="Nama" />
                            <x-th-sort field="email" label="Email" />
                            <x-th-sort field="role" label="Role" />
                            <th>Departemen</th>
                            <x-th-sort field="created_at" label="Dibuat" />
                            <th><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td class="cell-muted">{{ $user->email }}</td>
                                <td><span class="pill {{ $user->role->badgeClass() }}">{{ $user->role->label() }}</span></td>
                                <td class="cell-muted">{{ $user->department->name ?? '—' }}</td>
                                <td class="cell-muted">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="cell-actions">
                                    <div class="row-actions">
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn--secondary btn--icon"
                                           title="Edit {{ $user->name }}" aria-label="Edit {{ $user->name }}">
                                            <i class="bi bi-pencil" aria-hidden="true"></i>
                                        </a>
                                        @if ($user->id !== auth()->id())
                                            <button type="button" class="btn btn--secondary btn--icon user-delete-btn"
                                                    data-id="{{ $user->id }}" data-name="{{ $user->name }}"
                                                    title="Hapus {{ $user->name }}" aria-label="Hapus {{ $user->name }}">
                                                <i class="bi bi-trash" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="table-empty">Belum ada pengguna.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="panel__foot">
            <x-pagination-bar :paginator="$users" noun="user" />
        </div>
    </div>

    <dialog class="confirm-dialog" id="userDeleteDialog">
        <form method="POST" id="userDeleteForm">
            @csrf
            @method('DELETE')
            <h2>Hapus <span id="userDeleteName"></span>?</h2>
            <p>Akun ini akan dihapus permanen, tidak bisa login lagi.</p>
            <div class="form-row form-row--end">
                <button type="button" class="btn btn--secondary" id="userDeleteCancel">Batal</button>
                <button type="submit" class="btn btn--danger">Hapus</button>
            </div>
        </form>
    </dialog>

    @push('scripts')
    <script>
        (function () {
            const dialog = document.getElementById('userDeleteDialog');
            const form = document.getElementById('userDeleteForm');
            const nameEl = document.getElementById('userDeleteName');
            const routeBase = @json(route('users.destroy', ['user' => '__ID__']));

            document.querySelectorAll('.user-delete-btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    form.action = routeBase.replace('__ID__', btn.dataset.id);
                    nameEl.textContent = btn.dataset.name;
                    dialog.showModal();
                });
            });

            document.getElementById('userDeleteCancel')?.addEventListener('click', () => dialog.close());
        })();
    </script>
    @endpush
@endsection
