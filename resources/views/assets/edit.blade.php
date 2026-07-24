@extends('layouts.main')

@php($activeMenu = 'asset')

@section('title', 'Edit Asset')

@section('breadcrumb')
    <span>Home</span> / <a href="{{ route('assets.index') }}">Asset</a> / <span class="is-current">{{ $asset->asset_number }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Edit Asset</h1>
            <p>{{ $asset->asset_number }} — {{ $asset->name }}</p>
        </div>
    </div>

    <div class="card" style="max-width: 720px;">
        <form method="POST" action="{{ route('assets.update', $asset) }}" enctype="multipart/form-data">
            @include('assets._form')
        </form>
    </div>
@endsection
