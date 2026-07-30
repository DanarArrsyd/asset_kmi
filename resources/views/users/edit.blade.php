@extends('layouts.main')

@php($activeMenu = 'users')

@section('title', 'Edit Pengguna')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Pengguna', 'href' => route('users.index')],
        ['label' => $targetUser->name],
    ]" />
@endsection

@section('content')
    <x-page-header title="Edit Pengguna" :lede="$targetUser->name.' — '.$targetUser->email" />

    <form method="POST" action="{{ route('users.update', $targetUser) }}">
        @include('users._form')
    </form>
@endsection
