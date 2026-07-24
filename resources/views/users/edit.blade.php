@extends('layouts.main')

@php($activeMenu = 'users')

@section('title', 'Edit User')

@section('breadcrumb')
    <span>Home</span> / <a href="{{ route('users.index') }}">Users</a> / <span class="is-current">{{ $targetUser->name }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div>
            <h1>Edit User</h1>
            <p>{{ $targetUser->name }} — {{ $targetUser->email }}</p>
        </div>
    </div>

    <div class="card" style="max-width: 640px;">
        <form method="POST" action="{{ route('users.update', $targetUser) }}">
            @include('users._form')
        </form>
    </div>
@endsection
