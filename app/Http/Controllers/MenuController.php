<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:id,ten_hien_thi,thu_tu'],
            'direction' => ['nullable', 'in:asc,desc'],
            'perPage' => ['nullable', 'in:10,20,50'],
            'page' => ['nullable', 'integer', 'min:1'],
            'vi_tri' => ['nullable', 'in:sidebar,header'],
        ]);
        $query = Menu::query();
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('ten_hien_thi', 'like', '%'.$filters['search'].'%')
                    ->orWhere('slug', 'like', '%'.$filters['search'].'%')
                    ->orWhere('url', 'like', '%'.$filters['search'].'%');
            });
        }
        foreach (['trang_thai', 'vi_tri'] as $field) {
            if (isset($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }

        $direction = $filters['direction'] ?? 'asc';
        $perPage = (int) ($filters['perPage'] ?? 10);
        $menus = $query->orderBy($filters['sort'] ?? 'thu_tu', $direction)->orderBy('id')
            ->paginate($perPage)->withQueryString();

        return view('menus.index', [
            'menus' => $menus,
            'filters' => $filters,
            'direction' => $direction,
            'perPage' => $perPage,
            'perPageOptions' => [10, 20, 50],

        ]);
    }

    public function create(): View
    {
        return view('menus.create');
    }

    public function store(StoreMenuRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $validated['url'] = $validated['loai_lien_ket'] === 'page' ? '/pages/'.$validated['slug'] : $validated['url'];
        unset($validated['loai_lien_ket']);
        $validated['trang_thai'] = $request->boolean('trang_thai');

        Menu::create($validated);

        return redirect()
            ->route('menus.index')
            ->with('success', 'Thêm Menu thành công.');
    }

    public function show(Menu $menu): View
    {
        return view('menus.show', compact('menu'));
    }

    public function edit(Menu $menu): View
    {
        return view('menus.edit', compact('menu'));
    }

    public function update(
        UpdateMenuRequest $request,
        Menu $menu
    ): RedirectResponse {
        $validated = $request->validated();

        $validated['url'] = $validated['loai_lien_ket'] === 'page' ? '/pages/'.$validated['slug'] : $validated['url'];
        unset($validated['loai_lien_ket']);
        $validated['trang_thai'] = $request->boolean('trang_thai');

        $menu->update($validated);

        return redirect()
            ->route('menus.index')
            ->with('success', 'Cập nhật Menu thành công.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()
            ->route('menus.index')
            ->with('success', 'Xóa Menu thành công.');
    }
}
