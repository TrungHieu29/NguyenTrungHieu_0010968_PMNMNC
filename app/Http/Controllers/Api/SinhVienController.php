<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSinhVienRequest;
use App\Http\Requests\UpdateSinhVienRequest;
use App\Models\SinhVien;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SinhVienController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:id,ma_sinh_vien,ho_ten'],
            'direction' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'in:10,20,50'],
            'page' => ['nullable', 'integer', 'min:1'],
            'lop_hoc_id' => ['nullable', 'integer', 'exists:lop_hocs,id'],
        ]);
        $query = SinhVien::with('lopHoc');
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('ma_sinh_vien', 'like', '%'.$filters['search'].'%')
                    ->orWhere('ho_ten', 'like', '%'.$filters['search'].'%')
                    ->orWhere('email', 'like', '%'.$filters['search'].'%');
            });
        }
        foreach (['trang_thai', 'lop_hoc_id'] as $field) {
            if (isset($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        $direction = $filters['direction'] ?? 'desc';
        $perPage = (int) ($filters['per_page'] ?? 10);
        $sinhViens = $query->orderBy($filters['sort'] ?? 'id', $direction)->orderBy('id')
            ->paginate($perPage)->withQueryString();

        return $this->successResponse($sinhViens);
    }

    public function store(StoreSinhVienRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['trang_thai'] = $request->boolean('trang_thai', true);
        $record = SinhVien::create($data);

        return $this->successResponse($record->load('lopHoc'), 'Đã tạo sinh viên.', 201);
    }

    public function show(SinhVien $sinhVien): JsonResponse
    {
        return $this->successResponse($sinhVien->load('lopHoc'));
    }

    public function update(UpdateSinhVienRequest $request, SinhVien $sinhVien): JsonResponse
    {
        $sinhVien->update($request->validated());

        return $this->successResponse($sinhVien->refresh()->load('lopHoc'), 'Đã cập nhật sinh viên.');
    }

    public function destroy(SinhVien $sinhVien): JsonResponse
    {
        $sinhVien->delete();

        return $this->successResponse(null, 'Đã xóa sinh viên.');
    }
}
