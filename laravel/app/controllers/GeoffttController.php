<?php

class GeoffttController extends \BaseController
{
    protected $_uploadController;
    protected $_errorController;
    protected $_catComponenteController;
    
    private $_geofftt;


    public function __construct(
            UploadController $uploadController, 
            ErrorController $errorController,
            CatComponenteController $catComponenteController
        )
    {
        $this->beforeFilter('auth'); // bloqueo de acceso
        $this->_uploadController = $uploadController;
        $this->_errorController = $errorController;
        $this->_catComponenteController = $catComponenteController;
        
        $this->_geofftt = new Geofftt();
    }

    public function postGeomdf() 
    {
        if (Input::get('mdf')) {
            $mdfArray = $this->_geofftt->getMdf(Input::get('mdf'));
            print_r($mdfArray);
        }
    }
    
    public function postGeonodo() 
    {
        if (Input::get('nodo')) {
            
        }
    }

    /**
     * Obtiene lista de celulas y tecnicos
     * 
     * @return type
     */
    public function postPanelcelulatecnico() 
    {
        if (Request::ajax()) {
            $filtroArray = array();

            //Actividad
            if (Input::get('actividad')) {
                $filtroArray['actividad'] = Input::get('actividad');
            }

            //Estado
            if (Input::get('estado')) {
                $filtroArray['estado'] = Input::get('estado');
            }

            //Empresa
            if (Input::get('empresa')) {
                $filtroArray['empresa'] = Input::get('empresa');
            }

            //Celula
            $celulaId = 0;
            if (Input::get('celula')) {
                $celulaArray = array();
                foreach (Input::get('celula') as $val) {
                    $valArray = explode("_", $val);
                    $celulaId = $valArray[1];
                    $celulaArray[] = $valArray[1];
                }

                $filtroArray['celula'] = $celulaArray;
            }

            if (Input::get('quiebre')) {
                $filtroArray['quiebre'] = Input::get('quiebre');
            }

            $visorgps = new Visorgps();

            //Celula tecnico
            $iconArray = array();
            $icons = $this->iconArray();
            $agenda["tecnicos"] = $visorgps->getCelulaTecnico($celulaId);

            foreach ($agenda["tecnicos"] as $key => $val) {
                //Ubicacion y bateria de tecnico
                $tecLocation = $visorgps->getTecLocations(
                    $val->carnet_tmp, date("Y-m-d")
                );
                if (!empty($tecLocation)) {
                    foreach ($tecLocation as $loc) {
                        $agenda["tecnicos"][$key]->x = $loc->X;
                        $agenda["tecnicos"][$key]->y = $loc->Y;
                        $agenda["tecnicos"][$key]->battery = $loc->Battery;
                        $agenda["tecnicos"][$key]->phone = $loc->MobileNumber;
                    }
                } else {
                    $agenda["tecnicos"][$key]->x = "";
                    $agenda["tecnicos"][$key]->y = "";
                    $agenda["tecnicos"][$key]->battery = "";
                    $agenda["tecnicos"][$key]->phone = "";
                }

                //Iconos
                shuffle($icons);
                $iconArray[$val->carnet_tmp] = array_pop($icons);
            }

            //Actuaciones gestionadas o temporales
            $lista["data"] = $visorgps->getBuscar($filtroArray);

            //XY de la actuacion
            $agenda["data"] = $this->getActuCoord($lista);

            //Iconos para agendas y tecnicos
            $agenda["icons"] = $iconArray;

            return json_encode($agenda);
        }
    }

