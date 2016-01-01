<?php

class TramoController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('auth'); // bloqueo de acceso
    }
    
    public function postBuscar(){
        
        try {
            //Numeracion izquierda o derecha
            $izq = true;
            if ( Input::get("numero")%2 === 0 ) 
            {
                $izq = false;
            }
            
            $tramo = new Tramo();
            
            $data = $tramo->getTramos(
                    Input::get("distrito"), 
                    Input::get("calle"), 
                    Input::get("numero"), 
                    $izq
                );
            
            return json_encode( array('rst'=>1,'datos'=>$data) );
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
            
    }
}