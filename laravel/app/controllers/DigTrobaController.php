<?php

class DigTrobaController extends BaseController
{

    protected $_errorController;
    /**
     * Valida sesion activa
     */
    public function __construct(ErrorController $ErrorController)
    {
        $this->beforeFilter('auth');
        $this->_errorController = $ErrorController;
    }

    /**
     * cargar areas, mantenimiento
     * POST /dig_troba/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $digTroba = DigTroba::get(Input::all());
            return Response::json(array('rst'=>1,'datos'=>$digTroba));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /dig_troba/listar
     *
     * @return Response
     */
    public function postListar()
    {
        if ( Request::ajax() ) {
            $digTroba = DigTroba::getTrobas();

            return Response::json(
                array(
                    'rst'   => 1,
                    'datos' => $digTroba
                )
            );
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /dig_troba/listarzonal
     *
     * @return Response
     */
    public function postListarzonal()
    {
        if ( Request::ajax() ) {
            $digTroba = DigTroba::getZonal();

            return Response::json(
                array(
                    'rst'   => 1,
                    'datos' => $digTroba
                )
            );
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /dig_troba/listarnodo
     *
     * @return Response
     */
    public function postListarnodo()
    {
        if ( Request::ajax() ) {
            $zonalId = Input::get('zonal_id');
            $digTroba = DigTroba::getNodo($zonalId);

            return Response::json(
                array(
                    'rst'   => 1,
                    'datos' => $digTroba
                )
            );
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /dig_troba/listartroba
     *
     * @return Response
     */
    public function postListartroba()
    {
        if ( Request::ajax() ) {
            $nodoId = Input::get('nodo_id');
            $digTroba = DigTroba::getTroba($nodoId);

            return Response::json(
                array(
                    'rst'   => 1,
                    'datos' => $digTroba
                )
            );
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /dig_troba/crear
     *
     * @return Response
     */
    public function postCrear()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $numeric='numeric';

            $reglas = array(
                'nodo_id' => $required,
                'troba_id' => $required,
                'zonal_id' => $required,
                'can_clientes' => $required.'|'.$numeric,
                'fecha_inicio' => $required,
                'fecha_fin' => $required,
                //'fecha_planificacion' => $required,
                'digitalizacion' => $required.'|'.$numeric,
                'empresa_id' => $required,
                'contrata_zona' => $required
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
            //buscar 
            $nodo = Input::get('nodo_id');
            $troba = Input::get('troba_id');
            $zonal = Input::get('zonal_id');

            $res = DB::table('geo_trobapunto')
                        ->select('id')
                        ->where('nodo', '=', $nodo)
                        ->where('zonal', '=', $zonal)
                        ->where('troba', '=', $troba)
                        ->first();
            
            if (!isset($res)) {
                return Response::json(
                    array(
                    'rst'=>0,
                    'msj'=>'No se ah encontrado troba',
                    )
                );
            }

            $digTrobaId = $res->id;
            $trobas=DB::table('dig_trobas')
                    ->select('id')
                    ->where('troba_id', $digTrobaId)
                    ->first();
            if (!isset($trobas)) {
                $digTrobas = new DigTroba;
                $digTrobas['troba_id'] = $digTrobaId;
                $digTrobas['usuario_created_at'] = Auth::user()->id;
            } else {
                $digTrobas = DigTroba::find($trobas->id);
                $digTrobas['usuario_updated_at'] = Auth::user()->id;
            }

            $digTrobas['can_clientes'] = Input::get('can_clientes');
            $digTrobas['fecha_inicio'] = Input::get('fecha_inicio');
            $digTrobas['fecha_fin'] = Input::get('fecha_fin');
            //$digTrobas['fecha_planificacion']=Input::get('fecha_planificacion');
            $digTrobas['digitalizacion'] = Input::get('digitalizacion');
            $digTrobas['est_seguim'] = Input::get('est_seguim');
            $digTrobas['empresa_id'] = Input::get('empresa_id');
            $digTrobas['contrata_zona'] = Input::get('contrata_zona');
            $digTrobas['obs'] = Input::get('obs');

            try {
                $digTrobas->save();

                return Response::json(
                    array(
                    'rst'=>1,
                    'msj'=>'Registro realizado correctamente',
                    )
                );
            } catch (Exception $exc) {
                $this->_errorController->saveError($exc);
                return Response::json(
                    array(
                        'rst'=>2,
                        'datos'=>'Error'
                    )
                );
            }
        }
    }

    /**
     * Update the specified resource in storage.
     * POST /dig_troba/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if ( Request::ajax() ) {
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $numeric='numeric';

            $reglas = array(
                'nodo_id' => $required,
                'troba_id' => $required,
                'zonal_id' => $required,
                'can_clientes' => $required.'|'.$numeric,
                'fecha_inicio' => $required,
                'fecha_fin' => $required,
                //'fecha_planificacion' => $required,
                'digitalizacion' => $required.'|'.$numeric,
                'empresa_id' => $required,
                'contrata_zona' => $required
            );

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
                'numeric'   => ':attribute seleccione una opcion',
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
            $digTrobaId = Input::get('id');
            $digTrobas = DigTroba::find($digTrobaId);
            $digTrobas['can_clientes'] = Input::get('can_clientes');
            $digTrobas['fecha_inicio'] = Input::get('fecha_inicio');
            $digTrobas['fecha_fin'] = Input::get('fecha_fin');
            //$digTrobas['fecha_planificacion']=Input::get('fecha_planificacion');
            $digTrobas['digitalizacion'] = Input::get('digitalizacion');
            $digTrobas['est_seguim'] = Input::get('est_seguim');
            $digTrobas['empresa_id'] = Input::get('empresa_id');
            $digTrobas['contrata_zona'] = Input::get('contrata_zona');
            $digTrobas['obs'] = Input::get('obs');
            $digTrobas['usuario_updated_at'] = Auth::user()->id;;

            try {
                $digTrobas->save();

                return Response::json(
                    array(
                    'rst'=>1,
                    'msj'=>'Registro actualizado correctamente',
                    )
                );
                    
            } catch (Exception $exc) {
                $this->_errorController->saveError($exc);
                return Response::json(
                    array(
                        'rst'=>2,
                        'datos'=>'Error'
                    )
                );
            }
        }
    }

        /**
     * Cambiar estado del registro de usuario, ello implica cambiar el estado de
     * la tabla empresa_usuario, quiebre_grupo_usuario, submodulo_usuario.
     * POST /usuario/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {
        if (Request::ajax()) {
            $digTrobas = DigTroba::find(Input::get('id'));
            $digTrobas['est_seguim'] = Input::get('estado');
            $digTrobas->save();

            return Response::json(
                array(
                'rst' => 1,
                'msj' => 'Registro actualizado correctamente',
                )
            );
        }
    }

}
