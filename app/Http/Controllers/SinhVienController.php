<?php

namespace App\Http\Controllers;

use App\Models\SinhVien;
use Illuminate\Http\Request;
use App\Models\LopHoc;
use App\Http\Requests\StoreSinhVienRequest;
use App\Http\Requests\UpdateSinhVienRequest;

class SinhVienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = \Illuminate\Support\Facades\Validator::make($request->query(), [
            'search' => 'nullable|string|max:100',
            'lop_hoc_id' => 'nullable|integer|min:1',
            'trang_thai' => 'nullable|in:0,1',
            'direction' => 'nullable|in:asc,desc',
        ])->valid();
        $query = SinhVien::with('lopHoc');

        if (! empty($filters['search'])) {
            $search = $filters['search'];

            $query->where(function ($q) use ($search) { //function ($q) use ($search) { // Sử dụng closure để nhóm các điều kiện tìm kiếm
                $q->where('ma_sinh_vien', 'like', "%{$search}%")
                ->orWhere('ho_ten', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }
        if (isset($filters['lop_hoc_id'])) {
            $query->where('lop_hoc_id', $filters['lop_hoc_id']);
        }
        if (isset($filters['trang_thai'])) {
            $query->where('trang_thai', $filters['trang_thai']);
        }
        $direction = $filters['direction'] ?? 'desc';
        $query->orderBy('id', $direction);   
        $sinhViens = $query
            ->paginate(10)
            ->appends($filters);
        $lopHocs = LopHoc::where('trang_thai', true)->get();
        return view('sinh-viens.index', compact('sinhViens', 'lopHocs'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lopHocs = LopHoc::where('trang_thai', true)->get(); // Lấy tất cả các lớp học đang hoạt động từ cơ sở dữ liệu
        return view('sinh-viens.create', compact('lopHocs')); // Trả
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSinhVienRequest $request)
    {
        $validated = $request->validated(); 
        $validated['trang_thai'] = $request->boolean('trang_thai');
        SinhVien::create($validated); // Tạo sinh viên mới trong cơ sở dữ liệu
        return redirect()->route('sinh-viens.index')->with('success', 'Sinh viên đã được tạo thành công.'); // Chuyển hướng về danh sách sinh viên với thông báo thành công
    }

    /**
     * Display the specified resource.
     */
    public function show(SinhVien $sinhVien)
    {
        return view('sinh-viens.show', compact('sinhVien')); // Trả về view hiển thị thông tin sinh viên
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SinhVien $sinhVien)
    {

        $lopHocs = LopHoc::where('trang_thai', true)->orWhereKey($sinhVien->lop_hoc_id)->get();

        return view('sinh-viens.edit',compact('sinhVien', 'lopHocs')
    );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSinhVienRequest $request, SinhVien $sinhVien)
    {
        $validated = $request->validated();

        $validated['trang_thai'] = $request->boolean('trang_thai');

        $sinhVien->update($validated);

        return redirect()->route('sinh-viens.index')->with('success', 'Cập nhật sinh viên thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SinhVien $sinhVien)
    {
        $sinhVien->delete();

        return redirect()
            ->route('sinh-viens.index')
            ->with('success', 'Xóa sinh viên thành công.');
    }
}
