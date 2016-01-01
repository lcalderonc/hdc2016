<?php
require_once "lib/nusoap.php";
define("CURL_USER", "\$PSI20\$");
define("CURL_PASS", "\$1st3m@\$");

$url='http://10.226.44.222:7020/api/';
//$url='http://webpsi20/api/';
$client = new nusoap_client("http://10.226.44.222:7021/psi.php?wsdl", true);
//$client = new nusoap_client("http://test/soap_opp/psi.php?wsdl", true);

$error  = $client->getError();
if ($error) {
    echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
}

$tareaId = null;
$gestionId = null;

$xmlResponse = "<Response>"
                . "<Message>"
                    . "<Text>[msg]</Text>"
                    . "<Icon>Warning/Critical/Info</Icon>"
                    . "<ButtonText>OK</ButtonText>"
                . "</Message>"
                . "<ReturnValue>"
                    . "<ShortText>[shortText]</ShortText>"
                    . "<LongText>[longText]</LongText>"
                    . "<Value>[value]</Value>"
                    . "<Action></Action>"
                . "</ReturnValue>"
             . "</Response>";

//Captura de formulario
if (isset($_POST["Data"]) and ! empty($_POST["Data"])) {
    $xml = trim($_POST["Data"]);

    //Cadena vacia
    try {
        if ($xml === "") {
            throw new Exception("Cadena vac&iacute;a.");
        }
    } catch (Exception $exc) {
        //Registrar en DB lo capturado
        //echo $exc->getTraceAsString();
    }

    //Captura de datos
    $simpleXml = simplexml_load_string($xml);

    $gestionId = $simpleXml->Task->TaskNum;
}

