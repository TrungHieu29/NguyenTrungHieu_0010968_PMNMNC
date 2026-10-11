<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LopHoc;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LopHocController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', 'boolean'],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);
        $query = LopHoc::query();
        if (! empty($filters['search'])) {
            $query->where(fn ($q) => $q->where('ma_lop', 'like', '%'.$filters['search'].'%')
                ->orWhere('ten_lop', 'like', '%'.$filters['search'].'%'));
        }
        if (isset($filters['trang_thai'])) {
            $query->where('trang_thai', $filters['trang_thai']);
        }

        return $this->successResponse($query->orderBy('id', $filters['direction'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 10)->withQueryString());
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['trang_thai'] = $request->boolean('trang_thai', true);

        return $this->successResponse(LopHoc::create($data), 'Đã tạo lớp học.', 201);
    }

    public function show(LopHoc $lopHoc)
    {
        return $this->successResponse($lopHoc);
    }

    public function update(Request $request, LopHoc $lopHoc)
    {
        $data = $request->validate($this->rules($lopHoc, $request->isMethod('patch')));
        if ($request->exists('trang_thai')) {
            $data['trang_thai'] = $request->boolean('trang_thai');
        }
        $lopHoc->update($data);

        return $this->successResponse($lopHoc->refresh(), 'Đã cập nhật lớp học.');
    }

    public function destroy(LopHoc $lopHoc)
    {
        $lopHoc->delete();

        return $this->successResponse(null, 'Đã xóa lớp học.');
    }

    private function rules(?LopHoc $lopHoc = null, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return [
            'ten_lop' => [$required, 'string', 'max:255'],
            'ma_lop' => [$required, 'string', 'max:20', Rule::unique('lop_hocs', 'ma_lop')->ignore($lopHoc?->id)],
            'si_so' => [$required, 'integer', 'min:1'],
            'giao_vien' => [$required, 'string', 'max:255'],
            'sdt_gv' => ['sometimes', 'nullable', 'string', 'max:15'],
            'ghi_chu' => ['sometimes', 'nullable', 'string', 'max:500'],
            'trang_thai' => ['sometimes', 'boolean'],
        ];
    }
}
