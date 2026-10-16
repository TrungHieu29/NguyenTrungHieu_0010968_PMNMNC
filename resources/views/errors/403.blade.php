@extends('layouts.guest')
@section('title', 'Không có quyền truy cập')
@section('subtitle', 'Bạn không có quyền sử dụng chức năng này hoặc tài khoản đã bị khóa.')
@section('content')
<div class="display-2 fw-bold text-primary mb-4">403</div>
<a class="btn btn-primary" href="{{ route('dashboard') }}">Về trang tổng quan</a>
<a class="btn btn-outline-secondary ms-2" href="{{ route('login') }}">Đăng nhập</a>
@endsection
