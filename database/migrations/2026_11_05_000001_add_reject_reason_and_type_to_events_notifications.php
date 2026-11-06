<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('reject_reason')->nullable()->after('move_reason');
        });

        Schema::table('room_notifications', function (Blueprint $table) {
            // pindah | disetujui | ditolak
            $table->string('type', 20)->default('pindah')->after('event_id');
        });
    }

    public function down(): void
    {
        Schema::table('room_notifications', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('reject_reason');
        });
    }
};
