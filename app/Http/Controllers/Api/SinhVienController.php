<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SinhVien;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SinhVienController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'lop_hoc_id' => ['nullable', 'integer'],
            'trang_thai' => ['nullable', 'boolean'],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        $query = SinhVien::with('lopHoc');
        if (! empty($filters['search'])) {
            $query->where(fn ($q) => $q->where('ma_sinh_vien', 'like', '%'.$filters['search'].'%')
                ->orWhere('ho_ten', 'like', '%'.$filters['search'].'%'));
        }
        foreach (['lop_hoc_id', 'trang_thai'] as $filter) {
            if (isset($filters[$filter])) {
                $query->where($filter, $filters[$filter]);
            }
        }

        return $this->successResponse($query->orderBy('id', $filters['direction'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 10)->withQueryString());
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['trang_thai'] = $request->boolean('trang_thai', true);

        return $this->successResponse(SinhVien::create($data)->load('lopHoc'), 'Đã tạo sinh viên.', 201);
    }

    public function show(SinhVien $sinhVien)
    {
        return $this->successResponse($sinhVien->load('lopHoc'));
    }

    public function update(Request $request, SinhVien $sinhVien)
    {
        $data = $request->validate($this->rules($sinhVien, $request->isMethod('patch')));
        if ($request->exists('trang_thai')) {
            $data['trang_thai'] = $request->boolean('trang_thai');
        }
        $sinhVien->update($data);

        return $this->successResponse($sinhVien->refresh()->load('lopHoc'), 'Đã cập nhật sinh viên.');
    }

    public function destroy(SinhVien $sinhVien)
    {
        $sinhVien->delete();

        return $this->successResponse(null, 'Đã xóa sinh viên.');
    }

    private function rules(?SinhVien $sinhVien = null, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return [
            'ma_sinh_vien' => [$required, 'string', 'max:20', Rule::unique('sinh_viens', 'ma_sinh_vien')->ignore($sinhVien?->id)],
            'ho_ten' => [$required, 'string', 'max:255'],
            'email' => [$required, 'email', 'max:255', Rule::unique('sinh_viens', 'email')->ignore($sinhVien?->id)],
            'lop_hoc_id' => [$required, Rule::exists('lop_hocs', 'id')->whereNull('deleted_at')],
            'ngay_sinh' => ['sometimes', 'nullable', 'date'],
            'gioi_tinh' => ['sometimes', 'nullable', Rule::in(['Nam', 'Nữ'])],
            'so_dien_thoai' => ['sometimes', 'nullable', 'string', 'max:20'],
            'dia_chi' => ['sometimes', 'nullable', 'string', 'max:255'],
            'trang_thai' => ['sometimes', 'boolean'],
        ];
    }
}
