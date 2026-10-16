<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LopHocFactory extends Factory
{
    public function definition(): array
    {
        $maLop = strtoupper(fake()->unique()->bothify('PM??####'));

        return [
            'ten_lop' => 'Lớp '.$maLop,
            'ma_lop' => $maLop,
            'si_so' => fake()->numberBetween(25, 45),
            'giao_vien' => fake()->name(),
            'sdt_gv' => fake()->numerify('09########'),
            'ghi_chu' => fake()->sentence(),
            'trang_thai' => fake()->boolean(90),
        ];
    }
}
