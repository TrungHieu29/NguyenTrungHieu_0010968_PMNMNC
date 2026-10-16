<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['dashboard', 'Tổng quan', '/dashboard', 1],
            ['lop-hocs', 'Quản lý lớp học', '/lop-hocs', 2],
            ['sinh-viens', 'Quản lý sinh viên', '/sinh-viens', 3],
            ['menus', 'Quản lý Menu', '/menus', 4],
            ['api-playground', 'API Playground', '/api-playground', 5],
            ['users', 'Quản lý User', '/users', 6],
            ['profile', 'Hồ sơ cá nhân', '/profile', 7],
        ] as [$slug, $name, $url, $order]) {
            Menu::firstOrCreate(['slug' => $slug], [
                'ten_hien_thi' => $name,
                'url' => $url,
                'vi_tri' => 'sidebar',
                'thu_tu' => $order,
                'trang_thai' => true,
            ]);
        }
    }
}
