<?php
//controller
class EventosController extends \BaseController
{
    
    public function postCargar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $eventos = Eventos::getCargarEventos();

            return Response::json(array('rst'=>1,'datos'=>$eventos));
        }
    }
    
    public function postEditar()
    {
        if (Request::ajax()) {
            //$eventoid = Input::get('id');
            $eventos = Eventos::find(Input::get('id'));
            $eventos['estado'] = Input::get('estado');
            $eventod = Input::get('evento');
            $extraer = Input::get('extraer');
            $grupo = Input::get('grupo');
            $idsql = Input::get('id_sql');
            $idwhere = Input::get('id_where');
            $nombre = Input::get('nombre');
            $orden = Input::get('orden');
            $valorwhere = Input::get('valor_where');
            $tipotabla = Input::get('tipotabla');
            $eventos['usuario_updated_at'] = Auth::user()->id;
//            $eventos->save();
            if ($tipotabla=='1') {
                DB::table('evento_consulta')
                        ->where('id', Input::get('id'))
                        ->update(
                            array('estado' => $eventos['estado'],
                            'evento'=>$eventod,
                            'extraer' => $extraer,
                            'grupo' => $grupo,
                            'id_sql' => $idsql,
                            'id_where' => $idwhere,
                            'nombre' => $nombre,
                            'orden' => $orden,
                            'valor_where' => $valorwhere,
                            'usuario_updated_at'=>$eventos['usuario_updated_at'],
                            'updated_at'=> date('Y-m-d H:i:s')
                            )
                        );
            }
            if ($tipotabla=='2') {
                DB::table('evento_metodo')
                        ->where('id', Input::get('id'))
                        ->update(
                            array('estado' => $eventos['estado'],
                            'metodo'=>$eventod,
                            'nombre' => $nombre,
                            'usuario_updated_at' => $eventos['usuario_updated_at'],
                            'updated_at' => date('Y-m-d H:i:s')
                            )
                        );
            }

            return Response::json(
                array(
                'rst' => 1,
                'msj' => 'Registro actualizado correctamente',
                )
            );
        }
    }
    
    public function postCrear()
    {   
       //si la peticion es ajax
        if ( Request::ajax() ) {
            //validar la data y crear nueva gestion
//            if (Input::has('ed_motivo_id') && Input::has('observacion') &&
//                Input::has('ed_id')) {
            $tipotabla = Input::get('tipoevento');
                
            if ($tipotabla==1) {
                $data=array(
                    Input::get('estado'),
                    Input::get('evento'),
                    Input::get('id_sql'),
                    Input::get('id_where'),
                    Input::get('nombre'),
                    Input::get('valor_where'),
                    Input::get('extraer'),
                    Input::get('grupo'),
                    Input::get('orden'),
                    Auth::user()->id
                    );
                $datos=Eventos::getEstadoAgregarEvento($data);
            } else {
                $data2=array(
                    Input::get('estado'),
                    Input::get('evento'),
                    Input::get('nombre'),
                    Auth::user()->id
                    );
                $datos=Eventos::getEstadoAgregarMetodo($data2);
            }    
                return Response::json(
                    array('rst'=>1,
                        'datos'=>$datos,
                        'msj'=>"Se inserto registro exitosamente"
                        )
                );
           // }
        }
    }
    
    public function postCambiarestado()
    {
        if (Request::ajax()) {
            //$eventoid = Input::get('id');
            $eventos = Eventos::find(Input::get('id'));
            $eventos['estado'] = Input::get('estado');
            $tipotabla = Input::get('tipotabla');
            $eventos['usuario_updated_at'] = Auth::user()->id;
           // $eventos->save();
            if ($tipotabla=='1') {
                DB::table('evento_consulta')
                        ->where('id', Input::get('id'))
                        ->update(
                            array('estado' => $eventos['estado'],
                            'usuario_updated_at' => $eventos['usuario_updated_at'],
                            'updated_at'=> date('Y-m-d H:i:s')
                            )
                        );
            }
            if ($tipotabla=='2') {
                DB::table('evento_metodo')
                        ->where('id', Input::get('id'))
                        ->update(
                            array('estado' => $eventos['estado'],
                            'usuario_updated_at' => $eventos['usuario_updated_at'],
                            'updated_at' => date('Y-m-d H:i:s')
                            )
                        );
            }

            return Response::json(
                array(
                'rst' => 1,
                'msj' => 'Registro actualizado correctamente',
                )
            );
        }
    }
    
    public function postListar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            
            if (Input::has('usuario_id')) {
                $usuarioId = Input::get('usuario_id');
                $tipoevento=Input::get('tipo');
                $tipopersona=Input::get('tipo_persona');
                $usuarioSesion= Auth::user()->id;
                //$perfilId = Session::get('perfilId');
                $usuario = Usuario::find(Auth::user()->id);
                $perfilId=$usuario['perfil_id'];
                
                $tabla='consulta';
                if ($tipoevento==2) {
                    $tabla='metodo';
                }
                
                $query = "SELECT e.id, e.nombre, 
                            (SELECT estado 
                            FROM eventos
                            WHERE persona_id=? AND estado=1
                            AND evento_id=e.id and tipo_persona=? and tipo_evento=?
                            GROUP BY evento_id ) AS estado
                        FROM evento_".$tabla." e ";

                if ($perfilId=='8') {
                    $query.=" WHERE e.estado=1 ORDER BY e.nombre";
                    $eventos= DB::select(
                        $query, 
                        array($usuarioId,$tipopersona,$tipoevento)
                    );
                } else {
                    $query.="JOIN eventos eu 
                            ON e.id=eu.evento_id
                            WHERE eu.estado=1 AND e.estado=1 AND eu.persona_id=?
                            and tipo_evento=? group by e.id ORDER BY e.nombre";
                    $eventos= DB::select(
                        $query, 
                        array($usuarioId,$tipopersona,$tipoevento,$usuarioSesion,$tipoevento)
                    );
                } //
            } else {
               if (Input::get('tipo')==1) {
                   $eventos=  DB::table('evento_consulta')
                            ->select('id', 'nombre')
                            ->where('estado', '=', '1')
                            ->orderBy('nombre')
                            ->get();
               }
               if (Input::get('tipo')==2) {
                   $eventos=  DB::table('evento_metodo')
                            ->select('id', 'nombre')
                            ->where('estado', '=', '1')
                            ->orderBy('nombre')
                            ->get();
               }
            }    
            
            return Response::json(array('rst'=>1,'datos'=>$eventos));
        } // cierra
    }

}
