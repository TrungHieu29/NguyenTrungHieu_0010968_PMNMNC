<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['trang_thai' => 'boolean', 'thu_tu' => 'integer'];
    }

    protected $fillable = [
        'ten_hien_thi',
        'slug',
        'url',
        'vi_tri',
        'thu_tu',
        'trang_thai',
    ];

    public const EXISTING_PAGES = [
        '/dashboard' => 'Tổng quan',
        '/lop-hocs' => 'Quản lý lớp học',
        '/sinh-viens' => 'Quản lý sinh viên',
        '/menus' => 'Quản lý Menu',
        '/api-playground' => 'API Playground',
        '/users' => 'Quản lý User',
        '/profile' => 'Hồ sơ cá nhân',
    ];

    public function isPage(): bool
    {
        return $this->url === '/pages/'.$this->slug;
    }

    public function isVisibleTo(User $user): bool
    {
        if (! $this->trang_thai) {
            return false;
        }

        if (in_array($this->url, ['/menus', '/users'], true) || in_array($this->slug, ['menus', 'users'], true)) {
            return $user->role === 'admin';
        }

        return array_key_exists($this->url, self::EXISTING_PAGES) || $this->isPage();
    }
}