    public function getActuCoord($agenda) 
    {

        foreach ($agenda["data"] as $key => $val) {
            $x = "";
            $y = "";

            /**
             * Primera condicion: Gestionadas (-catv-)
             * Segunda condicion: Temporales (_catv)
             */
            if (
                    strpos($val->tipoactu, "-catv-") !== false or
                    strpos($val->tipoactu, "_catv") !== false
            ) {
                //Tipo CATV -> fftt obtener xy del tap
                $ffttArray = explode("|", $val->fftt);

                /**
                 * las tablas temporales retornan 5 datos
                 */
                $eNodo = "";
                $eTroba = "";
                $eAmplificador = "";
                $eTap = "";

                if (isset($ffttArray[0])) {
                    $eNodo = $ffttArray[0];
                }
                if (isset($ffttArray[1])) {
                    $eTroba = $ffttArray[1];
                }
                if (isset($ffttArray[3])) {
                    $eAmplificador = $ffttArray[3];
                }
                if (isset($ffttArray[4])) {
                    $eTap = $ffttArray[4];
                }

                if (count($ffttArray) === 4 or count($ffttArray) === 5) {
                    $eTroba = $ffttArray[1];
                    $eAmplificador = $ffttArray[2];
                    $eTap = $ffttArray[3];
                }

               if (count($ffttArray) === 6) {
                    $eTroba = $ffttArray[1];
                    $eAmplificador = $ffttArray[3];
                    $eTap = $ffttArray[4];
               }

                if (ctype_digit($eAmplificador)) {
                    $eAmplificador = intval($eAmplificador);
                }
                if (ctype_digit($eTap)) {
                    $eTap = intval($eTap);
                }

                $tap = DB::table("geo_tap")
                        ->where('zonal', '=', "LIM")
                        ->where('nodo', '=', "$eNodo")
                        ->where('troba', '=', "$eTroba")
                        ->where('amplificador', '=', "$eAmplificador")
                        ->where('tap', '=', "$eTap")
                        ->get();

                if (!empty($tap)) {
                    $x = $tap[0]->coord_x;
                    $y = $tap[0]->coord_y;
                }
                $agenda["data"][$key]->x = $x;
                $agenda["data"][$key]->y = $y;
            } else {
                //Tipo BASICA / ADSL -> llave obtener xy del terminal
                $ffttArray = explode("|", $val->fftt);

                /**
                 * Algunas llaves tienen 4 datos
                 * MDF|ARMARIO|CABLE|TERMINAL
                 * Las tablas temporal de provision tiene este formato
                 */
                $eTer = "";
                if (isset($ffttArray[6])) {
                    $eTer = str_pad($ffttArray[6], 3, "0", STR_PAD_LEFT);
                }

                if (count($ffttArray) === 4) {
                    $eTer = str_pad($ffttArray[3], 3, "0", STR_PAD_LEFT);
                }

                $terminal = array();

                //Sin armario red directa, usa cable
                // CRU0||P/23|649||0|065|9 -> Directa
                // MAU2|A003|P/07|385|S/03|25|014|25 -> Flexible
                if (isset($ffttArray[1])) {
                    if (trim($ffttArray[1]) === "") {
                        /* $terminal = $GepTerminald->listar(
                          $db,
                          array(
                          "LIM",
                          $ffttArray[0],
                          $ffttArray[2],
                          $eTer
                          )
                          ); */
                        $terminal = DB::table("geo_terminald")
                                ->where('zonal', '=', "LIM")
                                ->where('mdf', '=', "{$ffttArray[0]}")
                                ->where('cable', '=', "{$ffttArray[2]}")
                                ->where('terminald', '=', "$eTer")
                                ->get();
                    } else {
                        //Con armario red flexible
                        /* $terminal = $GepTerminalf->listar(
                          $db,
                          array(
                          "LIM",
                          $ffttArray[0],
                          $ffttArray[1],
                          $eTer
                          )
                          ); */
                        $terminal = DB::table("geo_terminalf")
                                ->where('zonal', '=', "LIM")
                                ->where('mdf', '=', "{$ffttArray[0]}")
                                ->where('armario', '=', "{$ffttArray[2]}")
                                ->where('terminalf', '=', "$eTer")
                                ->get();
                    }
                }

                if (!empty($terminal)) {
                    $x = $terminal[0]->coord_x;
                    $y = $terminal[0]->coord_y;
                }
                $agenda["data"][$key]->x = $x;
                $agenda["data"][$key]->y = $y;
            }
            /*
              if($val["xr"]!='' and $val["yr"]!=''){
              $agenda["data"][$key]["x"] = $val["xr"];
              $agenda["data"][$key]["y"] = $val["yr"];
              }
             */
        }

        return $agenda["data"];
    }

