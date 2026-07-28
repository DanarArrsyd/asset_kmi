@extends('layouts.main')

@php($activeMenu = 'asset')

@section('title', 'Tambah Asset')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Asset', 'href' => route('assets.index')],
        ['label' => 'Tambah'],
    ]" />
@endsection

@section('content')
    <x-page-header title="Tambah Asset" lede="Isi data asset baru. Nomor asset & QR dibuat otomatis." />

    <div class="panel panel--form">
        <div class="panel__body">
            <form method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data">
                @include('assets._form')
            </form>
        </div>
    </div>
@endsection
