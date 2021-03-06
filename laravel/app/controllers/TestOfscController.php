<?php

use Ofsc\Capacity;
use Ofsc\Activity;
use Ofsc\Inbound;

class TestOfscController extends \BaseController {
    
    protected $_errorController;
    protected $_bandejaController;
    protected $_gestionMovimientoController;
    
    public function __construct(
            ErrorController $errorController,
            BandejaController $bandejaController,
            GestionMovimientoController $gestionMovimientoController
        ) {
        $this->_errorController = $errorController;
        $this->_bandejaController = $bandejaController;
        $this->_gestionMovimientoController = $gestionMovimientoController;
    }
    public function getGetactivity()
    {
        $activity =  new Activity();
        $response = $activity->getActivity(434);
        
        print_r($response);dd("Home");
    }
    public function getTesthola()
    {
        $wsdl = file_get_contents('https://telefonica-pe.test.toadirect.com/soap/capacity/?wsdl');
        var_dump($http_response_header);
        //phpinfo();
    }


    public function getTestcancelactivity()
    {
       $code = Input::get('code'); 

       if (!isset($code) || $code == null) {
           return false;
       }

       $activity =  new Activity();
       //$response = $activity->cancelActivity($code);
       //$carga_xml = simplexml_load_string($response);
       //print_r($carga_xml);
    }


    public function getTestgetcapacity() {
        $capacity = new Capacity();
        $response = $capacity->getCapacity();
        $carga_xml = simplexml_load_string($response);
        print_r($carga_xml);
    }

    public function getTestgetquotadata() {
        $capacity = new Capacity();
        $response = $capacity->getQuotaData();
        $carga_xml = simplexml_load_string($response);
        print_r($carga_xml);
    }

/**
* ADD LINE CODE CLARENCE
*/


/**
* END LINE CODE CLARENCE
*/


    public function getTestcancelar(){
        $activityId = 324;
        
        $activity = new Activity();
        $response = $activity->cancelActivity($activityId);
        print_r($response);
    }
    
    public function getTestcreateactivity() {
        $inbound = new Inbound();
        $response = $inbound->createActivity();
        print_r($response);
    }
    
    public function getPruebaapicapacity(){
        $capacity = new Capacity();
        
        $data["fecha"] = array(
            "2015-12-10"
        );
        //Sin bucket
        //$data["bucket"] = "BK_PRUEBAS_TOA";
        $data["time_slot"] = "";
        $data["work_skill"] = "PROV_INS_M1";
        $data["activity_field"] = array(
                    array(
                        "name" => "worktype_label",
                        "value" => "PROV_INS_M1"
                    ),
                    array(
                        "name" => "XA_WORK_ZONE_KEY",
                        "value" => "MY"
                    ),
                );
        
        $response = $capacity->getCapacity($data);
        print_r($response);
    }

