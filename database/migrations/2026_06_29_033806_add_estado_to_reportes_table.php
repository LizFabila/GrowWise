<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reportes', function (Blueprint $table) {
            if (!Schema::hasColumn('reportes', 'estado')) {
                $table->string('estado')->default('Generado'); // 👈 Le quitamos el ->after('ruta')
            }
        });
    }

    public function down()
    {
        Schema::table('reportes', function (Blueprint $table) {
            if (Schema::hasColumn('reportes', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};
