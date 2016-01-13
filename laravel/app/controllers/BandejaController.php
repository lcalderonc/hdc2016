<?php
use Ofsc\Capacity;
use Ofsc\Activity;
use Ofsc\Inbound;

class BandejaController extends \BaseController
{
    protected $_VisorgpsController;
    protected $_errorController;
    protected $_gestionMovimientoController;

    public function __construct(
        VisorgpsController $VisorgpsController,
        ErrorController $ErrorController,
        GestionMovimientoController $gestionMovimientoController
    )
    {
        $this->beforeFilter('auth');
        $this->_VisorgpsController = $VisorgpsController ;
        $this->_errorController = $ErrorController;
        $this->_gestionMovimientoController = $gestionMovimientoController;
    }

    public function postUpdateofsc(){
        $activityId = Input::get('aid');
        
        $data["direccion"] = Input::get('direccion');
        $data["telefono"] = Input::get('telefono');
        $data["x"] = Input::get('coord_x');
        $data["y"] = Input::get('coord_y');
        
        $activity = new Activity();
        $response = $activity->updateActivity($activityId, $data);
        print_r($response);
    }

    public function postCancelarofsc(){
        $activityId = Input::get('aid');
        $codactu = Input::get('codactu');
        $save["aid"]=$activityId;

        $activity = new Activity();
        $response = $activity->cancelActivity($activityId);

        $resultCode = $response->data->result_code;
        
        if ($resultCode == 0) {
            $actu = \Gestion::getCargar($codactu);
            $actuObj = $actu["datos"][0];
            $actuArray = (array) $actuObj;

            $actuArray['estado_agendamiento']="3-0";
            $actuArray['gestion_id'] = $actuObj->id;
            $actuArray['estado']=4;
            $actuArray['motivo']=9;
            $actuArray['submotivo']=3;

            Input::replace($actuArray);
            $save = $this->_gestionMovimientoController->postRegistrar();

            $datos=array();
            $datos['aid']=$activityId;
            $datos['envio_ofsc']=3;
            $datos['gestion_id']=$save['gestion_id'];
            $datos['gestion_movimiento_id']=$save['gestion_movimiento_id'];
            $datos['estado_ofsc_id']=5;
            GestionMovimiento::OfscUpdate($datos);

            $save["msj"] = "Registro y envío a OFSC correcto.";            
        } else {
            $save["rst"] = 2;
            $save['error_msg']=$response->data->error_msg;
        }

        return json_encode($save);
    }
    
    public function postIniciarofsc(){
        $activityId = Input::get('aid');
        $codactu = Input::get('codactu');
        $save["aid"]=$activityId;

        $activity = new Activity();
        $response = $activity->startActivity($activityId);

        $resultCode = $response->data->result_code;
        
        if ($resultCode == 0) {
            $actu = \Gestion::getCargar($codactu);
            $actuObj = $actu["datos"][0];
            $actuArray = (array) $actuObj;

            //$actuArray['estado_agendamiento']="3-0";
            $actuArray['gestion_id'] = $actuObj->id;
            $actuArray['estado']=13;
            $actuArray['motivo']=7;
            $actuArray['submotivo']=11;

            Input::replace($actuArray);
            $save = $this->_gestionMovimientoController->postRegistrar();

            $datos=array();
            $datos['aid']=$activityId;
            $datos['envio_ofsc']=0;
            $datos['gestion_id']=$save['gestion_id'];
            $datos['gestion_movimiento_id']=$save['gestion_movimiento_id'];
            $datos['estado_ofsc_id']=2;
            GestionMovimiento::OfscUpdate($datos);

            $save["msj"] = "Registro y envío a OFSC correcto.";            
        } else {
            $save["rst"] = 2;
            $save['error_msg']=$response->data->error_msg;
        }

        return json_encode($save);
    }
    
