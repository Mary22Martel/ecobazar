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
        Schema::table('zone_user', function (Blueprint $table) {
            // Verificar si las columnas existen antes de agregarlas
            if (!Schema::hasColumn('zone_user', 'fecha_asignacion')) {
                $table->date('fecha_asignacion')->default(now());
            }
            
            if (!Schema::hasColumn('zone_user', 'activa')) {
                $table->boolean('activa')->default(true);
            }
            
            if (!Schema::hasColumn('zone_user', 'created_at')) {
                $table->timestamps();
            }
        });
        
        // Agregar índices si no existen
        try {
            Schema::table('zone_user', function (Blueprint $table) {
                $table->index(['user_id', 'fecha_asignacion'], 'zone_user_user_fecha_index');
                $table->index(['zone_id', 'fecha_asignacion'], 'zone_user_zone_fecha_index');
            });
        } catch (\Exception $e) {
            // Los índices ya pueden existir, ignorar
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zone_user', function (Blueprint $table) {
            $table->dropColumn(['fecha_asignacion', 'activa']);
            $table->dropTimestamps();
        });
    }
};