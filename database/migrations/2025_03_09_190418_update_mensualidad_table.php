<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMensualidadTable extends Migration
{
    public function up()
    {
        Schema::table('mensualidad', function (Blueprint $table) {
            $table->string('nombreMensualidad')->nullable();
            $table->string('placaMensualidad')->nullable();
            $table->string('telefonoMensualidad')->nullable();
            $table->date('entradaMensualidad')->nullable();
        });
    }

    public function down()
    {
        Schema::table('mensualidad', function (Blueprint $table) {
            $table->dropColumn('nombreMensualidad');
            $table->dropColumn('placaMensualidad');
            $table->dropColumn('telefonoMensualidad');
            $table->dropColumn('entradaMensualidad');
        });
    }
}