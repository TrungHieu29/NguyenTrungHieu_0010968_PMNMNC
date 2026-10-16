<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
            $table->string('role', 20)->default('lecturer')->change();
        });
        DB::table('users')->where('role', 'staff')->update(['role' => 'lecturer']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'lecturer')->update(['role' => 'staff']);
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->string('role', 20)->default('staff')->change();
        });
    }
};
