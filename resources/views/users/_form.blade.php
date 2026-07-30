@csrf
@if (isset($targetUser))
    @method('PUT')
@endif

<div class="form-layout">
    <div class="panel">
        <div class="panel__head">
            <div class="panel__head-text"><h2>Data Akun</h2></div>
        </div>

        <div class="panel__body">
            <div class="form-grid">
                <div class="form-group">
                    <x-input-label for="name" value="Nama" :required="true" />
                    <x-text-input id="name" name="name" type="text" :value="old('name', $targetUser->name ?? '')"
                                  class="{{ $errors->has('name') ? 'is-invalid' : '' }}" required autofocus />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div class="form-group">
                    <x-input-label for="email" value="Email" :required="true" />
                    <x-text-input id="email" name="email" type="email" :value="old('email', $targetUser->email ?? '')"
                                  class="{{ $errors->has('email') ? 'is-invalid' : '' }}" required />
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                <div class="form-group">
                    <x-input-label for="password"
                                   :value="isset($targetUser) ? 'Password (kosongkan jika tidak ganti)' : 'Password'"
                                   :required="! isset($targetUser)" />
                    <x-text-input id="password" name="password" type="password" :required="! isset($targetUser)"
                                  class="{{ $errors->has('password') ? 'is-invalid' : '' }}" autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" />
                </div>

                <div class="form-group">
                    <x-input-label for="password_confirmation" value="Konfirmasi Password" :required="! isset($targetUser)" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                  :required="! isset($targetUser)" autocomplete="new-password" />
                </div>

                <div class="form-group">
                    <x-input-label for="role" value="Role" :required="true" />
                    <select id="role" name="role" @class(['form-control', 'is-invalid' => $errors->has('role')]) required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" @selected(old('role', $targetUser->role->value ?? 'user') === $role->value)>{{ $role->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('role')" />
                </div>

                <div class="form-group">
                    <x-input-label for="department_id" value="Departemen" />
                    <select id="department_id" name="department_id" aria-describedby="departmentHint"
                            @class(['form-control', 'is-invalid' => $errors->has('department_id')])>
                        <option value="">— Tidak ada —</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id', $targetUser->department_id ?? '') == $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    <p class="form-hint" id="departmentHint">Wajib diisi untuk role Department dan User.</p>
                    <x-input-error :messages="$errors->get('department_id')" />
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary">
                    {{ isset($targetUser) ? 'Simpan Perubahan' : 'Simpan Pengguna' }}
                </button>
                <a href="{{ route('users.index') }}" class="btn btn--secondary">Batal</a>
            </div>
        </div>
    </div>

    {{-- Role is the one field on this form with consequences the label does not
         explain. The list answers "what am I about to grant?" without making
         anyone open the docs. --}}
    <aside class="form-layout__aside">
        <div class="panel">
            <div class="panel__head">
                <div class="panel__head-text"><h2>Hak Akses per Role</h2></div>
            </div>
            <div class="panel__body">
                <div class="role-list" id="roleList">
                    @foreach ($roles as $role)
                        <div data-role="{{ $role->value }}" @class(['role-list__item', 'is-active' => old('role', $targetUser->role->value ?? 'user') === $role->value])>
                            <div class="role-list__name">{{ $role->label() }}</div>
                            <p class="role-list__desc">{{ $role->description() }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @isset($targetUser)
            <div class="panel">
                <div class="panel__head">
                    <div class="panel__head-text"><h2>Info Akun</h2></div>
                </div>
                <div class="panel__body">
                    <div class="stack">
                        <div class="detail-field">
                            <div class="detail-field__label">Role Saat Ini</div>
                            <div class="detail-field__value">
                                <span class="pill {{ $targetUser->role->badgeClass() }}">{{ $targetUser->role->label() }}</span>
                            </div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field__label">Terdaftar</div>
                            <div class="detail-field__value">{{ $targetUser->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="panel">
                <div class="panel__head">
                    <div class="panel__head-text"><h2>Sebelum Menyimpan</h2></div>
                </div>
                <div class="panel__body">
                    <ul class="form-note-list">
                        <li>
                            <i class="bi bi-envelope" aria-hidden="true"></i>
                            <span>Email dipakai untuk login dan harus unik.</span>
                        </li>
                        <li>
                            <i class="bi bi-key" aria-hidden="true"></i>
                            <span>Password diserahkan langsung ke user — sistem tidak mengirim email.</span>
                        </li>
                        <li>
                            <i class="bi bi-diagram-3" aria-hidden="true"></i>
                            <span>Role Department dan User hanya melihat asset departemennya.</span>
                        </li>
                    </ul>
                </div>
            </div>
        @endisset
    </aside>
</div>

@push('scripts')
    <script>
        (function () {
            const select = document.getElementById('role');
            const list = document.getElementById('roleList');
            if (! select || ! list) return;

            select.addEventListener('change', () => {
                list.querySelectorAll('[data-role]').forEach((item) => {
                    item.classList.toggle('is-active', item.dataset.role === select.value);
                });
            });
        })();
    </script>
@endpush
