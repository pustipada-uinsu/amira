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
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('banner')->nullable();
            $table->string('flyer')->nullable();
            $table->boolean('public')->default(true);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->dateTime('registration_start')->nullable();
            $table->dateTime('registration_end')->nullable();
            $table->unsignedBigInteger('venue_id')->nullable()->index('events_venue_id_foreign');
            $table->string('custom_location')->nullable();
            $table->unsignedBigInteger('created_by')->index('events_created_by_foreign');
            $table->string('status')->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
