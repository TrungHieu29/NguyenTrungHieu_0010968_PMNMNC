@extends('layouts.app')
@section('title', 'Chỉnh sửa tài khoản')
@section('content')
<div class="mb-4"><a class="small text-decoration-none" href="{{ route('users.index') }}"><i class="bi bi-arrow-left me-1"></i>Danh sách tài khoản</a><h1 class="page-title h3 mt-3">Chỉnh sửa tài khoản</h1><p class="text-muted">Quản lý thông tin đăng nhập, vai trò và trạng thái truy cập.</p></div>
<div class="card"><div class="card-body"><form method="post" action="{{ route('users.update', $user) }}">@csrf @method('PUT')
@include('users._form')
</form></div></div>
@endsection