    public function postCodepath() 
    {
        $code = Input::get('codePath');
        $date = Input::get('pdate');

        //Dia consultado
        $fecha = substr($date, 6, 4)
                . "-"
                . substr($date, 3, 2)
                . "-"
                . substr($date, 0, 2);

        //Solo dia actual
        $fecha = date("Y-m-d");

        $fromTime = $fecha . " 07:00:00";
        $toTime = $fecha . " 22:00:00";
        //$path = $Location->getPath($db, $fecha, $code, $fromTime, $toTime);

        $visorgps = new Visorgps();
        $path = $visorgps->getPath($fecha, $code, $fromTime, $toTime);

        echo json_encode($path);
    }

    private function doIconList() 
    {
        $folder = "./img/icons/visorgps/";

        if ($od = opendir($folder)) {
            while (false !== ($file = readdir($od))) {
                if ($file != "." && $file != "..") {
                    $this->_iconList[] = $file;
                }
            }
            closedir($od);
        }

        return $this->_iconList;
    }

    public function iconArray() 
    {
        $iconArray = array();

        $list = $this->doIconList();
        foreach ($list as $val) {
            $part = explode("_", $val);
            $iconArray[substr($part[1], 0, 6)]["tec"] = "tec_" . $part[1];
            $iconArray[substr($part[1], 0, 6)]["cal"] = "cal_" . $part[1];
            $iconArray[substr($part[1], 0, 6)]["car"] = "car_" . $part[1];
        }

        return $iconArray;
    }

