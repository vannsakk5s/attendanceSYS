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
        if (!Schema::hasColumn('classes', 'share_token')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->string('share_token', 64)->nullable()->unique()->after('subject_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('classes', 'share_token')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->dropColumn('share_token');
            });
        }
    }
};
