<?php
class Helpers
{
    /**
     * retorna la cantidad de registros en gestiones_movimientos segun tecnico,
     * entre fechaIni y fechaFin, entre hora_inicio y hora_fin, y dia, segun la
     * ultima gestion en gestiones_movimientos.
     *
     * @return Response
     * @param $fechaIni '2015-03-10'
     * @param $fechaFin '2015-03-10'
     * @param $tecnicoId 20
     * @param $hora     09:00:00
     * @param $diaId    [1-7]
     * Helpers::buscarGestion()
     */
    public static function buscarGestion(
        $fechaIni, $fechaFin, $tecnicoId, $hora, $diaId
        ) 
    {

        $query = "SELECT 
                COUNT(g.id) as existe
                FROM
                gestiones g
                JOIN gestiones_movimientos gm ON g.id=gm.gestion_id
                JOIN ( 
                    SELECT MAX(gm2.id) id
                    FROM gestiones_movimientos gm2
                    INNER JOIN estado_motivo_submotivo ems 
                    ON (ems.estado_id=gm2.estado_id 
                    AND ems.submotivo_id=gm2.submotivo_id 
                    AND ems.motivo_id=gm2.motivo_id
                    AND ems.req_horario=1
                    AND ems.estado=1
                    )
                    GROUP BY gm2.gestion_id
                ) mx
                ON gm.id=mx.id
                JOIN actividades a ON g.actividad_id=a.id
                JOIN horarios h ON gm.horario_id=h.id
                WHERE gm.estado_id IN (SELECT estado_id 
                                       FROM estado_motivo_submotivo 
                                       WHERE req_horario=1 AND estado=1)
                AND gm.fecha_agenda BETWEEN ? AND ?
                AND gm.tecnico_id=?
                AND ? BETWEEN hora_inicio AND hora_fin
                AND gm.dia_id=?
                AND gm.estado=1";

        $resultado= DB::select(
            $query,
            array(
                $fechaIni,
                $fechaFin,
                $tecnicoId,
                $hora,
                $diaId
            )
        );
        foreach ( $resultado as $val ) {
            $resultado = $val->existe;
        }
        return $resultado;

    }
    
    /**
     * Metodo para crear request entre controladores
     * 
     * @param type $url Metodo destino
     * @param type $method Metodo de envio (GET o POST)
     * @param type $data Arreglo de datos
     * @param type $json Si el retorno es json o array
     * @return type JSON o ARRAY
     */
    public static function ruta($url, $method, $data, $json=true) 
    {
        //Datos enviados, vector
        Input::replace($data);
        //Crea Request via GET o POST
        $request = Request::create($url, $method);
        //Obtener response
        $response = Route::dispatch($request);
        //Solo contenido, en formato json
        $content = $response->getContent();
        
        if ($json) {
            //Retorna formato JSON
            return $content;
        } else {
            //Retorna un arreglo
            return json_decode($content);
        }
    }
    
    /**
     * Convierte una clase STD to Array
     * @param type $obj La StdClass
     * @return array
     */
    public static function stdToArray($obj,$tipo=true)
    {
        return json_decode(json_encode($obj), $tipo);
    }
    /**
     * Convierte una clase STD to Array
     * @param type $obj La StdClass
     * @return array
     */
    public static function limpia_campo_mysql_text($text)
    {
        $text = preg_replace('/<br\\\\s*?\\/??>/i', "\\n", $text);
        return str_replace("<br />", "\n", $text);
    }
    /**
     * Concatena fecha y hora a la caena ingresada
     * @param type $text string 
     * @return $filename string
     */
    public static function convert_to_file_excel($text)
    {
        $fecha = date("d/m/Y");
        $hora = date("h:i:s");
        $hh = substr($hora, 0, 2);
        $mm = substr($hora, 3, 2);
        $ss = substr($hora, 6, 2);
        $hora = $hh."_".$mm."_".$ss;

        $filename = $text.'_'.$fecha."-".$hora.".xls";
        return $filename;
    }
    /**
     * Concatena fecha y hora a la caena ingresada
     * @param type $fecha string {aaaa-mm-dd [hh:mm:ss]]}
     * @return $fecha string {dd-mm-aaaa [hh:mm:ss]}
     */
    public static function convert_to_date($fecha, $option = 'date')
    {
        list($fecha,$hora) = explode(" ", $fecha);
        list($anio,$mes,$dia) = explode("-", $fecha);

        if ($option=='date')
            $fecha = $dia."-".$mes."-".$anio;
        else
            $fecha = $dia."-".$mes."-".$anio." ".$hora;

        return $fecha;
    }
    