    public function postSaveplan() 
    {        
        if (Request::ajax()) {
            if (Input::get('savePlanAll') != '') {
                $post = Input::get('savePlanAll');

                //Resultado del proceso
                $saveActu = array();

                //Todos los datos
                $dataArray = explode("|^~", $post);

                /**
                 * $genArray[0]: empresa_id
                 * $genArray[1]: celula_id
                 * $genArray[2]: tecnico id
                 * $genArray[3]: fecha agenda: dd/mm/yyyy
                 */
                $genArray = explode("|", $dataArray[0]);
                $actuArray = explode("|^", $dataArray[1]);

                foreach ($actuArray as $key => $val) {
                    
                    $dbCommit = true;
                    DB::beginTransaction();
                    
                    /**
                     * $data[0]: 'Averia' o 'Provision'
                     * $data[1]: codigo (requerimiento o averia)
                     * $data[2]: id_gestion (''=temporal, num=gestionada)
                     * $data[3]: horario_id
                     * $data[4]: 1=coordinado,0=no coordinado
                     * $data[5]: Latitud
                     * $data[6]: Longitud
                     */
                    $data = explode("|", $val);
                    
                    /**
                     * Validar si temporal cambio a gestionado
                     */
                    if (trim($data[2])=='' or trim($data[2])==0)
                    {
                        $arrTmpGes = DB::table('ultimos_movimientos')
                                 ->where('codactu', $data[1])
                                 ->first();
                        $arrTmpGes = Helpers::stdToArray($arrTmpGes);
                        
                        if (count($arrTmpGes)>0 and isset($arrTmpGes['gestion_id']))
                        {
                            //ID de gestion
                            $data[2] = $arrTmpGes['gestion_id'];
                            $data[4] = $arrTmpGes['coordinado'];
                        }
                    }

                    $date = substr($genArray[3], 6, 4) . "-"
                            . substr($genArray[3], 3, 2) . "-"
                            . substr($genArray[3], 0, 2);
                    $dia_agenda = date('N', strtotime($date));

                    $tablatmp = Config::get(
                        "wpsi.db.tmp_" . strtolower($data[0])
                    );

                    $tmpdata = array();
                    //$actividad_id = 0;
                    $tmptable = new Tmptable();

                    $celula = explode("_", $genArray[1]);
                    
                    //Origen Temporal
                    if ($data[0] == "Averia") {
                        //$actividad_id = 1;
                        $tmpdata = $tmptable->getAveria($data[1]);
                    }
                    if ($data[0] == "Provision") {
                        //$actividad_id = 2;
                        $tmpdata = $tmptable->getProvision($data[1]);
                    }
                    
                    $actividad = DB::table('actividades')
                        ->where('nombre', $data[0])
                        ->first();
                    
                    //Horario
                    $horario = DB::table('horarios')
                        ->where('id', $data[3])
                        ->first();
                    
                    //Tecnico
                    $objTecnico = DB::table('tecnicos')
                        ->where('id', $genArray[2])
                        ->first();
                    
                    //Componentes
                    $arrComponentes = array();
                    $cmp = Helpers::ruta(
                        'cat_componente/cargar', 
                        'POST', 
                        array('codactu'=>$data[1]), 
                        false
                    );
                    $cmp = Helpers::stdToArray($cmp);

                    if ($cmp["rst"]==1 and  count($cmp["datos"]) > 0)
                    {
                        foreach ($cmp["datos"] as $val) {
                            $arrComponentes[] = $val["nombre"];
                        }
                    }
                    
                    //Respuesta GestionMovimiento
                    $rgm = array();

                    if ($data[2] == 0) {

                        if (isset($tmpdata[0])) {
                            //Temporal
                            $savedata = $tmpdata[0];
                            $savedata->actividad = $data[0];
                            $savedata->actividad_id = $actividad->id;
                            $savedata->nombre_cliente_critico 
                                = $savedata->nombre_cliente;
                            $savedata->telefono_cliente_critico 
                                = $savedata->telefono;
                            $savedata->celular_cliente_critico 
                                = $savedata->telefono;

                            $quiebre = DB::table('quiebres')
                                    ->where('apocope', $savedata->quiebre)
                                    ->first();
                            $empresa = DB::table('empresas')
                                    ->where('nombre', $savedata->eecc_final)
                                    ->first();
                            $zonal = DB::table('zonales')
                                    ->where('abreviatura', $savedata->zonal)
                                    ->first();
                            $tecnico = DB::table('tecnicos')
                                    ->where('carnet_tmp', $savedata->zonal)
                                    ->first();

                            $savedata->quiebre_id = $quiebre->id;
                            $savedata->empresa_id = $genArray[0];
                            $savedata->zonal_id = $zonal->id;

                            //Motivo gestion
                            $savedata->estado = 2;
                            $savedata->motivo = 1;
                            $savedata->submotivo = 1;

                            $savedata->flag_tecnico = 1;
                            $savedata->horario_id = $data[3];
                            $savedata->dia_id = $dia_agenda;
                            $savedata->fecha_agenda = $date;
                            $savedata->celula = $celula[1];
                            $savedata->tecnico = $genArray[2];
                            $savedata->hora_agenda = $horario->horario;
                            $savedata->duration = 2;
                            $savedata->carnet = $objTecnico->carnet_tmp;

                            $savedata->velocidad = "";
                            $velocidad = explode("|", $savedata->paquete);
                            if (isset($velocidad[2])) {
                                $savedata->velocidad = $velocidad[2];
                            }
                            
                            $savedata->estado_agendamiento = '1-1';
                            $savedata->submodulo_id = 1;
                            
                            $savedata->y = $data[5];
                            $savedata->x = $data[6];
                            
                            $savedata->componente_text = $arrComponentes;
                            
                            //Grabar gestiÃƒÂ³n
                            $rgm = Helpers::ruta(
                                'gestion_movimiento/crear', 
                                'POST', 
                                Helpers::stdToArray($savedata), 
                                false
                            );
                            $rgm = Helpers::stdToArray($rgm);
                            
                            $saveActu[$data[0]][$data[1]] = $rgm;

                            /**
                             * Validar si se enviarÃƒÂ¡ a officetrack
                             */
                            $valida = array();
                            //Si cumple con el envio a OT acitividad + quiebre
                            $valida["actividad_id"] = $savedata->actividad_id;
                            $valida["quiebre_id"] = $savedata->quiebre_id;
                            //El estado del tecnico de officetrack
                            $valida["tecnico_id"] = $savedata->tecnico;
                            $valida["celula_id"] = $savedata->celula;
                            //El estado de Agendamiento para officetrack
                            $valida["motivo_id"] = 1;
                            $valida["submotivo_id"] = 1;
                            $valida["estado_id"] = 2;
                            //Si anteriormente se realizÃƒÂ³ una transacciÃƒÂ³n OT
                            $valida["transmision"] = 0;
                            
                            

                            $rvalida = Helpers::ruta(
                                'officetrack/validar', 
                                'POST', 
                                $valida, 
                                false
                            );

                            //Puede enviar a OT y GrabÃƒÂ³ en DB
                            if ($rvalida == "1" and $rgm['rst'] == "1")
                            {
                                //Recuperar ID de gestion
                                $savedata->gestion_id = $rgm['gestion_id'];
                        
                                $otdata["otdata"] 
                                        = Helpers::stdToArray($savedata);

                                $rot = Helpers::ruta(
                                    'officetrack/enviartarea', 
                                    'POST', 
                                    $otdata, 
                                    false
                                );
                                $rot = Helpers::stdToArray($rot);
                                
                                $saveActu[$data[0]][$data[1]]['officetrack'] 
                                        = $rot['officetrack'];

                            } else {                                
                                $saveActu[$data[0]][$data[1]]['officetrack'] 
                                        = "Envio no valido";
                                $dbCommit = false;
                                $rgm["msj"] = "Envio no valido";
                                $geserror = "";
                                if (isset($rgm['err'])){
                                    $geserror = base64_decode($rgm['err']);
                                }
                                $rgm["err"] = base64_encode(
                                                $rgm["msj"] 
                                                . $rvalida
                                                . $geserror
                                                );
                            }
                            unset($savedata);
                        }
                    } else if ($data[2] > 0) {
                        //Origen Gestionado
                        $geoplan = new Geoplan();
                        $ultimo = $geoplan->getUltimoMovimiento($data[2]);
                        $gesmov = $ultimo["datos"][0];

                        if (isset($tmpdata[0])) {
                            $savedata = Helpers::stdToArray($tmpdata[0]);
                        }
                        
                        $ultimov = DB::table('ultimos_movimientos')
                        ->where('gestion_id', $data[2])
                        ->first();
                        $savedata = Helpers::stdToArray($ultimov);

                        //Coordinado con cliente
                        if ($data[4]==1){
                            $horario = DB::table('horarios')
                                     ->where('id', $ultimov->horario_id)
                                     ->first();
                            $data[3] = $horario->id;
                            $dia_agenda = $ultimov->dia_id;
                            $date = $ultimov->fecha_agenda;                            
                        }
                        
                        $savedata["actividad"] = $data[0];
                        $savedata["actividad_id"] = $actividad->id;
                        $savedata["gestion_id"] = $data[2];
                        $savedata["empresa_id"] = $genArray[0];
                        $savedata["zonal_id"] = $gesmov->zonal_id;
                        $savedata["estado"] = 2;
                        $savedata["motivo"] = 1;
                        $savedata["submotivo"] = 1;
                        $savedata["observacion2"] = "";
                        $savedata["coordinado2"] = "";
                        $savedata["flag_tecnico"] = 1;
                        $savedata["horario_id"] = $data[3];
                        $savedata["dia_id"] = $dia_agenda;
                        $savedata["fecha_agenda"] = $date;
                        $savedata["celula"] = $celula[1];
                        $savedata["tecnico"] = $genArray[2];
                        $savedata["hora_agenda"] = $horario->horario;
                        $savedata["duration"] = 60;
                        $savedata["estado_agendamiento"] = '1-1';
                        $savedata["submodulo_id"] = 1;
                        
                        $savedata["y"] = $data[5];
                        $savedata["x"] = $data[6];
                        
                        $savedata["componente_text"] = $arrComponentes;
                        
                        //Datos faltantes para officetrack
                        if (!isset($savedata["quiebre"])) {
                            $quiebre = DB::table('quiebres')
                                    ->where('id', $savedata["quiebre_id"])
                                    ->first();
                            
                            $savedata["quiebre"] = $quiebre->apocope;
                        }
                        
                        if (!isset($savedata["eecc_final"])) {
                            $quiebre = DB::table('empresas')
                                    ->where('id', $savedata["empresa_id"])
                                    ->first();
                            
                            $savedata["eecc_final"] = $quiebre->nombre;
                        }
                        
                        if (!isset($savedata["cr_observacion"])) {                            
                            $savedata["cr_observacion"] = $savedata["observacion"];
                        }
                        
                        if (!isset($savedata["carnet"])) {                            
                            $savedata["carnet"] = $objTecnico->carnet_tmp;
                        }
                        
                        $savedata["velocidad"] = "";
                        if (isset($savedata["paquete"])) {
                            $velocidad = explode("|", $savedata["paquete"]);
                            if (isset($velocidad[2])) {
                                $savedata["velocidad"] = $velocidad[2];
                            }
                        }
                        
                        /**
                         * Validar si se enviarÃƒÂ¡ a officetrack
                         */
                        $valida = array();
                        //Si cumple con el envio a OT acitividad + quiebre
                        $valida["actividad_id"] = $actividad->id;
                        $valida["quiebre_id"] = $gesmov->quiebre_id;
                        //El estado del tecnico de officetrack
                        $valida["tecnico_id"] = $genArray[2];
                        $valida["celula_id"] = $celula[1];
                        //El estado de Agendamiento para officetrack
                        $valida["motivo_id"] = 1;
                        $valida["submotivo_id"] = 1;
                        $valida["estado_id"] = 2;
                        //Si anteriormente se realizÃƒÂ³ una transacciÃƒÂ³n OT
                        $valida["transmision"] = $gesmov->transmision;
                        

                        $rvalida = Helpers::ruta(
                            'officetrack/validar', 
                            'POST', 
                            $valida, 
                            false
                        );
                        
                        //Grabar gestiÃƒÂ³n
                        $rgm = Helpers::ruta(
                            'gestion_movimiento/crear', 
                            'POST', 
                            Helpers::stdToArray($savedata), 
                            false
                        );
                        $rgm = Helpers::stdToArray($rgm);
                        $saveActu[$data[0]][$data[1]] = $rgm;
                        
                        //Envia a OT y GrabÃƒÂ³ gestiÃƒÂ³n
                        if ($rvalida == "1" and $rgm['rst'] == "1") {
                            $otdata["otdata"] = $savedata;
                            
                            $rot = Helpers::ruta(
                                'officetrack/enviartarea', 
                                'POST', 
                                $otdata, 
                                false
                            );
                            $rot = Helpers::stdToArray($rot);
                            
                            $saveActu[$data[0]][$data[1]]["officetrack"] 
                                    = $rot['officetrack'];

                        } else {
                            $saveActu[$data[0]][$data[1]]["officetrack"] = 
                                    "Envio no valido";
                            $dbCommit = false;
                            $rgm["msj"] = "Envio no valido";
                            $geserror = "";
                            if (isset($rgm['err'])){
                                $geserror = base64_decode($rgm['err']);
                            }
                            $rgm["err"] = base64_encode(
                                                $rgm["msj"] 
                                                . $rvalida
                                                . $geserror
                                                );
                        }
                        unset($savedata);
                    }
                    
                    //Commit o Rollback
                    if ($dbCommit) {
                        DB::commit();
                    } else {
                        DB::rollback();
                        $custom["code"] = "R001";
                        $custom["file"] = __FILE__;
                        $custom["line"] = __LINE__;
                        $custom["message"] = base64_decode($rgm["err"]);
                        $custom["trace"] = "RollBack Geo Planificacion";
                        $custom["usuario_id"] = Auth::user()->id;
                        $custom["date"] = date("Y-m-d H:i:s");
                        $this->_errorController->saveCustomError($custom);
                        
                        $saveActu[$data[0]][$data[1]]["rst"] = 2;
                        $saveActu[$data[0]][$data[1]]["msj"] = 
                                "No se registra movimiento";
                    }
                    
                }

                return json_encode($saveActu);
            }
        }
    }

