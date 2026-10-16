@extends('layouts.app')
@section('title', 'Chi tiết lớp học')
@section('content')
<div class="d-flex justify-content-between mb-4"><h1 class="page-title h3">{{ $lopHoc->ten_lop }}</h1><a class="btn btn-outline-secondary" href="{{ route('lop-hocs.index') }}">Quay lại</a></div>
<div class="card"><div class="card-body"><dl class="row detail-grid mb-0"><dt class="col-sm-3">Mã lớp</dt><dd class="col-sm-9">{{ $lopHoc->ma_lop }}</dd><dt class="col-sm-3">Sĩ số</dt><dd class="col-sm-9">{{ $lopHoc->si_so }}</dd><dt class="col-sm-3">Giáo viên</dt><dd class="col-sm-9">{{ $lopHoc->giao_vien }}</dd><dt class="col-sm-3">Điện thoại</dt><dd class="col-sm-9">{{ $lopHoc->sdt_gv ?: '—' }}</dd><dt class="col-sm-3">Ghi chú</dt><dd class="col-sm-9">{{ $lopHoc->ghi_chu ?: '—' }}</dd><dt class="col-sm-3">Trạng thái</dt><dd class="col-sm-9"><span class="badge {{ $lopHoc->trang_thai ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $lopHoc->trang_thai ? 'Hoạt động' : 'Ngừng' }}</span></dd></dl></div></div>
@endsection
