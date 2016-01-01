<?php
class NodoController extends \BaseController
{
    public function postListar()
    {

        if ( Input::get('modulo')!=null ) {
            $zonal = Input::get('zonal', 'LIM');
            $r =Nodo::getNodozonal($zonal);

        } else {
            $r =Nodo::getNodoAll();
        }

        if ( Request::ajax() ) {
            return Response::json(
                array(
                    'rst' => 1, 
                    'datos' => $r
                )
            );
        }

    }


}

