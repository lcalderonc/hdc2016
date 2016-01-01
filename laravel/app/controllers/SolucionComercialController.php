<?php

class SolucionComercialController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('auth'); // bloqueo de acceso
    }

    public function postListar()
    {
        $solucion = DB::table('soluciones_comerciales')
                    ->select('id', 'nombre')
                    ->where('estado', '=', 1)
                    ->orderBy('nombre')
                    ->get();

        return     Response::json(
            array(
                'rst'=>1,
                'datos'=>$solucion
            )
        );
    }

}
