@extends('layouts.main')

@php($activeMenu = 'profile')

@section('title', 'Profile')

@section('breadcrumb')
    <span>Home</span> / <span class="is-current">Profile</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Profile</h1>
            <p>Kelola informasi akun & keamanan.</p>
        </div>
    </div>

    <div class="card section-card" style="max-width: 560px;">
        @include('profile.partials.update-profile-information-form')
    </div>

    <div class="card section-card" style="max-width: 560px;">
        @include('profile.partials.update-password-form')
    </div>

    <div class="card section-card" style="max-width: 560px; border-color: oklch(57.7% 0.215 27.3 / 0.35);">
        @include('profile.partials.delete-user-form')
    </div>
@endsection
