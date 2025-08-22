<?php
// CREAR NUEVA MIGRACIÓN: add_mercadopago_fields_to_orders_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMercadopagoFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Campos para el desglose detallado
            $table->decimal('subtotal_productos', 10, 2)->nullable()->after('total')->comment('Precio de productos sin comisiones');
            $table->decimal('costo_envio', 10, 2)->nullable()->after('subtotal_productos')->comment('Costo del envío');
            $table->decimal('monto_neto', 10, 2)->nullable()->after('costo_envio')->comment('Total neto que debe llegar (productos + envío)');
            $table->decimal('comision_mercadopago', 10, 2)->nullable()->after('monto_neto')->comment('Comisión que se lleva MercadoPago');
            
            // El campo 'total' existente se mantiene como el monto total que paga el cliente
            $table->decimal('total', 10, 2)->comment('Total que paga el cliente (incluye comisión MP)')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['subtotal_productos', 'costo_envio', 'monto_neto', 'comision_mercadopago']);
        });
    }
}