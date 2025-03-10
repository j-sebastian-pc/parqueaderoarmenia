<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHoraToMotosTable extends Migration
{
    public function up()
    {
        Schema::table('motos', function (Blueprint $table) {
            $table->integer('hora')->nullable();
        });
    }

    public function down()
    {
        Schema::table('motos', function (Blueprint $table) {
            $table->dropColumn('hora');
        });
    }
}