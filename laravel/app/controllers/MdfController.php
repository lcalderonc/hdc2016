<?php
class MdfController extends \BaseController
{
    /**
     * Recepciona datos de Bandeja Controller
     * 
     * @return type
     */
    public function postListar()
    {

        if ( Input::get('modulo')!=null ) {
            $zonal = Input::get('zonal', 'LIM');
            $m =Mdf::getMdfzonal($zonal);
        } else {
             if ( Input::get('bandeja')!=null ) {
                 $m =Mdf::getMdfAll();
             } else {
                 $zonal = Input::get('zonal', 'LIM');
                 $m=array();
                 if ( Input::get('tipo')=='rutina-catv-pais' ) {
                     $m =Mdf::getMdfCatv($zonal);
                 } else {
                     $m =Mdf::getMdfs($zonal);
                 }
             }
        }
     
             if ( Request::ajax() ) {
                 return Response::json(
                     array(
                         'rst' => 1, 
                         'datos' => $m
                     )
                 );
             } 
        
    }

    public function postFiltrarcoord()
    {
        $id = Input::get('nodo');
        $tipo= Input::get('tipo');

        if ( $tipo[0]=='mdf' ) {
           $m =Mdf::getCoordMdf($id);
        }
        if ( $tipo[0]=='nodo' ) {
           $m =Nodo::getCoordNodo($id);
        }

        if ( Request::ajax() ) {
            return Response::json(
                array(
                    'rst' => 1, 
                    'datos' => $m,
                    'seleccionado' => $id
                )
            );
        } 
        
    }


}

