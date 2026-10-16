<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLopHocRequest;
use App\Http\Requests\UpdateLopHocRequest;
use App\Models\LopHoc;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LopHocController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:id,ma_lop,ten_lop,si_so'],
            'direction' => ['nullable', 'in:asc,desc'],
            'perPage' => ['nullable', 'in:10,20,50'],
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
        $perPage = (int) ($filters['perPage'] ?? 10);
        $lopHocs = $query->orderBy($filters['sort'] ?? 'id', $direction)->orderBy('id')
            ->paginate($perPage)->withQueryString();

        return view('lop-hocs.index', [
            'lopHocs' => $lopHocs,
            'filters' => $filters,
            'direction' => $direction,
            'perPage' => $perPage,
            'perPageOptions' => [10, 20, 50],
        ]);
    }

    public function create(): View
    {
        return view('lop-hocs.create');
    }

    public function store(StoreLopHocRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['trang_thai'] = $request->boolean('trang_thai');
        LopHoc::create($validated);

        return redirect()->route('lop-hocs.index')->with('success', 'Lớp học đã được tạo thành công.');
    }

    public function show(LopHoc $lopHoc): View
    {
        return view('lop-hocs.show', compact('lopHoc'));
    }

    public function edit(LopHoc $lopHoc): View
    {
        return view('lop-hocs.edit', compact('lopHoc'));
    }

    public function update(UpdateLopHocRequest $request, LopHoc $lopHoc): RedirectResponse
    {
        $validated = $request->validated();
        $validated['trang_thai'] = $request->boolean('trang_thai');
        $lopHoc->update($validated);

        return redirect()->route('lop-hocs.index')->with('success', 'Lớp học đã được cập nhật thành công.');
    }

    public function destroy(LopHoc $lopHoc): RedirectResponse
    {
        $lopHoc->delete();

        return redirect()->route('lop-hocs.index')->with('success', 'Lớp học đã được xóa thành công.');
    }
}
