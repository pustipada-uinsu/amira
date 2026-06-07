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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->string('no_wa', 20)->nullable();
            $table->string('jabatan')->nullable();
            $table->integer('institusi_id')->nullable();
            $table->text('alamat_kantor')->nullable();
            $table->string('nama_bank', 64)->nullable();
            $table->string('no_rekening', 32)->nullable();
            $table->string('ukuran_baju')->nullable();
            $table->boolean('is_smoking')->default(false);
            $table->string('avatar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
