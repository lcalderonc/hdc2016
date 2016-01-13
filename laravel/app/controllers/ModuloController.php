<?php

class ModuloController extends \BaseController
{
    public function __construct() 
    {
        $this->beforeFilter('csrf_token', ['only' => ['postCrear', 'postEditar']]);
    }
    
    /**
     * cargar modulos, mantenimiento
     * POST /modulo/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $modulos = DB::table('modulos')
                        ->select('id', 'nombre', 'path', 'estado')
                        ->get();
            return Response::json(array('rst'=>1,'datos'=>$modulos));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /modulo/listar
     *
     * @return Response
     */
    public function postListar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $usuarioId = Input::get('usuario_id');
            if ($usuarioId) {
                $modulos = DB::table('submodulo_usuario as su')
                        ->rightJoin(
                            'submodulos as s', function($join) use ($usuarioId)
                            {
                            $join->on('su.submodulo_id', '=', 's.id')
                            ->where('su.created_at', '=', $usuarioId);
                            }
                        )
                        ->rightJoin(
                            'modulos as m', 
                            's.modulo_id', '=', 'm.id'
                        )
                        ->select('m.nombre', DB::raw('MAX(su.estado) as estado'))
                        ->where('m.estado', '=', 1)
                        ->groupBy('m.nombre')
                        ->orderBy('m.nombre')
                        ->get();
            } else {
                $modulos = DB::table('modulos')
                            ->select('id', 'nombre', 'path')
                            ->where('estado', '=', '1')
                            ->orderBy('nombre')
                            ->get();
            }
            
            return Response::json(array('rst'=>1,'datos'=>$modulos));
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /modulo/crear
     *
     * @return Response
     */
    public function postCrear()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre' => $required.'|'.$regex,
                'path' =>$regex.'|unique:modulos,path,',
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

            $modulos = new Modulo;
            $modulos->nombre = Input::get('nombre');
            $modulos->path = Input::get('path');
            $modulos->estado = Input::get('estado');
            $modulos->save();

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
     * POST /modulo/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if ( Request::ajax() ) {
            $moduloId = Input::get('id');
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
                'nombre' => $required.'|'.$regex.'|unique:modulos,nombre,'.$moduloId,
                'path' =>$regex.'|unique:modulos,path,'.$moduloId,
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
            
            $modulos = Modulo::find($moduloId);
            $modulos->nombre = Input::get('nombre');
            $modulos->path = Input::get('path');
            $modulos->estado = Input::get('estado');
            $modulos->save();
            if (Input::get('estado') == 0 ) {
                //actualizando a estado 0 segun
                DB::table('submodulos')
                    ->where('modulo_id', $moduloId)
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

    /**
     * Changed the specified resource from storage.
     * POST /modulo/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {

        if ( Request::ajax() ) {

            $modulo = Modulo::find(Input::get('id'));
            $modulo->estado = Input::get('estado');
            $modulo->save();
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );    

        }
    }

}
