<?php

class SubmoduloUsuarioController extends \BaseController
{

    /**
     * consulta la lista de modulos y el estado de asignacion segun usuario
     * POST /submodulousuario/listar
     *
     * @param usuario_id
     * @return Response
     */
    public function postListar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {

            $usuarioId = Input::get('usuario_id');

            $submoduloUsuario = DB::table('submodulo_usuario as su')
                        ->rightJoin(
                            'submodulos as s', function($join) use ($usuarioId)
                            {
                            $join->on('su.submodulo_id', '=', 's.id')
                                ->on('su.usuario_id', '=', DB::raw($usuarioId));
                            }
                        )
                        ->rightJoin('modulos as m', 's.modulo_id', '=', 'm.id')
                        ->where('m.estado', '=', 1)
                        ->groupBy('m.nombre', 'm.id')
                        ->select(
                            'm.nombre', 'm.id',
                            DB::raw('MAX(su.estado) as estado')
                        )
                        ->get();
            return Response::json(array('rst'=>1,'datos'=>$submoduloUsuario));
        }
    }
}
