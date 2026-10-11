<?php

namespace App\Http\Controllers;

use App\Models\LopHoc;
use Illuminate\Http\Request;
use App\Http\Requests\StorelopHocRequest;
use App\Http\Requests\UpdateLopHocRequest;
use Illuminate\Support\Facades\Validator;

class LopHocController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private const PER_PAGE_OPTIONS = [10, 20, 50, 100];

    public function index(Request $request)
    {

        $filters = Validator::make($request->query(), [
            'search' => 'nullable|string|max:100',
            'trang_thai' => 'nullable|in:0,1',
            'si_so_min' => 'nullable|integer|min:0',
            'si_so_max' => 'nullable|integer|min:0',
            'direction' => 'nullable|in:asc,desc',
            'perPage' => 'nullable|in:' . implode(',', self::PER_PAGE_OPTIONS),
        ])->valid();

        $query = LopHoc::query();

        if (isset($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) {
                $q->where('ma_lop', 'like', "%{$search}%")
                ->orWhere('ten_lop', 'like', "%{$search}%")
                ->orWhere('giao_vien', 'like', "%{$search}%");
            });
        }

        if (isset($filters['trang_thai'])) {
            $query->where('trang_thai', $filters['trang_thai']);
        }

        if (isset($filters['si_so_min'])) {
            $query->where('si_so', '>=', $filters['si_so_min']);
        }

        if (isset($filters['si_so_max'])) {
            $query->where('si_so', '<=', $filters['si_so_max']);
        }

        $direction = $filters['direction'] ?? 'desc';

        $query->orderBy('id', $direction);

        $perPage = (int) ($filters['perPage'] ?? 10);

        $lopHocs = $query
            ->paginate($perPage)
            ->appends($filters);

        return view('lop-hocs.index', [
            'lopHocs' => $lopHocs,
            'filters' => $filters,
            'direction' => $direction,
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lop-hocs.create'); // Trả về view tạo lớp học mới
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorelopHocRequest $request)
    {
        $validated = $request->validated(); // Lấy dữ liệu đã được xác thực từ request
        $validated['trang_thai'] = $request->boolean('trang_thai');
        LopHoc::create($validated); // Tạo lớp học mới trong cơ sở dữ liệu
        return redirect()->route('lop-hocs.index')->with('success', 'Lớp học đã được tạo thành công.'); // Chuyển hướng về danh sách lớp học với thông báo thành công
    }

    /**
     * Display the specified resource.
     */
    public function show(LopHoc $lopHoc)
    {
        return view('lop-hocs.show', compact('lopHoc')); // Trả về view hiển thị thông tin lớp học
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LopHoc $lopHoc)
    {
        return view('lop-hocs.edit', compact('lopHoc')); // Trả về view chỉnh sửa thông tin lớp học
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLopHocRequest $request, LopHoc $lopHoc)
    {
        $validated = $request->validated(); // Lấy dữ liệu đã được xác thực từ request
        $validated['trang_thai'] = $request->boolean('trang_thai');
        $lopHoc->update($validated); // Cập nhật thông tin lớp học trong cơ sở dữ liệu
        return redirect()->route('lop-hocs.index')->with('success', 'Lớp học đã được cập nhật thành công.'); // Chuyển hướng về danh sách lớp học với thông báo thành công
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LopHoc $lopHoc)
    {
        $lopHoc->delete();
        return redirect()->route('lop-hocs.index')->with('success', 'Lớp học đã được xóa thành công.'); // Chuyển hướng về danh sách lớp học với thông báo thành công
    }
}