    public function postCompletarofsc(){
        $activityId = Input::get('aid');
        $codactu = Input::get('codactu');
        $save["aid"]=$activityId;

        $activity = new Activity();
        $response = $activity->completeActivity($activityId);

        $resultCode = $response->data->result_code;
        
        if ($resultCode == 0) {
            $actu = \Gestion::getCargar($codactu);
            $actuObj = $actu["datos"][0];
            $actuArray = (array) $actuObj;

            //$actuArray['estado_agendamiento']="3-0";
            $actuArray['gestion_id'] = $actuObj->id;
            $actuArray['estado']=6;
            $actuArray['motivo']=3;
            $actuArray['submotivo']=12;

            Input::replace($actuArray);
            $save = $this->_gestionMovimientoController->postRegistrar();

            $datos=array();
            $datos['aid']=$activityId;
            $datos['envio_ofsc']=0;
            $datos['gestion_id']=$save['gestion_id'];
            $datos['gestion_movimiento_id']=$save['gestion_movimiento_id'];
            $datos['estado_ofsc_id']=6;
            GestionMovimiento::OfscUpdate($datos);

            $save["msj"] = "Registro y envío a OFSC correcto.";            
        } else {
            $save["rst"] = 2;
            $save['error_msg']=$response->data->error_msg;
        }        

        return json_encode($save);
    }

    public function postEnvioofsc(){
        $datos=array();
        $datos['hf']            =explode("||",Input::get('hf'));
        $datos['fecha']         =$datos['hf'][0];
        $datos['bucket']        =$datos['hf'][1];
        $datos['slot']          =$datos['hf'][2]; //time slot 
        $datos['horas']         =explode("-",$datos['hf'][3]);
        $datos['empresa_id']    =Input::get('empresa_id');
        $datos['codactu']       =Input::get('codactu');
        
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
            if (Input::get('agdsla')=='sla' AND Input::get('slaini')!='') {
                $sla = true;
                $slaInicio = strtotime(Input::get('slaini'));
                
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
            $save = $this->_gestionMovimientoController->postRegistrar();

            unset($actuArray["fecha_agenda"]);
            unset($actuArray["horario_id"]);
            unset($actuArray["dia_id"]);
            $actuArray['estado_agendamiento']="3-0";
            $actuArray['motivo'] = 2;
            $actuArray['submotivo'] = 18;
            $actuArray['estado'] = 7;
            
            if ($save["rst"]==1) {
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
                    $datosofsc['gestion_movimiento_id']=$save['gestion_movimiento_id'];

                    GestionMovimiento::ActOfscAid($datosofsc);
                    $save["msj"] = "Registro y envío a OFSC correcto.";
                    $save["aid"]=$aid;
                }

                //Retorno ERROR
                if (!$resultBool) {
                    $errArray['type'] = $report->message->type;
                    $errArray['code'] = $report->message->code;
                    $errArray['desc'] = $report->message->description;

                    Input::replace($actuArray);
                    $save = $this->_gestionMovimientoController->postRegistrar();

                    $save["rst"] = 2;
                    $save["msj"] = "No se pudo enviar a OFSC. "
                                    . $report->message->description;
                }
            }
        }

