@extends('layouts.main')

@php($activeMenu = 'profile')

@section('title', 'Profile')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Profile']]" />
@endsection

@section('content')
    <x-page-header title="Profile" lede="Kelola informasi akun & keamanan." />

    <div class="stack">
        @include('profile.partials.update-profile-information-form')
        @include('profile.partials.update-password-form')
        @include('profile.partials.delete-user-form')
    </div>
@endsection
