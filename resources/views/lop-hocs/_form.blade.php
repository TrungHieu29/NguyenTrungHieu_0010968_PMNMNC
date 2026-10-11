@php($item = $lopHoc ?? null)
<div class="row g-3">
<div class="col-md-6"><label class="form-label" for="ma_lop">Mã lớp *</label><input class="form-control @error('ma_lop') is-invalid @enderror" id="ma_lop" name="ma_lop" value="{{ old('ma_lop', $item?->ma_lop) }}" required>@error('ma_lop')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="col-md-6"><label class="form-label" for="ten_lop">Tên lớp *</label><input class="form-control @error('ten_lop') is-invalid @enderror" id="ten_lop" name="ten_lop" value="{{ old('ten_lop', $item?->ten_lop) }}" required>@error('ten_lop')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="si_so">Sĩ số *</label><input class="form-control" id="si_so" type="number" min="1" name="si_so" value="{{ old('si_so', $item?->si_so) }}" required></div>
<div class="col-md-4"><label class="form-label" for="giao_vien">Giáo viên *</label><input class="form-control" id="giao_vien" name="giao_vien" value="{{ old('giao_vien', $item?->giao_vien) }}" required></div>
<div class="col-md-4"><label class="form-label" for="sdt_gv">Điện thoại giáo viên</label><input class="form-control" id="sdt_gv" name="sdt_gv" value="{{ old('sdt_gv', $item?->sdt_gv) }}"></div>
<div class="col-12"><label class="form-label" for="ghi_chu">Ghi chú</label><textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3">{{ old('ghi_chu', $item?->ghi_chu) }}</textarea></div>
<div class="col-12"><input type="hidden" name="trang_thai" value="0"><div class="form-check form-switch"><input class="form-check-input" id="trang_thai" type="checkbox" name="trang_thai" value="1" @checked(old('trang_thai', $item?->trang_thai ?? true))><label class="form-check-label" for="trang_thai">Đang hoạt động</label></div></div>
</div>
<div class="mt-4 d-flex gap-2"><button class="btn btn-primary">Lưu lớp học</button><a class="btn btn-outline-secondary" href="{{ route('lop-hocs.index') }}">Hủy</a></div>
