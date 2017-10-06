<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Reunion;
use App\UserReunion;
use App\Entidad;
use App\Entidades\Respuesta;
use Illuminate\Support\Facades\DB;

class ReunionController extends Controller
{
    public function store(Request $request){

        $respuesta = new Respuesta();
        //  echo "Entroooooooooooooooo";
        // User::create($request->all());
        $datos= request()->all();

        //return response()->json($datos);

        if(!empty($datos["participantes"] ) &&
            !empty($datos["descripcion"] ) &&
            !empty($datos["asunto"]) &&
            !empty($datos["hora_inicial"]) &&
            !empty($datos["hora_final"]) &&
           !empty($datos["fecha"]) &&
            !empty($datos["prioridad"]) &&
            !empty($datos["participacion_minima"])&&
            !empty($datos["lugar"])) {

            $DatosReunion["descripcion"] = $datos["descripcion"];
            $DatosReunion["asunto"] = $datos["asunto"];
            $DatosReunion["hora_inicial"] = $datos["hora_inicial"];
            $DatosReunion["hora_final"] = $datos["hora_final"];
            $DatosReunion["fecha"] = $datos["fecha"];
            $DatosReunion["prioridad"] = $datos["prioridad"];
            $DatosReunion["participacion_minima"] = $datos["participacion_minima"];
            $DatosReunion["lugar"] = $datos["lugar"];

            $participantes = $datos["participantes"];
            // return response()->json($participantes);
            if ($this->validarParticipantes($participantes)) {
                $DatosReunion['estado'] = "Programada";
                $reunion = new Reunion($DatosReunion);



                $userCreator = $this->getCreator($participantes);

                if ($userCreator) { //Si el usuario existe
                    if ($this->validarAsistencia($userCreator['id'], $reunion->fecha,
                        $reunion->hora_inicial, $reunion->hora_final)) {  //Si tiene el espacio libre

                       ///*/ return response()->json($reunion);
                        if ($reunion->save()) { //Guardamos la reunion

                            //Guardamos en la agenda primeramente al usuario creador de la reuinon
                            $DatosUserReunion['tipo_participante'] = $userCreator['tipo_participante'];
                            $DatosUserReunion['asistencia'] = 'si';
                            $DatosUserReunion['user_id'] = $userCreator['id'];
                            $DatosUserReunion['reunion_id'] = $reunion->id;

                            $intersecto = new UserReunion($DatosUserReunion);
                            $intersecto->save();


                            foreach ($participantes as $item) {
                                //cuando agregas un participante
                                //  return response()->json($DatosUserReunion);
                                    if($item['tipo_participante']!='creador'){
                                        if ($this->validarAsistencia($item['id'], $reunion->fecha,
                                            $reunion->hora_inicial, $reunion->hora_final)) {

                                            $DatosUserReunion['tipo_participante'] = $item['tipo_participante'];
                                            $DatosUserReunion['asistencia'] = 'si';
                                            $DatosUserReunion['user_id'] = $item['id'];
                                            $DatosUserReunion['reunion_id'] = $reunion->id;

                                            $intersecto = new UserReunion($DatosUserReunion);
                                            $intersecto->save();
                                        } else {

                                            $DatosUserReunion['tipo_participante'] = $item['tipo_participante'];
                                            $DatosUserReunion['asistencia'] = 'no';
                                            $DatosUserReunion['user_id'] = $item['id'];
                                            $DatosUserReunion['reunion_id'] = $reunion->id;
                                            $intersecto = new UserReunion($DatosUserReunion);
                                            $intersecto->save();

                                        }
                                    }


                            }
                            $respuesta->error = false;
                            $respuesta->mensaje = "Datos guardados correctamente";

                        }else{
                            $respuesta->error = true;
                            $respuesta->mensaje = "Error al guardar la reunion";
                        }


                    } else {

                        $respuesta->error = true;
                        $respuesta->mensaje = "Su agenda ya está ocupada para ese día y hora";
                    }

                } else {

                    $respuesta->error = true;
                    $respuesta->mensaje = "No hay un usuario creador de la reunion";
                }

            } else {

                $respuesta->error = true;
                $respuesta->mensaje = "Error en los participantes";
            }
        }else{
            $respuesta->error = true;
            $respuesta->mensaje = "Error en los participantes";

          }



        return response()->json($respuesta);
    }

    public function show($user_id)
    {
       // return $user_id;
        $respuesta = new Respuesta();
      //  $usuario = Reunion::where('email', $datos["email"])->first();

        $users = Reunion::join('user_reunion', 'reuniones.id', '=', 'user_reunion.reunion_id')
            ->select('reuniones.*','user_reunion.asistencia','user_reunion.tipo_participante')
            ->where('user_reunion.user_id', $user_id)
            ->get();

        if($users){
            $respuesta->error = false;
            $respuesta->mensaje = "Datos encontrados";
            $respuesta->datos = $users;
        }else{
            $respuesta->error = true;
            $respuesta->mensaje = "No tiene reuniones";
        }
        return response()->json($respuesta);
    }

