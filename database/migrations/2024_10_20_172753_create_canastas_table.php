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
    Schema::create('canastas', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->decimal('precio', 10, 2);
        $table->text('descripcion')->nullable();
        $table->timestamps();
    });

    Schema::create('canasta_producto', function (Blueprint $table) {
        $table->id();
        $table->foreignId('canasta_id')->constrained()->onDelete('cascade');
        $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
        $table->integer('cantidad');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canastas');
    }
};
