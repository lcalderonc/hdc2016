<?php

class HistoricoController extends BaseController
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
     * retornar reporte en formato xls
     * POST historico/buscacliente
     *
     * @return file xls
     */
    public function postBuscacliente()
    {
        //recibir los parametros y enviarlos al modelo, ahi ejecutar el query
        $telefono=$codcliatis=$codsercms=$codclicms='';
        if (Input::has('telefonoCliente'))
            $telefono = Input::get('telefonoCliente');
        if (Input::has('codigoClienteATIS'))
            $codcliatis = Input::get('codigoClienteATIS');
        if (Input::has('codigoServicioCMS'))
            $codsercms = Input::get('codigoServicioCMS');
        if (Input::has('codigoClienteCMS'))
            $codclicms = Input::get('codigoClienteCMS');
        
        //consulto la base de datos
        try {
            $arrcliente=Historico::getCliente(
                $telefono,
                $codcliatis,
                $codsercms,
                $codclicms
            );
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            $msj ='Ocurrió una interrupción en el registro del movimiento';
            return  array(
                'rst'=>0,
                'datos'=>$msj
            );
        }

        if (count($arrcliente)==0) {// No hay clientes 
            $msj="No se encontraron coincidencias segun criterio de busqueda.";
            $arrcliente[0]["encontrado"] = 0;
            return Response::json(
                array(
                    'rst'=>0,
                    'datos'=>$msj
                )
            );
        }
        try {
            $posibleCritico = Historico::esPosibleCritico("fono", $telefono);
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            $msj='Ocurrió una interrupción en el registro del movimiento';
            return  array(
                'rst'=>0,
                'datos'=>$msj
            );
        }
        $arrPosibleCritico = array("posibleCritico", $posibleCritico);
        foreach ($arrcliente as $key => $value) {
            $value->posibleCritico = $posibleCritico;
            $value->encontrado = 1;
            break;
        }
        
        return Response::json(
            array(
                'rst'=>1,
                'datos'=>$arrcliente
            )
        );
    }
    /**
     * retornar reporte en formato xls
     * POST historico/listaraverias
     *
     * @return json
     */
    public function postListaraverias()
    {
        $telefono=$codsercms=$codclicms=$esCritico='';
        if (Input::has('telefonoCliente'))
            $telefono = Input::get('telefonoCliente');
        if (Input::has('codigoServicioCMS'))
            $codsercms = Input::get('codigoServicioCMS');
        if (Input::has('codigoClienteCMS'))
            $codclicms = Input::get('codigoClienteCMS');
        if (Input::has('esCritico'))
            $esCritico = Input::get('esCritico');
        //consultas del archivo listarAverias.php
        
        $arrTotal = array();
        $arrTotalD = array();
        $arrTotalT = array();
        $arrTotalC = array();
        try {
            $arrTba = Historico::getAveriasTbaPendientes("fono", $telefono);
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            return array(
                    'rst'=>0,
                    'datos'=>'Error',
                    'arrTba'=>'',
                    'arrAdslPen'=>'',
                    'arrCatvPen'=>''
                    );
        }
        try {
            $arrAdslPen = Historico::getAveriasAdslPendientes("fono", $telefono);
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            return array(
                    'rst'=>0,
                    'datos'=>'Error',
                    'arrTba'=>'',
                    'arrAdslPen'=>'',
                    'arrCatvPen'=>''
                    );
        }
        try {
            $arrCatvPen = Historico::getAveriasCatvPendientes("cliente", $codclicms);
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            return array(
                    'rst'=>0,
                    'datos'=>'Error',
                    'arrTba'=>'',
                    'arrAdslPen'=>'',
                    'arrCatvPen'=>''
                    );
        }
        if (substr($telefono, 0, 1)=="1") {
            try {
                $arrTbaLiq = Historico::getAveriasTbaLiquidadasLima("fono", $telefono);
            } catch (Exception $exc) {
                $this->_errorController->saveError($exc);
                return array(
                        'rst'=>0,
                        'datos'=>'Error',
                        'arrTba'=>'',
                        'arrAdslPen'=>'',
                        'arrCatvPen'=>''
                        );
            }
            if (count($arrTbaLiq)>0) {
                foreach ($arrTbaLiq as $filaTbaLiq) {
                    $arrTotalD["tipo"] = "TBA";
                    $arrTotalD["averia"] = $filaTbaLiq->averia;
                    $arrTotalD["fecha_registro"] = $filaTbaLiq->fecha_reporte;
                    $arrTotalD["estado"] = "Liquidada";
                    $arrTotalD["fecha_liquidacion"] = $filaTbaLiq->fecha_de_liquidacion;
                    array_push($arrTotal, $arrTotalD);
                }
            }
        } else {
            try {
                $arrTbaLiq = Historico::getAveriasTbaLiquidadasProvincia("fono", $telefono);
            } catch (Exception $exc) {
                $this->_errorController->saveError($exc);
                return array(
                        'rst'=>0,
                        'datos'=>'Error',
                        'arrTba'=>'',
                        'arrAdslPen'=>'',
                        'arrCatvPen'=>''
                        );
            }
            
            if (count($arrTbaLiq)>0) {
                foreach ($arrTbaLiq as $filaTbaLiq) {
                    $arrTotalD["tipo"] = "TBA";
                    $arrTotalD["averia"] = $filaTbaLiq->averia;
                    $arrTotalD["fecha_registro"] = $filaTbaLiq->fecha_registro;
                    $arrTotalD["estado"] = "Liquidada";
                    $arrTotalD["fecha_liquidacion"] = $filaTbaLiq->fecha_de_liquidacion;
                    array_push($arrTotal, $arrTotalD);
                }
            }
        }
        try {
            $arrAdslLiq = Historico::getAveriasAdslLiquidadas("fono", $telefono);
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            return array(
                    'rst'=>0,
                    'datos'=>'Error',
                    'arrTba'=>'',
                    'arrAdslPen'=>'',
                    'arrCatvPen'=>''
                    );
        }
        
        if (count($arrAdslLiq)>0) {
            foreach ($arrAdslLiq as $filaAdslLiq) {
                $arrTotalT["tipo"] = "ADSL";
                $arrTotalT["averia"] = $filaAdslLiq->averia;
                $arrTotalT["fecha_registro"] = $filaAdslLiq->fecha_registro;
                $arrTotalT["estado"] = "Liquidada";
                $arrTotalT["fecha_liquidacion"] = $filaAdslLiq->fecha_liquidacion;
                array_push($arrTotal, $arrTotalT);
            }
        }
        try {
            $arrCatvLiq = Historico::getAveriasCatvLiquidadas("codServicio", $codsercms);
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            return array(
                    'rst'=>0,
                    'datos'=>'Error',
                    'arrTba'=>'',
                    'arrAdslPen'=>'',
                    'arrCatvPen'=>''
                    );
        }
        
        if (count($arrCatvLiq)>0) {
            foreach ($arrCatvLiq as $filaCatvLiq) {
                $arrTotalC["tipo"] = "CATV";
                $arrTotalC["averia"] = $filaCatvLiq->averia;
                $arrTotalC["fecha_registro"] = $filaCatvLiq->fecharegistro;
                $arrTotalC["estado"] = "Liquidada";
                $arrTotalC["fecha_liquidacion"] = $filaCatvLiq->fecha_liquidacion;
                array_push($arrTotal, $arrTotalC);
            }
        }
        usort($arrTotal, function($a, $b)
        {
            return strcmp($a["fecha_liquidacion"], $b["fecha_liquidacion"])*-1;
        });
        //$arrcliente='';
        return Response::json(
            array(
                'rst'=>1,
                'datos'=>$arrTotal,
                'arrTba'=>$arrTba,
                'arrAdslPen'=>$arrAdslPen,
                'arrCatvPen'=>$arrCatvPen
            )
        );
    }
    /**
     * retornar reporte en formato xls
     * POST historico/listarprovision
     *
     * @return json
     */
    public function postListarprovision()
    {
        $telefono = Input::get('telefonoCliente');

        //getRegistroAtis
        try {
            $reporte=Historico::getRegistroAtis("fono", $telefono);
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            return array(
                'rst'=>0,
                'datos'=>'Error'
            );
        }
        
        if (count($reporte)>0 && is_array($reporte)) {
            $rst=1;
        } else
            $rst=0;
        return Response::json(
            array(
                'rst'=>$rst,
                'datos'=>$reporte
            )
        );
    }
    /**
     * retornar reporte en formato xls
     * POST historico/listarllamadas
     *
     * @return json
     */
    public function postListarllamadas()
    {
        $telefono = Input::get('telefonoCliente');
        //getLlamadasCliente
        try {
            $reporte=Historico::getLlamadasCliente("fono", $telefono);
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            return array(
                'rst'=>0,
                'datos'=>'Error'
            );
        }
        
        if (count($reporte)>0 && is_array($reporte)) {
            $rst=1;
        } else
            $rst=0;
        return Response::json(
            array(
                'rst'=>$rst,
                'datos'=>$reporte
            )
        );
    }
    /**
     * retornar reporte en formato xls
     * POST historico/listarcriticos
     *
     * @return json
     */
    public function postListarcriticos()
    {
        $telefono = Input::get('telefonoCliente');
        //getListadoCriticosCobre
        try {
            $reporte=Historico::getListadoCriticosCobre("fono", $telefono);
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            return array(
                'rst'=>0,
                'datos'=>'Error'
            );
        }
        
        if (count($reporte)>0 && is_array($reporte)) {
            $rst=1;
        } else
            $rst=0;
        return Response::json(
            array(
                'rst'=>$rst,
                'datos'=>$reporte
            )
        );
    }
    /**
     * retornar reporte en formato xls
     * POST historico/listaraveriadetalle
     *
     * @return json
     */
    public function postListaraveriadetalle()
    {
        $tipo = Input::get('tipo');
        $negocio = Input::get('negocio');
        $actuacion = Input::get('actuacion');

        $reporte='';
        if ($tipo=="averia" ) {
            switch ($negocio) {
                case 'CATV-LIQ':
                    try {
                        $reporte= Historico::getAveriasCatvLiquidadas($tipo, $actuacion);
                    } catch (Exception $exc) {
                        $this->_errorController->saveError($exc);
                        return array(
                            'rst'=>0,
                            'datos'=>'',
                            'tipo' => ''
                        );
                    }
                    break;
                case 'TBA-LIQ':
                    try {
                        $reporte= Historico::getAveriasTbaLiquidadasLima($tipo, $actuacion);
                    } catch (Exception $exc) {
                        $this->_errorController->saveError($exc);
                        return array(
                            'rst'=>0,
                            'datos'=>'',
                            'tipo' => ''
                        );
                    }
                    break;
                case 'ADSL-LIQ':
                    try {
                        $reporte= Historico::getAveriasAdslLiquidadas($tipo, $actuacion);
                    } catch (Exception $exc) {
                        $this->_errorController->saveError($exc);
                        return array(
                            'rst'=>0,
                            'datos'=>'',
                            'tipo' => ''
                        );
                    }
                    break;
                case 'ADSL-PEN':
                    $reporte = null;
                    try {
                        $reporte = Historico::getAveriasAdslPendientes($tipo, $actuacion);
                    } catch (Exception $exc) {
                        $this->_errorController->saveError($exc);
                        return array(
                            'rst'=>0,
                            'datos'=>'',
                            'tipo' => ''
                        );
                    }
                    break;
                default:
                    $reporte='';
                    break;
            }
        }
        if (count($reporte)>0 && is_array($reporte)) {
            $rst=1;
        } else
            $rst=0;
        return Response::json(
            array(
                'rst'=>$rst,
                'datos'=>$reporte,
                'tipo' => $tipo
            )
        );
    }
}
