@extends('layouts.main')

@php($activeMenu = 'sto')

@section('title', 'Stock Opname')

@section('breadcrumb')
    <span>Home</span> / <a href="{{ route('stock-opname.index') }}">Stock Opname</a> / <span class="is-current">{{ $asset->asset_number }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Stock Opname</h1>
            <p>Verifikasi kondisi asset di lapangan.</p>
        </div>
    </div>

    <div class="detail-grid">
        <div class="card">
            <h2 class="section-card__title" style="margin-bottom: var(--space-md);">Asset</h2>
            <div class="detail-fields">
                <div>
                    <div class="detail-field__label">No. Asset</div>
                    <div class="detail-field__value">{{ $asset->asset_number }}</div>
                </div>
                <div>
                    <div class="detail-field__label">Nama</div>
                    <div class="detail-field__value">{{ $asset->name }}</div>
                </div>
                <div>
                    <div class="detail-field__label">Category</div>
                    <div class="detail-field__value">{{ $asset->category->name }}</div>
                </div>
                <div>
                    <div class="detail-field__label">Department</div>
                    <div class="detail-field__value">{{ $asset->department->name }}</div>
                </div>
                <div>
                    <div class="detail-field__label">Location</div>
                    <div class="detail-field__value">{{ $asset->location->name }}</div>
                </div>
                <div>
                    <div class="detail-field__label">Kondisi Terakhir</div>
                    <div class="detail-field__value">{{ $asset->condition->label() }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="section-card__title" style="margin-bottom: var(--space-md);">Update Kondisi</h2>

            <form method="POST" action="{{ route('stock-opname.store', $asset) }}" enctype="multipart/form-data">
                @csrf

                <div class="form-row-cols">
                    <div class="form-group">
                        <x-input-label for="condition" value="Condition" :required="true" />
                        <select id="condition" name="condition" class="form-control" required>
                            @foreach (\App\Enums\AssetCondition::cases() as $condition)
                                <option value="{{ $condition->value }}" @selected(old('condition', $asset->condition->value) === $condition->value)>{{ $condition->label() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('condition')" />
                    </div>

                    <div class="form-group">
                        <x-input-label for="status" value="Status" :required="true" />
                        <select id="status" name="status" class="form-control" required>
                            @foreach (\App\Enums\AssetStatus::cases() as $status)
                                <option value="{{ $status->value }}" @selected(old('status', $asset->status->value) === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" />
                    </div>
                </div>

                <div class="form-group">
                    <x-input-label for="notes" value="Catatan" />
                    <textarea id="notes" name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" />
                </div>

                <div class="form-group">
                    <x-input-label for="photo" value="Foto (Optional)" />
                    <input id="photo" name="photo" type="file" accept="image/png,image/jpeg,image/webp" class="form-file">
                    <x-input-error :messages="$errors->get('photo')" />
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn--primary"><i class="bi bi-check-lg"></i> Save STO</button>
                    <a href="{{ route('asset.public', $asset) }}" class="btn btn--secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
