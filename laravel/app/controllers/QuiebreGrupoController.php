<?php

class QuiebreGrupoController extends \BaseController
{
    public function __construct() 
    {
        $this->beforeFilter('csrf_token', ['only' => ['postCrear', 'postEditar']]);
    }

    /**
     * Store a newly created resource in storage.
     * POST /quiebregrupo/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $quiebreGrupos = DB::table('quiebre_grupos')
                        ->select('id', 'nombre', 'estado')
                        ->get();
            return Response::json(array('rst'=>1,'datos'=>$quiebreGrupos));
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /quiebregrupo/listar
     *
     * @return Response
     */
    public function postListar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            if (  Input::has('usuario') ) {
                
                $usuarioId = Auth::user()->id;
                
                $quiebreGrupos=  DB::table('quiebre_grupos as qg')
                            ->leftJoin(
                                'quiebre_grupo_usuario as qgu', 
                                function($join) use ($usuarioId)
                                {
                                    $join->on(
                                        'qg.id',
                                        '=',
                                        'qgu.quiebre_grupo_id'
                                    )
                                    ->where(
                                        'qgu.usuario_id',
                                        '=',
                                        $usuarioId
                                    )
                                    ->where(
                                        'qgu.estado',
                                        '=',
                                        '1'
                                    );
                                }
                            )
                            ->select(
                                'qg.id',
                                'qg.nombre',
                                'qgu.estado'
                            )
                            ->where('qg.estado', '=', '1')
                            ->orderBy('qg.nombre')
                            ->get();
            } elseif ( Input::has('usuario_id') ) {
                $usuarioId = Input::get('usuario_id');
                $usuarioSesion= Auth::user()->id;
                //$perfilId = Session::get('perfilId');
                $usuario = Usuario::find(Auth::user()->id);
                $perfilId=$usuario['perfil_id'];
                $query = "SELECT g.id, g.nombre, 
                            (SELECT estado 
                            FROM quiebre_grupo_usuario 
                            WHERE usuario_id=? AND estado=1
                            AND quiebre_grupo_id=g.id
                            GROUP BY quiebre_grupo_id ) AS estado
                        FROM quiebre_grupos g ";

                if ($perfilId=='8') {//si es super user
                    $query.="WHERE g.estado=1 ORDER BY g.nombre";
                    $quiebreGrupos= DB::select(
                        $query,
                        array($usuarioId)
                    );
                } else {
                    $query.="JOIN quiebre_grupo_usuario qg
                            ON g.id=qg.quiebre_grupo_id
                            WHERE qg.estado=1 AND g.estado=1 AND qg.usuario_id=?
                            ORDER BY g.nombre";
                    $quiebreGrupos= DB::select(
                        $query,
                        array($usuarioId,$usuarioSesion)
                    );
                }
            } elseif( Input::has('quiebre_id') ) {
                $quiebreId = Input::get('quiebre_id');
                $quiebreGrupos = DB::table('quiebres as q')
                        ->rightJoin(
                            'quiebre_grupos as g', function($join) use ($quiebreId)
                            {
                            $join->on('q.quiebre_grupo_id', '=', 'g.id')
                            ->on('q.id', '=', DB::raw($quiebreId));
                            }
                        )
                        ->select('g.id', 'g.nombre', 'q.estado')
                        ->where('g.estado', '=', 1)
                        ->get();
            } else {
                $quiebreGrupos=  DB::table('quiebre_grupos')
                            ->select('id', 'nombre')
                            ->where('estado', '=', '1')
                            ->orderBy('nombre')
                            ->get();
            }
            return Response::json(array('rst'=>1,'datos'=>$quiebreGrupos));
        }
    }
    
    /**
     * Store a newly created resource in storage.
     * POST /quiebregrupo/crear
     *
     * @return Response
     */
    public function postCrear()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $regex='regex:/^([a-zA-Z01-9 .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
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

            $quiebreGrupos = new QuiebreGrupo;
            $quiebreGrupos->nombre = Input::get('nombre');
            $quiebreGrupos->estado = Input::get('estado');
            $quiebreGrupos->save();

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
     * POST /quiebregrupo/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if ( Request::ajax() ) {
            $regex='regex:/^([a-zA-Z01-9 .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
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
            
            $quiebreGrupos = QuiebreGrupo::find(Input::get('id'));
            $quiebreGrupos->nombre = Input::get('nombre');
            $quiebreGrupos->estado = Input::get('estado');
            $quiebreGrupos->save();            

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
     * POST /quiebregrupo/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {

        if ( Request::ajax() ) {

            $quiebreGrupo = QuiebreGrupo::find(Input::get('id'));
            $quiebreGrupo->estado = Input::get('estado');
            $quiebreGrupo->save();
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );    

        }
    }

}
