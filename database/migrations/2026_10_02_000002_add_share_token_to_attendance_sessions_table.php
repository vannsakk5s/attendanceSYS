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
        if (!Schema::hasColumn('attendance_sessions', 'share_token')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                $table->string('share_token', 64)->nullable()->unique()->after('end_time');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('attendance_sessions', 'share_token')) {
            Schema::table('attendance_sessions', function (Blueprint $table) {
                $table->dropColumn('share_token');
            });
        }
    }
};
