<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; //Nhờ có Eloquent ORM để tương tác với cơ sở dữ liệu
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LopHoc extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lop_hocs'; // Tên bảng trong cơ sở dữ liệu

    protected $fillable = [
        'ten_lop',
        'ma_lop',
        'si_so',
        'giao_vien',
        'sdt_gv',
        'ghi_chu',
        'trang_thai',
    ];
    /**
     * Get the sinhViens for the LopHoc.
     */
    public function sinhViens(): HasMany
    {
        return $this->hasMany(SinhVien::class);
    }
}
