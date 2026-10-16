<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLopHocRequest;
use App\Http\Requests\UpdateLopHocRequest;
use App\Models\LopHoc;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LopHocController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:id,ma_lop,ten_lop,si_so'],
            'direction' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'in:10,20,50'],
            'page' => ['nullable', 'integer', 'min:1'],
            'si_so_min' => ['nullable', 'integer', 'min:0'],
            'si_so_max' => ['nullable', 'integer', 'min:0', ...($request->filled('si_so_min') ? ['gte:si_so_min'] : [])],
        ]);
        $query = LopHoc::query();
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('ma_lop', 'like', '%'.$filters['search'].'%')
                    ->orWhere('ten_lop', 'like', '%'.$filters['search'].'%')
                    ->orWhere('giao_vien', 'like', '%'.$filters['search'].'%');
            });
        }
        foreach (['trang_thai'] as $field) {
            if (isset($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        if (isset($filters['si_so_min'])) {
            $query->where('si_so', '>=', $filters['si_so_min']);
        }
        if (isset($filters['si_so_max'])) {
            $query->where('si_so', '<=', $filters['si_so_max']);
        }
        $direction = $filters['direction'] ?? 'desc';
        $perPage = (int) ($filters['per_page'] ?? 10);
        $lopHocs = $query->orderBy($filters['sort'] ?? 'id', $direction)->orderBy('id')
            ->paginate($perPage)->withQueryString();

        return $this->successResponse($lopHocs);
    }

    public function store(StoreLopHocRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['trang_thai'] = $request->boolean('trang_thai', true);
        $record = LopHoc::create($data);

        return $this->successResponse($record, 'Đã tạo lớp học.', 201);
    }

    public function show(LopHoc $lopHoc): JsonResponse
    {
        return $this->successResponse($lopHoc);
    }

    public function update(UpdateLopHocRequest $request, LopHoc $lopHoc): JsonResponse
    {
        $lopHoc->update($request->validated());

        return $this->successResponse($lopHoc->refresh(), 'Đã cập nhật lớp học.');
    }

    public function destroy(LopHoc $lopHoc): JsonResponse
    {
        $lopHoc->delete();

        return $this->successResponse(null, 'Đã xóa lớp học.');
    }
}
