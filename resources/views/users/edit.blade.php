@extends('layouts.main')

@php($activeMenu = 'users')

@section('title', 'Edit User')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Users', 'href' => route('users.index')],
        ['label' => $targetUser->name],
    ]" />
@endsection

@section('content')
    <x-page-header title="Edit User" :lede="$targetUser->name.' — '.$targetUser->email" />

    <form method="POST" action="{{ route('users.update', $targetUser) }}">
        @include('users._form')
    </form>
@endsection
