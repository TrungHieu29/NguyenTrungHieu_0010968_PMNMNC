@extends('layouts.app')
@section('title', 'Chi tiết tài khoản')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><h1 class="h3 page-title">Chi tiết tài khoản</h1><a class="btn btn-primary" href="{{ route('users.edit', $user) }}"><i class="bi bi-pencil me-1"></i>Chỉnh sửa</a></div>
<div class="card"><div class="card-body"><dl class="row detail-grid mb-0">
@foreach(['id'=>'ID','name'=>'Họ tên','username'=>'Tên đăng nhập','email'=>'Email'] as $field=>$label)<dt class="col-sm-3">{{ $label }}</dt><dd class="col-sm-9">{{ $user->$field ?? '—' }}</dd>@endforeach
<dt class="col-sm-3">Vai trò</dt><dd class="col-sm-9">{{ $user->role === 'admin' ? 'Quản trị viên' : 'Giảng viên' }}</dd>
<dt class="col-sm-3">Trạng thái</dt><dd class="col-sm-9"><span class="badge {{ $user->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $user->is_active ? 'Hoạt động' : 'Đã khóa' }}</span></dd>
<dt class="col-sm-3">Ngày tạo</dt><dd class="col-sm-9">{{ $user->created_at?->format('d/m/Y H:i') }}</dd>
</dl><a class="btn btn-outline-secondary" href="{{ route('users.index') }}">Quay lại</a></div></div>
@endsection
