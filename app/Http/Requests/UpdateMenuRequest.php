<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Menu;

class UpdateMenuRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['loai_lien_ket' => $this->input('loai_lien_ket', 'existing')]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $menu = $this->route('menu');

        return [
            'ten_hien_thi' => 'required|string|max:255',

            'slug' => [
                'required',
                'string',
                'max:80',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::notIn($this->input('slug') === $menu->slug ? [] : ['dashboard', 'login', 'register', 'logout', 'menus', 'lop-hocs', 'sinh-viens', 'api', 'api-playground', 'pages', 'up']),
                Rule::unique('menus', 'slug')
                    ->ignore($menu->id),
            ],

            'loai_lien_ket' => ['required', Rule::in(['existing', 'page'])],
            'url' => ['exclude_unless:loai_lien_ket,existing', 'required', Rule::in(array_keys(Menu::EXISTING_PAGES))],
            'vi_tri' => 'required|in:sidebar,header',
            'thu_tu' => 'required|integer|min:0',
            'trang_thai' => 'nullable|boolean',
        ];
    }
}
