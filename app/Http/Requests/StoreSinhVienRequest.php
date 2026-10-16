<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSinhVienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'lecturer'], true);
    }

    public function rules(): array
    {
        $rules = [
            'ma_sinh_vien' => ['required', 'string', 'max:20', Rule::unique('sinh_viens', 'ma_sinh_vien')],
            'ho_ten' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('sinh_viens', 'email')],
            'ngay_sinh' => ['nullable', 'date', 'before_or_equal:today'],
            'gioi_tinh' => ['nullable', Rule::in(['Nam', 'Nữ'])],
            'so_dien_thoai' => ['nullable', 'string', 'max:20'],
            'dia_chi' => ['nullable', 'string', 'max:255'],
            'lop_hoc_id' => ['required', 'integer', Rule::exists('lop_hocs', 'id')->whereNull('deleted_at')],
            'trang_thai' => ['sometimes', 'boolean'],
        ];

        return $rules;
    }
}
