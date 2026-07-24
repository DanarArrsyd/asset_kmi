@csrf
@if (isset($targetUser))
    @method('PUT')
@endif

<div class="form-row-cols">
    <div class="form-group">
        <x-input-label for="name" value="Nama" :required="true" />
        <x-text-input id="name" name="name" type="text" :value="old('name', $targetUser->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" />
    </div>

    <div class="form-group">
        <x-input-label for="email" value="Email" :required="true" />
        <x-text-input id="email" name="email" type="email" :value="old('email', $targetUser->email ?? '')" required />
        <x-input-error :messages="$errors->get('email')" />
    </div>
</div>

<div class="form-row-cols">
    <div class="form-group">
        <x-input-label for="password" :value="isset($targetUser) ? 'Password (kosongkan jika tidak ganti)' : 'Password'" :required="! isset($targetUser)" />
        <x-text-input id="password" name="password" type="password" :required="! isset($targetUser)" autocomplete="new-password" />
        <x-input-error :messages="$errors->get('password')" />
    </div>

    <div class="form-group">
        <x-input-label for="password_confirmation" value="Confirm Password" :required="! isset($targetUser)" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" :required="! isset($targetUser)" autocomplete="new-password" />
    </div>
</div>

<div class="form-row-cols">
    <div class="form-group">
        <x-input-label for="role" value="Role" :required="true" />
        <select id="role" name="role" class="form-control" required>
            @foreach ($roles as $role)
                <option value="{{ $role->value }}" @selected(old('role', $targetUser->role->value ?? 'user') === $role->value)>{{ $role->label() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('role')" />
    </div>

    <div class="form-group">
        <x-input-label for="department_id" value="Department" />
        <select id="department_id" name="department_id" class="form-control">
            <option value="">— Tidak ada —</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}" @selected(old('department_id', $targetUser->department_id ?? '') == $department->id)>{{ $department->name }}</option>
            @endforeach
        </select>
        <p class="section-card__desc" style="margin: var(--space-2xs) 0 0;">Wajib diisi untuk role Department/User.</p>
        <x-input-error :messages="$errors->get('department_id')" />
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn--primary">
        {{ isset($targetUser) ? 'Simpan Perubahan' : 'Simpan User' }}
    </button>
    <a href="{{ route('users.index') }}" class="btn btn--secondary">Batal</a>
</div>
