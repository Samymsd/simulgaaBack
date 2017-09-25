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
        // $this->call(UsersTableSeeder::class);
        DB::table('users')->insert([
            'nombres' => "Administrador",
            'apellidos' => "Absoluto",
            'telefono' => '3158025140',
            'rol' => "Admin",
            'entidad_id' =>1,
            'email' => 'admin@unicesar.edu.co',
            'password' => bcrypt('123456'),
        ]);

        DB::table('users')->insert([
            'nombres' => "Secretaria",
            'apellidos' => "Absoluto",
            'telefono' => '3158025140',
            'rol' => "Secretaria",
            'entidad_id' =>1,
            'email' => 'secretaria@unicesar.edu.co',
            'password' => bcrypt('123456'),
        ]);
    }
}
