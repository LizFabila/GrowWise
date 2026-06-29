<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('lecturas_sensores')) {
            Schema::create('lecturas_sensores', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('sensor_id');
                $table->decimal('valor', 8, 2);
                $table->timestamp('created_at')->useCurrent();
                // Si necesitas más columnas, agrégalas aquí
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('lecturas_sensores')) {
            Schema::dropIfExists('lecturas_sensores');
        }
    }
};
