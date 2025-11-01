<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('medidas', function (Blueprint $table) {
            if (!Schema::hasColumn('medidas', 'active')) {
                $table->boolean('active')->default(1)->after('nombre');
            }
        });
    }

    public function down()
    {
        Schema::table('medidas', function (Blueprint $table) {
            if (Schema::hasColumn('medidas', 'active')) {
                $table->dropColumn('active');
            }
        });
    }
};