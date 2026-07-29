@extends('layouts.main')

@php($activeMenu = 'profile')

@section('title', 'Profile')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Profile']]" />
@endsection

@section('content')
    <x-page-header title="Profile" lede="Kelola informasi akun & keamanan." />

    @if (session('error'))
        <div class="form-status form-status--error">{{ session('error') }}</div>
    @endif

    <div class="form-layout">
        <div class="stack">
            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
            @include('profile.partials.delete-user-form')
        </div>

        {{-- Role and department are set by a Super Admin, not here. Showing them
             answers the question the form itself raises — "what is this account
             allowed to do?" — without pretending they are editable. --}}
        <aside class="form-layout__aside">
            <div class="panel">
                <div class="panel__head">
                    <div class="panel__head-text"><h2>Akun Anda</h2></div>
                </div>
                <div class="panel__body">
                    <div class="stack">
                        <div class="detail-field">
                            <div class="detail-field__label">Role</div>
                            <div class="detail-field__value">
                                <span class="pill {{ $user->role->badgeClass() }}">{{ $user->role->label() }}</span>
                            </div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field__label">Department</div>
                            <div class="detail-field__value">{{ $user->department?->name ?? '—' }}</div>
                        </div>
                        <div class="detail-field">
                            <div class="detail-field__label">Terdaftar</div>
                            <div class="detail-field__value">{{ $user->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel__head">
                    <div class="panel__head-text"><h2>Hak Akses Anda</h2></div>
                </div>
                <div class="panel__body">
                    <p class="form-note">{{ $user->role->description() }}</p>
                    <p class="form-note">Role dan department hanya bisa diubah oleh Super Admin.</p>
                </div>
            </div>
        </aside>
    </div>
@endsection
