<?php
class CupoController extends \BaseController
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
     * Store a newly created resource in storage.
     * POST /cupo/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $cupos = Cupo::getCupos();

            return Response::json(array('rst' => 1, 'datos' => $cupos));
        }
    }

    /**
     * crear cupo cuando se esta guardando registros desde mantenimiento, LISTA
     * POST /cupo/crear
     *
     * @return Response
     */
    public function postCrear()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            //$regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $regex='regex:/^([a-zA-Z\/ .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
            $required='required';
            $numeric='numeric';
            $reglas = array(
                'capacidad' => $required.'|'.$numeric,
                'quiebregrupos' => $required,
                'empresa' => $required,
                'zonal' => $required,
                'dia' => $required,
                'horario' => $required,
            );

            $mensaje = array(
                'required'  => ':attribute Es requerido',
                'regex'     => ':attribute Solo debe ser Texto',
                'numeric'   => ':attribute valor numerico'
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ($validator->fails()) {
                return Response::json(
                    array(
                    'rst' => 2,
                    'msj' => $validator->messages(),
                    )
                );
            }
            $empresa = Input::get('empresa');
            $zonal = Input::get('zonal');
            $zonal = substr($zonal, -1);
            $quiebregrupos = Input::get('quiebregrupos');
            $horariotipo = Input::get('horariotipo');
            $horario = Input::get('horario');
            $dia = Input::get('dia');
            $capacidad = Input::get('capacidad');
            $estado = Input::get('estado');
            $id = Cupo::getIdCupo($empresa,$zonal,$quiebregrupos,$horariotipo);
            //si no existe $id, crear  registro en capacidad_horario
            if (count($id)==0)  {
                Cupo::createCupoHead($empresa,$zonal,$quiebregrupos,$horariotipo);
                //ahora que ya se creo buscar
                $id = Cupo::getIdCupo($empresa,$zonal,$quiebregrupos,$horariotipo);
            }
            Cupo::createCupo($id->id,$horario,$dia,$capacidad,$estado);

            return Response::json(
                array(
                'rst' => 1,
                'msj' => 'Registro actualizado correctamente',
                )
            );
        }
    }

    /**
     * Update the specified resource in storage.
     * POST /cupo/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if (Request::ajax()) {
            $cupoId = Input::get('id');
            //$regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $regex='regex:/^([a-zA-Z\/ .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
            $required='required';
            $numeric='numeric';
            $reglas = array(
                'capacidad' => $required.'|'.$numeric,
            );

            $mensaje = array(
                'required'  => ':attribute Es requerido',
                'regex'     => ':attribute Solo debe ser Texto',
                'numeric'   => ':attribute valor numerico'
            );
            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ($validator->fails()) {
                return Response::json(
                    array(
                    'rst' => 2,
                    'msj' => $validator->messages(),
                    )
                );
            }
            $capacidad = Input::get('capacidad');
            $estado = Input::get('estado');

            Cupo::updateCupo($cupoId,$capacidad,$estado);

            return Response::json(
                array(
                'rst' => 1,
                'msj' => 'Registro actualizado correctamente',
                )
            );
        }
    }

    /**
     * Cambiar estado del registro de celula, ello implica cambiar el estado de 
     * la tabla celula_quiebre.
     * POST /cupo/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {
        if (Request::ajax()) {
            $cupoId = Input::get('id');
            $estado = Input::get('estado');
            Cupo::updateEstadoCupo($cupoId,$estado);
            return Response::json(
                array(
                'rst' => 1,
                'msj' => 'Registro actualizado correctamente',
                )
            );
        }
    }
    /**
     * actualizar cupos
     * POST /cupo/updatecupos
     */
    public function postUpdatecupos()
    {
        if (Request::ajax()) {
            if (Input::has('data')) {
                $data = Input::get('data', '');
            } else {
                return Response::json(
                    array(
                    'rst' => 0,
                    'msj' => 'No se ha recibido datos para actualizar'
                    )
                );
            }
            $con='';
            DB::beginTransaction();
            foreach ($data as $key => $value) {
                $con=$value;
                //objeto cupo
                $capacidad=$value['capacidad'];
                if (!empty($value['id'])) {//si existe, entonces actualizar
                    $id=$value['id'];
                    try {
                        DB::table('capacidad_horario_detalle')
                           ->where('id', $id)
                           ->update(array('capacidad' => $capacidad));
                    } catch (Exception $exc) {
                        DB::rollback();
                        $this->_errorController->saveError($exc);
                        return Response::json(
                            array(
                                'rst'=>2,
                                'datos'=>'Error'
                            )
                        );
                    }
                } else {
                    $horario = $value['horario_id'];
                    $dia = $value['dia_id'];
                    
                    //validar si existe regis en  la tabla capacidad_horario
                    if (!empty($value['capacidad_horario_id'])) {
                        $capacidad_horario = $value['capacidad_horario_id'];
                    } else {
                        $empresa=$value['empresa_id'];
                        $zonal=$value['zonal_id'];
                        $quiebregrupos=$value['quiebre_grupo_id'];
                        $horariotipo=$value['horario_tipo_id'];
                        //buscar capacidad_horario
                        try {
                            $res=Cupo::getIdCupo($empresa,$zonal,$quiebregrupos,$horariotipo);
                        } catch (Exception $exc) {
                            DB::rollback();
                            $this->_errorController->saveError($exc);
                            return Response::json(
                                array(
                                    'rst'=>2,
                                    'datos'=>'Error'
                                )
                            );
                        }
                        //sino existe crear
                        if ( empty($capacidad_horario) ) {
                            try {
                                Cupo::createCupoHead($empresa,$zonal,$quiebregrupos,$horariotipo);
                            } catch (Exception $exc) {
                                DB::rollback();
                                $this->_errorController->saveError($exc);
                                return Response::json(
                                    array(
                                        'rst'=>2,
                                        'datos'=>'Error'
                                    )
                                );
                            }
                            try {
                                $res=Cupo::getIdCupo($empresa,$zonal,$quiebregrupos,$horariotipo);
                            } catch (Exception $exc) {
                                DB::rollback();
                                $this->_errorController->saveError($exc);
                                return Response::json(
                                    array(
                                        'rst'=>2,
                                        'datos'=>'Error'
                                    )
                                );
                            }
                            $capacidad_horario=$res->id;
                        } else {
                            $capacidad_horario=$res->id;
                        }
                    }
                    //$capacidad=$value['capacidad'];
                    try {
                        $return=Cupo::createCupo($capacidad_horario,$horario,$dia,$capacidad,1);
                    } catch (Exception $exc) {
                        DB::rollback();
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
            DB::commit();
            return Response::json(
                array(
                'rst' => 1,
                'msj' => 'Registro actualizado correctamente'
                )
            );
        }
    }
    /**
     * Cambiar estado del registro de celula, ello implica cambiar el estado de 
     * la tabla celula_quiebre.
     * POST /cupo/cambiarestado
     *
     * @return Response
     */
    public function postUpdate()
    {
        if (Request::ajax()) {
            $id=Input::get('id');
            $capacidad=Input::get('capacidad');

            DB::table('capacidad_horario_detalle')
           ->where('id', $id)
           ->update(array('capacidad' => $capacidad));
           $cupo = Cupo::getCupos($id);
            return Response::json(
                    array(
                    'rst' => 1,
                    'msj' => 'Registro actualizado correctamente',
                    'datos' => $cupo
                    )
                );
        }
    }
    /**
     * crear cupo cuando se esta guardando registros desde mantenimiento, TABLA
     * la tabla celula_quiebre.
     * POST /cupo/create
     *
     * @return Response
     */
    public function postCreate()
    {
        if (Request::ajax()) {/*
            $empresa = Input::get('empresa');
            $zonal = Input::get('zonal');
            $quiebregrupos = Input::get('quiebregrupos');
            $horariotipo = Input::get('horariotipo');
*/
            $horario = Input::get('horario_id');
            $dia = Input::get('dia_id');
            $capacidad_horario = Input::get('capacidad_horario_id');
            $capacidad=Input::get('capacidad');
            $return=Cupo::createCupo($capacidad_horario,$horario,$dia,$capacidad,1);
            //Cupo::createCupoHead($empresa,$zonal,$quiebregrupos,$horariotipo);
            //generar cupos segun el tipo de horario
            //si es tipo 240-> generar dos filas
            //si es 
            $cupo = Cupo::getCupos($return->id);

            return Response::json(
                    array(
                    'rst' => 1,
                    'msj' => 'Registro actualizado correctamente',
                    'datos' => $cupo,
                    )
                );
        }
    }
}