    public function getTestdataactivity()
    {
        //retorno de transaccion
        $save = array(
            "rst" => 2,
            "msj" => array("Sin resultados."),
            "error" => "",
            "gestion_id" => 0
        );

        $inbound = new Inbound();
        
        //Agenda (false) o SLA (true)
        $sla = false;
        
        $gestionId = "";
        $codactu = "6201387";
        $date = "2015-12-11";
        $bucket = "BK_PRUEBAS_TOA";
        $tini = "09:00";
        $tend = "13:00";
        $tobooking = date("Y-m-d H:i");
        $slot = "AM";
        $cuadrante = "Something";
        $techTp = "NULL";
        $techBb = "NULL";
        $techTv = "NULL";
        $wzKey = "";
        $wtLabel = "";
        $wType = "";
        $accTech = "COAXIAL";
        $businessType = "";
        $phone = array(0=>"", 1=>"");
        
        //Datos de la actuacion
        $actu = \Gestion::getCargar($codactu);
        //print_r($actu);die();
        //Existe la actuacion
        if (isset($actu["datos"][0])) {
            $actuObj = $actu["datos"][0];
            $actuArray = (array) $actuObj;
            
            $actuArray['noajax'] = "ok";
            
            //Motivo/Submotivo
            $actuArray['gestion_id'] = $actuObj->id;
            $actuArray['motivo'] = 1;
            $actuArray['submotivo'] = 1;
            $actuArray['estado'] = 2;
            
            //GestionID
            $gestionId = $actuObj->id;
            
            //Tipo de actividad: tabla "actividaes_tipos"
            $actTipo = DB::table('actividades_tipos')
                    ->select('nombre', 'label', 'sla', 'duracion')
                    ->where('id', '=', $actuArray['actividad_tipo_id'])
                    ->first();
            
            //Duracion en minutos
            $duration = $actTipo->duracion;
            
            //Quiebre grupo
            $quiebreGrupo = DB::table('quiebre_grupos')
                    ->select('nombre')
                    ->where('id', '=', $actuArray['quiebre_grupo_id'])
                    ->first();
            
            //Negocio por texto
            if (strpos($actuArray['tipo_averia'], "catv") > 0) {
                $businessType = "CATV";
            }
            
            if (strpos($actuArray['tipo_averia'], "adsl") > 0) {
                $businessType = "ADSL";
            }
            
            if (strpos($actuArray['tipo_averia'], "bas") > 0) {
                $businessType = "BASICA";
            }
            
            $wtLabel = $actTipo->label;
            $wType = $actTipo->nombre;
            
            //Telefonos de contacto
            $phoneArray = explode("|", $actuArray['fonos_contacto']);
            if (isset($phoneArray[0])) {
                $phone[0] = $phoneArray[0];
            }
            if (isset($phoneArray[1])) {
                $phone[1] = $phoneArray[1];
            }
            
            //Si no hay telefono de contacto
            if (strlen(trim($actuArray['fonos_contacto'])) < 6) {
                $phone = array("0000000", "0000000");
            }
            //Datos XY Web
            $actuArray['x'] = $actuArray['coord_x'];
            $actuArray['y'] = $actuArray['coord_y'];
            
            //Facilidades técnicas
            $ffttController = new \FfttController();
            $val = new stdClass();
            $val->fftt = $actuArray['fftt'];
            $val->tipoactu = $actuArray['tipo_averia'];
            $ffttArray = $ffttController->getExplodefftt($val);
            
            if ($ffttArray["tipo"]=='catv') {
                $wzKey = $ffttArray["nodo"] . "_" . $ffttArray["troba"];
            } else {
                $wzKey = $ffttArray["mdf"] . "_" . $ffttArray["armario"];
            }
            
            //Homologacion de campos
            $dataOfsc = array(
                "bucket" => $bucket,
                "actividad" => strtolower($actuObj->actividad),
                "date" => $date,
                "type" => "update_activity",
                "external_id" => $bucket,
                "start_time" => $tini,
                "end_time" => $tend,
                "appt_number" => $actuArray['codactu'],
                "customer_number" => $actuArray['inscripcion'],
                "worktype_label" => $wtLabel,
                "time_slot" => $slot,
                "time_of_booking" => $tobooking,
                "duration" => $duration,
                "name" => $actuArray['nombre_cliente'],
                "phone" => $phone[0],
                "email" => "",
                "cell" => $phone[1],
                "address" => $actuArray['direccion_instalacion'],
                "city" => "Lima",
                "state" => "Lima",
                "zip" => "LIMA 05",
                "language" => "1",
                "reminder_time" => "15",
                "time_zone" => "19",
                "coordx" => $actuArray['coord_x'],
                "coordy" => $actuArray['coord_y'],
                "XA_CREATION_DATE" => $tobooking,
                "XA_SOURCE_SYSTEM" => "PSI",
                "XA_CUSTOMER_SEGMENT" => "N",
                "XA_CUSTOMER_TYPE" => "",
                "XA_CONTACT_NAME" => "",
                "XA_CONTACT_PHONE_NUMBER_2" => "",
                "XA_CONTACT_PHONE_NUMBER_3" => "",
                "XA_CONTACT_PHONE_NUMBER_4" => "",
                "XA_CITY_CODE" => "",
                "XA_DISTRICT_CODE" => "",
                "XA_DISTRICT_NAME" => "",
                "XA_ZONE" => $actuArray['zonal'],
                "XA_QUADRANT" => $cuadrante,
                "XA_WORK_ZONE_KEY" => $wzKey,
                "XA_RURAL" => "",
                "XA_RED_ZONE" => "",
                "XA_WORK_TYPE" => $wType,
                "XA_APPOINTMENT_SCHEDULER" => "CLI",
                "XA_USER" => "",
                "XA_REQUIREMENT_NUMBER" => $actuArray['codactu'],
                "XA_NUMBER_SERVICE_ORDER" => $actuArray['orden_trabajo'],
                "XA_CHANNEL_ORIGIN" => "",
                "XA_SALES_POINT_CODE" => "",
                "XA_SALES_POINT_DESCRIPTION" => "",
                "XA_COMMERCIAL_VALIDATION" => "",
                "XA_TECHNICAL_VALIDATION" => "",
                "XA_WEB_UNIFICADA" => "",
                "XA_ORDER_AREA" => "",
                "XA_COMMERCIAL_PACKET" => "",
                "XA_COMPANY_NAME" => $actuArray['empresa'],
                "XA_GRUPO_QUIEBRE" => $quiebreGrupo->nombre,
                "XA_QUIEBRES" => $actuArray['quiebre'],
                "XA_BUSINESS_TYPE" => $businessType,
                "XA_PRODUCTS_SERVICES" => "",
                "XA_CURRENT_PRODUCTS_SERVICES" => "",
                "XA_EQUIPMENT" => "",
                "XA_NOTE" => $actuArray['observacion'],
                "XA_TELEPHONE_TECHNOLOGY" => $techTp,
                "XA_BROADBAND_TECHNOLOGY" => $techBb,
                "XA_TV_TECHNOLOGY" => $techTv,
                "XA_ACCESS_TECHNOLOGY" => $accTech,
                "XA_HFC_ZONE" => "",
                "XA_HFC_NODE" => "",
                "XA_HFC_TROBA" => "",
                "XA_HFC_AMPLIFIER" => "",
                "XA_HFC_TAP" => "",
                "XA_HFC_BORNE" => "",
                "XA_REQUIREMENT_TYPE" => "",
                "XA_REQUIREMENT_REASON" => "",
                "XA_CATV_SERVICE_CLASS" => "",
                "XA_MDF" => $actuArray['mdf'],
                "XA_CABLE" => "",
                "XA_CABINET" => "",
                "XA_BOX" => "",
                "XA_TERMINAL_ADDRESS" => "",
                "XA_TERMINAL_LINKHTTP" => "",
                "XA_ADSLSTB_PREFFIX" => "NULL",
                "XA_ADSLSTB_MOVEMENT" => "NULL",
                "XA_ADSL_SPEED" => "",
                "XA_ADSLSTB_SERVICE_TYPE" => "",
                "XA_PENDING_EXTERNAL_ACTION" => "",
                "XA_SMS_1" => $actuArray['sms1'],
                "XA_DIAGNOSIS" => "",
                "XA_TOTAL_REPAIRS" => $actuArray['total_averias'],
            );
            //print_r($dataOfsc);die();
            //Envio por SLA
            if ($sla) {
                $dataOfsc["sla_window_start"] = "2015-12-03 16:00";
                $dataOfsc["sla_window_end"] = "2015-12-05 17:00";
            }
            
            //Campos adicionales para averia
            if (strtolower($actuObj->actividad) == 'averia') {
                $dataOfsc["XA_DIAGNOSIS"] = "SB_01";
            }
            
            /**
             * Guardar en tablas de gestion
             * - gestiones
             * - gestiones_detalles
             * - gestiones_movimientos
             * - ultimos_movimientos
             */
            
            Input::replace($actuArray);
            $save = $this->_gestionMovimientoController->postRegistrar();
            
            if ($save["rst"]==1) {
                //Crear actividad en OFSC
                $response = $inbound->createActivity($dataOfsc, $sla);
                print_r($response);
                $report = $response->data
                    ->data
                    ->commands
                    ->command
                    ->appointment
                    ->report;
                
                $resultBool = true;
                
                if (is_array($report->message)) {
                    foreach ($report->message as $val) {
                        if ($val->result == 'warning') {
                            $save["error"][] = $val->description;
                        }
                        if ($val->result == 'error') {
                            $save["error"][] = $val->description;
                        }
                    }
                } else {
                    if ($report->message->result == 'error') {
                        $resultBool = false;
                        $save["error"][] = $report->message->description;
                    }
                }
                
                //Retorno OK
                if ($resultBool) {
                    //Appointment id
                    $aid = $response->data
                        ->data
                        ->commands
                        ->command
                        ->appointment
                        ->aid;
                    $save["msj"] = "Registro y envio a OFSC correcto.";
                }

                //Retorno ERROR
                if (!$resultBool) {
                    /*$errArray['type'] = $report->message->type;
                    $errArray['code'] = $report->message->code;
                    $errArray['desc'] = $report->message->description;*/

                    $save["rst"] = 2;
                    $save["msj"] = "No se pudo enviar a OFSC.";
                    //$save["error"] = $report->message->description;
                }
            }
        }
        print_r($save);
        //return $save;
    }
    
