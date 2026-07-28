@extends('layouts.main')

@php($activeMenu = 'asset')

@section('title', 'Edit Asset')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Asset', 'href' => route('assets.index')],
        ['label' => $asset->asset_number, 'href' => route('asset.public', $asset)],
        ['label' => 'Edit'],
    ]" />
@endsection

@section('content')
    <x-page-header title="Edit Asset" :lede="$asset->asset_number.' — '.$asset->name" />

    <div class="panel panel--form">
        <div class="panel__body">
            <form method="POST" action="{{ route('assets.update', $asset) }}" enctype="multipart/form-data">
                @include('assets._form')
            </form>
        </div>
    </div>
@endsection
