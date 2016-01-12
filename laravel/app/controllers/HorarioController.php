<?php

class HorarioController extends \BaseController
{
    public function __construct() 
    {
        $this->beforeFilter('csrf_token', ['only' => ['postCrear', 'postEditar']]);
    }

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
            $horario=  DB::table('horarios as h')
                        ->select(
                            'h.id',
                            DB::raw('ht.id as horarios_tipo_id'),
                            'h.horario',
                            'h.hora_inicio',
                            'h.hora_fin',
                            'h.estado',
                            'ht.nombre as thorario',
                            'ht.estado as activo',
                            'h.horario_tipo_id as idthorario'
                        )
                        ->join(
                            'horarios_tipo as ht',
                            'h.horario_tipo_id',
                            '=',
                            'ht.id'
                        )                        
                        ->orderBy('h.horario', 'asc')
                        ->get();

            return Response::json(array('rst'=>1,'datos'=>$horario));
        }
    }
    /**
     * mostrar lista de quiebres officetrack, para la carga masiva
     * POST /quiebre/cargarofficetrack
     *
     * @return Response
     */

    /**
     * Store a newly created resource in storage.
     * POST /quiebre/listar
     *
     * @return Response
     */
    public function postListar()
    {
        //si la peticion es ajax
    /*$horario=DB::table("horarios_tipo")
                        ->where( 
                            function($query){
                                if ( Input::get('estado') ) {
                                    $query->where('estado','=','1');
                                }
                            }
                        )
                        ->get();
        return $horario;       */
            if (Request::ajax()) {
            $horario=  DB::table('horarios_tipo as ht')
                        ->where(
                            'ht.estado',
                            '=',
                            1
                        )                        
                        ->orderBy('ht.nombre', 'asc')
                        ->get();
                        //dd($horario);
            return Response::json(array('rst'=>1,'datos'=>$horario));
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
                /*'nombre' => $required.'|'.$regex,
                'apocope' => $regex,
                'quiebregrupos' => $required.'|'.$numeric,*/
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
            $horario = new Horario;
            $horario->horario = Input::get('horario');
            $horario->hora_inicio = Input::get('hora_inicio');
            $horario->hora_fin = Input::get('hora_fin');
            $horario->horario_tipo_id = Input::get('thorario');
            $horario->estado = Input::get('estado');
            $horario->save();

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
        if ( Request::ajax() ) {
            $horariosId= Input::get('id');
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $numeric='numeric';
            $reglas = array(
                //'nombre' => $required.'|'.$regex,
            );

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
                'numeric'   => ':attribute valor numerico'
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
            
            $horario = Horario::find($horariosId);
            $horario->horario = Input::get('horario');
            $horario->hora_inicio = Input::get('hora_inicio');
            $horario->hora_fin = Input::get('hora_fin');
            $horario->horario_tipo_id = Input::get('thorario');
            $horario->estado = Input::get('estado');
            $horario->save();

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
            $horario = Horario::find(Input::get('id'));
            $horario->estado = Input::get('estado');
            $horario->save();
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

}
