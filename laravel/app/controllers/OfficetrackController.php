<?php
use ClassOT\OfficeTrackGetSet;
class OfficetrackController extends \BaseController
{
    public function __construct(ErrorController $ErrorController)
    {
        $this->error = $ErrorController;
    }

    /**
     * Envio de una tarea hacia officetrack
     *
     * $data['fecha_agenda']            : Fecha de agendamiento
     * ** $data['hora_agenda']           : Hora de agendamiento (rango)
     * $data["gestion_id"]              : ID de gestion autogenerado
     * $data['codactu']                 : Averia, peticion o requerimiento
     * $data['fecha_registro']          : Fecha de registro de la actuacion
     * $data['nombre_cliente']          : Nombre del cliente
     * $data['direccion_instalacion']   : Direccion del cliente
     * $data['actividad']               : Averia o Provision
     * $data['codmotivo_req_catv']      : Codigo CMS
     * $data['orden_trabajo']           : Orden de trabajo
     * $data['fftt']                    : Facilidades tecnicas
     * $data['dir_terminal']            : Direccion de terminal
     * $data['inscripcion']             : Inscripcion o codigo de cliente
     * $data['mdf']                     : MDF
     * $data['segmento']                : Codigo de segmento
     * $data['clase_servicio_catv']     : Dato CMS
     * $data['total_averias']           : Total averias reportadas
     * $data['zonal']                   : Zonal (LIM, ARE, ...)
     * $data['llamadastec15dias']       : Numero de llamadas tecnicas 15 dias
     * ** $data['quiebre']               : Nombre de quiebre
     * $data['lejano']                  : Dato lejano
     * $data['distrito']                : Nombre de distrito
     * $data['averia_m1']               : Dato M1
     * $data['telefono_codclientecms']  : Dato CMS
     * $data['area2']                   : Dato tmp area2
     * ** $data['carnet']                : Carnet del tenico
     * ** $data['eecc_final']            : Empresa colaboradora asignada
     * ** $data['estado']                : Estado en web psi
     * $data['cr_observacion']          : Observacion
     * ** $data['velocidad']             : Indice 2 de explode("|", paquete)
     * * $data['duration']              : Duracion actividad en horas
     *
     * @param type $data Arreglo de datos de la orden
     * @return Array 'officetrack'=> "OK", "Error"
     */
    public function postEnviartarea($data = array())
    {
        if (
                Input::has('otdata')
                and is_array(Input::get('otdata'))
                and count(Input::get('otdata')) > 0
        ) {
            $data = Input::get('otdata');
        }

        //Url OT
        $url = Config::get("wpsi.ot.url");

        if ($data["estado_agendamiento"] != '1-1') {
            $tecnico = "";
            $data['carnet'] = "LA0000";
            $data['fecha_agenda'] = "";
            $data['hora_agenda'] = "";
        }
        //$data['carnet']="LA0000";
        //$data['carnet'] = "666";
        //$data['codactu'] = 'TEST0006';

        $agendaArray = explode("-", $data['hora_agenda']);
        if (isset($agendaArray[1])) {
            $dueDate = date(
                "YmdHis",
                strtotime(
                    $data['fecha_agenda']
                    . " "
                    . $agendaArray[1] . ":00"
                )
            );
        } else {
            $dueDate = date("YmdHis", strtotime($data['fecha_registro']));
        }

        //Para horario libre
        if (isset($agendaArray[0]) and strtolower($agendaArray[0])=='libre') {
            $data["duration"] = 1439;
            $dueDate = date("YmdHis", strtotime($data['fecha_agenda'] . "23:59:59"));
        }

        //Ultimo movimiento
        $ultimo = DB::table("ultimos_movimientos")
                ->where('gestion_id', '=', $data["gestion_id"])
                ->get();

        //Seleccionar datos
        $envio['UserName'] = "";
        $envio['Password'] = "";
        $envio['Operation'] = "AutoSelect";
        $envio['TaskNumber'] = $data["gestion_id"];
        $envio['EmployeeNumber'] = $data['carnet'];
        $envio['DueDateAsYYYYMMDDHHMMSS'] = $dueDate;
        $envio['Duration'] = number_format(($data["duration"]/60), 2);
        $envio['Notes'] = "";

        $envio['Description'] = $data["gestion_id"]
                . "-"
                . $data['codactu'];
        $envio['Status'] = "NewTask";
        $envio['CustomerName'] = $data['hora_agenda']
                . " / "
                . $data['nombre_cliente'];

        //Ultimos datos
        $x = 0;
        $y = 0;
        $quiebreId = 0;

        if (count($ultimo) > 0) {
            $x = $ultimo[0]->x;
            $y = $ultimo[0]->y;
            $quiebreId = $ultimo[0]->quiebre_id;
        }

        $envio['Location'] = array(
            "East" => $x,
            "North" => $y,
            "Address" => $data['direccion_instalacion']
        );
        //$envio['Data1']		=	trim(
        //                                    $data['fecha_agenda']
        //                                    . " "
        //                                    . $data['hora_agenda']
        //                                );
        $envio['Data2'] = $data['codactu'];
        $envio['Data3'] = $data['fecha_registro'];
        $coordinado="NO";
        if ($data["coordinado2"]>0) {
            $coordinado="SI";
        }
        $envio['Data4'] = $coordinado;
        //$envio['Data5']		=	$data['direccion_instalacion'];
        $envio['Data6'] = $data["actividad"]
                . " - "
                . $data['codmotivo_req_catv'];
        $envio['Data7'] = $data['orden_trabajo'];
        $envio['Data8'] = $data['fftt'];
        $envio['Data9'] = $data['dir_terminal'];
        $envio['Data10'] = $data['inscripcion'];
        $envio['Data11'] = $data['mdf'];
        $envio['Data12'] = $data['segmento'];
        $envio['Data13'] = $data['clase_servicio_catv'];
        $envio['Data14'] = $data['total_averias'];
        $envio['Data15'] = $data['zonal'];
        $envio['Data16'] = $data['llamadastec15dias'];
        $envio['Data17'] = $data['quiebre'];
        $envio['Data18'] = $data['lejano'];
        $envio['Data19'] = $data['distrito'];
        $envio['Data20'] = $data['averia_m1'];
        $envio['Data21'] = $data['telefono_codclientecms'];
        $envio['Data22'] = $data['area2'];
        $envio['Data23'] = Config::get("wpsi.geo.public.mapord")
                . $data['carnet']
                . '/'
                . $data['gestion_id'];
        //tipo_servicio // ubicacion
        $envio['Data24'] = Config::get("wpsi.geo.public.maptec")
                . $data['carnet'];
        //tipo_actuacion// ruta del dia
        $envio['Data25'] = $data['eecc_final'];
        //Data26: La observacion (obs_dev, obs_102)
        if (count($ultimo) > 0) {
            $envio['Data26'] = $ultimo[0]->observacion;
        }
        //$envio['Data27']	=	$data['estado']; //Estado Webpsi
        $envio['Data28'] = $data['cr_observacion'];
        $envio['Data29'] = $data['velocidad'];

        $cantidadcomp=0;
        $auxComponente="";
        $conteocomp=1;
        $arrayComponentefin=array();
        if (isset($data['componente_text'])) {
            $componente = implode("^^", $data['componente_text']);
            $arrayComponente = explode("^^", $componente);
            for ($i = 0; $i < count($arrayComponente); $i++) {
                if ($i==0) {
                    $auxComponente=$arrayComponente[$i];
                }
                if ($arrayComponente[$i]!=$auxComponente) {
                    array_push(
                        $arrayComponentefin,
                        $conteocomp
                        . ") "
                        .$auxComponente
                        ."("
                        .$cantidadcomp
                        .")"
                    );
                    $auxComponente=$arrayComponente[$i];
                    $cantidadcomp=0;
                    $conteocomp++;
                }
                $cantidadcomp++;
                //$arrayComponente[$i] = ($i + 1) . ") " . $arrayComponente[$i];
            }
            array_push(
                $arrayComponentefin,
                $conteocomp. ") ".$auxComponente."(".$cantidadcomp.")"
            );
            $componente = implode("\n", $arrayComponentefin);
            $envio['Data30'] = $componente;
        }
        $envio['Options'] = "SendNotificationToMobile";

        /**
         * Validacion de distancia
         * Grupo-quiebre: Movistar 1 (11) y Exclusivo (7)
         * distancia 300 metros => 0.3 Km
         */
        $valorokArray = array(7, 11);
        $quiebreObj = DB::table('quiebres')
                        ->where('id', $quiebreId)
                        ->first();
        $quiebreGrupoId = $quiebreObj->quiebre_grupo_id;

        if (array_search($quiebreGrupoId, $valorokArray) !== false ) {
            $envio["MaximalRadiusForEntries"] = 0.3;
            $envio["ProhibitEntriesOutsideRadius"] = '1';
        }  

        /*$carnetArray = array(666, 'LA5407', 'LA3900', 'LA4093', 'LA5703', 'LA4261');
        if (array_search($data['carnet'], $carnetArray) !== false) {
            $envio["MaximalRadiusForEntries"] = 0.3;
            $envio["ProhibitEntriesOutsideRadius"] = '1';
        }*/

        //Array to json
        $cadena = json_encode($envio);

        //Data
        $postData = array(
            'cadena' => $cadena
        );
        $result = $this->enviarOfficeTrack($postData);
        /*$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //Retorno
        $result = curl_exec($ch);
        curl_close($ch);
        */
        return array('officetrack' => $result);
    }

