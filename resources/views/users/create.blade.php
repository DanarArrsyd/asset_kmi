@extends('layouts.main')

@php($activeMenu = 'users')

@section('title', 'Tambah User')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Users', 'href' => route('users.index')],
        ['label' => 'Tambah'],
    ]" />
@endsection

@section('content')
    <x-page-header title="Tambah User" lede="Buat akun baru & tentukan role akses." />

    <div class="panel panel--form-sm">
        <div class="panel__body">
            <form method="POST" action="{{ route('users.store') }}">
                @include('users._form')
            </form>
        </div>
    </div>
@endsection
