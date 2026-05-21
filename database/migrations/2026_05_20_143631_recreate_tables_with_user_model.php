<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * New schema based on updated PRD:
     * - Single 'users' table for authentication (email + role)
     * - 'admin_profiles' for admin details
     * - 'intern_profiles' for intern details
     */
    public function up(): void
    {
        // 1. Users - Single authentication table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'intern'])->default('intern');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Admin Profiles - Admin details
        Schema::create('admin_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama');
            $table->string('no_telp')->nullable();
            $table->timestamps();
        });

        // 3. Intern Profiles - Intern details (NIM, school, supervisor, etc.)
        Schema::create('intern_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nim_nis')->unique();
            $table->string('nama_lengkap');
            $table->string('asal_sekolah_kampus');
            $table->string('no_telp')->nullable();
            $table->string('nama_pembimbing');
            $table->text('alamat')->nullable();
            $table->string('foto_peserta')->nullable();
            $table->string('kontak_darurat')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['aktif', 'nonaktif', 'selesai'])->default('aktif');
            $table->timestamps();
        });

        // 4. Office Settings - Office location and schedule
        Schema::create('office_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('radius_meters')->default(100);
            $table->time('waktu_masuk')->default('08:00:00');
            $table->time('waktu_pulang')->default('17:00:00');
            $table->integer('qr_expiry_minutes')->default(5);
            $table->timestamps();
        });

        // 5. Attendances - Attendance transactions
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('intern_profiles')->onDelete('cascade');
            $table->date('attendance_date');
            $table->dateTime('scan_in_time')->nullable();
            $table->dateTime('scan_out_time')->nullable();
            $table->decimal('distance_in_meters', 8, 2)->nullable();
            $table->enum('status', ['hadir', 'terlambat', 'alpha', 'izin'])->default('alpha');
            $table->string('qr_validated_at')->nullable();
            $table->string('qr_type')->nullable(); // 'in' or 'out'
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['intern_id', 'attendance_date']);
            $table->index(['attendance_date', 'status']);
        });

        // 6. Daily Reports - Daily journals
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('intern_profiles')->onDelete('cascade');
            $table->date('report_date');
            $table->text('description');
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('admin_profiles')->onDelete('set null');
            $table->timestamps();

            $table->unique(['intern_id', 'report_date']);
            $table->index(['report_date']);
        });

        // 7. Izin Requests - Leave/permission requests
        Schema::create('izin_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intern_id')->constrained('intern_profiles')->onDelete('cascade');
            $table->enum('jenis', ['sakit', 'cuti', 'urgent'])->default('sakit');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->text('alasan');
            $table->string('bukti')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('admin_profiles')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        // 8. Personal Access Tokens - For Sanctum
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('izin_requests');
        Schema::dropIfExists('daily_reports');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('office_settings');
        Schema::dropIfExists('intern_profiles');
        Schema::dropIfExists('admin_profiles');
        Schema::dropIfExists('users');
    }
};