<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReunionTable extends Migration
{    /**
 * Run the migrations.
 *
 * @return void
 */
    public function up()
    {
        Schema::create('reuniones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('asunto');
            $table->enum('estado', ['En proceso', 'Programada','Cancelada','Aplazada']);
            $table->string('descripcion');
            $table->integer('participacion_minima');
            $table->string('lugar');
            $table->time('hora_final');
            $table->time('hora_inicial');
            $table->date('fecha');
            $table->enum('prioridad', ['baja', 'alta']);
            $table->integer('hastaNegociacion')->nullable();;
            $table->date('hastaRepetir')->nullable();;

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
Schema::dropIfExists('reuniones');
    }
}