    public function postSendot() {
        $data['fecha_agenda'] = "2015-03-25";
        $data['hora_agenda'] = "16:00-18:00";
        $data["gestion_id"] = "2020";
        $data['codactu'] = "29341430";
        $data['fecha_registro'] = "2014-10-06 21:07:17";
        $data['nombre_cliente'] = "SOTO CAYSAHUANA JUSTA";
        $data['direccion_instalacion'] = "JR RIO MAJES 0, Piso:   Int:   Mzn: F Lt: 17";
        $data['actividad'] = "Averia";
        $data['codmotivo_req_catv'] = "AT|A422";
        $data['orden_trabajo'] = "20729456";
        $data['fftt'] = "CG|R013|09|06|04";
        $data['dir_terminal'] = "";
        $data['inscripcion'] = "1426372";
        $data['mdf'] = "CG";
        $data['segmento'] = "N";
        $data['clase_servicio_catv'] = "ESTANDAR";
        $data['total_averias'] = "";
        $data['zonal'] = "LIM";
        $data['llamadastec15dias'] = "";
        $data['quiebre'] = "DIGITALIZACION";
        $data['lejano'] = "LEJANO";
        $data['distrito'] = "SAN JUAN DE LURIGANCHO";
        $data['averia_m1'] = "";
        $data['telefono_codclientecms'] = "1426372";
        $data['area2'] = "PAI";
        $data['carnet'] = "80";
        $data['eecc_final'] = "LARI";
        $data['estado'] = "Agendado con TÃƒÂ©cnico";
        $data['cr_observacion'] = "ATENDER 2-4PM";
        $data['velocidad'] = "MOVISTAR SPEEDY 4M";
        $data['duration'] = 2;

        $savedata["otdata"] = $data;

        $do = Helpers::ruta(
                        'officetrack/enviartarea', 'POST', $savedata, false
        );
        print_r($do);
    }
    
