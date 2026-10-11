@extends('layouts.app')
@section('title', $menu->ten_hien_thi)
@section('content')
<h1 class="page-title h3 mb-4">{{ $menu->ten_hien_thi }}</h1>
<div class="card"><div class="card-body p-5 text-center"><h2 class="h4 mb-3">Chức năng đang được phát triển</h2><p class="text-muted">Nội dung của trang này sẽ được bổ sung sau.</p><a class="btn btn-primary" href="{{ route('dashboard') }}">Về Dashboard</a></div></div>
@endsection
