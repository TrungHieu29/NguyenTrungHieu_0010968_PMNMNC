<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSinhVienRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ma_sinh_vien' => 'required|string|max:20|unique:sinh_viens,ma_sinh_vien',
            'ho_ten' => 'required|string|max:255',
            'ngay_sinh' => 'nullable|date',
            'gioi_tinh' => 'nullable|in:Nam,Nữ',
            'email' => 'required|email|unique:sinh_viens,email',
            'so_dien_thoai' => 'nullable|string|max:20',
            'dia_chi' => 'nullable|string|max:255',

            'lop_hoc_id' => ['required', \Illuminate\Validation\Rule::exists('lop_hocs', 'id')->whereNull('deleted_at')],

            'trang_thai' => 'nullable|boolean',
        ];
    }
}
