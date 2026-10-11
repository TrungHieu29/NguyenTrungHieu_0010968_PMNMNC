@extends('layouts.app')
@section('title', 'Tổng quan')
@section('content')
<h1 class="page-title h3 mb-1">Tổng quan</h1><p class="text-muted mb-4">Chào {{ auth()->user()->name }}, đây là tình hình dữ liệu hiện tại.</p>
<div class="row g-3 mb-4">
@foreach ([['Tổng lớp học',$lopHocCount,'primary'],['Tổng sinh viên',$sinhVienCount,'info'],['Lớp đang hoạt động',$activeLopHocCount,'success'],['Sinh viên đang học',$activeSinhVienCount,'warning']] as $stat)
<div class="col-sm-6 col-xl-3"><div class="card h-100"><div class="card-body"><div class="text-muted">{{ $stat[0] }}</div><div class="display-5 fw-bold text-{{ $stat[2] }}">{{ $stat[1] }}</div></div></div></div>
@endforeach
</div>
<div class="card"><div class="card-body"><h2 class="h5">Truy cập nhanh</h2><div class="d-flex flex-wrap gap-2"><a class="btn btn-primary" href="{{ route('lop-hocs.index') }}">Quản lý lớp</a><a class="btn btn-outline-primary" href="{{ route('sinh-viens.index') }}">Quản lý sinh viên</a>@if(auth()->user()->role === 'admin')<a class="btn btn-outline-secondary" href="{{ route('menus.index') }}">Quản lý Menu</a>@endif</div></div></div>
@endsection
