<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Menu::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('ten_hien_thi', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhere('url', 'like', "%{$search}%");
            });
        }

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $menus = $query
            ->orderBy('thu_tu', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('menus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMenuRequest $request)
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

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        return view('menus.show', compact('menu'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        return view('menus.edit', compact('menu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateMenuRequest $request,
        Menu $menu
    ) {
        $validated = $request->validated();

        $validated['url'] = $validated['loai_lien_ket'] === 'page' ? '/pages/'.$validated['slug'] : $validated['url'];
        unset($validated['loai_lien_ket']);
        $validated['trang_thai'] = $request->boolean('trang_thai');

        $menu->update($validated);

        return redirect()
            ->route('menus.index')
            ->with('success', 'Cập nhật Menu thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return redirect()
            ->route('menus.index')
            ->with('success', 'Xóa Menu thành công.');
    }
}
