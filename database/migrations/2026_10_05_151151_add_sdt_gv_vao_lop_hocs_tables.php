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
        Schema::table('lop_hocs', function (Blueprint $table) {
            $table->string('sdt_gv')->nullable()->after('giao_vien');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lop_hocs', function (Blueprint $table) {
            $table->dropColumn('sdt_gv');
        });
    }
};
