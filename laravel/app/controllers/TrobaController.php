<?php
class TrobaController extends \BaseController
{
    public function postListar(){
        $r=array();
        if ( Input::has('usuario') ) {
            $r =Geofftt::getTrobaUsu();
        }
        else{
            $r =Geofftt::getTroba(array());
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

