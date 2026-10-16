@extends('layouts.app')
@section('title', 'Lớp học')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4"><div><h1 class="page-title h3 mb-1">Lớp học</h1><p class="text-muted mb-0">Tổ chức lớp học và quản lý thông tin giảng dạy.</p></div><a class="btn btn-primary" href="{{ route('lop-hocs.create') }}"><i class="bi bi-plus-lg me-1"></i>Thêm lớp học</a></div>
<div class="card mb-4"><div class="card-body"><form action="{{ route('lop-hocs.index') }}" method="get" class="row g-3 align-items-end">
<div class="col-md-8"><label class="form-label" for="search">Tìm kiếm</label><input class="form-control" id="search" name="search" maxlength="100" value="{{ request('search') }}" placeholder="Mã lớp, tên lớp, giáo viên"></div>
<div class="col-md-4"><label class="form-label" for="trang_thai">Trạng thái</label><select class="form-select" name="trang_thai" id="trang_thai"><option value="">Tất cả trạng thái</option><option value="1" @selected(request('trang_thai') === '1')>Hoạt động</option><option value="0" @selected(request('trang_thai') === '0')>Ngừng hoạt động</option></select></div>
<div class="col-md-3"><label class="form-label" for="si_so_min">Sĩ số từ</label><input class="form-control" type="number" min="0" id="si_so_min" name="si_so_min" value="{{ request('si_so_min') }}"></div><div class="col-md-3"><label class="form-label" for="si_so_max">Sĩ số đến</label><input class="form-control" type="number" min="0" id="si_so_max" name="si_so_max" value="{{ request('si_so_max') }}"></div>
<div class="col-12"><hr class="my-1" style="border-color:#dce3ee"></div>
@include('partials.list-order', ['sortOptions' => ['id'=>'ID','ma_lop'=>'Mã lớp','ten_lop'=>'Tên lớp','si_so'=>'Sĩ số']])
</form></div></div>
<div class="card"><div class="card-body py-3 d-flex justify-content-between align-items-center"><span class="fw-semibold">Danh sách lớp học</span><span class="badge text-bg-primary">{{ $lopHocs->total() }} bản ghi</span></div>
<div class="table-responsive"><table class="table table-hover mb-0"><thead><tr>
<th>ID</th><th>Mã lớp</th><th>Tên lớp</th><th>Sĩ số</th><th>Giáo viên</th><th>Trạng thái</th><th class="text-end pe-4">Thao tác</th>
</tr></thead><tbody>
@forelse($lopHocs as $lopHoc)
<tr>
<td>{{ $lopHoc->id ?? '—' }}</td>
<td>{{ $lopHoc->ma_lop ?? '—' }}</td>
<td class="fw-semibold">{{ $lopHoc->ten_lop ?? '—' }}</td>
<td>{{ $lopHoc->si_so ?? '—' }}</td>
<td>{{ $lopHoc->giao_vien ?? '—' }}</td>
<td><span class="badge {{ $lopHoc->trang_thai ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $lopHoc->trang_thai ? 'Hoạt động' : 'Ngừng hoạt động' }}</span></td>
<td class="text-nowrap text-end pe-4"><a class="btn btn-sm btn-outline-primary" title="Xem chi tiết" href="{{ route('lop-hocs.show', $lopHoc) }}"><i class="bi bi-eye"></i><span class="visually-hidden">Xem</span></a> <a class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa" href="{{ route('lop-hocs.edit', $lopHoc) }}"><i class="bi bi-pencil"></i><span class="visually-hidden">Sửa</span></a> <form class="d-inline" method="post" action="{{ route('lop-hocs.destroy', $lopHoc) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản ghi này?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash3"></i><span class="visually-hidden">Xóa</span></button></form></td>
</tr>
@empty<tr><td colspan="7" class="empty-state"><i class="bi bi-inbox"></i>Không tìm thấy dữ liệu phù hợp.<div class="small mt-2">Thử thay đổi từ khóa hoặc xóa bộ lọc.</div></td></tr>@endforelse
</tbody></table></div><div class="card-footer bg-white">{{ $lopHocs->links() }}</div></div>
@endsection
