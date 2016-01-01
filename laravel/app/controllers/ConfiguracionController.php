<?php

class ConfiguracionController extends BaseController
{
    /**
     * Listar registro de configuraciones con estado 1
     * POST /configuracion/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        if (Request::ajax()) {
            $configuracion =  DB::table('config')
                                ->select(
                                    'id',
                                    'nombre',
                                    'descripcion',
                                    'estado'
                                )
                                ->get();

            return Response::json(array('rst' => 1, 'datos' => $configuracion));
        }
    }

    public function postCrear()
    {
        if(Request::ajax()){
            $regex='regex:/^([a-zA-Z01-9 .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre' => $required.'|'.$regex,
                'descripcion' => $regex
            );

            $mensaje = array(
                'required'  => ':attribute Es requerido',
                'regex'     => ':attribute Solo debe ser Texto',
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

            $configuracion = new Configuracion();
            $configuracion['nombre']        = Input::get('nombre');
            $configuracion['estado']        = Input::get('estado');
            $configuracion['descripcion']   = Input::get('descripcion');
            $configuracion->save();

            return Response::json(
                array(
                    'rst' => 1,
                    'msj' => 'Registro realizado correctamente',
                )
            );
        }
    }

    public function postEditar()
    {
        if (Request::ajax()) {
            $configuracionId = Input::get('id');
            $regex = 'regex:/^([a-zA-Z01-9 .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
            $required = 'required';
            $reglas = array(
                'nombre' => $required . '|' . $regex,
                'descripcion' => $regex,
            );
            $mensaje = array(
                'required' => ':attribute Es requerido',
                'regex' => ':attribute Solo debe ser Texto',
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
            $configuracion = Configuracion::find($configuracionId);
            $configuracion['nombre']        = Input::get('nombre');
            $configuracion['estado']        = Input::get('estado');
            $configuracion['descripcion']   = Input::get('descripcion');
            $configuracion->save();

            return Response::json(
                array(
                    'rst' => 1,
                    'msj' => 'Registro actualizado correctamente',
                )
            );
        }

    }

    public function postCambiarestado()
    {
        if (Request::ajax()) {
            $estado = Input::get('estado');
            $configuracion = Configuracion::find(Input::get('id'));
            $configuracion->estado = $estado;
            $configuracion->save();
            return Response::json(
                array(
                    'rst' => 1,
                    'msj' => 'Registro actualizado correctamente',
                )
            );
        }
    }

    public function postCargaractivos()
    {
        if (Request::ajax()) {
            $configuracion =  DB::table('config')
                                ->select(
                                    'id',
                                    'nombre',
                                    'descripcion',
                                    'estado'
                                )
                                ->where('estado',1)
                                ->get();

            return Response::json(
                    array(
                        'rst' => 1,
                        'datos' => $configuracion
                    )
            );
        }
    }

    public function postCargarrelaciones()
    {
        if(Request::ajax())
        {

            $configuracionTabla = DB::table('config_tablas as ct')
                                    ->select('ct.tabla','cv.config_tabla_id','ct.id','cv.estado')
                                    ->leftJoin('config_valores as cv', function($join)
                                    {
                                        $join->on('ct.id', '=', 'cv.config_tabla_id');
                                    })
                                    ->where('ct.config_id',Input::get('id'))
                                    ->get();
            return Response::json(
                    array(
                        'rest' => 1,
                        'datos' => $configuracionTabla
                    )
            );
        }
    }

    public function postGetvalores()
    {
        $id = Input::get('id');
        $val = array();
        $valores = DB::table('config_tablas as ct')
            ->select('*')
            ->join('config_valores as cv', 'ct.id', '=', 'cv.config_tabla_id')
            ->where('cv.config_tabla_id', '=', $id)
            ->get();
        foreach ($valores as $valor) {
            $val = explode('|', $valor->valores);
            $controlador = $valor->controlador;
        }
        if(count($valores) === 0){
            $tablas =  DB::table('config_tablas')
                ->select(
                    'controlador'
                )
                ->where('id',$id)
                ->get();
            foreach($tablas as $table){
                $controlador = $table->controlador;
            }
        }
        return Response::json(
            array(
                'rst' => 1,
                'controlador'=>$controlador,
                'datos' => $val
            )
        );
    }

    public function postGuardarvalores()
    {
        try {
            if (Request::ajax()) {
                $validaciones = Input::all();
                $tablas = array();
                foreach (array_keys($validaciones) as $keys) {
                    if ($keys != 'idValidacion' && $keys != 'tipoQuery') {
                        $pos = stripos($keys, '_');
                        $table = substr($keys, $pos + 1);
                        $tablas[] = $table;
                    }
                }

                DB::beginTransaction();
                foreach ($tablas as $tabla) {
                    $id = $validaciones['idValidacion'][$tabla];
                    $query = $validaciones["tipoQuery"][$tabla];
                    $data = implode('|', $validaciones['slct_' . $tabla]);
                    if($query == 'updateActive') {
                        if(empty($data)){
                            $res = array('valores'=>$data, 'estado'=> 0);
                        }else{
                            $res = array('valores'=>$data);
                        }
                        DB::table('config_valores')
                            ->where('config_tabla_id', $id)
                            ->update($res);
                    }else if($query == 'updateInactive'){
                        DB::table('config_valores')
                            ->where('config_tabla_id', $id)
                            ->update(array('valores' => $data,'estado'=>1));
                    }elseif($query == 'new'){
                        $validacion =  DB::table('config_tablas')
                                        ->select(
                                            'id',
                                            'config_id',
                                            'relacion',
                                            'id_relacion',
                                            'relacion_temporal',
                                            'id_relacion_temporal'
                                        )
                                        ->where('id',$id)
                                        ->first();
                        DB::table('config_valores')->insert(
                            array('valores' => $data,
                                'estado' => 1,
                                'config_id'=>$validacion->config_id,
                                'config_tabla_id'=>$id,
                                'relacion'=> $validacion->relacion,
                                'campo'=>$validacion->id_relacion,
                                'relacion_temporal'=>$validacion->relacion_temporal,
                                'campo_temporal'=>$validacion->id_relacion_temporal)
                        );
                    }
                    DB::commit();
                }
                return Response::json(
                    array(
                        'rst'=>1,
                        'msg'=> "Validaciones actualizada",
                    )
                );
            }
        }catch (PDOException $error){
            DB::rollback();
            return Response::json(
                array(
                    'rst'=>0,
                    'msg'=> $error->getMessage()
                )
            );
        }
    }
}