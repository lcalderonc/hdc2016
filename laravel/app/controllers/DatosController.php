<?php
/**
 * Front Controller: Módulo DATOS
 */

class DatosController extends \BaseController
{
    protected $_geoffttController;
    protected $_listaController;

    public function __construct(
            GeoffttController $geoffttController,
            ListaController $listaController
        )
    {
        $this->beforeFilter('auth'); // bloqueo de acceso
        $this->_geoffttController = $geoffttController;
        $this->_listaController = $listaController;
    }
    
    public function postUpdatexylistatroba()
    {
        if (Request::ajax()) {
            if (Input::get('nodo')) {
                //Generar lista de trobas
                $data['nodo'] = Input::get('nodo');
                $response = Helpers::ruta(
                    'lista/troba', 
                    'POST', 
                    $data, 
                    true
                );
                
                //Obtener coordenadas del poligono NODO
                $geofftt = new Geofftt();
                $coords = $geofftt->getNodo($data['nodo']);
                
                $response = json_decode($response);
                $response->coords = $coords;
                
                return json_encode($response);
            }
            
            if (Input::get('mdf')) {
                //Generar lista de trobas
                $data['mdf'] = Input::get('mdf');
                
                $response['rst'] = 1;
                $response['datos'] = array();
                
                //Obtener coordenadas del poligono NODO
                $geofftt = new Geofftt();
                $coords = $geofftt->getMdf($data['mdf']);
                $response['coords'] = $coords;
                
                return json_encode($response);
            }
        }
    }
    
    public function postUpdatexylistaamp()
    {
        if (Request::ajax()) {
            if (Input::get('troba')) {
                //Generar lista de trobas
                $data['nodo'] = Input::get('nodo');
                $data['troba'] = Input::get('troba');
                $response = Helpers::ruta(
                    'lista/amplificador', 
                    'POST', 
                    $data, 
                    true
                );
                
                //Obtener coordenadas del poligono TROBA
                $geofftt = new Geofftt();
                $coords = $geofftt->getTroba($data);
                
                $response = json_decode($response);
                $response->coords = $coords;
                
                return json_encode($response);
            }
        }
    }
    
    public function postUpdatexylistaterminal()
    {
        if (Request::ajax()) {
            if (Input::get('mdf')) {
                
                $data['mdf'] = Input::get('mdf');
                
                if (Input::get('cable')) {
                    $data['cable'] = Input::get('cable');
                }
                
                if (Input::get('armario')) {
                    $data['armario'] = Input::get('armario');
                }
                $response = Helpers::ruta(
                    'lista/terminal', 
                    'POST', 
                    $data, 
                    true
                );
                
                
                //Obtener coordenadas del poligono ARMARIO
                if (isset($data['armario'])) {
                    $geofftt = new Geofftt();
                    $coords = $geofftt->getArmario($data);

                    $response = json_decode($response);
                    $response->coords = $coords;
                    
                    $response = json_encode($response);
                }
                
                return $response;
            }
        }
    }
    /**
     * datos/cargarcambiosdirecciones
     */
    public function postCargarcambiosdirecciones()
    {
        if (Request::ajax()) {
            return SubCambioDireccion::cargarCambiosDirecciones();
        }
    }
    /**
     * datos/actualizardirecciones
     */
    public function postActualizardirecciones()
    {
        if (Request::ajax()) {
            $regex='regex:/^([a-zA-Z01-9 .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
            $required='required';
            $numeric='numeric';
            $reglas = array(
                'nombrecliente' => $required.'|'.$regex,
                'direccion' => $required,
                'referencia' => $required,
                'latitud' => $required.'|'.$numeric,
                'longitud' => $required.'|'.$numeric,
                'validacion' => $required.'|'.$numeric,
            );
            $mensaje = array(
                'required'  => ':attribute Es requerido',
                'regex'     => ':attribute Solo debe ser Texto',
            );
            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ($validator->fails()) {
                return Response::json(
                    array(
                    'rst' => 2,
                    'msj' => $validator->messages(),
                    )
                );
            }
            return SubCambioDireccion::ActualizarDirecciones(Input::all());
        }
    }
    /**
     * datos/insertardirecciones
     */
    public function postInsertardirecciones()
    {
        if (Request::ajax()) {
            $regex='regex:/^([a-zA-Z01-9 .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
            $required='required';
            $numeric='numeric';
            $reglas = array(
                'nombrecliente' => $required.'|'.$regex,
                'direccion' => $required,
                'referencia' => $required,
                'latitud' => $required.'|'.$numeric,
                'longitud' => $required.'|'.$numeric,
                'validacion' => $required.'|'.$numeric,
            );
            $mensaje = array(
                'required'  => ':attribute Es requerido',
                'regex'     => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ($validator->fails()) {
                return Response::json(
                    array(
                    'rst' => 2,
                    'msj' => $validator->messages(),
                    )
                );
            }
            return SubCambioDireccion::InsertarDirecciones(Input::all());
        }
    }
    /**
     * datos/cambiarestado
     */
    public function postCambiarestado()
    {
        if (Request::ajax()) {
            if (Input::has('estado') && Input::has('id') ) {
                $estado = Input::get('estado');
                $id = Input::get('id');
                return  SubCambioDireccion::ActualizarEstado($id, $estado);
            } else {
                return Response::json(
                    array(
                    'rst' => 1,
                    'msj' => 'Registro actualizado correctamente',
                    )
                );
            }
        }
    }
}