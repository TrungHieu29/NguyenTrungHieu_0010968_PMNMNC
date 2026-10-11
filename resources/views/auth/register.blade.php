@extends('layouts.guest')
@section('title', 'Đăng ký')
@section('subtitle', 'Tạo tài khoản nhân viên')
@section('content')
<form method="post" action="{{ route('register') }}">@csrf
<div class="mb-3"><label class="form-label" for="name">Họ tên</label><input class="form-control" id="name" name="name" value="{{ old('name') }}" required></div>
<div class="mb-3"><label class="form-label" for="username">Tên đăng nhập</label><input class="form-control" id="username" name="username" value="{{ old('username') }}" required></div>
<div class="mb-3"><label class="form-label" for="email">Email (không bắt buộc)</label><input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}"></div>
<div class="mb-3"><label class="form-label" for="password">Mật khẩu</label><input class="form-control" id="password" type="password" name="password" required><div class="form-text">Tối thiểu 6 ký tự.</div></div>
<div class="mb-3"><label class="form-label" for="password_confirmation">Xác nhận mật khẩu</label><input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required></div>
<button class="btn btn-primary w-100">Đăng ký</button></form>
<p class="text-center mt-3 mb-0">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
@endsection