    public function showParticipaciones($user_id)
    {
        // return $user_id;
        $respuesta = new Respuesta();
        //  $usuario = Reunion::where('email', $datos["email"])->first();

        $users = Reunion::join('user_reunion', 'reuniones.id', '=', 'user_reunion.reunion_id')
            ->select('reuniones.*','user_reunion.asistencia','user_reunion.id as user_reunion_id')
            ->where('user_reunion.user_id', $user_id)
            ->where('user_reunion.tipo_participante', "participante")
            ->get();

        if($users){
            $respuesta->error = false;
            $respuesta->mensaje = "Datos encontrados";
            $respuesta->datos = $users;
        }else{
            $respuesta->error = true;
            $respuesta->mensaje = "No tiene reuniones";
        }
        return response()->json($respuesta);
    }

    public function showCreaciones($user_id)
    {
        // return $user_id;
        $respuesta = new Respuesta();
        //  $usuario = Reunion::where('email', $datos["email"])->first();

        $users = Reunion::join('user_reunion', 'reuniones.id', '=', 'user_reunion.reunion_id')
            ->select('reuniones.*','user_reunion.asistencia')
            ->where('user_reunion.user_id', $user_id)
            ->where('user_reunion.tipo_participante', "creador")
            ->get();

        if($users){
            $respuesta->error = false;
            $respuesta->mensaje = "Datos encontrados";
            $respuesta->datos = $users;
        }else{
            $respuesta->error = true;
            $respuesta->mensaje = "No tiene reuniones";
        }
        return response()->json($respuesta);
    }

    /**
     * Método para validar que los participantes son usuario válidos
     * @param $participantes
     * @return \Illuminate\Http\JsonResponse
     */
    public function validarParticipantes($participantes){
        $cont =0;
        foreach($participantes as $item){
            $user = User::find($item['id']);
            if(!$user){
              $cont++;
            }

        }

        if($cont>0){
            return false;
        }else{
            return true;
        }
    }

    public function getCreator($participantes){
        foreach($participantes as $item){
            if($item['tipo_participante']=="creador"){
                return $item;
            }
        }
    }

    /**
     * Método para actualizar los datos de una reunion
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {

        $respuesta = new Respuesta();
        $datos = $request->all();

        if(!empty($datos["descripcion"] ) &&
            !empty($datos["asunto"]) &&
            !empty($datos["hora_inicial"]) &&
            !empty($datos["hora_final"]) &&
            !empty($datos["fecha"]) &&
            !empty($datos["prioridad"]) &&
            !empty($datos["participacion_minima"])&&
            !empty($datos["lugar"])){

            $reunion = Reunion::find($id);

            if($reunion){
                $reunion->update($datos);
                $respuesta->error = false;
                $respuesta->mensaje = "Datos actualizados existosamente";
                $respuesta->datos = $reunion;
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

    /**
     * Método para validar si puede o no participar en la reunion
     * @param $usuario_id
     * @param $fecha
     * @param $hora_ini
     * @param $hora_fin
     */
    public  function validarAsistencia($usuario_id,$fecha,$hora_ini,$hora_fin){


        $reuniones = UserReunion::where('user_id', $usuario_id)
            ->select('*')
            ->get();


        if(count($reuniones) > 0){

            foreach($reuniones as $item){

                $id =$item->reunion_id;

                $cita =Reunion::find($id);

                if($cita){


                    if($cita->fecha==$fecha){



                        if($hora_ini>= $cita->hora_inicial && $hora_ini<$cita->hora_final ||
                            $hora_fin> $cita->hora_inicial && $hora_fin <=$cita->hora_final){
                            return false;
                        }else{

                        }
                    }
                }



            }

            return true;

        }
        else{
            return true;
        }


    }

    public  function updateAsistencia(Request $request){
        $respuesta = new Respuesta();
        // echo "Entroooooooooooooooo";
        $datos= request()->all();
       //return response()->json($datos);
        if($datos){
            $userReunion = UserReunion::find($datos["user_reunion_id"]);

           // $reunion = Reunion::find()->get();
            if($userReunion) {
                $users = User::join('user_reunion', 'users.id', '=', 'user_reunion.user_id')
                    ->where('user_reunion.reunion_id', $datos["id"])
                    ->where('user_reunion.asistencia', "si")
                    ->select('*')
                    ->get();

                $reunion = Reunion::find($datos["id"]);


                if($datos["asistencia"]=="si"){
                    $userReunion->update($datos);
                    $respuesta->error = false;
                    $respuesta->mensaje = "Datos actualizados";
                    $respuesta->datos = $userReunion;
                }else{
                  // return response()->json($userReunion);

                    if($userReunion["asistencia"]=="no"){
                        $respuesta->error = false;
                        $respuesta->mensaje = "Datos actualizados";
                    }else{
                        if(count($users)> $reunion['participacion_minima']){
                            $userReunion->update($datos);
                            $respuesta->error = false;
                            $respuesta->mensaje = "Datos actualizados";
                            $respuesta->datos = $userReunion;
                        }else{
                            $respuesta->error = true;
                            $respuesta->mensaje = "No puede abandonar la reunion";
                        }
                    }
                }
                //return response()->json($userReunion);


            }else{
                $respuesta->error = true;
                $respuesta->mensaje = "No se encuentra esta reunion";
            }
        }

        return response()->json($respuesta);
    }
}
