@extends('layouts.app')
@section('title', 'Quản lý User')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4"><div><h1 class="page-title h3 mb-1">Quản lý User</h1><p class="text-muted mb-0">Cấp tài khoản và quản lý quyền truy cập hệ thống.</p></div><a class="btn btn-primary" href="{{ route('users.create') }}"><i class="bi bi-plus-lg me-1"></i>Thêm tài khoản</a></div>
<div class="card mb-4"><div class="card-body"><form action="{{ route('users.index') }}" method="get" class="row g-3 align-items-end">
<div class="col-md-8"><label class="form-label" for="search">Tìm kiếm</label><input class="form-control" id="search" name="search" maxlength="100" value="{{ request('search') }}" placeholder="Họ tên, tên đăng nhập, email"></div>
<div class="col-md-4"><label class="form-label" for="is_active">Trạng thái</label><select class="form-select" name="is_active" id="is_active"><option value="">Tất cả trạng thái</option><option value="1" @selected(request('is_active') === '1')>Hoạt động</option><option value="0" @selected(request('is_active') === '0')>Đã khóa</option></select></div>
<div class="col-md-6"><label class="form-label" for="role">Vai trò</label><select class="form-select" id="role" name="role"><option value="">Tất cả vai trò</option><option value="admin" @selected(request('role') === 'admin')>Quản trị viên</option><option value="lecturer" @selected(request('role') === 'lecturer')>Giảng viên</option></select></div>
<div class="col-12"><hr class="my-1" style="border-color:#dce3ee"></div>
@include('partials.list-order', ['sortOptions' => ['id'=>'ID','name'=>'Họ tên','username'=>'Tên đăng nhập','email'=>'Email','role'=>'Vai trò','is_active'=>'Trạng thái','created_at'=>'Ngày tạo']])
</form></div></div>
<div class="card"><div class="card-body py-3 d-flex justify-content-between align-items-center"><span class="fw-semibold">Danh sách tài khoản</span><span class="badge text-bg-primary">{{ $users->total() }} bản ghi</span></div>
<div class="table-responsive"><table class="table table-hover mb-0"><thead><tr>
<th>ID</th><th>Họ tên</th><th>Tên đăng nhập</th><th>Email</th><th>Vai trò</th><th>Ngày tạo</th><th>Trạng thái</th><th class="text-end pe-4">Thao tác</th>
</tr></thead><tbody>
@forelse($users as $user)
<tr>
<td>{{ $user->id ?? '—' }}</td>
<td class="fw-semibold">{{ $user->name ?? '—' }}</td>
<td>{{ $user->username ?? '—' }}</td>
<td>{{ $user->email ?? '—' }}</td>
<td>{{ $user->role === 'admin' ? 'Quản trị viên' : 'Giảng viên' }}</td>
<td>{{ $user->created_at?->format('d/m/Y') }}</td>
<td><span class="badge {{ $user->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $user->is_active ? 'Hoạt động' : 'Đã khóa' }}</span></td>
<td class="text-nowrap text-end pe-4"><a class="btn btn-sm btn-outline-primary" title="Xem chi tiết" href="{{ route('users.show', $user) }}"><i class="bi bi-eye"></i><span class="visually-hidden">Xem</span></a> <a class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa" href="{{ route('users.edit', $user) }}"><i class="bi bi-pencil"></i><span class="visually-hidden">Sửa</span></a> <form class="d-inline" method="post" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản ghi này?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash3"></i><span class="visually-hidden">Xóa</span></button></form></td>
</tr>
@empty<tr><td colspan="8" class="empty-state"><i class="bi bi-inbox"></i>Không tìm thấy dữ liệu phù hợp.<div class="small mt-2">Thử thay đổi từ khóa hoặc xóa bộ lọc.</div></td></tr>@endforelse
</tbody></table></div><div class="card-footer bg-white">{{ $users->links() }}</div></div>
@endsection
