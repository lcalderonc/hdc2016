<?php

class PublicController extends BaseController {	

    public function showAddress($codigo)
    {
        $ubicacion = DB::table('ubicaciones')->where('codigo', '=', $codigo)->first(array('nombre', 'descripcion', 'contacto', 'x', 'y', 'usuario_id', 'imagen'));
        
        //Si no se encuentran resultados
        if ( is_null( $ubicacion ) )
        {
            return View::make('ubicaciones.noaddress');
        } else {
            return View::make('ubicaciones.address', array('ubicacion' => $ubicacion));
        }
        
        
    }
}