<?php
class SubmoduloController extends \BaseController
{

    /**
     * Store a newly created resource in storage.
     * POST /submodulo/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $celulas =  DB::table('submodulos AS s')
                        ->join(
                            'modulos as m', 
                            's.modulo_id', '=', 'm.id'
                        )
                        ->select(
                            's.id',
                            's.nombre',
                            's.estado',
                            's.path',
                            's.modulo_id',
                            'm.nombre as modulo'
                        )
                        ->get();

            return Response::json(array('rst' => 1, 'datos' => $celulas));
        }
    }

    /**
     * Listar registro de celulas con estado 1
     * POST /submodulo/listar
     *
     * @return Response
     */
    public function postListar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            //SI ENVIO EL CODIGO DEL USUARIO
            //deberia buscar el menu y las submodulos asignadas a este usuario
            if (Input::has('usuario_id')) {
                $usuarioId = Input::get('usuario_id');

                $submodulos =  DB::table('submodulos')
                            ->select(
                                'id', 
                                'nombre', 
                                DB::raw(
                                    'CONCAT("M",modulo_id) as relation'
                                )
                            )
                            ->where('estado', '=', '1')
                            ->orderBy('nombre')
                            ->get();
            } elseif (Input::has('modulo_id')) {//Auth::user()->id
                //$perfilId = Session::get('perfilId');
                $usuario = Usuario::find(Auth::user()->id);
                $perfilId=$usuario['perfil_id'];
                $moduloId = Input::get('modulo_id');
                $submodulos = DB::table('submodulos as s')
                        ->leftjoin(
                            'submodulo_usuario as su', 
                            's.id', '=', 'su.submodulo_id'
                        )
                        ->select(
                            's.id',
                            's.nombre',
                            's.path'
                        )
                        ->where('modulo_id','=',$moduloId)
                        ->where('s.estado', '=', 1);
                        //->where('su.estado', '=', 1);
                if ($perfilId!=8) {
                    $submodulos=$submodulos->where('su.usuario_id', '=', Auth::user()->id);
                }
                        //->where('su.usuario_id', '=', Auth::user()->id)
                        $submodulos=$submodulos->groupby('s.id')
                        ->orderBy('s.nombre')
                        ->get();
            } else {
                $submodulos =  DB::table('submodulos')
                            ->select(
                                'id', 
                                'nombre', 
                                DB::raw(
                                    'CONCAT("M",modulo_id) as relation'
                                )
                            )
                            ->where('estado', '=', '1')
                            ->orderBy('nombre')
                            ->get();
            }
            

            return Response::json(array('rst' => 1, 'datos' => $submodulos));
        }
    }
    /**
     * Listar registro de celulas con estado 1
     * POST /submodulo/listarxtecnico
     *
     * @return Response
     */
    public function postListarxtecnico()
    {

        $tecnicoId = Input::get('tecnico_id');
        //si la peticion es ajax
        if (Request::ajax()) {

            $celulaQuiebre = DB::table('celula_tecnico as ct')
                ->rightJoin(
                    'celulas as c', function($join) use ($tecnicoId)
                    {
                    $join->on('ct.celula_id', '=', 'c.id')
                    ->on('ct.tecnico_id', '=', DB::raw($tecnicoId));
                    }
                )
                ->where('c.estado', '=', 1)
            ->get(array('c.id', 'c.nombre', 'ct.estado'));

            return Response::json(array('rst' => 1, 'datos' => $celulaQuiebre));
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /submodulo/crear
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
            $reglas = array(
                'nombre' => $required.'|'.$regex,
                'path' =>$regex.'|unique:submodulos,path,',
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

            $submodulos = new Submodulo();
            $submodulos['nombre'] = Input::get('nombre');
            $submodulos['estado'] = Input::get('estado');
            $submodulos['path'] = Input::get('path');
            $submodulos['modulo_id'] = Input::get('modulo_id');
            $submodulos->save();

            return Response::json(
                array(
                'rst' => 1,
                'msj' => 'Registro realizado correctamente',
                )
            );
        }
    }

    /**
     * Update the specified resource in storage.
     * POST /submodulo/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if (Request::ajax()) {
            $submoduloId = Input::get('id');
            //$regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $regex='regex:/^([a-zA-Z\/ .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre' => $required.'|'.$regex,
                'path' =>$regex.'|unique:submodulos,path,'.$submoduloId,
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
            $submodulos = Submodulo::find($submoduloId);
            $submodulos['nombre'] = Input::get('nombre');
            $submodulos['estado'] = Input::get('estado');
            $submodulos['path'] = Input::get('path');
            $submodulos['modulo_id'] = Input::get('modulo_id');
            $submodulos->save();

            //si estado de submodulo esta no activo
            if (Input::get('estado') == 0 ) {
                //actualizando a estado 0 segun quiebre seleccionado
                DB::table('submodulo_usuario')
                    ->where('submodulo_id', $submoduloId)
                    ->update(array('estado' => 0));
            }
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
     * POST /submodulo/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {
        if (Request::ajax()) {
            $estado = Input::get('estado');
            $submodulo = Submodulo::find(Input::get('id'));
            $submodulo->estado = Input::get('estado');
            $submodulo->save();

            if ($estado == 0) {
                DB::table('submodulo_usuario')
                        ->where('submodulo_id', Input::get('id'))
                        ->update(array('estado' => 0));
            }
            return Response::json(
                array(
                'rst' => 1,
                'msj' => 'Registro actualizado correctamente',
                )
            );
        }
    }
}
