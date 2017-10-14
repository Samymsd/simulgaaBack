<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('email',70);
            $table->string('telefono',10);
            $table->string('password');
            $table->integer('rol_id')->unsigned();
            $table->integer('entidad_id')->unsigned();
            $table->enum('estado', ['on', 'off']);
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('entidad_id')->references('id')->on('entidades');
            $table->foreign('rol_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
