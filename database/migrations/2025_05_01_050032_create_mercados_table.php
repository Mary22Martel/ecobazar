<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMercadosTable extends Migration
{
    public function up()
    {
        Schema::create('mercados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('zona')->nullable(); // p.ej. "Amarilis"
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mercados');
    }
}


