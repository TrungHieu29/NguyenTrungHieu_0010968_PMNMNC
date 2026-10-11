<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LopHoc;

class LopHocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (LopHoc::exists()) {
            return;
        }
        LopHoc::factory()->count(10)->create(); // Tạo 10 bản ghi lớp học ngẫu nhiên
    }
}
