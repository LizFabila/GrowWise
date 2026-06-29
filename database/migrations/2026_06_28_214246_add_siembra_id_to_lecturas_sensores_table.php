<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('lecturas_sensores', 'siembra_id')) {
            Schema::table('lecturas_sensores', function (Blueprint $table) {
                $table->unsignedBigInteger('siembra_id')->nullable()->after('id');
                // Si quieres agregar también cultivo_id, puedes hacerlo
                $table->foreign('siembra_id')->references('id')->on('siembras')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('lecturas_sensores', 'siembra_id')) {
            Schema::table('lecturas_sensores', function (Blueprint $table) {
                $table->dropForeign(['siembra_id']);
                $table->dropColumn('siembra_id');
            });
        }
    }
};
