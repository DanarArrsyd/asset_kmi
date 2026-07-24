@extends('layouts.main')

@php($activeMenu = 'users')

@section('title', 'Users')

@section('breadcrumb')
    <span>Home</span> / <span class="is-current">Users</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Users</h1>
            <p>Kelola akun & role akses.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn--primary">
            <i class="bi bi-plus-lg"></i> Tambah User
        </a>
    </div>

    @if (session('status') === 'created')
        <div class="form-status form-status--success">User berhasil ditambahkan.</div>
    @elseif (session('status') === 'updated')
        <div class="form-status form-status--success">User berhasil diperbarui.</div>
    @elseif (session('status') === 'deleted')
        <div class="form-status form-status--success">User berhasil dihapus.</div>
    @elseif (session('error'))
        <div class="form-status" style="color: var(--color-danger); background: oklch(57.7% 0.215 27.3 / 0.12);">{{ session('error') }}</div>
    @endif

    <form method="GET" class="table-toolbar">
        <label class="table-toolbar__search">
            <i class="bi bi-search"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / email...">
        </label>

        <select name="role" class="form-control" onchange="this.form.submit()">
            <option value="">Semua Role</option>
            @foreach ($roles as $role)
                <option value="{{ $role->value }}" @selected(request('role') === $role->value)>{{ $role->label() }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn--secondary">Filter</button>
        <a href="{{ route('users.index') }}" class="btn btn--secondary">Reset</a>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td class="cell-muted">{{ $user->email }}</td>
                        <td><span class="pill pill--success">{{ $user->role->label() }}</span></td>
                        <td class="cell-muted">{{ $user->department->name ?? '—' }}</td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn--secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                @if ($user->id !== auth()->id())
                                    <button type="button" class="btn btn--secondary user-delete-btn"
                                        data-id="{{ $user->id }}" data-name="{{ $user->name }}" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="table-empty">Belum ada user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-bar">
        <span>Menampilkan {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} user</span>
        {{ $users->onEachSide(1)->links() }}
    </div>

    <dialog class="confirm-dialog" id="userDeleteDialog">
        <form method="POST" id="userDeleteForm">
            @csrf
            @method('DELETE')
            <h2>Hapus <span id="userDeleteName"></span>?</h2>
            <p>Akun ini akan dihapus permanen, tidak bisa login lagi.</p>
            <div class="form-row" style="justify-content: flex-end;">
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
