@csrf
@if (isset($asset))
    @method('PUT')
@endif

<div class="form-row-cols">
    <div class="form-group">
        <x-input-label for="name" value="Nama Asset" :required="true" />
        <x-text-input id="name" name="name" type="text" :value="old('name', $asset->name ?? '')" required autofocus />
        <x-input-error :messages="$errors->get('name')" />
    </div>

    <div class="form-group">
        <x-input-label for="category_id" value="Kategori" :required="true" />
        <select id="category_id" name="category_id" class="form-control" required>
            <option value="">Pilih kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $asset->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" />
    </div>
</div>

<div class="form-row-cols">
    <div class="form-group">
        <x-input-label for="brand_id" value="Brand" />
        <select id="brand_id" name="brand_id" class="form-control">
            <option value="">Pilih brand</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id', $asset->brand_id ?? '') == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('brand_id')" />
    </div>

    <div class="form-group">
        <x-input-label for="model" value="Model" />
        <x-text-input id="model" name="model" type="text" :value="old('model', $asset->model ?? '')" />
        <x-input-error :messages="$errors->get('model')" />
    </div>
</div>

<div class="form-group">
    <x-input-label for="specification" value="Specification" />
    <textarea id="specification" name="specification" class="form-control" rows="3">{{ old('specification', $asset->specification ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('specification')" />
</div>

<div class="form-row-cols">
    <div class="form-group">
        <x-input-label for="department_id" value="Department" :required="true" />
        <select id="department_id" name="department_id" class="form-control" required>
            <option value="">Pilih department</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}" @selected(old('department_id', $asset->department_id ?? '') == $department->id)>{{ $department->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('department_id')" />
    </div>

    <div class="form-group">
        <x-input-label for="location_id" value="Location" :required="true" />
        <select id="location_id" name="location_id" class="form-control" required>
            <option value="">Pilih location</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}" @selected(old('location_id', $asset->location_id ?? '') == $location->id)>{{ $location->name }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('location_id')" />
    </div>
</div>

<div class="form-row-cols">
    <div class="form-group">
        <x-input-label for="pic" value="PIC" />
        <x-text-input id="pic" name="pic" type="text" :value="old('pic', $asset->pic ?? '')" />
        <x-input-error :messages="$errors->get('pic')" />
    </div>

    <div class="form-group">
        <x-input-label for="purchase_date" value="Purchase Date" />
        <x-text-input id="purchase_date" name="purchase_date" type="date" :value="old('purchase_date', isset($asset) ? $asset->purchase_date?->format('Y-m-d') : '')" />
        <x-input-error :messages="$errors->get('purchase_date')" />
    </div>
</div>

<div class="form-row-cols">
    <div class="form-group">
        <x-input-label for="status" value="Status" :required="true" />
        <select id="status" name="status" class="form-control" required>
            @foreach ($statuses as $status)
                <option value="{{ $status->value }}" @selected(old('status', $asset->status->value ?? 'active') === $status->value)>{{ $status->label() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('status')" />
    </div>

    <div class="form-group">
        <x-input-label for="condition" value="Condition" :required="true" />
        <select id="condition" name="condition" class="form-control" required>
            @foreach ($conditions as $condition)
                <option value="{{ $condition->value }}" @selected(old('condition', $asset->condition->value ?? 'good') === $condition->value)>{{ $condition->label() }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('condition')" />
    </div>
</div>

<div class="form-group">
    <x-input-label for="photo" value="Foto Asset" />
    <input id="photo" name="photo" type="file" accept="image/png,image/jpeg,image/webp" class="form-file">
    <p class="section-card__desc" style="margin: var(--space-2xs) 0 0;">JPG, PNG, atau WEBP. Maks 2MB.</p>
    <x-input-error :messages="$errors->get('photo')" />
</div>

<div class="form-actions">
    <button type="submit" class="btn btn--primary">
        {{ isset($asset) ? 'Simpan Perubahan' : 'Simpan Asset' }}
    </button>
    <a href="{{ isset($asset) ? route('asset.public', $asset) : route('assets.index') }}" class="btn btn--secondary">Batal</a>
</div>
