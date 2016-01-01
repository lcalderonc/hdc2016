<?php
class CatComponenteController extends \BaseController
{

    /**
     * Store a newly created resource in storage.
     * POST /celula/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $r=array();
            if( trim(Input::get('gestion_id'))!='' ){
            $r =  DB::table('componente_gestion AS cg')
                    ->join(
                        'cat_componentes AS cp',
                        'cp.cod_componente','=','cg.componente_id'
                    )
                    ->select(
                        'cp.cod_componente AS id',
                        'cp.desc_componente AS nombre'
                    )
                    ->where(
                        'cg.gestion_id', '=', Input::get('gestion_id')
                    )
                    ->orderBy('cp.desc_componente')
                    ->get();
            }
            
            if( count($r)==0 ){
                $r =  DB::table('cat_componentes AS cp')
                    ->join(
                        'schedulle_sistemas.prov_pen_catv_pais_componentes AS pp', 
                        'cp.cod_componente', '=', 'pp.codigo_de_componente'
                    )
                    ->select(
                        'cp.cod_componente AS id',
                        'cp.desc_componente AS nombre'
                    )
                    ->where(
                        'pp.codigo_req', '=', Input::get('codactu')
                    )
                    ->orderBy('cp.desc_componente')
                    ->get();
            }
            

            return Response::json(array('rst' => 1, 'datos' => $r));
        }
    }

    /**
     * Listar registro de celulas con estado 1
     * POST /celula/listar
     *
     * @return Response
     */
    public function postListar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $r =  DB::table('cat_componentes AS cp')
                    ->select(
                        DB::raw('MAX(cp.cod_componente) AS id'),
                        'cp.desc_componente AS nombre'
                    )
                    ->groupBy('cp.desc_componente')
                    ->orderBy('cp.desc_componente')
                    ->get();

            return Response::json(array('rst' => 1, 'datos' => $r));
        }
    }
}
