@extends('layouts.guest')
@section('title', 'Không tìm thấy trang')
@section('subtitle', 'Trang hoặc dữ liệu bạn tìm kiếm không tồn tại hoặc đã được xóa.')
@section('content')
<div class="display-2 fw-bold text-primary mb-4">404</div>
<a class="btn btn-primary" href="{{ route('dashboard') }}">Về trang tổng quan</a>
<a class="btn btn-outline-secondary ms-2" href="{{ route('login') }}">Đăng nhập</a>
@endsection