    public function postValidar()
    {
        $r = 0;
        $dataValidar = array();
        $dataValidar = Input::all();

        $vo = array();
        $vo['actividad_id'] = $dataValidar['actividad_id'];
        $vo['quiebre_id'] = $dataValidar['quiebre_id'];

        $validaOfficetrack = Helpers::ruta(
            'gestion_movimiento/validaofficetrack', 'POST', $vo, false
        );

        $eo = array();
        $eo['tecnico_id'] = $dataValidar['tecnico_id'];
        $eo['celula_id'] = $dataValidar['celula_id'];

        $estadoOfficetrack = Helpers::ruta(
            'tecnico/estadoofficetrack', 'POST', $eo, false
        );

        $ea = array();
        $ea['motivo_id'] = $dataValidar['motivo_id'];
        $ea['submotivo_id'] = $dataValidar['submotivo_id'];
        $ea['estado_id'] = $dataValidar['estado_id'];

        $estadoAgendamiento = Helpers::ruta(
            'estado/estadoagendamiento', 'POST', $ea, false
        );

        if ($validaOfficetrack * 1 > 0 &&
                $estadoOfficetrack == '1' &&
                ( $dataValidar['transmision'] != "0" ||
                $estadoAgendamiento == '11'
                )
        ) {
            $r = 1;
        }

        return $r;
    }

