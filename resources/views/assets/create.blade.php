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

    <form method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data">
        @include('assets._form')
    </form>
@endsection
