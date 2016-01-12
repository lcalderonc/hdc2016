<?php

class EstadoOfscController extends BaseController
{

    public function __construct(ErrorController $ErrorController){
        $this->error = $ErrorController;
    }

    public function postListar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $r = EstadoOfsc::Listar();
            return Response::json(array('rst'=>1,'datos'=>$r));
        }
    }
}
