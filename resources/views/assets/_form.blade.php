@csrf
@if (isset($asset))
    @method('PUT')
@endif

<div class="form-layout">
    <div class="panel">
        <div class="panel__head">
            <div class="panel__head-text"><h2>Data Asset</h2></div>
        </div>

        <div class="panel__body">
            <div class="form-grid">
                <div class="form-group">
                    <x-input-label for="name" value="Nama Asset" :required="true" />
                    <x-text-input id="name" name="name" type="text" :value="old('name', $asset->name ?? '')"
                                  class="{{ $errors->has('name') ? 'is-invalid' : '' }}" required autofocus />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div class="form-group">
                    <x-input-label for="category_id" value="Kategori" :required="true" />
                    <select id="category_id" name="category_id" @class(['form-control', 'is-invalid' => $errors->has('category_id')]) required>
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $asset->category_id ?? '') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" />
                </div>

                <div class="form-group">
                    <x-input-label for="brand_id" value="Brand" />
                    <select id="brand_id" name="brand_id" @class(['form-control', 'is-invalid' => $errors->has('brand_id')])>
                        <option value="">Pilih brand</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" @selected(old('brand_id', $asset->brand_id ?? '') == $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('brand_id')" />
                </div>

                <div class="form-group">
                    <x-input-label for="model" value="Model" />
                    <x-text-input id="model" name="model" type="text" :value="old('model', $asset->model ?? '')"
                                  class="{{ $errors->has('model') ? 'is-invalid' : '' }}" />
                    <x-input-error :messages="$errors->get('model')" />
                </div>

                <div class="form-group form-field--full">
                    <x-input-label for="specification" value="Spesifikasi" />
                    <textarea id="specification" name="specification" rows="3"
                              @class(['form-control', 'is-invalid' => $errors->has('specification')])>{{ old('specification', $asset->specification ?? '') }}</textarea>
                    <x-input-error :messages="$errors->get('specification')" />
                </div>

                <div class="form-group">
                    <x-input-label for="department_id" value="Departemen" :required="true" />
                    <select id="department_id" name="department_id" @class(['form-control', 'is-invalid' => $errors->has('department_id')]) required>
                        <option value="">Pilih departemen</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id', $asset->department_id ?? '') == $department->id)>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('department_id')" />
                </div>

                <div class="form-group">
                    <x-input-label for="location_id" value="Lokasi" :required="true" />
                    <select id="location_id" name="location_id" @class(['form-control', 'is-invalid' => $errors->has('location_id')]) required>
                        <option value="">Pilih lokasi</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(old('location_id', $asset->location_id ?? '') == $location->id)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('location_id')" />
                </div>

                <div class="form-group">
                    <x-input-label for="pic" value="PIC" />
                    <x-text-input id="pic" name="pic" type="text" :value="old('pic', $asset->pic ?? '')"
                                  class="{{ $errors->has('pic') ? 'is-invalid' : '' }}" />
                    <x-input-error :messages="$errors->get('pic')" />
                </div>

                <div class="form-group">
                    <x-input-label for="purchase_date" value="Tanggal Pembelian" />
                    <x-text-input id="purchase_date" name="purchase_date" type="date"
                                  :value="old('purchase_date', isset($asset) ? $asset->purchase_date?->format('Y-m-d') : '')"
                                  class="{{ $errors->has('purchase_date') ? 'is-invalid' : '' }}" />
                    <x-input-error :messages="$errors->get('purchase_date')" />
                </div>

                <div class="form-group">
                    <x-input-label for="status" value="Status" :required="true" />
                    <select id="status" name="status" @class(['form-control', 'is-invalid' => $errors->has('status')]) required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected(old('status', $asset->status->value ?? 'active') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('status')" />
                </div>

                <div class="form-group">
                    <x-input-label for="condition" value="Kondisi" :required="true" />
                    <select id="condition" name="condition" @class(['form-control', 'is-invalid' => $errors->has('condition')]) required>
                        @foreach ($conditions as $condition)
                            <option value="{{ $condition->value }}" @selected(old('condition', $asset->condition->value ?? 'good') === $condition->value)>{{ $condition->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('condition')" />
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn--primary">
                    {{ isset($asset) ? 'Simpan Perubahan' : 'Simpan Asset' }}
                </button>
                <a href="{{ isset($asset) ? route('asset.public', $asset) : route('assets.index') }}" class="btn btn--secondary">Batal</a>
            </div>
        </div>
    </div>

    {{-- The photo input lives here rather than in the field grid: it is the one
         field with something to show, and a preview is worth more beside the
         form than a bare file picker inside it. --}}
    <aside class="form-layout__aside">
        <div class="panel">
            <div class="panel__head">
                <div class="panel__head-text"><h2>Foto Asset</h2></div>
            </div>
            <div class="panel__body">
                <div class="stack">
                    <div class="detail-photo" id="photoPreview">
                        @if (isset($asset) && $asset->photo_path)
                            <img src="{{ Storage::url($asset->photo_path) }}" alt="Foto {{ $asset->name }}">
                        @else
                            <i class="bi bi-image" aria-hidden="true"></i>
                            <span class="sr-only">Belum ada foto</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <x-input-label for="photo" :value="isset($asset) && $asset->photo_path ? 'Ganti Foto' : 'Unggah Foto'" />
                        <input id="photo" name="photo" type="file" accept="image/png,image/jpeg,image/webp"
                               class="form-file" aria-describedby="photoHint">
                        <p class="form-hint" id="photoHint">JPG, PNG, atau WEBP. Maks 2MB.</p>
                        <x-input-error :messages="$errors->get('photo')" />
                    </div>
                </div>
            </div>
        </div>

        @isset($asset)
            <div class="panel">
                <div class="panel__head">
                    <div class="panel__head-text"><h2>Info Asset</h2></div>
                    <a href="{{ route('asset.public', $asset) }}" class="panel__head-link">Lihat detail</a>
                </div>
                <div class="panel__body">
                    <div class="stack">
                        <div class="detail-field">
                            <div class="detail-field__label">No. Asset</div>
                            <div class="detail-field__value">{{ $asset->asset_number }}</div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field__label">Dibuat</div>
                            <div class="detail-field__value">{{ $asset->created_at->format('d M Y, H:i') }}</div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field__label">Terakhir Diubah</div>
                            <div class="detail-field__value">{{ $asset->updated_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="panel">
                <div class="panel__head">
                    <div class="panel__head-text"><h2>Dibuat Otomatis</h2></div>
                </div>
                <div class="panel__body">
                    <ul class="form-note-list">
                        <li>
                            <i class="bi bi-hash" aria-hidden="true"></i>
                            <span>Nomor asset berurutan, format <strong>AST-KMI-0001</strong>.</span>
                        </li>
                        <li>
                            <i class="bi bi-qr-code" aria-hidden="true"></i>
                            <span>QR code digenerate saat disimpan — cetak atau unduh dari halaman detail.</span>
                        </li>
                        <li>
                            <i class="bi bi-clock-history" aria-hidden="true"></i>
                            <span>Asset langsung siap untuk stock opname.</span>
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
            const input = document.getElementById('photo');
            const preview = document.getElementById('photoPreview');
            if (! input || ! preview) return;

            let objectUrl = null;

            input.addEventListener('change', () => {
                const file = input.files && input.files[0];
                if (! file) return;

                if (objectUrl) URL.revokeObjectURL(objectUrl);
                objectUrl = URL.createObjectURL(file);

                const img = document.createElement('img');
                img.src = objectUrl;
                img.alt = 'Pratinjau foto asset';

                preview.replaceChildren(img);
            });
        })();
    </script>
@endpush
