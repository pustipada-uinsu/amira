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
        Schema::create('institusi', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_institusi')->nullable();
            $table->string('nama_institusi')->nullable();
            $table->integer('id_zona')->nullable();
            $table->string('nama_zona')->nullable();
            $table->dateTime('creted_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institusi');
    }
};
