@extends('layouts.guest')
@section('title', 'Đăng nhập')
@section('subtitle', 'Đăng nhập để quản lý lớp học và sinh viên')
@section('content')
<form method="post" action="{{ route('login') }}">@csrf
<div class="mb-3"><label class="form-label" for="username">Tên đăng nhập</label><input class="form-control" id="username" name="username" value="{{ old('username') }}" required autofocus></div>
<div class="mb-3"><label class="form-label" for="password">Mật khẩu</label><input class="form-control" id="password" type="password" name="password" required></div>
<button class="btn btn-primary w-100">Đăng nhập</button></form>
<p class="text-center mt-3 mb-0">Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký staff</a></p>
@endsection
