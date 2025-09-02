<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('sex');
            $table->date('birth_date');
            $table->string('password');
            $table->timestamps();
            
            $table->unique(['name', 'surname', 'birth_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};