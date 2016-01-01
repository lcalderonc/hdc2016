<?php

class ToaController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('auth'); // bloqueo de acceso
    }

    /**
     * Listar registro de actividades con estado 1
     * POST actividad/listar
     *
     * @return Response
     */
    public function postValidagestion()
    {
        if (Request::ajax()) {
            $permiso=Toa::ValidaPermiso();
            return Response::json(
                array(
                    'rst'=>1,
                    'permiso'=>$permiso
                )
            );
        }
    }

}
