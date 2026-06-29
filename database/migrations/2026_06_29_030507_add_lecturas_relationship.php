<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Esta migración solo asegura que la relación exista en el modelo
        // No se necesita modificar la base de datos
    }

    public function down()
    {
        //
    }
};
