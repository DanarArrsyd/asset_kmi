@extends('layouts.main')

@php($activeMenu = 'asset')

@section('title', 'Tambah Asset')

@section('breadcrumb')
    <span>Home</span> / <a href="{{ route('assets.index') }}">Asset</a> / <span class="is-current">Tambah</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Tambah Asset</h1>
            <p>Isi data asset baru. Nomor asset & QR dibuat otomatis.</p>
        </div>
    </div>

    <div class="card" style="max-width: 720px;">
        <form method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data">
            @include('assets._form')
        </form>
    </div>
@endsection
