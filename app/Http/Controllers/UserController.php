<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


use App\Entidades\Respuesta;

class UserController extends Controller
{
    public function store(Request $request)
    {

        $respuesta = new Respuesta();
      //  echo "Entroooooooooooooooo";
       // User::create($request->all());
        $datos= request()->all();
        if(!empty($datos["nombres"] ) &&
            !empty($datos["apellidos"]) &&
            !empty($datos["email"] )&&
            !empty($datos["password"])){

            $usuario = User::where('email', $datos["email"])->first();

            if(count($usuario) == 0){

                $datos["password"] = Hash::make($datos["password"]);
                $usuario= new User($datos);

                if($usuario->save()){
                    $respuesta->error = false;
                    $respuesta->mensaje = "Datos almacenados existosamente";
                    $respuesta->datos = $usuario;
                }
                else{
                    $respuesta->error = true;
                    $respuesta->mensaje = "No se pudieron almacenar los datos, intente nuevamente";
                }
            }else{

                $respuesta->error = true;
                $respuesta->mensaje = "Este Email ya existe";
            }



        }else{
            $respuesta->error = true;
            $respuesta->mensaje = "Faltan campos por llenar";
        }


        return response()->json($respuesta);


      //  return "Entro";
        //return ['created' => true];
    }

    public function update(Request $request, $id)
    {

        $respuesta = new Respuesta();
        $datos = $request->all();

        if(!empty($datos["nombres"] ) &&
            !empty($datos["apellidos"]) &&
            !empty($datos["email"] )){

            $user = User::find($id);

            if($user){
                $user->update($datos);
                $respuesta->error = false;
                $respuesta->mensaje = "Datos actualizados existosamente";
                $respuesta->datos = $user;
            }else{
                $respuesta->error = true;
                $respuesta->mensaje = "Usuario No encontrado";
            }

        }else{
            $respuesta->error = true;
            $respuesta->mensaje = "Faltan campos por llenar";
        }

        return response()->json($respuesta);
    }

    public function show($id)
    {
        $respuesta = new Respuesta();
        $user = User::find($id);
        if($user){
            $respuesta->error = false;
            $respuesta->mensaje = "Usuario encontrado";
            $respuesta->datos = $user;
        }else{
            $respuesta->error = true;
            $respuesta->mensaje = "Usuario No encontrado";
        }
        return response()->json($respuesta);
    }

    public function index()
    {
        $respuesta = new Respuesta();
        $users = User::all();
        if($users){
            $respuesta->error = false;
            $respuesta->mensaje = "Usuarios encontrados";
            $respuesta->datos = $users;
        }else{
            $respuesta->error = true;
            $respuesta->mensaje = "No hay usuario registrados";
        }

        return response()->json($respuesta);



    }

    public function destroy($id)
    {
        $respuesta = new Respuesta();
        $user = User::find($id);
        if($user){

            User::destroy($id);
            $respuesta->error = false;
            $respuesta->mensaje = "Usuario Eliminado";
            $respuesta->datos = $user;
        }else{
            $respuesta->error = true;
            $respuesta->mensaje = "Usuario No encontrado";
        }

        return response()->json($respuesta);/**/

    }


    public function  login(Request $request){


        $respuesta = new Respuesta();
        //  echo "Entroooooooooooooooo";
        // User::create($request->all());
        $datos= request()->all();
        if(!empty($datos["email"] )&&
            !empty($datos["password"])){

            $usuario = User::where('email', $datos["email"])->first();

            if(count($usuario) == 0){
                $respuesta->error = true;
                $respuesta->mensaje = "Usuario y password incorrecto";
            }else{

                $pas1 = $datos['password'];


                $pas2 = $usuario->password;

                if(Hash::check($pas1,$pas2 )){
                    $respuesta->error = false;
                    $respuesta->mensaje = "Acceso concedido";
                    $respuesta->datos = $usuario;
                }else{
                    $respuesta->error = true;
                    $respuesta->mensaje = "Usuario y password incorrecto";
                }

            }
        }
        return response()->json($respuesta);/**/
    }
}
