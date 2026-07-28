@extends('layouts.main')

@php($activeMenu = 'sto')

@section('title', 'Stock Opname')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Stock Opname', 'href' => route('stock-opname.index')],
        ['label' => $asset->asset_number],
    ]" />
@endsection

@section('content')
    <x-page-header title="Stock Opname" lede="Verifikasi kondisi asset di lapangan." />

    <div class="detail-grid detail-grid--even">
        <div class="panel">
            <div class="panel__head">
                <div class="panel__head-text"><h2>Asset</h2></div>
                <span class="pill {{ $asset->status->badgeClass() }}">{{ $asset->status->label() }}</span>
            </div>
            <div class="panel__body">
                <div class="detail-fields">
                    <div class="detail-field">
                        <div class="detail-field__label">No. Asset</div>
                        <div class="detail-field__value">{{ $asset->asset_number }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-field__label">Nama</div>
                        <div class="detail-field__value">{{ $asset->name }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-field__label">Category</div>
                        <div class="detail-field__value">{{ $asset->category->name }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-field__label">Department</div>
                        <div class="detail-field__value">{{ $asset->department->name }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-field__label">Location</div>
                        <div class="detail-field__value">{{ $asset->location->name }}</div>
                    </div>
                    <div class="detail-field">
                        <div class="detail-field__label">Kondisi Terakhir</div>
                        <div class="detail-field__value">{{ $asset->condition->label() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head">
                <div class="panel__head-text"><h2>Update Kondisi</h2></div>
            </div>
            <div class="panel__body">
                <form method="POST" action="{{ route('stock-opname.store', $asset) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <x-input-label for="condition" value="Condition" :required="true" />
                            <select id="condition" name="condition" @class(['form-control', 'is-invalid' => $errors->has('condition')]) required>
                                @foreach (\App\Enums\AssetCondition::cases() as $condition)
                                    <option value="{{ $condition->value }}" @selected(old('condition', $asset->condition->value) === $condition->value)>{{ $condition->label() }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('condition')" />
                        </div>

                        <div class="form-group">
                            <x-input-label for="status" value="Status" :required="true" />
                            <select id="status" name="status" @class(['form-control', 'is-invalid' => $errors->has('status')]) required>
                                @foreach (\App\Enums\AssetStatus::cases() as $status)
                                    <option value="{{ $status->value }}" @selected(old('status', $asset->status->value) === $status->value)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" />
                        </div>

                        <div class="form-group form-field--full">
                            <x-input-label for="notes" value="Catatan" />
                            <textarea id="notes" name="notes" rows="3"
                                      @class(['form-control', 'is-invalid' => $errors->has('notes')])>{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" />
                        </div>

                        <div class="form-group form-field--full">
                            <x-input-label for="photo" value="Foto (Optional)" />
                            <input id="photo" name="photo" type="file" accept="image/png,image/jpeg,image/webp" class="form-file">
                            <x-input-error :messages="$errors->get('photo')" />
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn--primary">
                            <i class="bi bi-check-lg" aria-hidden="true"></i> Save STO
                        </button>
                        <a href="{{ route('asset.public', $asset) }}" class="btn btn--secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
