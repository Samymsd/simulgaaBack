<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserReunionTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_reunion', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('asistencia', ['si', 'no']);
            $table->integer('user_id')->unsigned();
            $table->integer('reunion_id')->unsigned();
            $table->enum('tipo_participante',['participante', 'creador']);


            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('reunion_id')->references('id')->on('reuniones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personas_locales');
    }
}