    /**
     * Retorna el contenido de un archivo en formato JSON
     * @param type $file
     * @return type JSON
     */
    public static function fileToJsonAddress($file, $cabecera=1) 
    {
        $data = array();
        $gestor = fopen($file, "r");
        $n = 0;
        if ($gestor) {
            while (($bufer = fgets($gestor, 4096)) !== false) {
                if ($n >= $cabecera) {
                    $s = array("á", "é", "í", "ó", "ú", "\r", "\n", "\r\n", "\"");
                    $r = array("a", "e", "i", "o", "u", "", "", "", "");

                    $line = str_replace($s, $r, $bufer);

                    $data[] = utf8_encode($line);
                }
                $n++;
            }
            fclose($gestor);
        }
        
        return $data;
    }
    /**
     * convertir long a imagen
     */
    public static function base64_to_jpeg($base64_string, $output_file) {
        $ifp = fopen($output_file, "w+");

        //$data = explode(',', $base64_string);
        fwrite($ifp, base64_decode($base64_string));
        fclose($ifp);

        return $output_file;
    }
    /**
     * actualizar el codigo segun los parametros contenidos en $array y $sql
     *  Si encuentra -> 
     *    1.1 Si en ultimos_movimientos, estado_legado='LIQUIDADO' => FIN (msg: LIQ EN LEGADO)
     *    1.2 Si estado en webPSI = 'CANCELADO' => FIN (msg: ORDEN CANCELADA)
     *    1.3 Si tiene fecha_agenda fecha_agenda >= hoy
     *        1.3.1   Si forzar cambio => UPDATE ultimos_movimientos, fecha_agenda = '', estado='pendiente'
     *                     => UPDATE gestiones_detalles
     *                     => INSERT GESTIONES_MOVIMIENTOS
     *            (msg: Se actualiza ... )
     *        1.3.2   Si no fuerza cambio => FIN (msg: NO se actualiza por...)
     *    1.4 actualizar sin cambiar ultimos_movimientos
     *  No encuentra -> Buscar en temporal
     *    2.1 Buscar en averias_criticos_final => Actualiza => FIN
     *    2.2 Buscar en tmp_provision  => Actualiza => FIN
     *  Si no encuentra (gestion , temporal)
     * @param type string $codigo
     * @param type array $array
     * @param type $sql
     * @return type array
     *           $row = array(
     *          'codigo'=>$averia,
     *          'quiebre'=>$quiebre,
     *          'contrata'=>$empresa,
     *          'estado'=>$respuesta
     *          );
     */
    public static function actualizarQuiebre($codigo, $quiebreId, $quiebre, $empresaId, $empresa, $forzar) 
    {
        $gestion = GestionDetalle::getGestiones($codigo);
        $cantGestion = count($gestion);
        $temporal = GestionDetalle::getGestionesTemporales($codigo);
        $cantTemporal = count($temporal);
        $hoy = date("Y-m-d");
        $mensaje='';

        if ($cantGestion>0) {//1
            $gestionId=$gestion[0]->id;
            $estadoLegado=$gestion[0]->estadoLegado;
            $estadoId=$gestion[0]->estadoId;
            $fechaAgenda=$gestion[0]->fechaAgenda;
            $oldQuiebreId=$gestion[0]->quiebreId;
            $oldEmpresaId=$gestion[0]->empresaId;
            $oldQuiebre=$gestion[0]->quiebre;
            $oldEmpresa=$gestion[0]->empresa;

            //validar empresa y/o quiebre igual
            if ($quiebre!='' && $empresa!='') {//selecciona quiebre y contrata
                if ($oldQuiebreId!=$quiebreId && $oldEmpresaId!=$empresaId) {
                    $array['quiebre_id']=$quiebreId;
                    $array['empresa_id']=$empresaId;
                    $mensaje="Se actualizo Quiebre y Contrata en PSI";
                } elseif ($oldQuiebreId!=$quiebreId) {
                    $array['quiebre_id']=$quiebreId;
                    $mensaje="Se actualizo solo quiebre, No se actualizo Contrata de ".$oldEmpresa." a ".$empresa;
                } elseif ($oldEmpresaId!=$empresaId) {
                    $array['empresa_id']=$empresaId;
                    $mensaje="Se actualizo solo Contrata, No se actualizo Quiebre de ".$oldQuiebre." a ".$quiebre;
                } else {//igual ambos
                    return "No se actualizo Quiebre de ".$oldQuiebre." a ".$quiebre.', ni Contrata '.$oldEmpresa." a ".$empresa;
                }
            } elseif ($quiebre!='') {//selecciona solo quiebre
                if ($oldQuiebreId!=$quiebreId) {//el quiebre anterior y el actual son diferentes
                    $array['quiebre_id']=$quiebreId;
                    $mensaje="Se actualizo Quiebre en PSI";
                } else {
                    return "No se actualizo Quiebre de ".$oldQuiebre." a ".$quiebre;
                }
            } elseif ($empresa!='') {//selecciona solo empresa
                if ($oldEmpresaId!=$empresaId) {//la contrata anterior es diferente a la actual
                    $array['empresa_id']=$empresaId;
                    $mensaje="Se actualizo Contrata en PSI";
                } else {
                    return "No se actualizo Contrata de ".$oldEmpresa." a ".$empresa;
                }
            } else {
                return "No se actualizo, no ha seleccionado quiebre ni contrata";
            }

            if ($estadoLegado=='LIQUIDADO') {//1.1
                return "No se actualizo, se encuentra LIQUIDADO en LEGADO";
            } elseif ($estadoId==4) {//1.2
                return "No se actualizo, orden CANCELADA";
            } elseif ($fechaAgenda>=$hoy) {//1.3
                if ($forzar=='1') {//1.3.1
                    $respuesta=GestionDetalle::updateGestiones($codigo,$gestionId, $array,'agendado',$oldQuiebre,$oldEmpresa);
                    //si hubo un error
                    if ($respuesta==0) {
                        return "Ocurrio un error al intentar actualizar";
                    } else {
                        return $mensaje;
                    }
                } else {//1.3.2
                    return "No se actualizo, tiene agenda pendiente";
                }
            } else {//1.4
                $respuesta=GestionDetalle::updateGestiones($codigo,$gestionId, $array,'',$oldQuiebre,$oldEmpresa);
                //si hubo un error
                if ($respuesta==0) {
                    return "Ocurrio un error al intentar actualizar";
                } else {
                    return $mensaje;
                }
            }
        } else {
            if($cantTemporal>0) {//2
                $actividad = $temporal[0]->actividad;
                $oldQuiebre = $temporal[0]->quiebre;
                $oldEmpresa = $temporal[0]->empresa;
                        //validar cambio de quiebre y/o empresa
                if ($quiebre!='' && $empresa!='') {
                    if ($oldQuiebre!=$quiebre && $oldEmpresa!=$empresa) {
                        $sql = " eecc_final='$empresa', quiebre='$quiebre' ";
                        $mensaje="Se actualizo  Quiebre y Contrata en Temporales";
                    } elseif ($oldQuiebre!=$quiebre) {
                        $sql = "  quiebre='$quiebre' ";
                        $mensaje="Se actualizo solo quiebre, No se actualizo Contrata de ".$oldEmpresa." a ".$empresa;
                    } elseif ($oldEmpresa!=$empresa) {
                        $sql = " eecc_final='$empresa' ";
                        $mensaje="Se actualizo solo Contrata, No se actualizo Quiebre de ".$oldQuiebre." a ".$quiebre;
                    } else {//igual ambos
                        return "No se actualizo Quiebre de ".$oldQuiebre." a ".$quiebre.', ni Contrata '.$oldEmpresa." a ".$empresa;
                    }
                } elseif ($quiebre!='') {
                    if ($oldQuiebre!=$quiebre) {//el quiebre anterior y el actual son diferentes
                        $sql = " quiebre='$quiebre' ";
                        $mensaje="Se actualizo Quiebre en Temporales";
                    } else {
                        return "No se actualizo Quiebre de ".$oldQuiebre." a ".$quiebre;
                    }
                } elseif ($empresa!='') {
                    if ($oldEmpresa!=$empresa) {//la contrata anterior es diferente a la actual
                        $sql = " eecc_final='$empresa' ";
                        $mensaje="Se actualizo Contrata en Temporales";
                    } else {
                        return "No se actualizo Contrata de ".$oldEmpresa." a ".$empresa;
                    }
                } else {
                    return "No se actualizo, no ha seleccionado quiebre ni contrata";
                }
                //2.1
                //2.2
                try {
                    GestionDetalle::updateGestionesTemporales($actividad, $sql, $codigo);
                } catch (Exception $exc) {
                    $this->_errorController->saveError($exc);
                    if ($exc->errorInfo['0']=='42S02') {//id, tabla not found
                        return 'No se actualizo, vuelva a intentar';
                    }
                    return "No se actualizo, hubo un error";
                }
                return $mensaje;
            } else {
                return "No se actualizo, orden no existe";
            }
        }
        return '';
    }
    
    /**
     * Lee iconos de un directorio
     * @return type
     */
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

    /**
     * Genera un arreglo de iconos de tecnicos y agendas
     * @return string
     */
    public static function iconArray() 
    {
        $lista = array();
        $iconArray = array();        
        
        $folder = "./img/icons/visorgps/";

        if ($od = opendir($folder)) {
            while (false !== ($file = readdir($od))) {
                if ($file != "." && $file != "..") {
                    $lista[] = $file;
                }
            }
            closedir($od);
        }
        
        foreach ($lista as $val) {
            $part = explode("_", $val);
            $iconArray[substr($part[1], 0, 6)] = $part[1];
        }
        
        $n = 0;
        foreach ($iconArray as $key=>$val) {
            $iconArray[$n] = $iconArray[$key];
            unset($iconArray[$key]);
            $n++;
        }

        return $iconArray;
    }
}
