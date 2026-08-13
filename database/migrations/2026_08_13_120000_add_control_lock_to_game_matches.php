<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_matches', function (Blueprint $table) {
            // Session ID device yang sedang mengendalikan scoreboard (admin control).
            // Null = bebas. Hanya 1 device yang boleh kontrol per match.
            $table->string('control_session_id')->nullable()->after('finished_at');
            // Waktu heartbeat terakhir. Kalau lewat LOCK_TIMEOUT detik, lock dianggap lepas otomatis.
            $table->timestamp('control_heartbeat')->nullable()->after('control_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('game_matches', function (Blueprint $table) {
            $table->dropColumn(['control_session_id', 'control_heartbeat']);
        });
    }
};