        return json_encode($save);
    }

    public function postCapacity() {
        $capacity = new Capacity();

        $fecha= explode(" - ",Input::get('fecha'));
        $actividad_tipo_id=Input::get('actividad_tipo_id');
        $mdf=Input::get('mdf');

        $actividadTipo= ActividadTipo::find($actividad_tipo_id);
        $label=$duracion='';
        if( $actividadTipo!=NULL AND $actividadTipo!='' ){
            $label=$actividadTipo->label;
            $duracion=$actividadTipo->duracion;
        }

        $data["fecha"] = array();

        while($fecha[0]<=$fecha[1]){
            array_push($data['fecha'],$fecha[0]);
            $fecha[0]=date("Y-m-d", strtotime($fecha[0]." + 1 days"));
        }
        //Sin bucket
        //$data["bucket"] = "BK_PRUEBAS_TOA";
        $data["time_slot"] = "";
        $data["work_skill"] = "$label";
        $data["activity_field"] = array(
                    array(
                        "name" => "worktype_label",
                        "value" => "$label"
                    ),
                    array(
                        "name" => "XA_WORK_ZONE_KEY",
                        "value" => "$mdf"
                    ),
                );
        
        $response = $capacity->getCapacity($data);

        return json_encode(array('rst'=>1,'datos'=>$response,'duracion'=>$duracion)); 
    }
    /**
     * Recepciona datos de Bandeja Controller
     * 
     * @return type
     */
    public function postRecepccion() 
    {        
        $data=array();
        $valida=array();

        $dataOfficetrack=Input::all();
        $dataGestion=Input::all();
        $dataGestionPendiente=Input::all();

        if( trim($dataGestion["fecha_agenda"]) !='' and trim($dataGestion['dia_id']) !='' and $dataGestion['dia_id']!=date("N",strtotime(date($dataGestion["fecha_agenda"]))) ){
            $exc['code']='0000';
            $exc['file']='BandejaController.php';
            $exc['line']='26';
            $exc['message']=$dataGestion['codactu'].', No cuadran dias =>'+$dataGestion['dia_id'].' == '.date("N",strtotime( date( $dataGestion["fecha_agenda"] ) ) );
            $exc['trace']=$dataGestion['dia_id']."|".$dataGestion['fecha_agenda']."|".$dataGestion['estado_id']."|".$dataGestion['submotivo_id']."|".$dataGestion['motivo_id']."|".$dataGestion['quiebre_id']."|".$dataGestion['actividad_id'];

            $this->_errorController->saveError($exc);
            return Response::json(
                array(
                    'rst'=>2,
                    'msj'=>'Ocurrio una interrupción en el registro de la información.',
                    'codactu'=>$dataGestion['codactu']
                )
            );
            /*
            $dataGestion['dia_id'].' == '.date("N",strtotime(date($dataGestion["fecha_agenda"])))
            */
            exit(0);
        }
        
        /**
         * Validacion Técnico en un solo horario y fecha
         * 
         * Datos requeridos:
         * tecnico:12
         * horario_id:19
         * dia_id:5
         * fecha_agenda:2015-05-08
         * estado_agendamiento: 1-1
         */
        $tecAsignadoBool = false;
        $resultAsignado  = array();
        if ($dataGestion["estado_agendamiento"]=='1-1') 
        {
            //No validar para tecnico NINGUNO
            $tecArray = DB::table('tecnicos')
                        ->where('id', '=', $dataGestion["tecnico"])
                        ->where('estado', '=', '1')
                        ->select(
                                'ninguno'
                            )
                        ->get();
            if (isset($tecArray[0]) and $tecArray[0]->ninguno==0)
            {
                $dataValidaCupo = new stdClass();
                $dataValidaCupo->tecnico_id     = $dataGestion["tecnico"];
                $dataValidaCupo->horario_id     = $dataGestion["horario_id"];
                $dataValidaCupo->dia_id         = $dataGestion["dia_id"];
                $dataValidaCupo->fecha_agenda   = $dataGestion["fecha_agenda"];

                $asignado = GestionMovimiento::getTecnicoHorario($dataValidaCupo);

                if (is_array($asignado) and count($asignado["asignado"])>0) {
                    $ordenAgenda = $asignado["asignado"][0];
                    $tecAsignadoBool = true;
                    $resultAsignado = array(
                            'rst'=>2,
                            'msj'=>'El técnico seleccionado ya tiene una orden '
                                   . 'agendada para el horario asignado',
                            'codactu'=>$ordenAgenda->codactu
                        );
                }

                //Respuesta tecnico con agenda asignada
                if ($tecAsignadoBool)
                {
                    return $resultAsignado;
                    exit;
                }
            }
        }
        
        /**
         * Para estados: Cancelado y Pendiente, evaluar si
         * la orden está asignada a un técnico y dejarla 
         * sin efecto. 2015-06-25
         * 
         * Obtener ultimo movimiento antes de grabar la gestion
         */
        $getOtoff = "";
        if ( $dataGestion["estado"]==5 or $dataGestion["estado"]==7) {
            if ( isset($dataGestion["gestion_id"]) and $dataGestion["gestion_id"]>0 ) {
                $ultimov = DB::table('ultimos_movimientos')
                        ->where('gestion_id', $dataGestion["gestion_id"])
                        ->first();
                $ultimov = Helpers::stdToArray($ultimov);
                
                /**
                 * Valida si la orden tiene: 
                 * - horario
                 * - dia
                 * - celula
                 * - tecnico
                 */
                if ($ultimov["horario_id"] > 0
                    and $ultimov["dia_id"] > 0
                    and $ultimov["celula_id"] > 0
                    and $ultimov["tecnico_id"] > 0) 
                {
                    
                    $ultimov["estado"]              = "";
                    $ultimov["actividad"]           = "";
                    $ultimov["duration"]            = 1;
                    $ultimov["quiebre"]             = "";
                    $ultimov["eecc_final"]          = "";
                    $ultimov["cr_observacion"]      = "";
                    $ultimov["carnet"]              = "";
                    $ultimov["velocidad"]           = "";
                    $ultimov["paquete"]             = "";
                    $ultimov['fecha_agenda']        = "";
                    $ultimov['hora_agenda']         = "";
                    $ultimov["estado_agendamiento"] = "1-1";
                    $ultimov["coordinado2"]         = "0";
                    
                    //Envio a OT
                    $savedata["otdata"] = $ultimov;
                    $rot = Helpers::ruta(
                        'officetrack/enviartarea',
                        'POST', 
                        $savedata, 
                        false
                    );
                    
                    //Respuesta OT
                    //$getOtoff = $rot->officetrack;
                }
                
            }
        }

        $getOtoff = "";
        DB::beginTransaction();

        $rgm2['sql']='';
        $rgm2['estofic']='';
        if ( isset($dataGestion["gestion_id"]) and $dataGestion["gestion_id"]>0 
            AND $dataGestion["estado_officetrack"]==0
        ) {
            $ultimov = DB::table('ultimos_movimientos')
                        ->where('gestion_id', $dataGestion["gestion_id"])
                        ->first();
            $ultimov = Helpers::stdToArray($ultimov);
            
            $sql="  SELECT ct.officetrack
                    FROM tecnicos t
                    INNER JOIN celula_tecnico ct ON t.id=ct.tecnico_id
                    WHERE ct.tecnico_id='".$ultimov["tecnico_id"]."'
                    AND ct.celula_id='".$ultimov["celula_id"]."'
                    AND ct.estado=1";

            $tecnicoinfo = DB::select($sql);
            $tecnicoinfo = Helpers::stdToArray($tecnicoinfo);
            if(count($tecnicoinfo)>0){
                $rgm2['estofic']=$tecnicoinfo[0]['officetrack'];
            }
            $rgm2['sql']=$sql;
            /**
             * Valida si la orden tiene: 
             * - horario
             * - dia
             * - celula
             * - tecnico
             */
            if ($ultimov["horario_id"] > 0
                and $ultimov["dia_id"] > 0
                and $ultimov["celula_id"] > 0
                and $ultimov["tecnico_id"] > 0
                and $rgm2['estofic']==1) 
            {
                
                $ultimov["estado"]              = "";
                $ultimov["actividad"]           = "";
                $ultimov["duration"]            = 1;
                $ultimov["quiebre"]             = "";
                $ultimov["eecc_final"]          = "";
                $ultimov["cr_observacion"]      = "";
                $ultimov["carnet"]              = "";
                $ultimov["velocidad"]           = "";
                $ultimov["paquete"]             = "";
                $ultimov['fecha_agenda']        = "";
                $ultimov['hora_agenda']         = "";
                $ultimov["estado_agendamiento"] = "0-0";
                $ultimov["coordinado2"]         = "0";
                
                //Envio a OT
                $savedata["otdata"] = $ultimov;
                $rot = Helpers::ruta(
                    'officetrack/enviartarea',
                    'POST', 
                    $savedata, 
                    false
                );

                //Registrar Pendiente
                $dataGestionPendiente['estado_agendamiento']='0-0';
                $dataGestionPendiente['motivo']='2';
                $dataGestionPendiente['submotivo']='18';
                $dataGestionPendiente['estado']='7';
                $dataGestionPendiente['horario_id']='';
                $dataGestionPendiente['dia_id']='';
                $dataGestionPendiente['fecha_agenda']='7';
                $dataGestionPendiente['tecnico']='';
                $dataGestionPendiente['usuario_sistema']='sistema';//697

                $rgm = Helpers::ruta(
                    'gestion_movimiento/crear',
                    'POST', 
                    $dataGestionPendiente, 
                    false
                );
                
                //Respuesta OT
                //$getOtoff = $rot->officetrack;
            }
            
        }

        
        $rgm = Helpers::ruta(
                    'gestion_movimiento/crear',
                    'POST', 
                    $dataGestion, 
                    false
            );

        $rgm= Helpers::stdToArray($rgm);
        //$rgm["sql"]=$rgm2["sql"];
        $rgm['estofic']=$rgm2['estofic'];
        
        //Registra o actualiza XY del cliente
        $dataXyCliente = array(
            'codigo' => $dataGestion['inscripcion'], 
            'nombre' => $dataGestion['nombre_cliente_critico'],
            'coord_x' => $dataGestion['x'],
            'coord_y' => $dataGestion['y'],
            'direccion' => $dataGestion['direccion_instalacion'],
            'estado' => 1
        );
        
        if ($dataGestion["cliente_xy_insert"]==1) {
            $query = DB::table('clientes')->insert($dataXyCliente);
        } else {
            $query = DB::table('clientes')
                ->where('codigo', $dataGestion['inscripcion'])
                ->update($dataXyCliente);
        }
        
        $rvalida="0";
        if ( /*$dataGestion["estado_agendamiento"]!='2-0' and*/ Input::get('tecnico') and Input::get('tecnico')!='' ) {
            $valida=array();
            //Indica si cumple con el envio a officetrack acitividad + quiebre
            $valida["actividad_id"]=$dataOfficetrack["actividad_id"];
            $valida["quiebre_id"]=$dataOfficetrack["quiebre_id"];
            //El estado del tecnico de officetrack
            $valida["tecnico_id"]=$dataOfficetrack["tecnico"];
            $valida["celula_id"]=$dataOfficetrack["celula"];
            //El estado de Agendamiento para officetrack
            $valida["motivo_id"]=$dataOfficetrack["motivo"];
            $valida["submotivo_id"]=$dataOfficetrack["submotivo"];
            $valida["estado_id"]=$dataOfficetrack["estado"];
            //El evento indica si anteriormente ya se realizÃ³ una transacciÃ³n OT
            $valida["transmision"]=$dataOfficetrack["transmision"];
            
            $rvalida =  Helpers::ruta(
                'officetrack/validar',
                'POST', 
                $valida, 
                false
            );
        }

        // true indica que se enviara a officetrack
        $dataGestion['officetrack_envio']=$rvalida;
        if ( $rvalida=="1" and $rgm['rst']=="1" ) {
            $dataOfficetrack['gestion_id']=$rgm['gestion_id'];
            /*if ( !isset($dataOfficetrack['id_gestion']) ) {
                $idGestion=Gestion::getGenerarID();
                $dataOfficetrack['gestion_id']=$idGestion;
                $dataGestion['gestion_id_officetrack']=$idGestion;
            }*/
            $tecnico = Tecnico::find($dataOfficetrack['tecnico']);
            $dataOfficetrack['carnet']=$tecnico['carnet_tmp'];

            $estado = Estado::find($dataOfficetrack['estado']);
            $dataOfficetrack['estado']=$estado['nombre'];

            $horarioTipo = HorarioTipo::find($dataOfficetrack['horario_tipo']);
            $dataOfficetrack['duration']=$horarioTipo['minutos'];


            $velocidad=array('','','');
            if(trim($dataOfficetrack['paquete'])!=''){
                $velocidad=explode("|",$dataOfficetrack['paquete']);
            }

            $dataOfficetrack['velocidad']=$velocidad[2];
            $dataOfficetrack['eecc_final']=$dataOfficetrack['empresa_id'];
            $dataOfficetrack['cr_observacion']=$dataOfficetrack['observacion2'];
            
            $savedata["otdata"] = $dataOfficetrack;
            $rot = Helpers::ruta(
                'officetrack/enviartarea',
                'POST', 
                $savedata, 
                false
            );

            $rot= Helpers::stdToArray($rot);
            if ( $rot['officetrack']=="OK" ) { //registrara normalmente
                DB::commit();
                $rgm['msj']='Registro realizado correctamente con Officetrack';
                $rgm['estado_agendamiento']=$dataGestion['estado_agendamiento'];
                $rgm['tecnico']=$dataGestion['tecnico'];

                if($dataGestion["estado_agendamiento"]!='1-1' AND $dataGestion["tecnico"]!=''){
                    $url="http://psiweb.ddns.net:2230/webpsi/sms_enviar_individual_ajax.php";
                    $tecnicoinfo = DB::table('tecnicos')
                                    ->where('id', $dataGestion["tecnico"])
                                    ->first();
                    $postData=array(
                        'enviar_sms' =>1,
                        //'celular'    =>substr($tecnicoinfo->celular,-9),
                        'celular' =>'996475583', // poner celular para probar
                        'iduser'     =>Auth::user()->id,
                        'mensaje'    =>"La actuación: ".$dataGestion['codactu']." ha sido eliminada, favor de sincronizar para actualizar. PSI-OFFICETRACK"
                    );
                    $rgm['mensaje']="Llego";
                    $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_HEADER, false);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    //Retorno  
                    $result = curl_exec($ch);
                    curl_close($ch);
                }
                else{
                    $rgm['mensaje']="No Llego :(";
                }
                return Response::json(
                    $rgm
                );
            } else {
                DB::rollback();
                return Response::json(
                    array(
                        'rst'=>2,
                        'msj'=>'No se pudo realizar el envio : '.
                                $rot['officetrack'].
                                '; Intente nuevamente el envio a officetrack',
                        'codactu'=>$dataGestion['codactu']
                    )
                );
            }
        } elseif( $rgm['rst']=="1" ) {
            DB::commit();
            $rgm['msj']='Registro realizado correctamente';
            return Response::json(
                $rgm
            );
        } else {
            DB::rollback();
            $this->_errorController->saveError($rgm['err']);
            
            return Response::json(
                array(
                    'rst'=>2,
                    'msj'=>$rgm['msj']
                )
            );
        } /*else { // registrarÃ¡ directo sin officetrack
            $rgm = Helpers::ruta(
                'gestion_movimiento/crear',
                'POST', 
                $dataGestion, 
                false
            );

            $rgm= Helpers::stdToArray($rgm);
            $rgm['msj']='Registro realizado correctamente';
            return Response::json(
                $rgm
            );
        }*/
        

    }

    public function postExtraerxy()
    {
        $coord = array("coord"=>array("x"=>"", "y"=>""), "rst"=>0, "ins"=>0);
        $array = new stdClass();
        $array->tipoactu = Input::get('tipoactu');
        $array->fftt = Input::get('fftt');

        //Buscar xy en tabla clientes 
        $xy = DB::table('clientes')
                ->where('codigo', '=', Input::get('cod_cliente'))
                ->where('estado', '=', 1)
                ->get();
        if (count($xy) > 0) {
            foreach ($xy as $key=>$val) {
                $coord["coord"]["x"] = $val->coord_x;
                $coord["coord"]["y"] = $val->coord_y;
            }
            $coord["rst"] = 1;
        } else {
            $data["data"][]=$array;
            $xy=$this->_VisorgpsController->getActuCoord($data);

            if (count($xy) > 0) {
                foreach ($xy as $key=>$val) {
                    $coord["coord"]["x"] = $val->x;
                    $coord["coord"]["y"] = $val->y;
                }
                $coord["rst"] = 1;
                $coord["ins"] = 1;
            }
        }
        return json_encode($coord);
    }

    public function postUse()
    {
        /*$gestion_id=Input::get('gestion_id');
        $gestionUsuario = new GestionUsuario;
        $gestionUsuario['gestion_id']=$gestion_id;
        $gestionUsuario['usuario_id']=Auth::user()->id;
        $gestionUsuario['tiempo']=5;
        $gestionUsuario->save();

        $gestionUsuarioBk = new GestionUsuarioBk;
        $gestionUsuarioBk['gestion_id']=$gestion_id;
        $gestionUsuarioBk['usuario_id']=Auth::user()->id;
        $gestionUsuarioBk['tiempo']=5;
        $gestionUsuarioBk->save();*/

        /*$sqlv=DB::table('gestion_usuario as gu')
                ->join(
                    'usuarios AS u',
                    'u.id', '=', 'gu.usuario_id'
                )
                ->join(
                    'gestiones_detalles AS gd',
                    'gd.gestion_id', '=', 'gu.usuario_id'
                )
                ->get();*/
    }

    public function postEmpty()
    {
        $gestion_id=Input::get('gestion_id');
    }

}