if (isset($_POST["type"]) and $_POST["type"] == 'cambiodir') {
    //data
    $ubicacion = "";

    $carnet = $simpleXml->Employee->EmployeeNumber;

    $actualizaUbicacion = false;
    $datos = array();

    foreach ($simpleXml->Fields->Field as $key => $val) {
        $id = $val->Id;
        $value = $val->Value;

        if ($id == 'actualiza_ubicacion' and $value == 'si') {
            $actualizaUbicacion = true;
        }

        if ($id == 'tarea') {
            $tareaId = $val->Value;
            $gestionId = $val->Value;
        }

        if ($id == 'ubicacion') {
            $ubicacion = $val->Value;
            $ubicacionArray = explode(",", trim($ubicacion));

            if (isset($ubicacionArray[0])) {
                $datos["nuevo_x"] = $ubicacionArray[1];
            }

            if (isset($ubicacionArray[1])) {
                $datos["nuevo_y"] = $ubicacionArray[0];
            }
        }

        if ($id == 'direccion') {
            $datos["direccion"] = (string) $value;
        }

        if ($id == 'referencia') {
            $datos["referencia"] = (string) $value;
        }

        if ($id == 'nodo') {
            $datos["nodo"] = $value;
        }

        if ($id == 'troba') {
            $datos["troba"] = $value;
        }

        if ($id == 'tap') {
            $datos["tap"] = $value;
        }

        if ($id == 'amplificador') {
            $datos["amplificador"] = $value;
        }
    }

    /**
     * Validacion:
     *
     * Si la orden le pertece al tecnico y
     * estado = "Agendado con tecnico"
     * entonces puede cambiar la direccion
     */
    $url = "http://10.226.44.222:7020/api/tareatecnico";
    $hashg = hash('sha256', CURL_USER . CURL_PASS . $gestionId);
    $postData["hashg"] = $hashg;
    $postData["gestion_id"] = "$tareaId";
    $postData["carnet"] = "$carnet";

    try {
        $ch = curl_init();

        if (FALSE === $ch) {
            throw new Exception('failed to initialize');
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $tareaEstado = curl_exec($ch);

        if (FALSE === $tareaEstado) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }
        
        $tareaEstadoObj = json_decode($tareaEstado);
        
        //No pasa validacion
        if ($tareaEstadoObj->cantidad == 0) {
            $actualizaUbicacion = false;
        }

        curl_close($ch);
    } catch (Exception $e) {

        trigger_error(
            sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(),
                $e->getMessage()
            ),
            E_USER_ERROR
        );
    }

    //Actualizar xy de direccion y reenviar al tecnico
    if ($actualizaUbicacion) {
        //1. Updates tablas y guardar histórico
        $datos['gestion_id'] = $gestionId;
        $datos['carnet'] = $carnet;

        //Call API
        $url = "http://10.226.44.222:7020/api/actualizardireccion";

        $hashg = hash('sha256', CURL_USER . CURL_PASS . $gestionId);
        $postData["hashg"] = $hashg;
        $postData["gestion_id"] = "{$datos['gestion_id']}";
        $postData["carnet"] = "{$datos['carnet']}";
        $postData["x"] = "{$datos['nuevo_x']}";
        $postData["y"] = "{$datos['nuevo_y']}";
        $postData["direccion"] = "{$datos['direccion']}";
        $postData["referencia"] = "{$datos['referencia']}";

        try {
            $ch = curl_init();

            if (FALSE === $ch)
                throw new Exception('failed to initialize');

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $content = curl_exec($ch);

            if (FALSE === $content)
                throw new Exception(curl_error($ch), curl_errno($ch));

            // ...process $content now
            $visitaObj = json_decode($content);
            if ($visitaObj->estado !== false) {
                $msg = $visitaObj->msg;
                $searchArray = array(
                    '[msg]',
                    '[shortText]',
                    '[longText]',
                    '[value]'
                );
                $replaceArray = array(
                    $msg,
                    $msg,
                    'Proceso terminado',
                    $visitaObj->estado
                );
                $xmlResponse = str_replace(
                    $searchArray,
                    $replaceArray,
                    $xmlResponse
                );
                echo $xmlResponse;
            }

            curl_close($ch);
        } catch (Exception $e) {

            trigger_error(
                sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(),
                    $e->getMessage()
                ),
                E_USER_ERROR
            );
        }

        //2. Reenvío al técnico
        $url = "http://10.226.44.222:7020/api/reenviarot";

        $hashg = hash('sha256', CURL_USER . CURL_PASS . $gestionId);
        $postData["hashg"] = $hashg;
        $postData["gestion_id"] = "$gestionId";

        try {
            $ch = curl_init();

            if (FALSE === $ch)
                throw new Exception('failed to initialize');

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $content = curl_exec($ch);
            echo $content;

            if (FALSE === $content)
                throw new Exception(curl_error($ch), curl_errno($ch));

            curl_close($ch);
        } catch (Exception $e) {

            trigger_error(
                sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(),
                    $e->getMessage()
                ),
                E_USER_ERROR
            );
        }
    } else {
        //Mensaje "No se actualiza direccion"
        $searchArray = array(
            '[msg]',
            '[shortText]',
            '[longText]',
            '[value]'
        );
        $replaceArray = array(
            'Error',
            'Error',
            'No se pudo actualizar la direccion',
            ''
        );
        $xmlResponse = str_replace(
            $searchArray,
            $replaceArray,
            $xmlResponse
        );
        echo $xmlResponse;
    }
}

