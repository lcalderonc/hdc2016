<?php
use Ofsc\Capacity;
use Ofsc\Activity;
use Ofsc\Inbound;

class EnviosOfscController extends  \BaseController
{

    /**
     * Registrar las acciones del webservice:capacity,activity,interface
     *
     */

    public function registrarEnviosOfsc($action, $setArray, $result)
    { 

        $resultaoArrayReq = json_encode($setArray); 
        $resultadoArrayResp = json_encode($result);

        if ($resultadoArrayResp == "{}") $resultadoArrayResp = "";

        if(isset(Auth::user()->id)) $usuario = Auth::user()->id; else $usuario = 0;

        $envios = new EnvioOfsc();
        $envios->registrarAccionWebservice(
            $action, $resultaoArrayReq,
            $resultadoArrayResp, $usuario
        );
    }

    public function postReporteofsc()
    {
   
        //si la peticion es ajax
        if (Request::ajax()) {

            $fechaIni = Input::get('fechaIni');
            $fechaFin = Input::get('fechaFin');            

            $query = "SELECT e.accion, e.enviado, e.respuesta ,
                        e.created_at, u.usuario
                      FROM envios_ofsc e
                        LEFT JOIN usuarios u 
                        ON u.id = e.usuario_created_at 
                        WHERE date(e.created_at) BETWEEN '".$fechaIni."' AND '".$fechaFin."'                      
                      ";
            $consulta = DB::select($query);   
            $envios["data"] = $consulta;                     
 
            return Response::json(array('rst' => 1, 'datos' => $consulta));
            // return Response::json($consulta);
        }
    
        /*
        if ( Request::ajax() ) {

            $fechaIni = Input::get('fechaIni');
            $fechaFin = Input::get('fechaFin');

            $count = "SELECT COUNT(*) AS total
                      FROM envios_ofsc
                      WHERE date(created_at) BETWEEN '".$fechaIni."' AND '".$fechaFin."'";
 
            $query = "SELECT e.id, e.accion, e.enviado, e.respuesta ,
                        e.created_at,IF(LENGTH(e.enviado) >50, 
                        CONCAT(SUBSTR(e.enviado,1, 53),'...'),e.enviado)
                        AS enviadoV, IF(LENGTH(e.respuesta) >50,
                        CONCAT(SUBSTR(respuesta,1, 53),'...'),e.respuesta)
                        AS respuestaV, u.usuario
                      FROM envios_ofsc e
                        LEFT JOIN usuarios u 
                        ON u.id = e.usuario_created_at 
                      WHERE date(e.created_at) BETWEEN '".$fechaIni."' AND '".$fechaFin."'
                      ";

            //ordenamiento por columnas
            if( Input::get('draw') ){
                if ( Input::get('order') ){
                    $inorder=Input::get('order');
                    $incolumns=Input::get('columns');
                    $query.=    ' ORDER BY '.
                                $incolumns[ $inorder[0]['column'] ]['name'].' '.
                                $inorder[0]['dir'];
                }
                $query.=' LIMIT '.Input::get('start').','.Input::get('length');

                $envios["draw"]=Input::get('draw');
            }
            
            $rcount= DB::select($count);
            $consulta = DB::select($query);
            
            //Asignado los números de Paginacion al Arreglo $envios
            $envios["draw"]=Input::get('draw');
            $envios["recordsTotal"]=$rcount[0]->total;
            $envios["recordsFiltered"]=$rcount[0]->total;
            
            //Asignado la data al Arreglo $envios
            $envios["data"] = $consulta;
            
            return Response::json($envios);
        }*/
    }

}//fin class
