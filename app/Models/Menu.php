<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'ten_hien_thi',
        'slug',
        'url',
        'vi_tri',
        'thu_tu',
        'trang_thai',
    ];

    public const EXISTING_PAGES = [
        '/dashboard' => 'Dashboard',
        '/lop-hocs' => 'Quản lý lớp học',
        '/sinh-viens' => 'Quản lý sinh viên',
        '/menus' => 'Quản lý Menu',
        '/api-playground' => 'API Playground',
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

        if ($this->url === '/menus' || $this->slug === 'menus') {
            return $user->role === 'admin';
        }

        return array_key_exists($this->url, self::EXISTING_PAGES) || $this->isPage();
    }
}
