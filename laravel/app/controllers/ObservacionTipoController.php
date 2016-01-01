<?php
class ObservacionTipoController extends \BaseController
{
    /**
     * Recepciona datos de Bandeja Controller
     * 
     * @return type
     */
    public function postListar(){
        $m=array();
        $m=  DB::table('observaciones_tipos')
            ->select('id', 'nombre')
            ->where('estado', '=', '1')
            ->get();
        

        if ( Request::ajax() ) {
            return Response::json(
                array(
                    'rst' => 1, 
                    'datos' => $m
                )
            );
        }

    }

}

