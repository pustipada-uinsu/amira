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
        Schema::table('session_attendances', function (Blueprint $table) {
            $table->foreign(['event_session_id'])->references(['id'])->on('event_sessions')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session_attendances', function (Blueprint $table) {
            $table->dropForeign('session_attendances_event_session_id_foreign');
            $table->dropForeign('session_attendances_user_id_foreign');
        });
    }
};
