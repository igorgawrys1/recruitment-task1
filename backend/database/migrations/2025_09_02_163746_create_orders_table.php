<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->index('patient_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};