<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSinhVienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sinhVien = $this->route('sinh_vien');

        return [
            'ma_sinh_vien' => [
                'required',
                'string',
                'max:20',
                Rule::unique('sinh_viens', 'ma_sinh_vien')
                    ->ignore($sinhVien->id),
            ],

            'ho_ten' => 'required|string|max:255',
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|in:Nam,Nữ',

            'email' => [
                'required',
                'email',
                Rule::unique('sinh_viens', 'email')
                    ->ignore($sinhVien->id),
            ],

            'so_dien_thoai' => 'nullable|string|max:20',
            'dia_chi' => 'nullable|string|max:255',
            'lop_hoc_id' => ['required', \Illuminate\Validation\Rule::exists('lop_hocs', 'id')->whereNull('deleted_at')],
            'trang_thai' => 'nullable|boolean',
        ];
    }
}
