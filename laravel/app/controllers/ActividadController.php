<?php

class ActividadController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('auth'); // bloqueo de acceso
        $this->beforeFilter('csrf_token', ['only' => ['postCrear', 'postEditar']]);
    }

    /**
     * Listar registro de actividades con estado 1
     * POST actividad/listar
     *
     * @return Response
     */
    public function postListar()
    {
        if (Request::ajax()) {
            if (Input::get('quiebre_id')) {
                
                $quiebreId = Input::get('quiebre_id');
                $actividades = DB::table('actividad_quiebre as aq')
                        ->rightJoin(
                            'actividades as a', function($join) use ($quiebreId)
                            {
                            $join->on('aq.actividad_id', '=', 'a.id')
                            ->on('aq.quiebre_id', '=', DB::raw($quiebreId));
                            }
                        )
                        ->select('a.id', 'a.nombre', 'aq.estado')
                        ->where('a.estado', '=', 1)
                        ->get();
            } else {
                $actividades = DB::table('actividades')
                            ->select('id', 'nombre')
                            ->where('estado', '=', '1')
                            ->orderBy('nombre')
                            ->get();
            }
            return Response::json(
                array(
                    'rst'=>1,
                    'datos'=>$actividades
                )
            );
        }
    }

    public function postCargar()
    {
        if(Request::ajax()) {
            $actividades = Actividad::get();
            return Response::json(
                array(
                    'rst'=>1,
                    'datos'=>$actividades
                )
            );
        }
    }

    public function postCrear()
    {
        if(Request::ajax()){
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

            $actividades = new Actividad;
            $actividades->nombre = Input::get('nombre');
            $actividades->estado = Input::get('estado');
//            $actividades->usuario_created_at = Auth::user()->id;
            $actividades->save();

            return Response::json(
                array(
                    'rst'=>1,
                    'msj'=>'Registro realizado correctamente',
                )
            );
        }
    }

    public function postEditar()
    {
        if ( Request::ajax() ) {
            $areaId = Input::get('id');
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre' => $required.'|'.$regex.'|unique:actividades,nombre,'.$areaId,
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

            $actividades = Actividad::find($areaId);
            $actividades->nombre = Input::get('nombre');
            $actividades->estado = Input::get('estado');
//            $actividades->usuario_updated_at = Auth::user()->id;
            $actividades->save();

            return Response::json(
                array(
                    'rst'=>1,
                    'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

    public function postCambiarestado()
    {
        if ( Request::ajax() ) {

            $actividad = Actividad::find(Input::get('id'));
            $actividad->estado = Input::get('estado');
//            $actividad->usuario_updated_at = Auth::user()->id;
            $actividad->save();
            return Response::json(
                array(
                    'rst'=>1,
                    'msj'=>'Registro actualizado correctamente',
                )
            );

        }
    }

    public function postCargartipos()
    {
        if(Request::ajax()) {
            $actividadesTipo = ActividadTipo::get();
            return Response::json(
                array(
                    'rst'=>1,
                    'datos'=>$actividadesTipo
                )
            );
        }
    }

    public function postCreartipo()
    {
        if(Request::ajax()){
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $integer='integer';
            $reglas = array(
                'nombreTipo' => $required.'|'.$regex,
                'label' => $required.'|'.$regex,
                'sla' => $required.'|'.$integer,
                'duracion' => $required.'|numeric'
            );

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
                'integer'        => ':attribute Solo debe ser Numerico',
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

            $actividades = new ActividadTipo;
            $actividades->nombre = Input::get('nombreTipo');
            $actividades->label = Input::get('label');
            $actividades->actividad_id = Input::get('actividad');
            $actividades->sla = Input::get('sla');
            $actividades->duracion = Input::get('duracion');
            $actividades->estado = Input::get('estado');
            $actividades->usuario_created_at = Auth::user()->id;
            $actividades->save();

            return Response::json(
                array(
                    'rst'=>1,
                    'msj'=>'Registro realizado correctamente',
                )
            );
        }
    }

    public function postEditartipo()
    {
        if ( Request::ajax() ) {
            $actividadId = Input::get('id');
            $regex='regex:/^([a-zA-Z ._,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $integer = 'integer';
            $reglas = array(
                'nombreTipo' => $required.'|'.$regex.'|unique:actividades_tipos,nombre,'.$actividadId,
                'label' => $required.'|'.$regex,
                'sla' => $required.'|'.$integer,
                'duracion' => $required.'|numeric'
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
            $actividades = ActividadTipo::find($actividadId);
            $actividades->nombre = Input::get('nombreTipo');
            $actividades->actividad_id = Input::get('actividad');
            $actividades->label = Input::get('label');
            $actividades->sla = Input::get('sla');
            $actividades->duracion = floatval(Input::get('duracion'));
            $actividades->estado = Input::get('estado');
            $actividades->usuario_updated_at = Auth::user()->id;
            $actividades->save();

            return Response::json(
                array(
                    'rst'=>1,
                    'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

    public function postCambiarestadotipo()
    {
        if ( Request::ajax() ) {

            $actividad = ActividadTipo::find(Input::get('id'));
            $actividad->estado = Input::get('estado');
            $actividad->usuario_updated_at = Auth::user()->id;
            $actividad->save();
            return Response::json(
                array(
                    'rst'=>1,
                    'msj'=>'Registro actualizado correctamente',
                )
            );

        }
    }
}
