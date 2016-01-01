<?php

class QuiebreController extends \BaseController
{
    /**
     * Store a newly created resource in storage.
     * POST /quiebre/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $quiebres=  DB::table('quiebres as q')
                        ->select(
                            'q.id',
                            DB::raw('g.id as grupo_quiebre_id'),
                            'q.nombre',
                            'q.estado',
                            'q.apocope',
                            'g.nombre as grupo'
                        )
                        ->join(
                            'quiebre_grupos as g',
                            'q.quiebre_grupo_id',
                            '=',
                            'g.id'
                        )
                        ->orderBy('q.nombre', 'asc')
                        ->get();

            return Response::json(array('rst'=>1,'datos'=>$quiebres));
        }
    }
    /**
     * mostrar lista de quiebres officetrack, para la carga masiva
     * POST /quiebre/cargarofficetrack
     *
     * @return Response
     */
    public function postCargarofficetrack()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $quiebres = Quiebre::getQuiebresAllOfficeTrack();

            return Response::json(array('rst'=>1,'datos'=>$quiebres));
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /quiebre/listar
     *
     * @return Response
     */
    public function postListar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            if ( Input::get('usuario')=='1' ) {
                $quiebres=  DB::table('quiebres as q')
                            ->join(
                                'quiebre_grupo_usuario as qgu',
                                function($join)
                                {
                                    $join->on(
                                        'q.quiebre_grupo_id',
                                        '=',
                                        'qgu.quiebre_grupo_id'
                                    )
                                    ->where(
                                        'qgu.usuario_id',
                                        '=',
                                        Auth::user()->id
                                    )
                                    ->where(
                                        'qgu.estado',
                                        '=',
                                        '1'
                                    );
                                }
                            )
                            ->select(
                                'q.id',
                                'q.nombre',
                                DB::raw(
                                    'IFNULL(qgu.estado,"disabled") as block'
                                )
                            )
                            ->where('q.estado', '=', '1')
                            ->whereRaw(
                                'q.id NOT IN (
                                    SELECT quiebre_id
                                    FROM quiebre_usuario_restringido
                                    WHERE usuario_id="'.Auth::user()->id.'"
                                    AND estado=1
                                )'
                            )
                            ->orderBy('q.nombre')
                            ->get();
            } elseif (Input::get('celula_id')) {
                $celulaId = Input::get('celula_id');
                $quiebres = DB::table('celula_quiebre as cq')
                            ->rightJoin(
                                'quiebres as q', function($join) use ($celulaId)
                                {
                                $join->on('cq.quiebre_id', '=', 'q.id')
                                ->on('cq.celula_id', '=', DB::raw($celulaId));
                                }
                            )
                            ->select('q.id', 'q.nombre', 'cq.estado')
                            ->where('q.estado', '=', 1)
                            ->get();
            } elseif (Input::get('usuario_id')) {
                $usuarioId = Input::get('usuario_id');
                $quiebres = DB::table('quiebre_usuario as qu')
                        ->rightJoin(
                            'quiebres as q', function($join) use ($usuarioId)
                            {
                            $join->on('qu.quiebre_id', '=', 'q.id')
                            ->on('qu.usuario_id', '=', DB::raw($usuarioId));
                            }
                        )
                        ->select('q.id', 'q.nombre', 'qu.estado')
                        ->where('q.estado', '=', 1)
                        ->get();
            } elseif (Input::get('grupo_quiebre_id')) {
                $grupoQuiebreId = Input::get('grupo_quiebre_id');
                $usuarioId = Input::get('user_id');
                /*$quiebres=  DB::table('quiebres')
                            ->select('id', 'nombre')
                            ->where('estado', '=', '1')
                            ->where('quiebre_grupo_id', '=', $grupoQuiebreId)
                            ->orderBy('nombre')
                            ->get();*/

                    $sql="  SELECT q.id, q.nombre,
                                (   SELECT IFNULL(estado,0) 
                                    FROM quiebre_usuario_restringido qur
                                    WHERE qur.quiebre_id=q.id 
                                    AND qur.usuario_id=? )
                                AS estado
                            FROM quiebres q
                            WHERE quiebre_grupo_id=?
                            AND q.estado=1";

                            $quiebres=DB::select(
                                $sql, 
                                array($usuarioId,$grupoQuiebreId)
                            );
            } else {
                $quiebres=  DB::table('quiebres')
                            ->select('id', 'nombre')
                            ->where('estado', '=', '1')
                            ->orderBy('nombre')
                            ->get();
            }

            return Response::json(array('rst'=>1,'datos'=>$quiebres));
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /quiebre/crear
     *
     * @return Response
     */
    public function postCrear()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $regex='regex:/^([a-zA-Z01-9 .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
            $required='required';
            $numeric='numeric';
            $reglas = array(
                'nombre' => $required.'|'.$regex,
                'apocope' => $regex,
                'quiebregrupos' => $required.'|'.$numeric,
            );

            $mensaje= array(
                'required'  => ':attribute Es requerido',
                'regex'     => ':attribute Solo debe ser Texto',
                'numeric'   => ':attribute seleccione'
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ($validator->fails()) {
                return Response::json(
                    array(
                    'rst'=>2,
                    'msj'=>$validator->messages(),
                    )
                );
            }
            $quiebres = new Quiebre;
            $quiebres['nombre'] = Input::get('nombre');
            $quiebres['estado'] = Input::get('estado');
            $quiebres['apocope'] = Input::get('apocope');
            $quiebres['quiebre_grupo_id'] = Input::get('quiebregrupos');
            $quiebres->save();
            $actividades = Input::get('actividad');

            for ($i=0; $i<count($actividades); $i++) {
                $actividadId = $actividades[$i];
                $actividad = Actividad::find($actividadId);
                $quiebres->actividades()->save($actividad, array('estado'=>1));
            }

            $motivos = Input::get('motivo');

            for ($i=0; $i<count($motivos); $i++) {
                $motivoId = $motivos[$i];
                $motivo = Motivo::find($motivoId);
                
                $quiebres->motivos()->save(
                            $motivo, array(
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'usuario_created_at'=> Auth::user()->id
                                    )
                        );
            }

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro realizado correctamente',
                )
            );
        }
    }

    /**
     * actualizar los quiebres y actividades relacionadas
     * POST /quiebre/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if (Request::ajax()) {
            $regex='regex:/^([a-zA-Z01-9 .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
            $required='required';
            $numeric='numeric';
            $reglas = array(
                'nombre' => $required.'|'.$regex,
                'apocope' => $regex,
                'quiebregrupos' => $required.'|'.$numeric,
            );

            $mensaje= array(
                'required'  => ':attribute Es requerido',
                'regex'     => ':attribute Solo debe ser Texto',
                'numeric'   => ':attribute seleccione'
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ($validator->fails()) {
                return Response::json(
                    array(
                    'rst'=>2,
                    'msj'=>$validator->messages(),
                    )
                );
            }
            //editando quiebre
            $quiebreId = Input::get('id');
            $quiebres = Quiebre::find($quiebreId);
            $quiebres['nombre'] = Input::get('nombre');
            $quiebres['estado'] = Input::get('estado');
            $quiebres['apocope'] = Input::get('apocope');
            $quiebres['quiebre_grupo_id'] = Input::get('quiebregrupos');
            $quiebres->save();
            //actulizando a estado 0 segun quiebre seleccionado
            DB::table('actividad_quiebre')
                    ->where('quiebre_id', $quiebreId)
                    ->update(array('estado' => 0));

            $actividades = Input::get('actividad');
            //si estado de celula esta activo y no selecciono nin gun quebre
            if (Input::get('estado') == 1 and !empty($actividades)) {
                for ($i=0; $i<count($actividades); $i++) {
                    $actividadId = $actividades[$i];
                    $actividad = Actividad::find($actividadId);
                    //buscando en la tabla
                    $actividadQuiebre = DB::table('actividad_quiebre')
                        ->where('quiebre_id', '=', $quiebreId)
                        ->where('actividad_id', '=', $actividadId)
                        ->first();
                    if (is_null($actividadQuiebre)) {
                        $quiebres->actividades()->save(
                            $actividad, array('estado' => 1)
                        );
                    } else {
                        //update a la tabla actividad_quiebre
                        DB::table('actividad_quiebre')
                            ->where('quiebre_id', '=', $quiebreId)
                            ->where('actividad_id', '=', $actividadId)
                            ->update(array('estado' => 1));
                    }
                }

            }

            //actulizando a estado 0 segun quiebre seleccionado
            DB::table('motivo_quiebre')
                    ->where('quiebre_id', $quiebreId)
                    ->update(array(
                            'estado' => 0,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'usuario_updated_at'=> Auth::user()->id
                            )
                    );

            $motivos = Input::get('motivo');
            //si estado de motivo esta activo y no selecciono nin gun quebre
            if (Input::get('estado') == 1 and !empty($motivos)) {
                for ($i=0; $i<count($motivos); $i++) {
                    $motivoId = $motivos[$i];
                    $motivo = Motivo::find($motivoId);
                    //buscando en la tabla
                    $motivoQuiebre = DB::table('motivo_quiebre')
                        ->where('quiebre_id', '=', $quiebreId)
                        ->where('motivo_id', '=', $motivoId)
                        ->first();
                    if (is_null($motivoQuiebre)) {
                        $quiebres->motivos()->save(
                            $motivo, array(
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'usuario_created_at'=> Auth::user()->id
                                    )
                        );
                    } else {
                        //update a la tabla actividad_quiebre
                        DB::table('motivo_quiebre')
                            ->where('quiebre_id', '=', $quiebreId)
                            ->where('motivo_id', '=', $motivoId)
                            ->update(array(
                                    'estado' => 1,
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'usuario_updated_at'=> Auth::user()->id
                                    )
                            );
                    }
                }

            }
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

    /**
     * Cambiar estado del registro de quiebre, ello implica cambiar el estado de
     * la tabla celula_quiebre, actividad_quiebre.
     * POST /quiebre/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {
        if (Request::ajax()) {
            $quiebre = Quiebre::find(Input::get('id'));
            $quiebre->estado = Input::get('estado');
            $quiebre->save();
            if (Input::get('estado') == 0) {
                DB::table('celula_quiebre')
                        ->where('quiebre_id', Input::get('id'))
                        ->update(array('estado' => 0));
                DB::table('actividad_quiebre')
                        ->where('quiebre_id', Input::get('id'))
                        ->update(array('estado' => 0));
            }
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

}
