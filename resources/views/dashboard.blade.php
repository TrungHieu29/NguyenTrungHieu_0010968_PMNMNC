@extends('layouts.app')
@section('title', 'Tổng quan')
@section('content')
<div class="welcome-panel mb-4">
    <div class="small mb-3 text-uppercase" style="letter-spacing:2px;color:#a9bce7">Không gian làm việc</div>
    <h1 class="h3 fw-bold mb-2">Chào {{ auth()->user()->name }}!</h1>
    <p class="mb-4">Mọi thông tin lớp học và sinh viên, luôn trong tầm tay bạn.</p>
    <a href="{{ route('sinh-viens.create') }}" class="btn btn-light"><i class="bi bi-plus-lg me-2"></i>Thêm sinh viên</a>
</div>
<div class="d-flex justify-content-between mb-3"><h2 class="h5 fw-bold mb-0">Tổng quan đào tạo</h2><span class="small text-muted">Dữ liệu hiện tại</span></div>
<div class="row g-3 mb-4">
@foreach ([['Tổng số lớp học', $lopHocCount, 'collection'], ['Tổng số sinh viên', $sinhVienCount, 'mortarboard'], ['Lớp đang hoạt động', $activeLopHocCount, 'journal-check'], ['Sinh viên đang học', $activeSinhVienCount, 'person-check']] as $stat)
    <div class="col-6 col-xl-3"><div class="card stat-card h-100"><span class="stat-icon"><i class="bi bi-{{ $stat[2] }}"></i></span><div class="stat-label">{{ $stat[0] }}</div><div class="stat-value">{{ number_format($stat[1]) }}</div><div class="stat-foot">Cập nhật từ hệ thống</div></div></div>
@endforeach
@if(auth()->user()->role === 'admin')
    @foreach ([['Tổng tài khoản', $userCount, 'people'], ['Giảng viên', $lecturerCount, 'person-workspace']] as $stat)
    <div class="col-6"><div class="card"><div class="card-body d-flex align-items-center gap-3"><span class="stat-icon mb-0"><i class="bi bi-{{ $stat[2] }}"></i></span><div><div class="stat-label mb-1">{{ $stat[0] }}</div><div class="h4 fw-bold mb-0">{{ number_format($stat[1]) }}</div></div></div></div></div>
    @endforeach
@endif
</div>
<div class="card"><div class="card-body"><h2 class="h5 fw-bold mb-4">Truy cập nhanh</h2><div class="row g-3">
@foreach ([['lop-hocs.index','collection','Quản lý lớp học','Danh sách và thông tin lớp'], ['sinh-viens.index','mortarboard','Quản lý sinh viên','Hồ sơ và thông tin sinh viên'], ['profile.edit','person-circle','Hồ sơ cá nhân','Thông tin và bảo mật tài khoản']] as [$route, $icon, $title, $subtitle])
<div class="col-md-4"><a class="quick-link" href="{{ route($route) }}"><i class="bi bi-{{ $icon }}"></i><span class="fw-semibold">{{ $title }}<small>{{ $subtitle }}</small></span></a></div>
@endforeach
@if(auth()->user()->role === 'admin')
<div class="col-md-6"><a class="quick-link" href="{{ route('users.index') }}"><i class="bi bi-people"></i><span class="fw-semibold">Quản lý User<small>Cấp tài khoản và phân quyền</small></span></a></div>
<div class="col-md-6"><a class="quick-link" href="{{ route('menus.index') }}"><i class="bi bi-layout-sidebar"></i><span class="fw-semibold">Quản lý Menu<small>Tùy chỉnh điều hướng hệ thống</small></span></a></div>
@endif
</div></div></div>
@endsection