    public function getTestupdateactivity()
    {
        $activityId = 374;
        
        $data["direccion"] = "Av. Los Frutos 159";
        
        $activity = new Activity();
        $response = $activity->updateActivity($activityId, $data);
        print_r($response);
    }
    
    public function getTeststartactivity()
    {
        $activityId = 391;
        
        $activity = new Activity();
        $response = $activity->startActivity($activityId);
        print_r($response);
    }

    public function getEnvioofsc(){
        //2016-01-04||TEST_LARI_INS_MOVISTAR1||PM||13-18
        $actuaciones=array(

        );

    $tablabien="<table border=1><tr><th colspan='3'>Ofsc Envio Correcto</th></tr><tr><th>Cod Actu</th><th>Aid</th><th>Mensaje</th></tr>";
    $tablamal="<table border=1><tr><th colspan='3'>Ofsc Envio Incorrecto</th></tr><tr><th>Cod Actu</th><th>Aid</th><th>Mensaje</th></tr>";
    for($i=0; $i<count($actuaciones);$i++){
        $datos=array();
        //$datos['hf']            =explode("||",Input::get('hf'));
        $rand=rand(0,1); 
        //$rand=1;
        $arrayslot=array('AM','PM');
        $arrayhora=array( array('9','13'), array('13','18') );
        $datos['fecha']         =date('Y-m-d'); //,strtotime('+1 day')
        $datos['bucket']        ='BK_TDPROUTE'; //BK_TDPROUTE
        $datos['slot']          = $arrayslot[$rand]; //time slot 
        $datos['horas']         = $arrayhora[$rand];
        //$datos['empresa_id']    =5;
        $datos['codactu']       =$actuaciones[$i];
        $datos['slaini']=''; //date('Y-m-d')
        
        $sla = false;
        $slaInicio = "0000-00-00 00:00";
        
        //retorno de transaccion
        $save = array(
            "rst" => 2,
            "msj" => "Sin reultados.",
            "error" => "",
            "gestion_id" => 0
        );

        $inbound = new Inbound();
        
        $gestionId = "";
        $codactu = $datos['codactu']; //"35268199"; //
        $date = $datos['fecha']; //date("Y-m-d"); //
        $bucket = $datos['bucket']; //"BK_PRUEBAS_TOA";
        $tini = trim($datos['horas'][0]);; //"09:00"
        $tend = trim($datos['horas'][1]); //"13:00"
        $tobooking = date("Y-m-d H:i");
        $slot = $datos['slot']; //"AM"
        $cuadrante = "Something";
        $techTp = "NULL";
        $techBb = "NULL";
        $techTv = "NULL";
        $wzKey = "";
        $wtLabel = "";
        $wType = "";
        $accTech = "COAXIAL";
        $businessType = "";
        $phone = array(0=>"", 1=>"");
        
        //Datos de la actuacion
        $actu = \Gestion::getCargar($codactu);
        
        //Existe la actuacion
        if (isset($actu["datos"][0])) {
            $actuObj = $actu["datos"][0];
            $actuArray = (array) $actuObj;
            
            $actuArray['noajax'] = "ok";
            
            //Motivo/Submotivo
            $actuArray['gestion_id'] = $actuObj->id;
            $actuArray['motivo'] = 1;
            $actuArray['submotivo'] = 1;
            $actuArray['estado'] = 2;
            
            //GestionID
            $gestionId = $actuObj->id;

            if(trim($actuArray['actividad_tipo_id'])==''){
                $tablamal.="<tr>";
                $tablamal.=    "<td>";
                $tablamal.=    $actuArray['codactu'];
                $tablamal.=    "</td>";
                $tablamal.=    "<td>";
                $tablamal.=    "";
                $tablamal.=    "</td>";
                $tablamal.=    "<td>";
                $tablamal.=    "No cuenta con actividad id";
                $tablamal.=    "</td>";
                $tablamal.="</tr>";
                continue;
            }
            
            //Tipo de actividad: tabla "actividaes_tipos"
            $actTipo = DB::table('actividades_tipos')
                    ->select('nombre', 'label', 'sla', 'duracion')
                    ->where('id', '=', $actuArray['actividad_tipo_id'])
                    ->first();
            
            //Duracion en minutos
            $duration = $actTipo->duracion;
            
            //Quiebre grupo
            $quiebreGrupo = DB::table('quiebre_grupos')
                    ->select('nombre')
                    ->where('id', '=', $actuArray['quiebre_grupo_id'])
                    ->first();
            
            //Negocio por texto
            if (strpos($actuArray['tipo_averia'], "catv") > 0) {
                $businessType = "CATV";
            }
            
            if (strpos($actuArray['tipo_averia'], "adsl") > 0) {
                $businessType = "ADSL";
            }
            
            if (strpos($actuArray['tipo_averia'], "bas") > 0) {
                $businessType = "BASICA";
            }
            
            $wtLabel = $actTipo->label;
            $wType = $actTipo->nombre;
            
            //Telefonos de contacto
            $phoneArray = explode("|", $actuArray['fonos_contacto']);
            if (isset($phoneArray[0])) {
                $phone[0] = $phoneArray[0];
            }
            if (isset($phoneArray[1])) {
                $phone[1] = $phoneArray[1];
            }
            
            //Si no hay telefono de contacto
            if (strlen(trim($actuArray['fonos_contacto'])) < 6) {
                $phone = array("0000000", "0000000");
            }
            //Datos XY Web
            $actuArray['x'] = $actuArray['coord_x'];
            $actuArray['y'] = $actuArray['coord_y'];
            
            //Facilidades técnicas
            $ffttController = new \FfttController();
            $val = new stdClass();
            $val->fftt = $actuArray['fftt'];
            $val->tipoactu = $actuArray['tipo_averia'];
            $ffttArray = $ffttController->getExplodefftt($val);
            
            if ($ffttArray["tipo"]=='catv') {
                $wzKey = $ffttArray["nodo"] . "_" . $ffttArray["troba"];
            } else {
                $wzKey = $ffttArray["mdf"] . "_" . $ffttArray["armario"];
            }
            
            //Parche: datos no concuerdan -> enumeration
            if ($actuArray['segmento'] == "NO-VIP") {
                $actuArray['segmento'] = "N";
            }
            
            //Homologacion de campos
            $dataOfsc = array(
                "bucket" => $datos['bucket'],
                "actividad" => strtolower($actuObj->actividad),
                "date" => $date,
                "type" => "update_activity",
                "external_id" => $bucket,
                "start_time" => $tini,
                "end_time" => $tend,
                "appt_number" => $actuArray['codactu'],
                "customer_number" => $actuArray['inscripcion'],
                "worktype_label" => $wtLabel,
                "time_slot" => $slot,
                "time_of_booking" => $tobooking,
                "duration" => $duration,
                "name" => $actuArray['nombre_cliente'],
                "phone" => $phone[0],
                "email" => "",
                "cell" => $phone[1],
                "address" => $actuArray['direccion_instalacion'],
                "city" => "Lima",
                "state" => "Lima",
                "zip" => "LIMA 05",
                "language" => "1",
                "reminder_time" => "15",
                "time_zone" => "19",
                "coordx" => $actuArray['coord_x'],
                "coordy" => $actuArray['coord_y'],
                "XA_CREATION_DATE" => $tobooking,
                "XA_SOURCE_SYSTEM" => "PSI",
                "XA_CUSTOMER_SEGMENT" => $actuArray['segmento'],
                "XA_CUSTOMER_TYPE" => "",
                "XA_CONTACT_NAME" => "",
                "XA_CONTACT_PHONE_NUMBER_2" => "",
                "XA_CONTACT_PHONE_NUMBER_3" => "",
                "XA_CONTACT_PHONE_NUMBER_4" => "",
                "XA_CITY_CODE" => "",
                "XA_DISTRICT_CODE" => $actuArray['codigo_distrito'],
                "XA_DISTRICT_NAME" => $actuArray['distrito'],
                "XA_ZONE" => $actuArray['zonal'],
                "XA_QUADRANT" => $cuadrante,
                "XA_WORK_ZONE_KEY" => $wzKey,
                "XA_RURAL" => "",
                "XA_RED_ZONE" => "",
                "XA_WORK_TYPE" => $wType,
                "XA_APPOINTMENT_SCHEDULER" => "CLI",
                "XA_USER" => "",
                "XA_REQUIREMENT_NUMBER" => $actuArray['codactu'],
                "XA_NUMBER_SERVICE_ORDER" => $actuArray['orden_trabajo'],
                "XA_CHANNEL_ORIGIN" => "",
                "XA_SALES_POINT_CODE" => "",
                "XA_SALES_POINT_DESCRIPTION" => "",
                "XA_COMMERCIAL_VALIDATION" => "",
                "XA_TECHNICAL_VALIDATION" => "",
                "XA_WEB_UNIFICADA" => "",
                "XA_ORDER_AREA" => "",
                "XA_COMMERCIAL_PACKET" => "",
                "XA_COMPANY_NAME" => $actuArray['empresa'],
                "XA_GRUPO_QUIEBRE" => $quiebreGrupo->nombre,
                "XA_QUIEBRES" => $actuArray['quiebre'],
                "XA_BUSINESS_TYPE" => $businessType,
                "XA_PRODUCTS_SERVICES" => "",
                "XA_CURRENT_PRODUCTS_SERVICES" => "",
                "XA_EQUIPMENT" => "",
                "XA_NOTE" => $actuArray['observacion'],
                "XA_TELEPHONE_TECHNOLOGY" => $techTp,
                "XA_BROADBAND_TECHNOLOGY" => $techBb,
                "XA_TV_TECHNOLOGY" => $techTv,
                "XA_ACCESS_TECHNOLOGY" => $accTech,
                "XA_HFC_ZONE" => "",
                "XA_HFC_NODE" => "",
                "XA_HFC_TROBA" => "",
                "XA_HFC_AMPLIFIER" => "",
                "XA_HFC_TAP" => "",
                "XA_HFC_BORNE" => "",
                "XA_REQUIREMENT_TYPE" => "",
                "XA_REQUIREMENT_REASON" => "",
                "XA_CATV_SERVICE_CLASS" => "",
                "XA_MDF" => $actuArray['mdf'],
                "XA_CABLE" => "",
                "XA_CABINET" => "",
                "XA_BOX" => "",
                "XA_TERMINAL_ADDRESS" => "",
                "XA_TERMINAL_LINKHTTP" => "",
                "XA_ADSLSTB_PREFFIX" => "NULL",
                "XA_ADSLSTB_MOVEMENT" => "NULL",
                "XA_ADSL_SPEED" => "",
                "XA_ADSLSTB_SERVICE_TYPE" => "",
                "XA_PENDING_EXTERNAL_ACTION" => "",
                "XA_SMS_1" => $actuArray['sms1'],
                "XA_DIAGNOSIS" => "",
                "XA_TOTAL_REPAIRS" => $actuArray['total_averias'],
            );
            
            //Envio por SLA
            if ($datos['slaini']!='') {
                $sla = true;
                $slaInicio = strtotime($datos['slaini']);
                
                $slaDay = $actTipo->sla * 3600;
                
                $slaFin = $slaInicio + $slaDay;
                
                $dataOfsc["sla_window_start"] = date("Y-m-d H:i", $slaInicio);
                $dataOfsc["sla_window_end"] = date("Y-m-d H:i", $slaFin);
                
                $dataOfsc["date"] = date("Y-m-d", $slaInicio);
            }
            
            //Campos adicionales para averia
            if (strtolower($actuObj->actividad) == 'averia') {
                $dataOfsc["XA_DIAGNOSIS"] = "SB_01";
            }
            
            /**
             * Guardar en tablas de gestion
             * - gestiones
             * - gestiones_detalles
             * - gestiones_movimientos
             * - ultimos_movimientos
             */
            $actuArray['fecha_agenda']=$date;
            $horario= Horario::where('horario','=',$slot)->first();

            if( $horario!=NULL AND $horario!='' ){
                $actuArray['horario_id']= $horario->id;
                $actuArray['dia_id']= date("N",strtotime($date));
            }

            $actuArray['estado_agendamiento']="1-1";
            
            unset($actuArray["tecnico"]);
            unset($actuArray["tecnico_id"]);
            unset($actuArray["celula_id"]);
            
            Input::replace($actuArray);
            //$save = $this->_gestionMovimientoController->postRegistrar();

            unset($actuArray["fecha_agenda"]);
            unset($actuArray["horario_id"]);
            unset($actuArray["dia_id"]);
            $actuArray['estado_agendamiento']="3-0";
            $actuArray['motivo'] = 2;
            $actuArray['submotivo'] = 18;
            $actuArray['estado'] = 7;
            
            //if ($save["rst"]==1) {
                //Crear actividad en OFSC
                /*if ( $dataOfsc['date']==0 ) {
                    $dataOfsc['date']=date("Y-m-d",strtotime(Input::get('slaini')) );
                }*/

                $response = $inbound->createActivity($dataOfsc, $sla);
                $report = $response->data
                    ->data
                    ->commands
                    ->command
                    ->appointment
                    ->report;
                
                $resultBool = true;

                /**
                 * $report->message:
                 * 
                 * El mensaje de respuesta puede ser un arreglo
                 * o un único mensaje.
                 * Se valida la respuesta para cada mensaje recibido.
                 */
                if (is_array($report->message)) {
                    foreach ($report->message as $val) {
                        if ($val->result == 'warning') {
                            $save["error"][] = $val->description;
                        }
                        if ($val->result == 'error') {
                            $save["error"][] = $val->description;
                        }
                    }
                } else {
                    if ($report->message->result == 'error') {
                        $resultBool = false;
                        $save["error"][] = $report->message->description;
                    }
                }
                
                //$result = $report->message->result;

                //Retorno OK
                if ($resultBool) {
                    //Appointment id
                    $aid = $response->data
                        ->data
                        ->commands
                        ->command
                        ->appointment
                        ->aid;

                    $datosofsc=array();
                    $datosofsc['aid']=$aid;
                    $datosofsc['gestion_id']=$save['gestion_id'];
                    $datosofsc['envio_ofsc']=2;
                    if($sla==false){
                        $datosofsc['envio_ofsc']=1;
                    }
                    //$datosofsc['gestion_movimiento_id']=$save['gestion_movimiento_id'];

                    //GestionMovimiento::ActOfscAid($datosofsc);
                    $save["msj"] = "Registro y envio a OFSC correcto.";
                    $save["aid"]=$aid;

                    $tablabien.="<tr>";
                    $tablabien.=    "<td>";
                    $tablabien.=    $actuArray['codactu'];
                    $tablabien.=    "</td>";
                    $tablabien.=    "<td>";
                    $tablabien.=    $save["aid"];
                    $tablabien.=    "</td>";
                    $tablabien.=    "<td>";
                    $tablabien.=    $save["msj"];
                    $tablabien.=    "</td>";
                    $tablabien.="</tr>";

                }

                //Retorno ERROR
                if (!$resultBool) {
                    $errArray['type'] = $report->message->type;
                    $errArray['code'] = $report->message->code;
                    $errArray['desc'] = $report->message->description;

                    Input::replace($actuArray);
                    //$save = $this->_gestionMovimientoController->postRegistrar();

                    $save["rst"] = 2;
                    $save["msj"] = "No se pudo enviar a OFSC. "
                                    . $report->message->description;

                    $tablamal.="<tr>";
                    $tablamal.=    "<td>";
                    $tablamal.=    $actuArray['codactu'];
                    $tablamal.=    "</td>";
                    $tablamal.=    "<td>";
                    $tablamal.=    "";
                    $tablamal.=    "</td>";
                    $tablamal.=    "<td>";
                    $tablamal.=    $save["msj"];
                    $tablamal.=    "</td>";
                    $tablamal.="</tr>";
                }
            //}
        }
        else{
                    $tablamal.="<tr>";
                    $tablamal.=    "<td>";
                    $tablamal.=    $actuArray['codactu'];
                    $tablamal.=    "</td>";
                    $tablamal.=    "<td>";
                    $tablamal.=    "";
                    $tablamal.=    "</td>";
                    $tablamal.=    "<td>";
                    $tablamal.=    $save["msj"];
                    $tablamal.=    "</td>";
                    $tablamal.="</tr>";
        }
    }
        $tablabien.="</table>";
        $tablamal.="</table>";
        echo $tablabien;
        echo "<br><br>";
        echo $tablamal;

    }

}
