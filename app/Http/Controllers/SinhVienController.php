<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSinhVienRequest;
use App\Http\Requests\UpdateSinhVienRequest;
use App\Models\LopHoc;
use App\Models\SinhVien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SinhVienController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:id,ma_sinh_vien,ho_ten'],
            'direction' => ['nullable', 'in:asc,desc'],
            'perPage' => ['nullable', 'in:10,20,50'],
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
        $perPage = (int) ($filters['perPage'] ?? 10);
        $sinhViens = $query->orderBy($filters['sort'] ?? 'id', $direction)->orderBy('id')
            ->paginate($perPage)->withQueryString();

        $lopHocs = LopHoc::withTrashed()->orderBy('ten_lop')->get();

        return view('sinh-viens.index', [
            'sinhViens' => $sinhViens,
            'filters' => $filters,
            'direction' => $direction,
            'perPage' => $perPage,
            'perPageOptions' => [10, 20, 50],
            'lopHocs' => $lopHocs,
        ]);
    }

    public function create(): View
    {
        $lopHocs = LopHoc::where('trang_thai', true)->get();

        return view('sinh-viens.create', compact('lopHocs'));
    }

    public function store(StoreSinhVienRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['trang_thai'] = $request->boolean('trang_thai');
        SinhVien::create($validated);

        return redirect()->route('sinh-viens.index')->with('success', 'Sinh viên đã được tạo thành công.');
    }

    public function show(SinhVien $sinhVien): View
    {
        return view('sinh-viens.show', compact('sinhVien'));
    }

    public function edit(SinhVien $sinhVien): View
    {
        $lopHocs = LopHoc::withTrashed()->where(fn ($query) => $query->whereNull('deleted_at')->orWhereKey($sinhVien->lop_hoc_id))->orderBy('ten_lop')->get();

        return view('sinh-viens.edit', compact('sinhVien', 'lopHocs'));
    }

    public function update(UpdateSinhVienRequest $request, SinhVien $sinhVien): RedirectResponse
    {
        $validated = $request->validated();

        $validated['trang_thai'] = $request->boolean('trang_thai');

        $sinhVien->update($validated);

        return redirect()->route('sinh-viens.index')->with('success', 'Cập nhật sinh viên thành công.');
    }

    public function destroy(SinhVien $sinhVien): RedirectResponse
    {
        $sinhVien->delete();

        return redirect()
            ->route('sinh-viens.index')
            ->with('success', 'Xóa sinh viên thành công.');
    }
}
