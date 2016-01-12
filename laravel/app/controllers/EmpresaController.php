<?php

class EmpresaController extends \BaseController
{
    public function __construct() 
    {
        $this->beforeFilter('csrf_token', ['only' => ['postCrear', 'postEditar']]);
    }

    /**
     * Store a newly created resource in storage.
     * POST /empresa/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $empresas = DB::table('empresas')
                    ->select('id', 'nombre', 'estado', 'es_ec')
                    ->get();
            return Response::json(array('rst' => 1, 'datos' => $empresas));
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /empresa/listar
     *
     * @return Response
     */
    public function postListar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            if (Input::get('usuario') == '1') {
                $empresas = DB::table('empresas as e')
                        ->join(
                            'empresa_usuario as eu', function($join) {
                            $join->on('e.id', '=', 'eu.empresa_id')
                            ->where('eu.usuario_id', '=', Auth::user()->id)
                            ->where('eu.estado', '=', '1');
                            }
                        )
                        ->select(
                            'e.id', 'e.nombre', 'e.es_ec', DB::raw(
                                'IFNULL(eu.estado,"disabled") as block'
                            )
                        )
                        ->where('e.estado', '=', '1')
                        ->orderBy('e.nombre')
                        ->get();
            } elseif (Input::has('usuario_id')) {
//                $usuarioId = Input::get('usuario_id');
                $usuarioSesion = Auth::user()->id;
                //$perfilId = Session::get('perfilId');
                $usuario = Usuario::find(Auth::user()->id);
                $perfilId = $usuario['perfil_id'];
                /* $empresas = DB::table('empresas as e')
                  ->leftJoin(
                  'empresa_usuario as eu', function($join) use ($usuarioId)
                  {
                  $join->on('e.id', '=', 'eu.empresa_id')
                  ->on('eu.usuario_id', '=', DB::raw($usuarioId));
                  }
                  )
                  ->select('e.id', 'e.nombre', 'eu.estado')
                  ->where('e.estado', '=', 1)
                  ->get(); */
                $query = "SELECT e.id, e.nombre, 
                            IFNULL((SELECT estado 
                            FROM empresa_usuario 
                            WHERE usuario_id=? AND estado=1
                            AND empresa_id=e.id
                            GROUP BY empresa_id),0) AS estado
                        FROM empresas e ";

                if ($perfilId == '8') {//super user
                    $query.=" WHERE e.estado=1 ORDER BY e.nombre";
                    $arrParamEmpresas = array($usuarioSesion);
                } else { // $usuarioId
                    $query.=" JOIN empresa_usuario eu 
                            ON e.id=eu.empresa_id
                            WHERE e.estado=1 AND eu.estado=1 AND eu.usuario_id=?
                            ORDER BY e.nombre";
                $arrParamEmpresas = array($usuarioSesion, $usuarioSesion);
                } // $usuarioId, $usuarioSesion
                $empresas = DB::select($query, $arrParamEmpresas);
            } else {
                $empresas = DB::table('empresas as e')
                        ->join(
                            'empresa_usuario as eu', function($join) {
                            $join->on('e.id', '=', 'eu.empresa_id')
                            ->where('eu.usuario_id', '=', Auth::user()->id)
                            ->where('eu.estado', '=', '1');
                            }
                        )
                        ->select('e.id', 'e.nombre', 'eu.estado')
                        ->where('e.estado', '=', '1')
                        ->orderBy('e.nombre')
                        ->get();
            }
            return Response::json(
                array(
                        'rst' => 1, 
                        'datos' => $empresas, 
//                        'query' => $query
                    )
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /empresa/crear
     *
     * @return Response
     */
    public function postCrear()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $regex = 'regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required = 'required';
            $reglas = array(
                'nombre' => $required . '|' . $regex,
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

            $empresas = new Empresa;
            $empresas['nombre'] = Input::get('nombre');
            $empresas['es_ec'] = Input::get('es_ec');
            $empresas['estado'] = Input::get('estado');
            $empresas->save();

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
     * POST /empresa/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if (Request::ajax()) {
            $regex = 'regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required = 'required';
            $reglas = array(
                'nombre' => $required . '|' . $regex,
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

            $empresas = Empresa::find(Input::get('id'));
            $empresas['nombre'] = Input::get('nombre');
            $empresas['es_ec'] = Input::get('es_ec');
            $empresas['estado'] = Input::get('estado');
            $empresas->save();

            return Response::json(
                array(
                    'rst' => 1,
                    'msj' => 'Registro actualizado correctamente',
                )
            );
        }
    }

    /**
     * Changed the specified resource from storage.
     * POST /empresa/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {

        if (Request::ajax()) {

            $empresa = Empresa::find(Input::get('id'));
            $empresa->estado = Input::get('estado');
            $empresa->save();
            return Response::json(
                array(
                    'rst' => 1,
                    'msj' => 'Registro actualizado correctamente',
                )
            );
        }
    }

}
