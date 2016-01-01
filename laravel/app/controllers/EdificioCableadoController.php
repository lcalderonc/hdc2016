<?php
class EdificioCableadoController extends \BaseController
{
    protected $_errorController;
    /**
     * Valida sesion activa
     */
    public function __construct(ErrorController $ErrorController)
    {
        $this->beforeFilter('auth');
        $this->_errorController = $ErrorController;
    }
    /**
     * edificio_cableado/edificios
     * obtener edificios
     */
    public function postListar()
    {
        if (Input::has('coord_x') && Input::has('coord_y') && Input::has('cantidad')) {
            
            $x =Input::get('coord_x');
            $y =Input::get('coord_y');
            $cant =Input::get('cantidad');

            try {
                $edificios=EdificioCableado::getEdificios( $x, $y, $cant );
            } catch (Exception $exc) {
                $this->_errorController->saveError($exc);
                $msj ='Ocurrió una interrupción en la busqueda';
                return  array(
                    'rst'=>0,
                    'datos'=>$msj
                );
            }

            return array('rst'=>1,'datos'=>$edificios);
        } else {
            return array('rst'=>0,'datos'=>'');
        }
    }
}