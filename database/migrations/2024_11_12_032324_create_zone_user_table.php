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
        Schema::create('zone_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('zone_id')->constrained()->onDelete('cascade');
            $table->date('fecha_asignacion')->default(now());
            $table->boolean('activa')->default(true);
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['user_id', 'fecha_asignacion']);
            $table->index(['zone_id', 'fecha_asignacion']);
            
            // Evitar duplicados por fecha
            $table->unique(['user_id', 'zone_id', 'fecha_asignacion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone_user');
    }
};