if (isset($_POST["type"]) and $_POST["type"] == 'ussd') {
    $carnet = $simpleXml->Employee->EmployeeNumber;
    $valor = "";
    $criterio = "";

    foreach ($simpleXml->Fields->Field as $key => $val) {
        $id = $val->Id;

        if ($id == 'Criterio de Consulta') {
            $criterio = trim($val->Value);
        }

        if ($id == 'Ingresar Dato') {
            $valor = trim($val->Value);
        }
    }

    if ($criterio == "Cliente CMS") {
        $postData["codclicms"] = "{$valor}";
    } elseif ($criterio == "Cliente ATIS") {
        $postData["codcli"] = "{$valor}";
    } elseif ($criterio == "Telefono") {
        $postData["telefono"] = "{$valor}";
    } elseif ($criterio == "Requerimiento") {
        $postData["requerimiento"] = "{$valor}";
    } elseif ($criterio == "Peticion") {
        $postData["peticion"] = "{$valor}";
    } elseif ($criterio == "Orden") {
        $postData["orden"] = "{$valor}";
    } elseif ($criterio == "DNI") {
        $postData["dni"] = "{$valor}";
    } elseif ($criterio == "X Y") {
        //
        $coord = explode(',', $valor);
        $x=trim($coord[0]);
        $y=trim($coord[1]);
        $postData["x"] = "{$x}";
        $postData["y"] = "{$y}";
    }
    //Cliente CMS
    //Cliente ATIS
    //Telefono
    //Requerimiento
    //Peticion
    //Orden
    //DNI
    //Nombre Cliente
    //X Y

    $url .= "getactu";
    $gestionId = "";
    $hashg = hash('sha256', CURL_USER . CURL_PASS . $gestionId);
    $postData["hashg"] = $hashg;
    $postData["gestion_id"] = "{$gestionId}";
    //$postData["tipo"] = "{$tipo}";
    //$postData["buscar"] = "{$valor}";

    try {
        //CURL
        $ch = curl_init();

        if (FALSE === $ch)
            throw new Exception('failed to initialize');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        //if (FALSE === $content)
        //    throw new Exception(curl_error($ch), curl_errno($ch));

        curl_close($ch);
        //web service
        $array=array(
            'asunto'=>'consulta',
            'actividad'=>'',
            'requerimiento'=>'',
            'serieDeco'=>'',
            'serieTarjeta'=>'',
            'telefonoOrigen'=>'',
            'clave'=>$criterio,
            'valor'=>$valor
            );

        $resultDos = $client->call("Tarea.insert",array($array));


    } catch (Exception $e) {

        trigger_error(
            sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(),
                $e->getMessage()
            ),
            E_USER_ERROR
        );
    }

    $resultData = json_decode($result);
    $datos = $resultData->datos;
    $longText="";
    if (isset($datos)) {
        foreach ( $datos as $key => $value) {
            if ( count( $value )>0 ) {
                for ($i=0;$i<count($value);$i++) {
                        $longText.="\n$key\n";
                        $longText.="----------(".($i+1).")----------\n";
                    if ($key=='fftt_directa' || $key=='fftt_secundaria') {
                        $longText.= "Par : ".$value[$i]->par."\n".
                                    "Caja : ".$value[$i]->caja."\n".
                                    "Inscripcion : ".$value[$i]->inscripcion."\n".
                                    "Solicitud : ".$value[$i]->solicitud."\n".
                                    "EstadoPar: ".$value[$i]->estadopar."\n".
                                    "Negocio: ".$value[$i]->negocio."\n".
                                    "Zonal: ".$value[$i]->zonal."\n".
                                    "DescCiudad: ".$value[$i]->DescCiudad."\n".
                                    "MDF: ".$value[$i]->MDF."\n".
                                    "DescMDF: ".$value[$i]->DescMDF."\n".
                                    "Cable: ".$value[$i]->Cable."\n".
                                    "Armario: ".$value[$i]->Armario."\n".
                                    "Bloque: ".$value[$i]->Bloque."\n".
                                    "Telefono : ".$value[$i]->telefono."\n".
                                    "Circuito: ".$value[$i]->circuito."\n".
                                    "DDN     : ".$value[$i]->ddn."\n".
                                    "Direccion: ".$value[$i]->direccion."\n".
                                    "Cliente : ".$value[$i]->cliente."\n".
                                    "posicionAdslGestel: ".$value[$i]->posicionAdslGestel."\n".
                                    "segmentoGestel: ".$value[$i]->segmentoGestel."\n".
                                    "sectorGestel: ".$value[$i]->sectorGestel."\n".
                                    "manzanaGestel: ".$value[$i]->manzanaGestel."\n".
                                    "velocidadBajadaGestel: ".$value[$i]->velocidadBajadaGestel."\n".
                                    "velocidadSubidaGestel: ".$value[$i]->velocidadSubidaGestel."\n".
                                    "descripcionModalidadGestel: ".$value[$i]->descripcionModalidadGestel."\n".
                                    "fecha insert: ".$value[$i]->fecha_insert."\n".
                                    "X: ".$value[$i]->X."\n".
                                    "Y: ".$value[$i]->Y."\n";;
                    } else {
                        $longText.= "Telefono: ".$value[$i]->telefono."\n".
                                    "Paterno : ".$value[$i]->appater."\n".
                                    "Materno : ".$value[$i]->apmater."\n".
                                    "Nombres : ".$value[$i]->nombre."\n".
                                    "C.Client: ".$value[$i]->codclie."\n".
                                    "C.CliCms: ".$value[$i]->codclicms."\n".
                                    "C.SerCms: ".$value[$i]->codservcms."\n".
                                    "Inscrip : ".$value[$i]->inscripcio."\n".
                                    "MDF     : ".$value[$i]->mdf."\n".
                                    "T. Paque: ".$value[$i]->tipopaq."\n".
                                    "Modalid : ".$value[$i]->modalidad."\n".
                                    "Velocid : ".$value[$i]->veloc."\n".
                                    "Nodo Tro: ".$value[$i]->nodotroba."\n".
                                    "DNI     : ".$value[$i]->nrodni."\n".
                                    "Direcci : ".$value[$i]->direccion."\n";
                    }
                }
            }
        }
    }
    $searchArray = array('[msg]', '[shortText]', '[value]', '[longText]');
    $replaceArray = array(
        'Respuesta',
        'Se procesó la consulta',
        '0',
        $longText
    );
    $xmlResponse = str_replace($searchArray, $replaceArray, $xmlResponse);
    echo $xmlResponse;
}

