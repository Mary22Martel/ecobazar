<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMercadoIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Añadimos el campo mercado_id como FK nullable
            $table->foreignId('mercado_id')
                  ->nullable()
                  ->after('role')                // o la columna que prefieras
                  ->constrained('mercados')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar la FK y la columna
            $table->dropConstrainedForeignId('mercado_id');
        });
    }
}
