<?php

class PerfilController extends \BaseController
{

    /**
     * Store a newly created resource in storage.
     * POST /perfil/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            //$perfilId = Session::get('perfilId');
            $usuario = Usuario::find(Auth::user()->id);
            $perfilId=$usuario['perfil_id'];
            if ($perfilId==8) {
                $perfiles = DB::table('perfiles')
                        ->select('id', 'nombre', 'estado')
                        ->get();
            } else {
                $perfiles = DB::table('perfiles')
                        ->select('id', 'nombre', 'estado')
                        ->where('id','<>',8)
                        ->get();
            }
            return Response::json(array('rst'=>1,'datos'=>$perfiles));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /perfil/cargar
     *
     * @return Response
     */
    public function postListar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            //$perfilId = Session::get('perfilId');
            $usuario = Usuario::find(Auth::user()->id);
            $perfilId=$usuario['perfil_id'];
            if ($perfilId==8) {
                $perfiles = DB::table('perfiles')
                        ->select('id', 'nombre', 'estado')
                        ->get();
            } else {
                $perfiles = DB::table('perfiles')
                        ->select('id', 'nombre', 'estado')
                        ->where('id','<>',8)
                        ->get();
            }
            return Response::json(array('rst'=>1,'datos'=>$perfiles));
        }
    }
}
