<?php

class HorarioTipoController extends \BaseController
{

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
            if (Input::has('empresa_id') ||
                Input::has('empresa_id') ||
                Input::has('zonal_id') || Input::has('quiebre_grupo_id')) {
                # code...
            
                $horariotipo = DB::table('horarios_tipo AS ht')
                            ->join(
                                'capacidad_horario AS ch', 
                                'ht.id', '=','ch.horario_tipo_id'
                            )
                            ->select('ht.id', 'ht.nombre AS nombre', 'ht.id AS evento')
                            ->where('ht.estado', '=', '1')
                            ->where('ch.estado', '=', '1')
                            ->where(
                                function($query)
                                {
                                    if ( Input::get('empresa_id') ) {
                                        $query->where(
                                            'ch.empresa_id', 
                                            '=', 
                                            Input::get('empresa_id')
                                        )
                                        ->where(
                                            'ch.zonal_id', 
                                            '=', 
                                            Input::get('zonal_id')
                                        )
                                        ->where(
                                            'ch.quiebre_grupo_id', 
                                            '=', 
                                            Input::get('quiebre_grupo_id')
                                        );
                                    }
                                }
                            )
                            ->groupBy('ht.id')
                            ->orderBy('ht.id')
                            ->get();
            } else {
                $horariotipo = DB::table('horarios_tipo AS ht')
                            /*->join(
                                'capacidad_horario AS ch', 
                                'ht.id', '=','ch.horario_tipo_id'
                            )*/
                            ->select('ht.id', 'ht.nombre AS nombre')
                            ->where('ht.estado', '=', '1')
                            //->where('ch.estado', '=', '1')
                            ->groupBy('ht.id')
                            ->orderBy('ht.id')
                            ->get();
            }
            return Response::json(array('rst'=>1,'datos'=>$horariotipo));
        }
    }
}
