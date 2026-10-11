<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SinhVien extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'sinh_viens'; // Tên bảng trong cơ sở dữ liệu
    protected $fillable = [
        'ma_sinh_vien',
        'ho_ten',
        'ngay_sinh',
        'gioi_tinh',
        'email',
        'so_dien_thoai',
        'dia_chi',
        'lop_hoc_id',
        'trang_thai',
    ];
    /**
     * Get the lopHoc that owns the SinhVien.
     */
    public function lopHoc(): BelongsTo
    {
        return $this->belongsTo(LopHoc::class)->withTrashed();
    }
}
