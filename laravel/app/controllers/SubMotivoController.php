<?php
class SubMotivoController extends \BaseController
{
    public function postListar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $subMotivos =  DB::table('submotivos AS s')
                        ->join(
                            'estado_motivo_submotivo AS ems',
                            'ems.submotivo_id', '=', 's.id'
                        )
                        ->select(
                            's.id', 
                            's.nombre', 
                            DB::raw(
                                'GROUP_CONCAT(
                                    DISTINCT(CONCAT("M",ems.motivo_id)) 
                                        SEPARATOR "|,|"
                                ) AS relation'
                            ),
                            's.id AS select'
                        )
                        ->where('s.estado', '=', '1')
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
                        ->groupBy('ems.submotivo_id')
                        ->orderBy('s.nombre')
                        ->get();

            return Response::json(array('rst' => 1, 'datos' => $subMotivos));
        }
    }

     public function postCargar()
     {
        //si la peticion es ajax
        if (Request::ajax()) {
            $submotivos = Submotivo::getSubmotivos();
            return Response::json(array('rst' => 1, 'datos' => $submotivos));
        }
     }


    public function postCambiarestado()
    {
        if (Request::ajax()) {
            $submotivoId = Input::get('id');
            $estado = Input::get('estado');
            Submotivo::updateEstadoSubmotivo($submotivoId, $estado);
            Estadomotivosubmotivo::updateEstadoPorSubmotivo(
                $submotivoId, $estado
            );
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
                'nombre2' => $required.'|'.$regex,
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

            $submotivos = new Submotivo;
            $submotivos['nombre'] = Input::get('nombre2');
            $submotivos['estado'] = Input::get('estado2');
            $submotivos['usuario_created_at'] = Auth::user()->id;
            $submotivos->save();

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
     * POST /submotivo/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if ( Request::ajax() ) {
            $submotivoId = Input::get('id2');
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre2' => $required.'|'.$regex.'|unique:submotivos,nombre,'.
                $submotivoId,
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
            
            $submotivos = Submotivo::find($submotivoId);
            $submotivos['nombre'] = Input::get('nombre2');
            $submotivos['estado'] = Input::get('estado2');
            $submotivos['usuario_created_at'] = Auth::user()->id;
            $submotivos->save();

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

}
