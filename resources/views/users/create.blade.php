@extends('layouts.main')

@php($activeMenu = 'users')

@section('title', 'Tambah User')

@section('breadcrumb')
    <span>Home</span> / <a href="{{ route('users.index') }}">Users</a> / <span class="is-current">Tambah</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Tambah User</h1>
            <p>Buat akun baru & tentukan role akses.</p>
        </div>
    </div>

    <div class="card" style="max-width: 640px;">
        <form method="POST" action="{{ route('users.store') }}">
            @include('users._form')
        </form>
    </div>
@endsection
