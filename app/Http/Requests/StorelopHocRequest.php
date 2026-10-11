<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorelopHocRequest extends FormRequest
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
            'ten_lop' => 'required|string|max:255',
            'ma_lop' => 'required|string|max:255|unique:lop_hocs,ma_lop',
            'si_so' => 'required|integer|min:1',
            'giao_vien' => 'required|string|max:255',
            'sdt_gv' => 'nullable|string|max:15',
            'ghi_chu' => 'nullable|string',
            'trang_thai' => 'boolean',
        ];
    }
}
