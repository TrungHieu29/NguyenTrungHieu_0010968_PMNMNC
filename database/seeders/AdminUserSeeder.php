<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }
        foreach ([
            ['admin', 'Quản trị viên', 'admin', 'Admin@123456'],
            ['lecturer', 'Giảng viên 1', 'lecturer', 'Lecturer@123456'],
            ['lecturer2', 'Giảng viên 2', 'lecturer', 'Lecturer@123456'],
        ] as [$username, $name, $role, $password]) {
            if (User::where('username', $username)->orWhere('email', $username.'@example.test')->exists()) {
                continue;
            }
            $user = new User(['username' => $username, 'name' => $name, 'email' => $username.'@example.test', 'password' => $password]);
            $user->role = $role;
            $user->is_active = true;
            $user->save();
        }
    }
}
