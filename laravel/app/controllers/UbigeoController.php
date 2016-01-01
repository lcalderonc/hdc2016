<?php
class UbigeoController extends \BaseController
{
    /**
     * Recepciona datos de Bandeja Controller
     * 
     * @return type
     */
    public function postListar(){
        $u =Ubigeo::getUbigeo();

        if ( Request::ajax() ) {
            return Response::json(
                array(
                    'rst' => 1, 
                    'datos' => $u
                )
            );
        }
        /*else{
            return 
                array(
                    'rst' => 1, 
                    'datos' => $u
                );
        }*/

    }

}

