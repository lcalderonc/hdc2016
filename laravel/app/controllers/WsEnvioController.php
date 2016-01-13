<?php

class WsEnvioController extends BaseController 
{
    public function postPaginacion()
    {
        if ( Request::ajax() ) {
            $count = 'SELECT COUNT(*) AS total
                      FROM webpsi_officetrack.wsenvios';
            
            $query = 'SELECT id, fecha, trama, response 
                      FROM webpsi_officetrack.wsenvios 
                      ORDER BY id asc 
                      LIMIT '.Input::get('start').', '.Input::get('length').'';
            
            $rcount= DB::select($count);
            $consulta = DB::select($query);
            
            //Asignado los números de Paginacion al Arreglo $wsenvios
            $wsenvios["draw"]=Input::get('draw');
            $wsenvios["recordsTotal"]=$rcount[0]->total;
            $wsenvios["recordsFiltered"]=$rcount[0]->total;
            
            //Asignado la data al Arreglo $wsenvios
            $wsenvios["data"] = $consulta;
            
            return Response::json($wsenvios);
        }
    }
}
