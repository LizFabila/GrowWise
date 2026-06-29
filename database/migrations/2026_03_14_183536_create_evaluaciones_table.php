<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('evaluaciones')) {
            Schema::create('evaluaciones', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siembra_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->date('fecha_evaluacion');
                $table->decimal('rendimiento', 3, 1);
                $table->tinyInteger('eficiencia')->unsigned()->nullable();
                $table->text('observaciones')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('evaluaciones');
    }
};
