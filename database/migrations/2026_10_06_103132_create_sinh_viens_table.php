<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sinh_viens', function (Blueprint $table) {
            $table->id();

            $table->string('ma_sinh_vien')->unique();
            $table->string('ho_ten');
            $table->date('ngay_sinh')->nullable();
            $table->string('gioi_tinh', 10)->nullable();
            $table->string('email')->unique();
            $table->string('so_dien_thoai', 20)->nullable();
            $table->string('dia_chi')->nullable();

            $table->foreignId('lop_hoc_id')
                ->constrained('lop_hocs')
                ->restrictOnDelete();

            $table->boolean('trang_thai')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinh_viens');
    }
};
