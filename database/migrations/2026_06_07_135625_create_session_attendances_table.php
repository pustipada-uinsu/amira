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
        Schema::create('session_attendances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_session_id')->nullable()->index('session_attendances_event_session_id_foreign');
            $table->unsignedBigInteger('user_id')->index('session_attendances_user_id_foreign');
            $table->integer('event_id');
            $table->date('attendance_date')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->string('method')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_attendances');
    }
};
