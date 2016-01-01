<?php

class ConsultaController extends BaseController
{

    /**
     * Listar registro de actividades con estado 1
     * POST actividad/listar
     *
     * @return Response
     */
    private $acceso;
    private $clave;
    private $hashg;
    private $fecha;

    public function __construct()
    {
        $this->acceso = "\$PSI20\$";
        $this->clave = "132756acac57eeec8564aa89cb0cedb7"; //"\$1st3m@\$" ;
        $this->fecha = Input::get('fecha');
        $this->hashg = Input::get('hashg');
    }

    public function postUltimomovimento()
    {
        if (!Request::ajax()) {
            $rf=array();
            foreach (Input::all() as $r=>$i) {
                $dato[$i]=$r;
            }

            $hash=hash('sha256',$this->acceso.$this->clave.$this->fecha);

            if($hash==$this->hashg){
                $r=Consulta::getUltimoMovimiento();
                echo json_encode($r);
            }
            else{
                echo "Error:0002";
            }
        }
        else{
            echo "Error:0001";
        }
    }

    public function postMovimientos()
    {
        if (!Request::ajax()) {
            $rf=array();
            foreach (Input::all() as $r=>$i) {
                $dato[$i]=$r;
            }

            $hash=hash('sha256',$this->acceso.$this->clave.$this->fecha);

            if($hash==$this->hashg){
                $r=Consulta::getMovimientos();
                echo json_encode($r);
            }
            else{
                echo "Error:0002";
            }
        }
        else{
            echo "Error:0001";
        }
    }

}
