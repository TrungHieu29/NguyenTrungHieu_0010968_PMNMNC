@extends('layouts.guest')
@section('title', 'Phiên làm việc đã hết hạn')
@section('subtitle', 'Vui lòng tải lại trang và thực hiện lại thao tác.')
@section('content')
<div class="display-2 fw-bold text-primary mb-4">419</div>
<a class="btn btn-primary" href="{{ route('dashboard') }}">Về trang tổng quan</a>
<a class="btn btn-outline-secondary ms-2" href="{{ route('login') }}">Đăng nhập</a>
@endsection
