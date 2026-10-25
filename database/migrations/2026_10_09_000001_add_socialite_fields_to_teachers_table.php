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
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('password_hash')->nullable()->change();
            $table->string('provider')->nullable()->after('password_hash');
            $table->string('provider_id')->nullable()->after('provider');
            $table->string('avatar')->nullable()->after('provider_id');
            $table->string('telegram_username')->nullable()->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('password_hash')->nullable(false)->change();
            $table->dropColumn([
                'provider',
                'provider_id',
                'avatar',
                'telegram_username',
            ]);
        });
    }
};
