<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLopHocRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'lecturer'], true);
    }

    public function rules(): array
    {
        $rules = [
            'ten_lop' => ['required', 'string', 'max:255'],
            'ma_lop' => ['required', 'string', 'max:20', Rule::unique('lop_hocs', 'ma_lop')->ignore($this->route('lop_hoc'))],
            'si_so' => ['required', 'integer', 'min:0', 'max:1000'],
            'giao_vien' => ['required', 'string', 'max:255'],
            'sdt_gv' => ['nullable', 'string', 'max:15'],
            'ghi_chu' => ['nullable', 'string', 'max:500'],
            'trang_thai' => ['sometimes', 'boolean'],
        ];
        if ($this->is('api/*') && $this->isMethod('patch')) {
            foreach ($rules as $field => $fieldRules) {
                $rules[$field] = ['sometimes', ...$fieldRules];
            }
        }

        return $rules;
    }
}
