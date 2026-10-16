@extends('layouts.app')
@section('title', 'Chi tiết Menu')
@section('content')
<div class="d-flex justify-content-between mb-4"><h1 class="page-title h3">{{ $menu->ten_hien_thi }}</h1><a class="btn btn-outline-secondary" href="{{ route('menus.index') }}">Quay lại</a></div><div class="card"><div class="card-body"><dl class="row detail-grid mb-0"><dt class="col-sm-3">Slug</dt><dd class="col-sm-9">{{ $menu->slug }}</dd><dt class="col-sm-3">URL</dt><dd class="col-sm-9">{{ $menu->url }}</dd><dt class="col-sm-3">Vị trí</dt><dd class="col-sm-9">{{ $menu->vi_tri === 'sidebar' ? 'Thanh bên' : 'Đầu trang' }}</dd><dt class="col-sm-3">Thứ tự</dt><dd class="col-sm-9">{{ $menu->thu_tu }}</dd><dt class="col-sm-3">Trạng thái</dt><dd class="col-sm-9"><span class="badge {{ $menu->trang_thai ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $menu->trang_thai ? 'Hiển thị' : 'Ẩn' }}</span></dd></dl></div></div>
@endsection