if (isset($_POST["type"]) and $_POST["type"] == 'consultname') {
    $carnet = $simpleXml->Employee->EmployeeNumber;
    $paterno = "";
    $materno = "";
    $nombre = "";

    foreach ($simpleXml->Fields->Field as $key => $val) {
        $id = $val->Id;

        if ($id == 'Apellido Paterno') {
            $paterno = trim($val->Value);
        }

        if ($id == 'Apellido Materno') {
            $materno = trim($val->Value);
        }

        if ($id == 'Nombres') {
            $nombre = trim($val->Value);
        }
    }


    $url .= "getactu";
    $gestionId = "";
    $hashg = hash('sha256', CURL_USER . CURL_PASS . $gestionId);
    $postData["hashg"] = $hashg;
    $postData["gestion_id"] = "{$gestionId}";
    $postData["paterno"] = "{$paterno}";
    $postData["materno"] = "{$materno}";
    $postData["nombre"] = "{$nombre}";

    try {
        //CURL
        $ch = curl_init();

        if (FALSE === $ch)
            throw new Exception('failed to initialize');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        //if (FALSE === $content)
        //    throw new Exception(curl_error($ch), curl_errno($ch));

        curl_close($ch);
        /*//web service
        $array=array(
            'asunto'=>'consulta',
            'actividad'=>'',
            'requerimiento'=>'',
            'serieDeco'=>'',
            'serieTarjeta'=>'',
            'telefonoOrigen'=>'',
            'clave'=>$criterio,
            'valor'=>$valor
            );

        $resultDos = $client->call("Tarea.insert",array($array));*/


    } catch (Exception $e) {

        trigger_error(
            sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(),
                $e->getMessage()
            ),
            E_USER_ERROR
        );
    }
    $resultData = json_decode($result);
    $datos = $resultData->datos;
    $longText="";
    if (isset($datos)) {
        foreach ( $datos as $key => $value) {
            if ( count( $value )>0 ) {
                for ($i=0;$i<count($value);$i++) {
                    $longText.="$key\n";
                    $longText.="----------(".($i+1).")----------\n";
                    $longText.= "Telefono: ".$value[$i]->telefono."\n".
                                "Paterno : ".$value[$i]->appater."\n".
                                "Materno : ".$value[$i]->apmater."\n".
                                "Nombres : ".$value[$i]->nombre."\n".
                                "C.Client: ".$value[$i]->codclie."\n".
                                "C.CliCms: ".$value[$i]->codclicms."\n".
                                "C.SerCms: ".$value[$i]->codservcms."\n".
                                "Inscrip : ".$value[$i]->inscripcio."\n".
                                "MDF     : ".$value[$i]->mdf."\n".
                                "T. Paque: ".$value[$i]->tipopaq."\n".
                                "Modalid : ".$value[$i]->modalidad."\n".
                                "Velocid : ".$value[$i]->veloc."\n".
                                "Nodo Tro: ".$value[$i]->nodotroba."\n".
                                "DNI     : ".$value[$i]->nrodni."\n".
                                "Direcci : ".$value[$i]->direccion."\n";
                }
            }
        }
    }
    $searchArray = array('[msg]', '[shortText]', '[value]', '[longText]');
    $replaceArray = array(
        'Respuesta',
        'Se procesó la consulta',
        '0',
        $longText
    );
    $xmlResponse = str_replace($searchArray, $replaceArray, $xmlResponse);
    echo $xmlResponse;
}