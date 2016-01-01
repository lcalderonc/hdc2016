<?php

class EstadoController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('auth'); // bloqueo de acceso
    }

    public function postListar()
    {
        if ( Request::ajax() ) {
            $estado =  DB::table('estados AS e')
                        ->join(
                            'estado_motivo_submotivo AS ems',
                            'ems.estado_id', '=', 'e.id'
                        )
                        ->select(
                            'e.id', 
                            'e.nombre', 
                            DB::raw(
                                'CONCAT(
                                    GROUP_CONCAT(
                                        DISTINCT(CONCAT("M",ems.motivo_id)) 
                                            SEPARATOR "|,|"
                                    ),"|,|",
                                    GROUP_CONCAT(
                                        DISTINCT(CONCAT("S",ems.submotivo_id)) 
                                            SEPARATOR "|,|"
                                    )
                                ) AS relation'
                            ),
                            DB::raw(
                                'GROUP_CONCAT(
                                    DISTINCT(CONCAT("M",ems.motivo_id,
                                                    "S",ems.submotivo_id,
                                                    "-",ems.req_tecnico,
                                                    "-",ems.req_horario
                                                    )
                                            ) 
                                        SEPARATOR "|,|"
                                ) AS evento'
                            )
                        )
                        ->where('e.estado', '=', '1')
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
                                        IN ("'.Input::get('requerimiento').'")'
                                    );
                                } else if ( Input::get('requerimiento') ) {
                                    $query->whereRaw(
                                        'CONCAT(ems.req_tecnico,
                                                "-",ems.req_horario
                                         )="'.Input::get('requerimiento').'"'
                                    );
                                }
                            }
                        )
                        ->groupBy('ems.estado_id')
                        ->orderBy('e.nombre')
                        ->get();
            return Response::json(
                array(
                    'rst'=>1,
                    'datos'=>$estado
                )
            );
        }
    }

    public function postValidar()
    {
        if ( Request::ajax() ) {
            $estado=array();
            if ( Input::get('estado_id')=='-1' ) {
                $estado=array(array('valida'=>'-1-0'));
            } else {
                $estado =   DB::table('estados AS e')
                            ->join(
                                'estado_motivo_submotivo AS ems',
                                'ems.estado_id', '=', 'e.id'
                            )
                            ->select(
                                DB::raw(
                                    'CONCAT(
                                        ems.req_tecnico,"-",ems.req_horario
                                    ) AS valida'
                                )
                            )
                            ->where('e.id', '=', Input::get('estado_id'))
                            ->get();
            }
            
            return Response::json(
                array(
                    'rst'=>1,
                    'datos'=>$estado
                )
            );
        }
    }

    public function postEstadoagendamiento()
    {
        $estado= Estado::getEstadoAgendamiento();
        return $estado;
    }

    public function postCargar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $estados = Estado::getEstados();
            return Response::json(array('rst' => 1, 'datos' => $estados));
        }
    }


    public function postCambiarestado()
    {
        if (Request::ajax()) {
            $estadoId = Input::get('id');
            $estado = Input::get('estado');
            Estado::updateEstadoEstado($estadoId, $estado);
            Estadomotivosubmotivo::updateEstadoPorEstado($estadoId, $estado);
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
                'nombre3' => $required.'|'.$regex,
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

            $estados = new Estado;
            $estados['nombre'] = Input::get('nombre3');
            $estados['estado'] = Input::get('estado3');
            $estados['usuario_created_at'] = Auth::user()->id;
            $estados->save();

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
     * POST /estado/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if ( Request::ajax() ) {
            $estadoId = Input::get('id3');
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre3' => $required.'|'.$regex.'|unique:estados,nombre,'.
                $estadoId,
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
            
            $estados = Estado::find($estadoId);
            $estados['nombre'] = Input::get('nombre3');
            $estados['estado'] = Input::get('estado3');
            $estados['usuario_created_at'] = Auth::user()->id;
            $estados->save();

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }
}