    public function postHorariogeoplan(){
        $quiebre = Input::get('quiebre');
        $empresa = Input::get('empresa');        
        $zonal = DB::table('zonales')
                    ->where('abreviatura', Input::get('zonal'))
                    ->first();
        
        $horario = array();
        $geoplan = new Geoplan();
        $data = $geoplan->getPlanHorario($quiebre, $zonal->id, $empresa);
        
        foreach ($data["data"] as $val) {
            $horario[$val->quiebre][] = array(
                "horario_id"=>$val->horario_id,
                "horario"=>$val->horario,
            );
        }
        
        return json_encode($horario);
    }
    
    
    public function postUploadfile(){
        
        $visorgps = new Visorgps();
        
        $upload = $this->_uploadController->postCargartmp(1, 1);
        $data = json_decode($upload);
        $filtro["codactu"] = $data->data;
        $filtro["estado"] = array(-1);
        
        $estados = DB::table("estados")
                    ->where("estado", 1)
                    ->get(array("id"));
        foreach ($estados as $val) {
            $filtro["estado"][] = $val->id;
        }
        $filtro["estado"][] = -1;
        
        $filtro["actividad"] = explode(",", Input::get('actividad'));
        
        //$agenda = $visorgps->getBuscar($filtro);
        //print_r($filtro);
        
        $iconArray = array();
        $icons = $this->iconArray();
        
        $quiebreIdArray = array();
        $empresaIdArray = array();
        
        //Actuaciones gestionadas o temporales
        $lista["data"] = $visorgps->getBuscar($filtro);
        foreach ($lista["data"] as $key=>$val) {
            if (array_key_exists($val->carnet_tmp, $iconArray) === false ) {
                $icon = array_pop($icons);
                $iconArray[$val->carnet_tmp] = $icon["cal"];
                $lista["data"][$key]->icon = $icon["cal"];
            } else {
                $lista["data"][$key]->icon = $iconArray[$val->carnet_tmp];
            }
            
            //ID quiebre
            if (array_search($val->quiebre_id, $quiebreIdArray)===false) {
                $quiebreIdArray[] = $val->quiebre_id;
            }
            
            //ID empresa
            if (array_search($val->empresa_id, $empresaIdArray)===false) {
                $empresaIdArray[] = $val->empresa_id;
            }
        }

        //XY de la actuacion
        //$agenda["data"] = $this->getActuCoord($lista);
        $agenda["data"] = $lista["data"];

        //Iconos para agendas y tecnicos
        $agenda["icons"] = $iconArray;
        
        //Horarios por quiebre

        $geoplan = new Geoplan();
        $horario = array();
        $datah = $geoplan->getPlanHorario(
            implode(",", $quiebreIdArray), 
            Input::get('zonal'), 
            Input::get('empresa')
        );

        foreach ($datah["data"] as $valh) {
            $horario[$valh->quiebre][] = array(
                "horario_id"=>$valh->horario_id,
                "horario"=>$valh->horario,
            );
        }
        //$agenda["horario"] = $horario;
        
        $return = array(
            'upload' => $data->upload, 
            'data' => $data->data,
            'agenda' => $agenda,
            'horario' => $horario
        );
        
        return json_encode($return);
    }

}