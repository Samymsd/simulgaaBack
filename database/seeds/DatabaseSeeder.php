<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('entidades')->insert([
            'nombre' => "Comité Académico",
        ]);

        DB::table('roles')->insert([
            'nombre_rol' => "Administrador",
        ]);
        DB::table('roles')->insert([
            'nombre_rol' => "Profesor",
        ]);
        DB::table('roles')->insert([
            'nombre_rol' => "Secretaria",
        ]);
        // $this->call(UsersTableSeeder::class);
        DB::table('users')->insert([
            'nombres' => "Sandra ",
            'apellidos' => "Sajonero David",
            'telefono' => '3158025140',
            'rol_id' => 1,
            'entidad_id' =>1,
            'email' => 'samy@unicesar.edu.co',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'nombres' => "Carlos amaya",
            'apellidos' => "Absoluto",
            'telefono' => '3158025140',
            'rol_id' => 2,
            'entidad_id' =>1,
            'email' => 'carlos@unicesar.edu.co',
            'password' => bcrypt('123456'),
        ]);
    }
}
