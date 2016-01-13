<?php
class VisorgpsController extends \BaseController
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
     * Store a newly created resource in storage.
     * POST /quiebre/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $quiebres=  DB::table('quiebres')
                        ->select('id', 'nombre', 'estado', 'apocope')
                        ->orderBy('quiebres.nombre', 'asc')
                        ->get();

            return Response::json(array('rst'=>1,'datos'=>$quiebres));
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /quiebre/listar
     *
     * @return Response
     */
    public function postListar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            if ( Input::get('usuario')=='1' ) {
                $quiebres=  DB::table('quiebres as q')
                            ->leftJoin(
                                'quiebre_usuario as qu', 
                                function($join)
                                {
                                    $join->on(
                                        'q.id',
                                        '=',
                                        'qu.quiebre_id'
                                    )
                                    ->where(
                                        'qu.usuario_id',
                                        '=',
                                        Auth::user()->id
                                    );
                                }
                            )
                            ->select(
                                'q.id',
                                'q.nombre',
                                DB::raw(
                                    'IFNULL(qu.estado,"disabled") as block'
                                )
                            )
                            ->where('q.estado', '=', '1')
                            ->orderBy('q.nombre')
                            ->get();
            } else {
                $quiebres=  DB::table('quiebres')
                            ->select('id', 'nombre')
                            ->where('estado', '=', '1')
                            ->orderBy('nombre')
                            ->get();
            }

            return Response::json(array('rst'=>1,'datos'=>$quiebres));
        }
    }

    /**
     * Obtiene lista de celulas y tecnicos
     * 
     * @return type
     */
    public function postPanelcelulatecnico()
    {
        if ( Request::ajax() ) {
            $filtroArray = array();

            //Actividad
            if ( Input::get('actividad') ) {
                $filtroArray['actividad'] = Input::get('actividad');
            }

            //Estado
            if ( Input::get('estado') ) {
                $filtroArray['estado'] = Input::get('estado');
            }

            //Empresa
            if ( Input::get('empresa') ) {
                $filtroArray['empresa'] = Input::get('empresa');
            }

            //Fecha agenda
           /* if ( Input::get('fecha_agenda') ) {
                $agendaArray = explode("/", Input::get('fecha_agenda'));
                $fechaAgenda = $agendaArray[2]
                               . "-"
                               . $agendaArray[1]
                               . "-"
                               . $agendaArray[0];
                $filtroArray['fecha_agenda'] = $fechaAgenda;
            }*/
            if ( Input::get('fecha_agenda') ) {
                $fechaAgenda= explode(" - ", Input::get('fecha_agenda'));
                $filtroArray['fecha_agenda']=$fechaAgenda;
            }

            //Celula
            $celulaId = 0;
            if ( Input::get('celula') ) {
                $celulaArray = array();
                foreach (Input::get('celula') as $val) {
                    $valArray = explode("_", $val);
                    $celulaId = $valArray[1];
                    $celulaArray[] = $valArray[1];
                }
                
                $filtroArray['celula'] = $celulaArray;
            }

            if ( Input::get('quiebre') ) {
                $filtroArray['quiebre'] = Input::get('quiebre');
            }
            
            $visorgps = new Visorgps();
            
            //Celula tecnico
            $iconArray = array(); 
            $icons = $this->iconArray();
            $agenda["tecnicos"] = $visorgps->getCelulaTecnico($celulaId);
            
            foreach ($agenda["tecnicos"] as $key=>$val) {
                //Ubicacion y bateria de tecnico
                $tecLocation = $visorgps->getTecLocations(
                    $val->carnet_tmp, 
                    date("Y-m-d")
                );
                if ( !empty($tecLocation) ) {
                    foreach ($tecLocation as $loc) {
                        $agenda["tecnicos"][$key]->x = $loc->X;
                        $agenda["tecnicos"][$key]->y = $loc->Y;
                        $agenda["tecnicos"][$key]->battery = $loc->Battery;
                        $agenda["tecnicos"][$key]->phone = $loc->MobileNumber;
                        $agenda["tecnicos"][$key]->tiempo = $loc->tiempo;
                    }
                } else {
                    $agenda["tecnicos"][$key]->x = "";
                    $agenda["tecnicos"][$key]->y = "";
                    $agenda["tecnicos"][$key]->battery = "";
                    $agenda["tecnicos"][$key]->phone = "";
                    $agenda["tecnicos"][$key]->tiempo = "";
                }
                
                //Iconos
                if (empty($icons)){
                    $icons = $this->iconArray();
                }
                shuffle($icons);
                $iconArray[$val->carnet_tmp] = array_pop($icons);
                
                //Grupos
                $agenda["tecnicos"][$key]->grupo = "";
                $grupos = $visorgps->getTecnicoGrupo($celulaId, $val->id);
                if (isset($grupos["data"]) and count($grupos["data"])>0) {
                    $agenda["tecnicos"][$key]->grupo = $grupos["data"][0]
                                                        ->grupo;
                }
                
            }
            
            //Actuaciones gestionadas o temporales
            //$lista["data"] = $visorgps->getBuscar($filtroArray);
            $agenda["data"] = $visorgps->getBuscar($filtroArray);
            
            //XY de la actuacion
            //$agenda["data"] = $this->getActuCoord($lista);
            
            //Iconos para agendas y tecnicos
            $agenda["icons"] = $iconArray;
            
            //Horarios por quiebre
            $zonal = DB::table('zonales')
                    ->where('abreviatura', Input::get('zonal'))
                    ->first();
            
            $geoplan = new Geoplan();
            $horario = array();
            $data = $geoplan->getPlanHorario(
                implode(",", Input::get('quiebre')), 
                Input::get('zonal'), 
                implode(",", Input::get('empresa'))
            );
        
            foreach ($data["data"] as $val) {
                $horario[$val->quiebre][] = array(
                    "horario_id"=>$val->horario_id,
                    "horario"=>$val->horario,
                );
            }
            $agenda["horario"] = $horario;
                        
            return json_encode($agenda);
        }
    }
    
    /**
     * Retorna XY de FFTT
     * 
     * @param type $agenda
     * @return type
     * 
     * Formato $agenda
     * $agenda["data"][] = Object.array(tipoactu=>?, fftt=>?);
     * 
     */
    public function getActuCoord($agenda) 
    {
        
        foreach ($agenda["data"] as $key=>$val) {
            $x = "";
            $y = "";
            
            /**
             * Primera condicion: Gestionadas (-catv-)
             * Segunda condicion: Temporales (_catv)
             */
            if (
                    strpos($val->tipoactu, "-catv-") !== false  or 
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

                if ( count($ffttArray) === 5 ) {
                    $eTroba = $ffttArray[1];
                    $eAmplificador = $ffttArray[2];
                    $eTap = $ffttArray[3];
                }

                if (ctype_digit($eAmplificador) ) {
                    $eAmplificador = intval($eAmplificador);
                }
                if (ctype_digit($eTap) ) {
                    $eTap = intval($eTap);
                }
                
                $tap = DB::table("geo_tap")
                        ->where('zonal', '=', "LIM")
                        ->where('nodo', '=', "$eNodo")
                        ->where('troba', '=', "$eTroba")
                        ->where('amplificador', '=', "$eAmplificador")
                        ->where('tap', '=', "$eTap")
                        ->get();                
                
                if ( !empty( $tap ) ) {
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
                if ( isset( $ffttArray[6] ) ) {
                    $eTer = str_pad($ffttArray[6], 3, "0", STR_PAD_LEFT);
                }

                if ( count($ffttArray)===4 ) {
                    $eTer = str_pad($ffttArray[3], 3, "0", STR_PAD_LEFT);
                }

                $terminal = array();

                //Sin armario red directa, usa cable
                // CRU0||P/23|649||0|065|9 -> Directa
                // MAU2|A003|P/07|385|S/03|25|014|25 -> Flexible
                if ( isset( $ffttArray[1] ) ) {
                    if (trim($ffttArray[1])==="") {
                        /*$terminal = $GepTerminald->listar(
                                $db, 
                                array(
                                    "LIM",
                                    $ffttArray[0], 
                                    $ffttArray[2],
                                    $eTer
                                    )
                                );*/
                        $terminal = DB::table("geo_terminald")
                                    ->where('zonal', '=', "LIM")
                                    ->where('mdf', '=', "{$ffttArray[0]}")
                                    ->where('cable', '=', "{$ffttArray[2]}")
                                    ->where('terminald', '=', "$eTer")
                                    ->get();
                    } else {
                        //Con armario red flexible
                        /*$terminal = $GepTerminalf->listar(
                                $db, 
                                array(
                                    "LIM",
                                    $ffttArray[0], 
                                    $ffttArray[1],
                                    $eTer
                                    )
                                );*/
                        $terminal = DB::table("geo_terminalf")
                                    ->where('zonal', '=', "LIM")
                                    ->where('mdf', '=', "{$ffttArray[0]}")
                                    ->where('armario', '=', "{$ffttArray[2]}")
                                    ->where('terminalf', '=', "$eTer")
                                    ->get();
                    }
                }            

                if ( !empty( $terminal ) ) {
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
    
    
    public function postPrueba(){print_r(Input::all());
        $data = array(array("a"=>123, "b"=>456));
        return $data;
    }
    
    
    public function postListacelula(){
        $celula = array();
        
        try {
            $celula["data"] = DB::table('celulas')
                    ->where('empresa_id', '=', Input::get('empresa_id'))
                    ->where('estado', '=', 1)
                    ->get();
            return json_encode($celula);
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            return "Error";
        }
        
    }
    
    
    public function postSavegrupotecnico(){
        try {
            //Eliminar datos del tecnico y celula
            DB::table('celula_grupos')
                ->where('celula_id', '=', Input::get('celula_id'))
                ->where('tecnico_id', '=', Input::get('tecnico_id'))
                ->delete();
            
            //Grabar datos
            $grupoArray = explode(",", Input::get('grupos'));
            foreach ($grupoArray as $val) {
                if ($val > 0) {
                    DB::table('celula_grupos')->insert(
                        array(
                            'celula_id' => Input::get('celula_id'), 
                            'tecnico_id' => Input::get('tecnico_id'),
                            'grupo' => $val
                        )
                    );
                }
            }
            
            return json_encode( array("data"=>array("rst"=>1)) );
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            return "Error";
        }
    }



}