    public function postDetallepaso()
    {
        $dato = trim(Input::get("dato"));
        $datoArray = explode("-|", $dato);
        $dataArray = explode("_", $datoArray[1]);
        $gestionId = $dataArray[2];

        $officetrack = new Officetrack();

        $data = array();
        $view = "";
        if ($datoArray[0] == '0001') {
            $view = "detalle_uno";
            $data = $officetrack->getPasouno($gestionId);
        }

        if ($datoArray[0] == '0002') {
            $view = "detalle_dos";
            $data = $officetrack->getPasodos($gestionId);
        }

        if ($datoArray[0] == '0003') {
            $view = "detalle_tres";
            $data = $officetrack->getPasotres($gestionId);
        }

        if (!empty($data)) {
            return View::make(
                'admin.officetrack.' . $view, array('data' => $data[0])
            );
        } else {
            return "Sin resultados";
        }
    }

    public function getPasouno()
    {

    }

    public function getPasodos()
    {

    }

    public function getPasotres()
    {

    }

    public function postCargar()
    {
        $o = DB::table('webpsi_officetrack.tareas')
                ->where('task_id', '=', Input::get('task_id'))
                ->orderBy('fecha_recepcion', 'ASC')
                ->get();

        return Response::json(
            array(
                'rst' => 1,
                'msj' => "Tareas Cargadas",
                'datos' => $o
            )
        );
    }

