<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            $table->string('nim')->unique();
            $table->string('nama');
            $table->string('email')->nullable();
            $table->string('kelas')->nullable();

            $table->string('mysql_database')->unique();
            $table->string('mysql_username')->unique();
            $table->text('mysql_password');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};