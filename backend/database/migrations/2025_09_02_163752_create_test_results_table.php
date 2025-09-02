<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('value');
            $table->text('reference')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};