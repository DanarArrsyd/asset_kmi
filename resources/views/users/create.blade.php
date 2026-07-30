@extends('layouts.main')

@php($activeMenu = 'users')

@section('title', 'Tambah Pengguna')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Pengguna', 'href' => route('users.index')],
        ['label' => 'Tambah'],
    ]" />
@endsection

@section('content')
    <x-page-header title="Tambah Pengguna" lede="Buat akun baru & tentukan role akses." />

    <form method="POST" action="{{ route('users.store') }}">
        @include('users._form')
    </form>
@endsection
