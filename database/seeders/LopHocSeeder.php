<?php

namespace Database\Seeders;

use App\Models\LopHoc;
use Illuminate\Database\Seeder;

class LopHocSeeder extends Seeder
{
    public function run(): void
    {
        if (LopHoc::exists()) {
            return;
        }
        LopHoc::factory()->count(10)->create();
    }
}