    public function postCargardetalle()
    {
        $o = array();
        $id=Input::get('task_id');
        $paso= explode("-", strtolower(Input::get('paso')));
        if ( substr($paso[1], 0, 6)=='inicio' ) {
            //it.id,pu.x, pu.y, pu.observacion
            $query='SELECT it.nombre url,iti.nombre,it.fecha_creacion,pu.*
                    FROM webpsi_officetrack.imagenes_tareas it
                    INNER JOIN webpsi_officetrack.imagenes_tipo iti
                        ON iti.id=it.imagen_tipo_id
                    INNER JOIN webpsi_officetrack.paso_uno pu
                        ON it.tarea_id=pu.task_id
                    WHERE substr(it.nombre,1,3)="p01"
                    AND pu.task_id="'.$id.'"
                    ORDER BY url';
                    //echo $query;
            $o = DB::select($query);
        } else if ( substr($paso[1], 0, 11)=='supervision' ) {
            //it.id,p.observacion,p.motivo
            $query='SELECT it.nombre url,iti.nombre,it.fecha_creacion,p.*
                    FROM webpsi_officetrack.imagenes_tareas it
                    INNER JOIN webpsi_officetrack.imagenes_tipo iti
                        ON iti.id=it.imagen_tipo_id
                    INNER JOIN webpsi_officetrack.paso_dos p
                        ON it.tarea_id=p.task_id
                    WHERE substr(it.nombre,1,3)="p02"
                    AND p.task_id="'.$id.'"
                    ORDER BY url';
            $o = DB::select($query);
        } else if ( substr($paso[1], 0, 6)=='cierre' ) {
            //it.id,p.observacion,p.estado
            $query='SELECT it.nombre url,iti.nombre,it.fecha_creacion,p.*
                    FROM webpsi_officetrack.imagenes_tareas it
                    INNER JOIN webpsi_officetrack.imagenes_tipo iti
                        ON iti.id=it.imagen_tipo_id
                    INNER JOIN webpsi_officetrack.paso_tres p
                        ON it.tarea_id=p.task_id
                    WHERE substr(it.nombre,1,3)="p03"
                    AND p.task_id="'.$id.'"
                    ORDER BY url';
            $o = DB::select($query);
        }

        return Response::json(
            array(
                'rst' => 1,
                'datos' => $o
            )
        );
    }

    /**
     * Reenvio hacia Officetrack
     * @return type
     */
    public function postProcesarot()
    {
        //Obtener carnet de tecnico
        $data['carnet'] = "";
        $tecnico = DB::table('tecnicos')
                ->where('id', '=', Input::get("tecnico_id"))
                ->get();
        if (isset($tecnico[0])) {
            $data['carnet'] = $tecnico[0]->carnet_tmp;
        }
        $data["coordinado2"] = Input::get("coordinado2");
        $data["gestion_id"] = Input::get("gestion_id");
        //$data['carnet']                 = "";
        $data['fecha_registro'] = Input::get("fecha_registro");

        $horaIni = "04:00:00";
        $horaFin = "06:00:00";
        $fechaHora = explode("/", Input::get("fh_agenda"));
        $data['fecha_agenda'] = trim($fechaHora[0]);
        $data['hora_agenda'] = trim($fechaHora[1]);
        $horas = explode("-", $data['hora_agenda']);
        if ( isset($horas[0]) ) {
            $horaIni = trim($horas[0]) . ":00";
        }
        if ( isset($horas[1]) ) {
            $horaFin = trim($horas[1]) . ":00";
        }

        //Duracion
        $toTime = strtotime("{$data['fecha_agenda']} $horaIni");
        $fromTime = strtotime("{$data['fecha_agenda']} $horaFin");
        $data["duration"] = round(abs($toTime - $fromTime) / 60, 2);

        $data['codactu'] = Input::get("codactu");
        $data['fecha_registro'] = Input::get("fecha_registro");
        $data['nombre_cliente'] = Input::get("nombre_cliente");
        $data['direccion_instalacion'] = Input::get("direccion_instalacion");
        $data["actividad"] = Input::get("actividad");

        $data['codmotivo_req_catv'] = Input::get("act_codmotivo_req_catv");
        $data['orden_trabajo'] = Input::get("orden_trabajo");
        $data['fftt'] = Input::get("fftt");
        $data['dir_terminal'] = Input::get("dir_terminal");
        $data['inscripcion'] = Input::get("inscripcion");
        $data['mdf'] = Input::get("mdf");
        $data['segmento'] = Input::get("segmento");
        $data['clase_servicio_catv'] = Input::get("clase_servicio_catv");
        $data['total_averias'] = Input::get("total_averias");
        $data['zonal'] = Input::get("zonal");
        $data['llamadastec15dias'] = Input::get("llamadastec15dias");
        $data['quiebre'] = Input::get("quiebre");
        $data['lejano'] = Input::get("lejano");
        $data['distrito'] = Input::get("distrito");
        $data['averia_m1'] = Input::get("averia_m1");
        $data['telefono_codclientecms'] = Input::get("telefono_codclientecms");
        $data['area2'] = Input::get("area2");
        $data['eecc_final'] = Input::get("eecc_final");
        $data["gestion_id"] = Input::get("gestion_id");
        $data['estado'] = Input::get("estado");
        $data['cr_observacion'] = Input::get("cr_observacion");
        $data['velocidad'] = Input::get("velocidad");
        $data["estado_agendamiento"] = "1-1";

        //Inicio componentes
        $arrComponentes = array();
        $cmp = Helpers::ruta(
            'cat_componente/cargar',
            'POST',
            array('codactu' => $data['codactu']), false
        );
        $cmp = Helpers::stdToArray($cmp);

        if ($cmp["rst"] == 1 and count($cmp["datos"]) > 0) {
            foreach ($cmp["datos"] as $val) {
                $arrComponentes[] = $val["nombre"];
            }
        }
        $data["componente_text"] = $arrComponentes;
        //Fin componentes

        $savedata["otdata"] = $data;

        $rot = Helpers::ruta(
            'officetrack/enviartarea', 'POST', $savedata, false
        );

        $rot = Helpers::stdToArray($rot);

        if($rot['officetrack']=='OK'){
            $query="select GenerarReenvio(".$data["gestion_id"].",".Auth::user()->id.",'".$data['cr_observacion']."')";
            $reenvio= DB::select($query);
        }

        return json_encode($rot);
    }

