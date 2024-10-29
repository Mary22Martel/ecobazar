<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Relación con el usuario que realiza la orden
            $table->string('nombre');
            $table->string('apellido');
            $table->string('empresa')->nullable();
            $table->string('email');
            $table->string('telefono');
            $table->string('delivery');
            $table->string('direccion')->nullable();
            $table->string('distrito')->nullable();
            $table->string('pago');
            $table->decimal('total', 10, 2); // Total de la orden
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
