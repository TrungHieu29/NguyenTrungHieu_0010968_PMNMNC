<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SinhVienFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ma_sinh_vien' => fake()->unique()->bothify('SV####'),
            'ho_ten' => fake()->name(),

            'ngay_sinh' => fake()
                ->dateTimeBetween('-24 years', '-18 years')
                ->format('Y-m-d'),

            'gioi_tinh' => fake()->randomElement([
                'Nam',
                'Nữ'
            ]),

            'email' => fake()->unique()->safeEmail(),
            'so_dien_thoai' => fake()->numerify('09########'),
            'dia_chi' => fake()->randomElement([
                'Hà Nội',
                'Hải Phòng',
                'Nam Định',
                'Ninh Bình',
                'Bắc Ninh',
                'Hưng Yên',
                'Thái Bình'
            ]),

            'trang_thai' => fake()->boolean(90),
        ];
    }
}