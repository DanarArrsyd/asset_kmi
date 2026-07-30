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

    @isset($duplicatedFrom)
        <div class="form-status form-status--info">
            Disalin dari <strong>{{ $duplicatedFrom }}</strong>. Periksa kondisi, PIC dan lokasinya —
            barang kembar jarang kembar sepenuhnya. Foto tidak ikut disalin.
        </div>
    @endisset

    <form method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data">
        @include('assets._form')
    </form>
@endsection
