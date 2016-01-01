<?php
ini_set('memory_limit', '1024M');
class GestionController extends BaseController
{

    public function __construct(ErrorController $ErrorController)
    {
        $this->beforeFilter('auth'); // bloqueo de acceso
        $this->_errorController = $ErrorController;
    }

    public function postCargar()
    {
        if ( Request::ajax() ) {
            return Response::json(  Gestion::getCargar() );
        }
    }
    /**
     * Buscar codigo en tablas temporales y las gestionadas
     */
    public function postValidacodigo()
    {
        if (Input::has('codactu')) {
            $codactu= Input::get('codactu');
            try {
               $row = Gestion::getRowgestiones($codactu);
            } catch (Exception $e) {
                $this->_errorController->saveError($exc);
                $msj='Ocurrió una interrupción en el registro del movimiento';
                return  array(
                    'rst'=>0,
                    'datos'=>$msj
                );
            }
            //si existe
            return Response::json(
                array('rst'=>$row)
            );
        }
        //vacio
        return Response::json(
            array('rst'=>0)
        );
    }
    
    /**
     * Actualiza coordenadas de una orden
     * @return type
     */
    public function postActualizaxy(){
        if ( Request::ajax() ) {
            $actu = Input::get('actu');
            $lat = Input::get('lat');
            $lng = Input::get('lng');
            
            $result = array();
            
            try {
                //Iniciar transaccion
                DB::beginTransaction();
                
                $gestionDetalle = GestionDetalle::Where(
                    'codactu',
                    $actu
                )->first();
                
                //Guardar direccion previa
                UltimoMovimiento::actualizar_direccion(
                    $lng, $lat, '', $gestionDetalle->gestion_id
                );
                                
                //Gestionada: gestiones_detalles
                DB::table('gestiones_detalles')
                    ->where('codactu', $actu)
                    ->update(
                        array('x' => $lng, 'y' => $lat)
                    );
                
                //Ultimos movimientos
                DB::table('ultimos_movimientos')
                    ->where('codactu', $actu)
                    ->update(
                        array('x' => $lng, 'y' => $lat)
                    );
                
                //Temporales
                DB::table(Config::get("wpsi.db.tmp_averia"))
                    ->where('averia', $actu)
                    ->update(
                        array('xcoord' => $lng, 'ycoord' => $lat)
                    );
                DB::table(Config::get("wpsi.db.tmp_provision"))
                    ->where('codigo_req', $actu)
                    ->update(
                        array('xcoord' => $lng, 'ycoord' => $lat)
                    );
                
                //INSERT en cambios_direcciones
                if( $gestionDetalle->gestion_id!= null ){
                    $sql = "INSERT INTO cambios_direcciones
                           (gestion_id, tipo_usuario, usuario_id,
                           coord_x, coord_y, direccion, referencia)
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $data = array(
                        $gestionDetalle->gestion_id, 'sys', Auth::user()->id,
                        $lng, $lat, '', ''
                    );
                    //DB::insert($sql, $data);
                }
                
                DB::commit();

                //Update OK
                $result['rst'] = 1;
                $result['msj'] = 'Coordenadas actualizadas correctamente';
            } catch (PDOException $exc) {
                DB::rollback();
                
                $this->_errorController->saveError($exc);
                $result['rst'] = 2;
                $result['msj'] = 'Error al actualizar coordenadas';
            }
            
            return json_encode($result);
        }
    }
}
