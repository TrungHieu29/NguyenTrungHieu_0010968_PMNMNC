@extends('layouts.guest')
@section('title', 'Chào mừng trở lại')
@section('subtitle', 'Đăng nhập tài khoản để bắt đầu phiên làm việc.')
@section('content')
<form method="post" action="{{ route('login') }}">@csrf
    <div class="mb-3"><label class="form-label" for="username">Tên đăng nhập</label><input class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" placeholder="Nhập tên đăng nhập" autocomplete="username" required autofocus>@error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="mb-4"><label class="form-label" for="password">Mật khẩu</label><input class="form-control @error('password') is-invalid @enderror" id="password" type="password" name="password" placeholder="Nhập mật khẩu" autocomplete="current-password" required>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <button class="btn btn-primary w-100">Đăng nhập <i class="bi bi-arrow-right ms-2"></i></button>
</form>
<p class="text-muted small text-center mt-4">Cần tài khoản hoặc hỗ trợ đăng nhập?<br>Vui lòng liên hệ quản trị viên của bạn.</p>
@endsection
