<?php
class MotivoController extends \BaseController
{
    public function postListar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            if (Input::get('quiebre_id')) {
                $quiebreId = Input::get('quiebre_id');
                $motivos = DB::table('motivos AS m')
                            ->join(
                                'estado_motivo_submotivo AS ems',
                                'ems.motivo_id', '=', 'm.id'
                            )
                            ->leftJoin(
                                'motivo_quiebre as mq', function($join) use (
                                    $quiebreId
                                    )
                                {
                                    $join->on('mq.motivo_id', '=', 'm.id')
                                    ->where(
                                        'mq.quiebre_id', '=', DB::raw(
                                            $quiebreId
                                        )
                                    );
                                }
                            )
                        ->select('m.id', 'm.nombre', 'mq.estado')
                        ->where('m.estado', '=', 1)
                        ->where('ems.estado', '=', 1)
                        ->where(
                            function($query)
                                {
                                    if ( Input::get('requerimiento') && 
                                         Input::get('mas')
                                    ) {
                                        $query->whereRaw(
                                            'CONCAT(ems.req_tecnico,
                                                    "-",ems.req_horario
                                             ) 
                                            IN ("'.Input::get(
                                                'requerimiento'
                                            ).'")'
                                        )
                                        ->where('mq.estado', 1);
                                    } else if ( Input::get('requerimiento') ) {
                                        $query->whereRaw(
                                            'CONCAT(ems.req_tecnico,
                                                    "-",ems.req_horario
                                             )="'.Input::get(
                                                 'requerimiento'
                                             ).'"'
                                        )
                                        ->where('mq.estado', 1);
                                    }
                            }
                        )
                        ->groupBy('ems.motivo_id')
                        ->orderBy('m.nombre')
                        ->get();
            } else {
                $motivos =  DB::table('motivos AS m')
                            ->join(
                                'estado_motivo_submotivo AS ems',
                                'ems.motivo_id', '=', 'm.id'
                            )
                            ->select(
                                'm.id', 
                                'm.nombre'
                            )
                            ->where('m.estado', '=', '1')
                            ->where('ems.estado', '=', '1')
                            ->where(
                                function($query)
                                {
                                    if ( Input::get('requerimiento') && 
                                         Input::get('mas')
                                    ) {
                                        $query->whereRaw(
                                            'CONCAT(ems.req_tecnico,
                                                    "-",ems.req_horario
                                             ) 
                                            IN ("'.Input::get(
                                                'requerimiento'
                                            ).'")'
                                        );
                                    } else if ( Input::get('requerimiento') ) {
                                        $query->whereRaw(
                                            'CONCAT(ems.req_tecnico,
                                                    "-",ems.req_horario
                                             )="'.Input::get(
                                                 'requerimiento'
                                             ).'"'
                                        );
                                    }
                                }
                            )
                            ->groupBy('ems.motivo_id')
                            ->orderBy('m.nombre')
                            ->get();
            }

            return Response::json(array('rst' => 1, 'datos' => $motivos));
        }
    }

    public function postCargar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $motivos = Motivo::getMotivos();
            return Response::json(array('rst' => 1, 'datos' => $motivos));
        }
    }


    public function postCambiarestado()
    {
        if (Request::ajax()) {
            $motivoId = Input::get('id');
            $estado = Input::get('estado');
            Motivo::updateEstadoMotivo($motivoId, $estado);
            Estadomotivosubmotivo::updateEstadoPorMotivo($motivoId, $estado);
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
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre' => $required.'|'.$regex,
            );

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json(
                    array(
                    'rst'=>2,
                    'msj'=>$validator->messages(),
                    )
                );
            }

            $motivos = new Motivo;
            $motivos['nombre'] = Input::get('nombre');
            $motivos['estado'] = Input::get('estado');
            $motivos['usuario_created_at'] = Auth::user()->id;
            $motivos->save();

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro realizado correctamente',
                )
            );
        }
    }

    /**
     * Update the specified resource in storage.
     * POST /motivo/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if ( Request::ajax() ) {
            $motivoId = Input::get('id');
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre' => $required.'|'.$regex.'|unique:motivos,nombre,'.
                $motivoId,
            );

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json(
                    array(
                    'rst'=>2,
                    'msj'=>$validator->messages(),
                    )
                );
            }
            
            $motivos = Motivo::find($motivoId);
            $motivos['nombre'] = Input::get('nombre');
            $motivos['estado'] = Input::get('estado');
            $motivos['usuario_created_at'] = Auth::user()->id;
            $motivos->save();

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

}
