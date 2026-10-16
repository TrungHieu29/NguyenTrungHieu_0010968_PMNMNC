<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LopHoc extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return ['trang_thai' => 'boolean', 'si_so' => 'integer'];
    }

    protected $table = 'lop_hocs';

    protected $fillable = [
        'ten_lop',
        'ma_lop',
        'si_so',
        'giao_vien',
        'sdt_gv',
        'ghi_chu',
        'trang_thai',
    ];

    public function sinhViens(): HasMany
    {
        return $this->hasMany(SinhVien::class);
    }
}
