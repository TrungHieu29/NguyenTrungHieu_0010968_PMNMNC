<?php

namespace Database\Seeders;

use App\Models\LopHoc;
use App\Models\SinhVien;
use Illuminate\Database\Seeder;

class SinhVienSeeder extends Seeder
{
    public function run(): void
    {
        if (SinhVien::exists()) {
            return;
        }
        $lopHocIds = LopHoc::pluck('id');

        if ($lopHocIds->isEmpty()) {
            return;
        }

        SinhVien::factory()
            ->count(50)
            ->state(function () use ($lopHocIds) {
                return [
                    'lop_hoc_id' => $lopHocIds->random(),
                ];
            })
            ->create();
    }
}
