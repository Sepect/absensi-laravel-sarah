<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])
                ->default('pending')
                ->after('is_approved');
        });

        // Backfill the new status column from the existing boolean flag.
        DB::table('daily_reports')->where('is_approved', true)->update(['status' => 'disetujui']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
