<?php

class ActividadTipoController extends BaseController
{

    public function __construct(ErrorController $ErrorController){
        $this->error = $ErrorController;
    }

    public function postListar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $r = ActividadTipo::Listar();
            return Response::json(array('rst'=>1,'datos'=>$r));
        }
    }
}
