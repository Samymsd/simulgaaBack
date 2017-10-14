<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\User;
use App\Reunion;
use App\UserReunion;
use App\Entidad;
use App\Entidades\Respuesta;
use Illuminate\Support\Facades\DB;
use DateTime;
use Carbon\Carbon;
use DateInterval;

class ReunionController extends Controller
{
    public function store(Request $request)
    {

        $respuesta = new Respuesta();
        //  echo "Entroooooooooooooooo";
        // User::create($request->all());
        $datos = request()->all();

        //return response()->json($datos);

        if (!empty($datos["participantes"]) &&
            !empty($datos["descripcion"]) &&
            !empty($datos["asunto"]) &&
            !empty($datos["hora_inicial"]) &&
            !empty($datos["hora_final"]) &&
            !empty($datos["fecha"]) &&
            !empty($datos["prioridad"]) &&
            !empty($datos["tipo"]) &&
            !empty($datos["participacion_minima"]) &&
            !empty($datos["lugar"])) {

            $DatosReunion["descripcion"] = $datos["descripcion"];
            $DatosReunion["asunto"] = $datos["asunto"];
            $DatosReunion["hora_inicial"] = $datos["hora_inicial"];
            $DatosReunion["hora_final"] = $datos["hora_final"];
            $DatosReunion["fecha"] = $datos["fecha"];
            $DatosReunion["hastaNegociacion"] = $datos["hastaNegociacion"];
            //$DatosReunion["hastaRepetir"] = $datos["hastaRepetir"];
            //  return response()->json( $datos["hastaNegociacion"]);
            $DatosReunion["prioridad"] = $datos["prioridad"];
            $DatosReunion["participacion_minima"] = $datos["participacion_minima"];
            $DatosReunion["lugar"] = $datos["lugar"];
            $DatosReunion["tipo"] = $datos["tipo"];

            $participantes = $datos["participantes"];
            // return response()->json($participantes);


            if (!empty($datos["hastaRepetir"])) {



                $FechaReunion = new DateTime($datos["fecha"]);
                $FechaHasta = new DateTime($datos["hastaRepetir"]);


                while ($FechaReunion <= $FechaHasta) {

                    $DatosReunion["fecha"] = date_format($FechaReunion, 'Y/m/d');



                    if ($this->validarParticipantes($participantes)) {

                        $DatosReunion['estado'] = "En proceso";
                        $reunion = new Reunion($DatosReunion);

                        $userCreator = $this->getCreator($participantes);

                        if ($userCreator) { //Si el usuario existe
                            if ($this->validarAsistencia($userCreator['id'], new DateTime($reunion->fecha),
                                $reunion->hora_inicial, $reunion->hora_final)) {  //Si tiene el espacio libre el creador

                                // print_r("Si tiene espacio el creador");
                                // exit();
                                if ($this->validarAsistenciaMiniminaAReunion(new DateTime($reunion->fecha),
                                    $reunion->hora_inicial, $reunion->hora_final, $participantes,
                                    $reunion->participacion_minima)) {


                                    if ($this->saveReunion($reunion, $userCreator, $participantes)) {
                                        $respuesta->error = false;
                                        $respuesta->mensaje = "Datos guardados correctamente";
                                    } else {
                                        $respuesta->error = true;
                                        $respuesta->mensaje = "Error al guardar la reunion";
                                    }


                                } else {
                                    // return phpinfo();

                                    //print_r("No se puede en esta fecha");
                                    //exit();

                                    $fecha = $this->NegociarOtraFecha($reunion, $participantes);  //Buscamos una nueva fecha posible
                                    if ($fecha != null) {
                                        // return response()->json($fecha);
                                        $reunion->fecha = $fecha;  //Asignamos la fecha a la reunion

                                        if ($this->saveReunion($reunion, $userCreator, $participantes)) {
                                            $respuesta->error = false;
                                            $respuesta->mensaje = "Reunion guardada, en nueva fecha despues de la negociacion";
                                            $respuesta->datos = $reunion;
                                        } else {
                                            $respuesta->error = true;
                                            $respuesta->mensaje = "Error al guardar la reunion";
                                        }


                                    } else {
                                        $respuesta->error = true;
                                        $respuesta->mensaje = "No se ha podido nogociar una fecha en estas fechas";
                                    }

                                }

                                ///*/ return response()->json($reunion);


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

                    $FechaReunion->add(new DateInterval('P7D'));

                }

            } else {
                if ($this->validarParticipantes($participantes)) {

                    $DatosReunion['estado'] = "En proceso";
                    $reunion = new Reunion($DatosReunion);

                    $userCreator = $this->getCreator($participantes);

                    if ($userCreator) { //Si el usuario existe
                        if ($this->validarAsistencia($userCreator['id'], new DateTime($reunion->fecha),
                            $reunion->hora_inicial, $reunion->hora_final)) {  //Si tiene el espacio libre el creador

                            // print_r("Si tiene espacio el creador");
                            // exit();
                            if ($this->validarAsistenciaMiniminaAReunion(new DateTime($reunion->fecha),
                                $reunion->hora_inicial, $reunion->hora_final, $participantes,
                                $reunion->participacion_minima)) {


                                if ($this->saveReunion($reunion, $userCreator, $participantes)) {
                                    $respuesta->error = false;
                                    $respuesta->mensaje = "Datos guardados correctamente";
                                } else {
                                    $respuesta->error = true;
                                    $respuesta->mensaje = "Error al guardar la reunion";
                                }


                            } else {
                                // return phpinfo();

                               // print_r("No se puede en esta fecha");
                               // exit();

                                $fecha = $this->NegociarOtraFecha($reunion, $participantes);  //Buscamos una nueva fecha posible
                                if ($fecha != null) {
                                    // return response()->json($fecha);
                                    $reunion->fecha = $fecha;  //Asignamos la fecha a la reunion

                                    if ($this->saveReunion($reunion, $userCreator, $participantes)) {
                                        $respuesta->error = false;
                                        $respuesta->mensaje = "Reunion guardada, en nueva fecha despues de la negociacion";
                                        $respuesta->datos = $reunion;
                                    } else {
                                        $respuesta->error = true;
                                        $respuesta->mensaje = "Error al guardar la reunion";
                                    }


                                } else {
                                    $respuesta->error = true;
                                    $respuesta->mensaje = "No se ha podido nogociar una fecha en estas fechas";
                                }

                            }

                            ///*/ return response()->json($reunion);


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
            }

        }else {
                $respuesta->error = true;
                $respuesta->mensaje = "Faltan algunos datos";

            }


            return response()->json($respuesta);
        }

    public function show($user_id)
    {
        //return $user_id;
        $respuesta = new Respuesta();
        //  $usuario = Reunion::where('email', $datos["email"])->first();


        $users = Reunion::join('user_reunion', 'reuniones.id', '=', 'user_reunion.reunion_id')
            ->select('reuniones.*', 'user_reunion.asistencia', 'user_reunion.tipo_participante')
            ->where('user_reunion.user_id', $user_id)
            ->where('reuniones.estado', 'Programada')
            ->orderBy('reuniones.fecha', 'DESC')
            ->get();




        if ($users) {
            $respuesta->error = false;
            $respuesta->mensaje = "Datos encontrados";
            $respuesta->datos = $users;
        } else {
            $respuesta->error = true;
            $respuesta->mensaje = "No tiene reuniones";
        }
        return response()->json($respuesta);
    }

    public function showHistorico($user_id)
    {
        //return $user_id;
        $respuesta = new Respuesta();
        //  $usuario = Reunion::where('email', $datos["email"])->first();


        $users = Reunion::join('user_reunion', 'reuniones.id', '=', 'user_reunion.reunion_id')
            ->select('reuniones.*', 'user_reunion.asistencia', 'user_reunion.tipo_participante')
            ->where('user_reunion.user_id', $user_id)
            ->orderBy('reuniones.created_at', 'DESC')
            ->get();




        if ($users) {
            $respuesta->error = false;
            $respuesta->mensaje = "Datos encontrados";
            $respuesta->datos = $users;
        } else {
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
        //$hoy = strftime( "%Y-%m-%d-%H-%M-%S", time() );
        $hoy = Carbon::now();
        $hoy->setTimezone('-5');
        $hoy->toDateString();

        $siguiente_semana =Carbon::now();
        $siguiente_semana->setTimezone('-5');
        $siguiente_semana->addDays(7);
        $siguiente_semana->toDateString();



        $users = Reunion::join('user_reunion', 'reuniones.id', '=', 'user_reunion.reunion_id')
            ->select('reuniones.*', 'user_reunion.asistencia', 'user_reunion.id as user_reunion_id')
            ->where('user_reunion.user_id', $user_id)
            ->where('user_reunion.tipo_participante', "participante")
            ->where('reuniones.fecha','>=',$hoy)
            ->where('reuniones.fecha','<=',$siguiente_semana)
            ->where('reuniones.tipo','=',"organizacion")
            ->where('reuniones.estado','=',"Programada")
            ->orderBy('reuniones.fecha', 'ASC')
            ->get();



        if ($users) {
            $respuesta->error = false;
            $respuesta->mensaje = "Datos encontrados";
            $respuesta->datos = $users;
        } else {
            $respuesta->error = true;
            $respuesta->mensaje = "No tiene reuniones";
        }
        return response()->json($respuesta);
    }

    public function showCreaciones($user_id)
    {
        $hoy = strftime( "%Y-%m-%d-%H-%M-%S", time() );
        // return $user_id;
        $respuesta = new Respuesta();
        //  $usuario = Reunion::where('email', $datos["email"])->first();

        $users = Reunion::join('user_reunion', 'reuniones.id', '=', 'user_reunion.reunion_id')
            ->select('reuniones.*', 'user_reunion.asistencia')
            ->where('user_reunion.user_id', $user_id)
            ->where('user_reunion.tipo_participante', "creador")
            ->where('reuniones.fecha','>=',$hoy)
            ->where('reuniones.tipo','=',"organizacion")
            ->orderBy('reuniones.created_at', 'DESC')
            ->get();

        if ($users) {
            $respuesta->error = false;
            $respuesta->mensaje = "Datos encontrados";
            $respuesta->datos = $users;
        } else {
            $respuesta->error = true;
            $respuesta->mensaje = "No tiene reuniones";
        }
        return response()->json($respuesta);
    }

    public function showCreacionesPersonales($user_id)
    {
        //$hoy = strftime( "%Y-%m-%d-%H-%M-%S", time() );

        $hoy = Carbon::now();
        $hoy->setTimezone('-5');
        $hoy->toDateString();

        $siguiente_semana =Carbon::now();
        $siguiente_semana->setTimezone('-5');
        $siguiente_semana->addDays(7);
        $siguiente_semana->toDateString();


        // return $user_id;
        $respuesta = new Respuesta();
        //  $usuario = Reunion::where('email', $datos["email"])->first();



        $users = Reunion::join('user_reunion', 'reuniones.id', '=', 'user_reunion.reunion_id')
            ->select('reuniones.*', 'user_reunion.asistencia')
            ->where('user_reunion.user_id', $user_id)
            ->where('user_reunion.tipo_participante', "creador")
            ->where('reuniones.fecha','>=',$hoy)
            ->where('reuniones.fecha','<=',$siguiente_semana)
            ->where('reuniones.tipo','=',"personal")
            ->orderBy('reuniones.created_at', 'DESC')
            ->get();

        if ($users) {
            $respuesta->error = false;
            $respuesta->mensaje = "Datos encontrados";
            $respuesta->datos = $users;
        } else {
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
    public function validarParticipantes($participantes)
    {
        $cont = 0;
        foreach ($participantes as $item) {
            $user = User::find($item['id']);
            if (!$user) {
                $cont++;
            }

        }

        if ($cont > 0) {
            return false;
        } else {
            return true;
        }
    }

    public function getCreator($participantes)
    {
        foreach ($participantes as $item) {
            if ($item['tipo_participante'] == "creador") {
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

        if (!empty($datos["descripcion"]) &&
            !empty($datos["asunto"]) &&
            !empty($datos["hora_inicial"]) &&
            !empty($datos["hora_final"]) &&
            !empty($datos["fecha"]) &&
            !empty($datos["prioridad"]) &&
            !empty($datos["tipo"]) &&
            !empty($datos["participacion_minima"]) &&
            !empty($datos["lugar"])) {

            $reunion = Reunion::find($id);

            if ($reunion) {
                $reunion->update($datos);
                $respuesta->error = false;
                $respuesta->mensaje = "Datos actualizados existosamente";
                $respuesta->datos = $reunion;
            } else {
                $respuesta->error = true;
                $respuesta->mensaje = "Usuario No encontrado";
            }

        } else {
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
    public function validarAsistencia($usuario_id, $fecha, $hora_ini, $hora_fin)
    {



        $reuniones = UserReunion::where('user_id', $usuario_id)
            ->select('*')
            ->get();


        if (count($reuniones) > 0) {


            foreach ($reuniones as $item) {

                $id = $item->reunion_id;


                $cita = Reunion::find($id);

                if ($cita) {



                    // print_r($cita->fecha);
                    //print_r($fecha);
                    // exit();


                    $f1 = new DateTime($cita->fecha);


                    if (is_string($fecha)) {
                        $f2 = new DateTime($fecha);
                    } else {
                        $f2 = $fecha;
                    }


                    //exit();

                    $String1 = date_format($f1, 'Y/m/d');
                    $String2 = date_format($f2, 'Y/m/d');


                    if ($String1 == $String2) {


                        //$date = Carbon::now();
                        $hora_ini =$this->getHoraCarbon($hora_ini);
                        $hora_fin =$this->getHoraCarbon($hora_fin);

                        $hora_ini_DB = new DateTime($cita->hora_inicial);
                        $hora_fin_DB = new DateTime($cita->hora_final);

/**
// print_r("entro 1  ");
//exit();
                        if($hora_fin>$hora_ini_DB){
                            // print_r("entro 1");
                        //    print_r($hora_ini);
                           // print_r($hora_ini_DB);
                            exit;
                        }else{
                            print_r("entro 2");
                              print_r($hora_ini);
                            print_r($hora_ini_DB);
                            exit;
                        }  */

                       // $hora_ini_DB =$this->getHoraCarbon($cita->hora_inicial);
                       // $hora_fin_DB =$this->getHoraCarbon($cita->hora_final);



                        if ($hora_ini < $hora_ini_DB && $hora_fin <= $hora_ini_DB||
                            $hora_ini >= $hora_ini_DB && $hora_ini >= $hora_fin_DB) {
                            // print_r("entro 1  ");

                        }else{
                            return false;
                        }




                        /**
                         * if ($hora_ini >= $cita->hora_inicial && $hora_ini < $cita->hora_final ||
                         * $hora_fin > $cita->hora_inicial && $hora_fin <= $cita->hora_final) {
                         * return false;
                         * } else {
                         *
                         * }  **/
                    }


                }



            }


               return true;

        }else {
            return true;
        }


    }

    public function updateAsistencia(Request $request)
    {
        $respuesta = new Respuesta();
        // echo "Entroooooooooooooooo";
        $datos = request()->all();
        //return response()->json($datos);
        if ($datos) {
            $userReunion = UserReunion::find($datos["user_reunion_id"]);

            // $reunion = Reunion::find()->get();
            if ($userReunion) {
                $users = User::join('user_reunion', 'users.id', '=', 'user_reunion.user_id')
                    ->where('user_reunion.reunion_id', $datos["id"])
                    ->where('user_reunion.asistencia', "si")
                    ->select('*')
                    ->get();

                $reunion = Reunion::find($datos["id"]);


                if ($datos["asistencia"] == "si") {
                    $userReunion->update($datos);
                    $respuesta->error = false;
                    $respuesta->mensaje = "Datos actualizados";
                    $respuesta->datos = $userReunion;
                } else {
                    // return response()->json($userReunion);

                    if ($userReunion["asistencia"] == "no") {
                        $respuesta->error = false;
                        $respuesta->mensaje = "Datos actualizados";
                    } else {
                        if (count($users) > $reunion['participacion_minima']) {
                            $userReunion->update($datos);
                            $respuesta->error = false;
                            $respuesta->mensaje = "Datos actualizados";
                            $respuesta->datos = $userReunion;
                        } else {
                            $respuesta->error = true;
                            $respuesta->mensaje = "No puede abandonar la reunion";
                        }
                    }
                }
                //return response()->json($userReunion);


            } else {
                $respuesta->error = true;
                $respuesta->mensaje = "No se encuentra esta reunion";
            }
        }

        return response()->json($respuesta);
    }

    /**
     * Método que permite validar que en una fecha determinada, se puede dar o no la sistencia minima de participantes
     * @param $fecha
     * @param $hora_ini
     * @param $hora_fin
     * @param $participantes
     * @param $asistenciaMinima
     * @return bool
     */
    public function validarAsistenciaMiniminaAReunion($fecha, $hora_ini, $hora_fin, $participantes, $asistenciaMinima)
    {


        // $String1 = date_format($fecha, 'd/m/y');
        // print_r($String1+" || ");

        $cont = 0;

        foreach ($participantes as $item) {
            /*
            print_r($item['id']);
            print_r("**********");
             print_r($fecha);
            print_r("**********");
            print_r($hora_ini);
            print_r("**********");
            print_r($hora_fin);
            print_r("**********");
            */

            if ($this->validarAsistencia($item['id'], $fecha, $hora_ini, $hora_fin)) {
                $cont++;
            }
        }
        // print_r($cont);
        //print_r($asistenciaMinima);
        //exit();

        // print_r($cont+" "+$asistenciaMinima+"||");
        if ($cont >= $asistenciaMinima) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Método para guardar la Reunion
     * @param $reunion
     * @param $userCreator
     * @param $participantes
     *
     */
    private function saveReunion($reunion, $userCreator, $participantes)
    {
        $reunion->estado = "Programada";
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
                if ($item['tipo_participante'] != 'creador') {
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
            return true;

        } else {
            return false;
        }
    }

    /**
     * @param $reunion
     * @param $participantes
     * @return DateTime
     */
    private function NegociarOtraFecha($reunion, $participantes)
    {

        $fechaInicial = new DateTime($reunion->fecha);

        $fecha = new DateTime($reunion->fecha);


        $num  = $reunion->hastaNegociacion;

        $cont =0;

        // $bandera = true;


        while ($cont < $num) {

            $fecha->add(new DateInterval('P1D')); //Agregamos un dia a la fecha

            $result = $this->validarAsistenciaMiniminaAReunion($fecha,
                $reunion->hora_inicial, $reunion->hora_final, $participantes,
                $reunion->participacion_minima);

            if ($result == true) {
                return $fecha;
            }

            $cont++;
        }

        return null;

    }


    private function getHoraCarbon($string){


        $date = Carbon::now();
        // Ejemplo 1
        //$pizza  = "porción1 porción2 porción3 porción4 porción5 porción6";
        $porciones = explode(":", $string);
       // print_r($porciones);
        //print_r('**********************');
        $date->setTime($porciones[0],$porciones[1]);


       return $date;
    }

}

