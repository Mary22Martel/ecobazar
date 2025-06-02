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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('mercadopago_payment_id')->nullable()->after('total');
            $table->timestamp('paid_at')->nullable()->after('mercadopago_payment_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['mercadopago_payment_id', 'paid_at']);
        });
    }

};
