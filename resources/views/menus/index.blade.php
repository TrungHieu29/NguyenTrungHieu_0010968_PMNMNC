@extends('layouts.app')
@section('title', 'Quản lý Menu')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4"><div><h1 class="page-title h3 mb-1">Quản lý Menu</h1><p class="text-muted mb-0">Tùy chỉnh liên kết, vị trí và thứ tự điều hướng.</p></div><a class="btn btn-primary" href="{{ route('menus.create') }}"><i class="bi bi-plus-lg me-1"></i>Thêm Menu</a></div>
<div class="card mb-4"><div class="card-body"><form action="{{ route('menus.index') }}" method="get" class="row g-3 align-items-end">
<div class="col-md-8"><label class="form-label" for="search">Tìm kiếm</label><input class="form-control" id="search" name="search" maxlength="100" value="{{ request('search') }}" placeholder="Tên Menu, slug hoặc liên kết"></div>
<div class="col-md-4"><label class="form-label" for="trang_thai">Trạng thái</label><select class="form-select" name="trang_thai" id="trang_thai"><option value="">Tất cả trạng thái</option><option value="1" @selected(request('trang_thai') === '1')>Hiển thị</option><option value="0" @selected(request('trang_thai') === '0')>Ẩn</option></select></div>
<div class="col-md-6"><label class="form-label" for="vi_tri">Vị trí</label><select class="form-select" id="vi_tri" name="vi_tri"><option value="">Tất cả vị trí</option><option value="sidebar" @selected(request('vi_tri') === 'sidebar')>Thanh bên</option><option value="header" @selected(request('vi_tri') === 'header')>Đầu trang</option></select></div>
<div class="col-12"><hr class="my-1" style="border-color:#dce3ee"></div>
@include('partials.list-order', ['sortOptions' => ['id'=>'ID','ten_hien_thi'=>'Tên Menu','thu_tu'=>'Thứ tự'], 'defaultSort' => 'thu_tu', 'defaultDirection' => 'asc'])
</form></div></div>
<div class="card"><div class="card-body py-3 d-flex justify-content-between align-items-center"><span class="fw-semibold">Danh sách Menu</span><span class="badge text-bg-primary">{{ $menus->total() }} bản ghi</span></div>
<div class="table-responsive"><table class="table table-hover mb-0"><thead><tr>
<th>ID</th><th>Thứ tự</th><th>Tên hiển thị</th><th>Slug</th><th>Liên kết</th><th>Vị trí</th><th>Trạng thái</th><th class="text-end pe-4">Thao tác</th>
</tr></thead><tbody>
@forelse($menus as $menu)
<tr>
<td>{{ $menu->id ?? '—' }}</td>
<td>{{ $menu->thu_tu ?? '—' }}</td>
<td class="fw-semibold">{{ $menu->ten_hien_thi ?? '—' }}</td>
<td>{{ $menu->slug ?? '—' }}</td>
<td>{{ $menu->url ?? '—' }}</td>
<td>{{ $menu->vi_tri === 'sidebar' ? 'Thanh bên' : 'Đầu trang' }}</td>
<td><span class="badge {{ $menu->trang_thai ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $menu->trang_thai ? 'Hiển thị' : 'Ẩn' }}</span></td>
<td class="text-nowrap text-end pe-4"><a class="btn btn-sm btn-outline-primary" title="Xem chi tiết" href="{{ route('menus.show', $menu) }}"><i class="bi bi-eye"></i><span class="visually-hidden">Xem</span></a> <a class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa" href="{{ route('menus.edit', $menu) }}"><i class="bi bi-pencil"></i><span class="visually-hidden">Sửa</span></a> <form class="d-inline" method="post" action="{{ route('menus.destroy', $menu) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản ghi này?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash3"></i><span class="visually-hidden">Xóa</span></button></form></td>
</tr>
@empty<tr><td colspan="8" class="empty-state"><i class="bi bi-inbox"></i>Không tìm thấy dữ liệu phù hợp.<div class="small mt-2">Thử thay đổi từ khóa hoặc xóa bộ lọc.</div></td></tr>@endforelse
</tbody></table></div><div class="card-footer bg-white">{{ $menus->links() }}</div></div>
@endsection
