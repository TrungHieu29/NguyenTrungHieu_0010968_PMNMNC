<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('Không tạo tài khoản admin demo trên production.');
        }

        $user = User::where('username', 'admin')->first();
        if ($user && $user->role !== 'admin') {
            throw new RuntimeException('Username admin đã được dùng bởi tài khoản khác.');
        }
        $user ??= User::firstOrNew(['email' => 'admin@example.test']);
        $user->name = 'Quản trị viên demo';
        $user->username = 'admin';
        $user->role = 'admin';
        $user->password = Hash::make('123456');
        $user->save();
    }
}
