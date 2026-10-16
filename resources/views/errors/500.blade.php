@extends('layouts.guest')
@section('title', 'Có lỗi xảy ra')
@section('subtitle', 'Hệ thống chưa thể xử lý yêu cầu. Vui lòng thử lại sau.')
@section('content')
<div class="display-2 fw-bold text-primary mb-4">500</div>
<a class="btn btn-primary" href="{{ route('dashboard') }}">Về trang tổng quan</a>
<a class="btn btn-outline-secondary ms-2" href="{{ route('login') }}">Đăng nhập</a>
@endsection