    private function enviarOfficeTrack($cadena)
    {
        //WebServices
        $otWsdl["tareas"]["ID"]         = Config::get("ot.tareas.ID");
        //$otWsdl["tareas"]["WSDL"]       = "http://officetrack.pe/services/TaskManagement.asmx?WSDL";
        $otWsdl["tareas"]["WSDL"]       = Config::get("ot.tareas.WSDL");

        $otWsdl["localizacion"]["ID"]   = Config::get("ot.localizacion.ID");
        $otWsdl["localizacion"]["WSDl"] = Config::get("ot.localizacion.WSDL");

        //Accesos

        $otAccess["UserName"] = Config::get("ot.UserName");
        $otAccess["Password"] = Config::get("ot.Password");
        //set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
        //require_once ($_SERVER['DOCUMENT_ROOT'].'/webpsi20/laravel/app/classOT/officetrack.php');

        $OfficeTrack = new OfficeTrackGetSet();
        //Existe la variable "cadena"
        $json = str_replace("&", "%26", $cadena['cadena']);
        if (isset($cadena) and ! empty($json))
        {
            try {
                //Validar cadena json
                $object = json_decode($json);
                if (is_object($object))
                {
                    //Cadena json OK, agregar usuario y clave
                    $object->UserName = base64_decode($otAccess["UserName"]);
                    $object->Password = base64_decode($otAccess["Password"]);

                    //Convertir a XMl
                    $xml = $this->arrayToXml(
                        $object,
                        '<CreateOrUpdateTaskRequest></CreateOrUpdateTaskRequest>'
                    );

                    $OfficeTrack->set_wsdl($otWsdl['tareas']['WSDL']);
                    $tareas = $OfficeTrack->get_client();

                    $result = $tareas->CreateOrUpdateTask(array('Request' => $xml));
                    $otRes = $result->CreateOrUpdateTaskResult;
                    //Almacenar trama enviada hacia OT

                    $officetrackModel = new Officetrack();

                    $officetrackModel->registrar($json, $result->CreateOrUpdateTaskResult);

                    return $otRes;
                } else {
                    //Cadena KO
                    throw new Exception("Cadena Json no valida");
                }
            } catch (Exception $exc) {
                //print_r($exc);
                //$this->error->handlerError($exc,$exc->getCode());
                /*$fo = fopen("/var/www/test/error_registro", "w+");
                fwrite($fo, date("Y-m-d H:i:s") . $exc->getMessage());
                fclose($fo);*/
            }
        }
    }

    private function arrayToXml($array, $rootElement = null, $xml = null) {
        $_xml = $xml;

        if ($_xml === null) {
            $_xml = new SimpleXMLElement($rootElement !== null ? $rootElement : '<root><root/>');
        }

        foreach ($array as $k => $v) {
            if (is_array($v)) { //nested array
                $this->arrayToXml($v, $k, $_xml->addChild($k));
            } elseif(is_object($v)) {
                $this->arrayToXml((array)$v, $k, $_xml->addChild($k));
            } else {
                $_xml->addChild($k, $v);
            }
        }
        return $_xml->asXML();
    }
}
