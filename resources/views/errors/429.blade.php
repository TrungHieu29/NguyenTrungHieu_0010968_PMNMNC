@extends('layouts.guest')
@section('title', 'Quá nhiều yêu cầu')
@section('subtitle', 'Bạn đã thao tác quá nhiều lần. Vui lòng chờ một phút rồi thử lại.')
@section('content')
<div class="display-2 fw-bold text-primary mb-4">429</div>
<a class="btn btn-primary" href="{{ route('dashboard') }}">Về trang tổng quan</a>
<a class="btn btn-outline-secondary ms-2" href="{{ route('login') }}">Đăng nhập</a>
@endsection
