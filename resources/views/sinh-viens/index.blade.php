@extends('layouts.app')
@section('title', 'Sinh viên')
@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4"><div><h1 class="page-title h3 mb-1">Sinh viên</h1><p class="text-muted mb-0">Theo dõi hồ sơ và thông tin học tập của sinh viên.</p></div><a class="btn btn-primary" href="{{ route('sinh-viens.create') }}"><i class="bi bi-plus-lg me-1"></i>Thêm sinh viên</a></div>
<div class="card mb-4"><div class="card-body"><form action="{{ route('sinh-viens.index') }}" method="get" class="row g-3 align-items-end">
<div class="col-md-8"><label class="form-label" for="search">Tìm kiếm</label><input class="form-control" id="search" name="search" maxlength="100" value="{{ request('search') }}" placeholder="Mã sinh viên, họ tên, email"></div>
<div class="col-md-4"><label class="form-label" for="trang_thai">Trạng thái</label><select class="form-select" name="trang_thai" id="trang_thai"><option value="">Tất cả trạng thái</option><option value="1" @selected(request('trang_thai') === '1')>Đang học</option><option value="0" @selected(request('trang_thai') === '0')>Ngừng học</option></select></div>
<div class="col-md-6"><label class="form-label" for="lop_hoc_id">Lớp học</label><select class="form-select" id="lop_hoc_id" name="lop_hoc_id"><option value="">Tất cả lớp học</option>@foreach($lopHocs as $class)<option value="{{ $class->id }}" @selected(request('lop_hoc_id') == $class->id)>{{ $class->ma_lop }} · {{ $class->ten_lop }}{{ $class->trashed() ? ' (đã xóa)' : '' }}</option>@endforeach</select></div>
<div class="col-12"><hr class="my-1" style="border-color:#dce3ee"></div>
@include('partials.list-order', ['sortOptions' => ['id'=>'ID','ma_sinh_vien'=>'Mã sinh viên','ho_ten'=>'Họ tên']])
</form></div></div>
<div class="card"><div class="card-body py-3 d-flex justify-content-between align-items-center"><span class="fw-semibold">Danh sách sinh viên</span><span class="badge text-bg-primary">{{ $sinhViens->total() }} bản ghi</span></div>
<div class="table-responsive"><table class="table table-hover mb-0"><thead><tr>
<th>ID</th><th>Mã SV</th><th>Họ tên</th><th>Email</th><th>Lớp học</th><th>Trạng thái</th><th class="text-end pe-4">Thao tác</th>
</tr></thead><tbody>
@forelse($sinhViens as $sinhVien)
<tr>
<td>{{ $sinhVien->id ?? '—' }}</td>
<td>{{ $sinhVien->ma_sinh_vien ?? '—' }}</td>
<td class="fw-semibold">{{ $sinhVien->ho_ten ?? '—' }}</td>
<td>{{ $sinhVien->email ?? '—' }}</td>
<td>{{ $sinhVien->lopHoc?->ten_lop ?? '—' }}@if($sinhVien->lopHoc?->trashed())<span class="badge text-bg-secondary ms-1">Đã xóa</span>@endif</td>
<td><span class="badge {{ $sinhVien->trang_thai ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $sinhVien->trang_thai ? 'Đang học' : 'Ngừng học' }}</span></td>
<td class="text-nowrap text-end pe-4"><a class="btn btn-sm btn-outline-primary" title="Xem chi tiết" href="{{ route('sinh-viens.show', $sinhVien) }}"><i class="bi bi-eye"></i><span class="visually-hidden">Xem</span></a> <a class="btn btn-sm btn-outline-secondary" title="Chỉnh sửa" href="{{ route('sinh-viens.edit', $sinhVien) }}"><i class="bi bi-pencil"></i><span class="visually-hidden">Sửa</span></a> <form class="d-inline" method="post" action="{{ route('sinh-viens.destroy', $sinhVien) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bản ghi này?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash3"></i><span class="visually-hidden">Xóa</span></button></form></td>
</tr>
@empty<tr><td colspan="7" class="empty-state"><i class="bi bi-inbox"></i>Không tìm thấy dữ liệu phù hợp.<div class="small mt-2">Thử thay đổi từ khóa hoặc xóa bộ lọc.</div></td></tr>@endforelse
</tbody></table></div><div class="card-footer bg-white">{{ $sinhViens->links() }}</div></div>
@endsection
