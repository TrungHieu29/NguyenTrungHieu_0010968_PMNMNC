<?php

namespace App\Http\Controllers;

use App\Models\Menu;

class MenuPageController extends Controller
{
    public function show(string $slug)
    {
        $menu = Menu::where('slug', $slug)
            ->where('url', '/pages/'.$slug)
            ->where('trang_thai', true)
            ->firstOrFail();

        abort_unless($menu->isVisibleTo(request()->user()), 403);

        return view('menus.page', compact('menu'));
    }
}
