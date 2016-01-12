<?php

class Api extends Eloquent
{

    protected $table = '';
    /**
     * pruebas de sergio miranda, cuando inicia la peticion Tareas (procesado)
     * 0 => inicia, 1 => exito en el proceso de comunicaicon, 0 => error
     */
    public static function getWSClearView()
    {
        $procesado =2;
        $id=Input::get('id', '');//id de la tarea
        $sql ="UPDATE tareas SET procesado=? WHERE id=?";
        try {
            DB::update($sql, array($procesado,$id));
        } catch (Exception $e) {
            
        }
        $result = array();
        $msj="";
        $telefono=Input::get('telefono', '');
        //$idConsulta=Input::get('idConsulta', '');
        $idConsulta = date('YmdHis'); // Debe enviar una fecha en este formato
     
        $url = "http://10.226.88.214/WSASSIA/wsASSIA.php/RegistrarDiagnostico/"
                .$telefono."/".$idConsulta;
        try {
            $ch = curl_init();
            if (FALSE === $ch)
                throw new Exception('failed to initialize');
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            if( $result === false)
                $msj= 'no se pudo establecer conexion ' . curl_error($ch);
            
            $result = json_decode($result);
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
        //$result = json_decode('[{"id":16,"Desc":"No se obtiene respuesta. 
        //Revisar linea o modem","Tel":"12421836","Ok":0}]');
        
        if (count($result)>0) {
            $id = $result[0]->id;
            $Desc = $result[0]->Desc;
            $Tel = $result[0]->Tel;
            $Ok = $result[0]->Ok;
            if ($Ok==1) {
                $msj = $Tel.": Linea OK";
            } elseif ($Ok==0) {
                $msj = $Tel.": No OK";
            }
            $procesado=1;

            try {
                DB::update($sql, array($procesado,$id));
            } catch (Exception $e) {
                
            }
        }
        
        return
            array(
                'rst'=>'1',
                'datos'=> $msj,
                'msj'=>$msj,
            );
    }
    public static function getWS()
    {
        $result = array();
        $telefono=Input::get('telefono', '');
        $telefono = substr($telefono, strlen($telefono)-7);
        $url='http://172.28.13.119/amfphp/services/ws_pdm_request.php';
        //$url = Config::get("wpsi.pruebas.pcba");
        $url=$url.'?CodArea=1&Telefono='.$telefono;
        $xml = new DOMDocument();

        try{
          $xml->load($url);
        } catch (Exception $e) {
          $xml = null;
        }

        if($xml == null){
            $rst=0;
            $msj=$telefono.": no se pudo establecer conexion con el servicio";
        } else {
            //$xml->load($url);
            $xmlTest = $xml->saveXML();
            $xml->save('prueba.xml');
            $xmlObj = simplexml_load_string($xmlTest);

            $msj = "N: ".$telefono.", Prueba Dslam: velocidad subida->".
                    $xmlObj->PruebaDslam->VelocidadActualSubida->Descripcion;
            $rst=1;
        }
        return
            array(
                'rst'=>$rst,
                'datos'=> $result,
                'msj'=>$msj,
            );

    }
    public static function permisosGetactu($telefonoOrigen)
    {
        $inscripcion='';
        $result= array();
        $data=array();

        $sqlpermisos = "SELECT  e.nombre,e.`evento`,e.id_sql,e.extraer,
                                e.id_where,e.valor_where
                        FROM evento_consulta e
                        INNER JOIN eventos ep
                                ON ep.evento_id=e.id
                               AND ep.estado=1
                        LEFT JOIN tecnicos t
                               ON t.id=ep.persona_id
                              AND ep.tipo_persona=2
                              AND t.estado=1
                        LEFT JOIN usuarios u
                               ON u.id=ep.persona_id
                              AND ep.tipo_persona=1
                              AND u.estado=1
                        WHERE e.estado=1 AND grupo = 1
                        AND ep.tipo_evento=1
                        AND (t.celular=? OR u.celular=? )
                        ORDER BY orden";
        $parpermisos = array($telefonoOrigen,$telefonoOrigen);
        $qpermisos= DB::select($sqlpermisos, $parpermisos);
        $array=array();
        $arrayDep=array();
        if ( count($qpermisos)>0 ) {
            $nombre="";
            foreach ($qpermisos as $key => $value) {
                if ($key==0) {
                    $nombre=$value->nombre;
                }

                $metodo= $value->evento;
                $ids= explode(",", $value->id_sql);
                $iwhere= explode("|", $value->id_where);
                $vwhere= explode("|", $value->valor_where);

                for ($i=0;$i<count($ids);$i++) {
                    $array=array();
                    $result=array();
                    $detids= explode("0", $ids[$i]);
                    $anddetids= explode("&", $detids[0]);
                    if ( $key==0 AND count($detids)>1
                                AND Input::has($anddetids[0])
                                AND Input::has($anddetids[1]) ) {
                        $array[$anddetids[0]]=trim(Input::get($anddetids[0]));
                        $array[$anddetids[1]]=trim(Input::get($anddetids[1]));
                        $array[$detids[1]]=trim(Input::get($detids[1]));
                        eval("\$result = $metodo;");

                        if (count($result)>0) {
                            $data['maestro']= $result;
                            return array(
                                    'rst'=>1,
                                    'datos'=> $data,
                                    );
                        } else {
                            return array(
                                    'rst'=>1,
                                    'datos'=> '',
                                    'msj'=> 'no se encontraron registros',
                                    );
                        }
                        break;
                    } elseif ( $key==0 AND count($anddetids)>1
                                        AND Input::has($anddetids[0])
                                        AND Input::has($anddetids[1]) ) {
                        $array[$anddetids[0]]=trim(Input::get($anddetids[0]));
                        $array[$anddetids[1]]=trim(Input::get($anddetids[1]));
                        $result = eval("\$data = $metodo;");
                        break;
                    } elseif ( $key==0 AND Input::has($ids[$i]) ) {
                        $array[$ids[$i]]=trim(Input::get($ids[$i]));
                        eval("\$result = $metodo;");
                        break;
                    } elseif ( $key>0 ) {

                        eval("\$result = $metodo;");
                        //$sqls.= $iwhere[$i];
                        //$valores=$vwhere[$i];
                        //$array=array();
                        //eval("\$array = array($valores);");

                        //if ( $array[0] !='') {
                        //    $result = DB::select($sqls, $array);
                        //} else {
                        //    $result  = array();
                        //}
                        break;
                    }
                }

                if (count($result)>0) {
                    if (count($result)>1 AND $key==0 ) {
                        $data[$value->nombre]= $result;
                        return
                            array(
                                'rst'=>1,
                                'datos'=> $data,
                                'msj'=>'Se envio el resultado por mensaje de'.
                                ' texto a su celular; Seleccione 1 registro y'.
                                ' busque nuevamente.',
                            );
                    } else {
                        if ($key==0) {
                            if ( $value->extraer!='' ) {
                                $extraer= explode(",", $value->extraer);
                                for ($i=0;$i<count($extraer);$i++) {
                                    //$arrayDep= array();

                                    $variable=$extraer[$i];
                                    //$arrayDep[$variable] = $variable ;
                                    eval("\$arrayDep[\$variable] = \$result[0]->$variable;");
                                }
                            }
                        }
                        $data[$value->nombre]= $result;
                    }
                } elseif ( $key==0 ) {
                    return
                        array(
                            'rst'=>2,
                            'datos'=> '',
                            'msj'=>'No se encontraron registros, verifique su '.
                            'dato ingresado e intente nuevamente.',
                        );
                }
            }

            return
                array(
                'rst'=>1,
                'datos'=> $data,
                'msj'=>'Se envio el resultado por mensaje de texto '
                    . 'a su celular.',
                );
        } else {
            return
                array(
                    'rst'=>2,
                    'datos'=> '',
                    'msj'=> 'Ud. No cuenta con permisos para consultar.',
                );
        }
    }

    public static function eventoConsulta($nombre)
    {
        $result= array();
        $data=array();

        $sqlpermisos = "SELECT  e.nombre,e.`sql`,e.id_sql,e.extraer,
                                e.id_where,e.valor_where
                        FROM evento_consulta e
                        WHERE e.estado=1
                        AND e.nombre='$nombre'";

        $qpermisos= DB::select($sqlpermisos);

        if ( count($qpermisos)>0 ) {
        $value=$qpermisos[0];

            $sqls= $value->sql;
            $ids= explode(",", $value->id_sql);
            $iwhere= explode("|", $value->id_where);
            $vwhere= explode("|", $value->valor_where);

            for ($i=0;$i<count($ids);$i++) {
                $detids= explode("0", $ids[$i]);
                $anddetids= explode("&", $detids[0]);
                if ( count($detids)>1
                            AND Input::has($anddetids[0])
                            AND Input::has($anddetids[1]) ) {
                    ${$anddetids[0]}=trim(Input::get($anddetids[0]));
                    ${$anddetids[1]}=trim(Input::get($anddetids[1]));
                    ${$detids[1]}=trim(Input::get($detids[1]));
                    $sqls.= $iwhere[$i];
                    $valores=$vwhere[$i];
                    $array=array();
                    eval("\$array = array($valores);");
                    $result = DB::select($sqls, $array);
                    break;
                } elseif ( count($anddetids)>1
                                    AND Input::has($anddetids[0])
                                    AND Input::has($anddetids[1]) ) {
                    ${$anddetids[0]}=trim(Input::get($anddetids[0]));
                    ${$anddetids[1]}=trim(Input::get($anddetids[1]));
                    $sqls.= $iwhere[$i];
                    $valores=$vwhere[$i];
                    $array=array();
                    eval("\$array = array($valores);");
                    $result = DB::select($sqls, $array);
                    break;
                } elseif ( Input::has($ids[$i]) ) {
                    ${$ids[$i]}=trim(Input::get($ids[$i]));
                    $sqls.= $iwhere[$i];
                    $valores=$vwhere[$i];
                    $array=array();
                    eval("\$array = array($valores);");
                    $result = DB::select($sqls, $array);
                    break;
                }
            }

            if (count($result)>0) {
                return
                    array(
                        'rst'=>1,
                        'datos'=> $result,
                        'msj'=>'Mostrando datos de la consulta '.$nombre,
                    );
            } else {
                return
                    array(
                        'rst'=>1,
                        'datos'=> '',
                        'msj'=>'No se encontraron registros, verifique su '.
                        'dato ingresado e intente nuevamente.',
                    );
            }
        } else {
            return
                array(
                    'rst'=>1,
                    'datos'=> '',
                    'msj'=>'No existe la consulta que desea realizar',
                );
        }
    }
    /**
     * este evento es llamado para ejecutar metodos que tengan permisos
     * en la tabla evento_metodo
     */
    public static function eventoMetodo($nombre,$telefono)
    {
        $data=array();

        $sqlpermisos = "SELECT  e.nombre,e.metodo, ep.tipo_persona AS tipo,
                                e.retorno
                        FROM evento_metodo e
                        INNER JOIN eventos ep
                                ON ep.evento_id=e.id
                               AND ep.estado=1
                        LEFT JOIN tecnicos t
                               ON t.id=ep.persona_id
                              AND ep.tipo_persona=2
                              AND t.estado=1
                        LEFT JOIN usuarios u
                               ON u.id=ep.persona_id
                              AND ep.tipo_persona=1
                              AND u.estado=1
                        WHERE e.estado=1
                        AND ep.tipo_evento=2
                        AND e.nombre=?
                        AND (t.celular=? OR u.celular=? )";
        //tipo_evento=2, solo metodos
        $qpermisos=DB::select($sqlpermisos, array($nombre,$telefono,$telefono));

        if (isset($qpermisos[0]->metodo)) {
            $metodo=$qpermisos[0]->metodo;
            $tipo=$qpermisos[0]->tipo;
            $data = Input::all();
            $data['tipo']=$tipo;
            Input::replace($data);
            //print_r($qpermisos[0]);print_r($data);exit;
            $rst = eval("\$data = $metodo;");
            //$data['msj'] = "Se ejecuto la accion($nombre) ";
            /*if (isset($data['rst'])) {
                if ($data['rst']==1) {
                    //los metodos invocados deberan retornar rst = 1 ,
                    //cuando se terminaron de ejcutar con normalidad

                    //se retorna esta variable solo cuando se recibe rst=1
                    //del metodo llamado
                    //$data['retorno']=$qpermisos[0]->retorno;
                }
            }*/
            return $data;
        } else {
            return
                array(
                    'rst'=>1,
                    'datos'=> '',
                    'msj'=>"Ud. No cuenta con permisos para realizar esta "
                    . "accion ($nombre)",
                );
        }
    }

    public static function getCantVisitas($gestionId)
    {
        $sql="SELECT MOD(f.cant, 3) final
            FROM(
                SELECT(
                    SELECT COUNT(t2.id)
                    FROM webpsi_officetrack.tareas t2
                    INNER JOIN webpsi_officetrack.paso_tres p3_2
                    ON t2.id=p3_2.task_id
                    WHERE t2.id>=t.id
                    AND t2.task_id=t.task_id
                    AND p3_2.estado=p3.estado
                    GROUP BY t2.task_id
                    HAVING COUNT(DISTINCT(t2.cod_tecnico))=1
                ) AS cant,
                t.task_id, t.cod_tecnico, p3.id, t.id tid
                FROM webpsi_officetrack.paso_tres p3
                INNER JOIN webpsi_officetrack.tareas t ON t.id=p3.task_id
                WHERE (p3.estado_codigo = 'DT02' OR p3.estado_codigo = '7705')
                AND t.task_id=?
                ORDER BY p3.id
            ) f
            WHERE f.cant IS NOT NULL
            ORDER BY f.cant DESC
            LIMIT 0,1";
        return DB::select($sql, array($gestionId));
    }

    public static function getMaestro( $data = array() )
    {
        $result= array();
        $sql = "SELECT telefono, appater, apmater, nombre, codclie, codclicms,
        codservcms, inscripcio, mdf, tipopaq, modalidad, veloc,
        nodotroba, IFNULL( nrodni,'') as nrodni,
        CONCAT(tipocalle,' ',nomcalle,' ',numcalle) AS direccion
        FROM `webpsi_coc`.`tb_lineas_servicio_total` WHERE ";
        if ( isset($data['codclicms']) ) {
            $codclicms = trim($data['codclicms']);
            $sql .= " codclicms=? ";
            $result = DB::select($sql, array($codclicms));
        } elseif ( isset($data['codcli'] )) {
            $codcli = trim($data['codcli']);
            $sql .= " codclie =? ";
            $result = DB::select($sql, array($codcli));
        } elseif ( isset($data['telefono'] )) {
            $telefono = trim($data['telefono']);
            $sql .= " telefono=? ";
            $result = DB::select($sql, array($telefono));
        } elseif ( isset($data['dni'] )) {
            $dni = trim($data['dni']);
            $sql .= " nrodni=? ";
            $result = DB::select($sql, array($dni));
        } elseif ( isset($data['x']) && isset($data['y'] )) {
            $x = trim($data['x']);
            $y = trim($data['y']);
            $sql .= " (xtroba=? AND ytroba =?)
                          OR (xterminal=? AND yterminal=?)
                          OR (xtab=? AND ytab=?) ";
            $result = DB::select($sql, array($x,$y,$x,$y,$x,$y));
        } elseif ( (   isset($data['paterno'])
                    && isset($data['materno']) )
                    || isset($data['nombre']) ) {

            $paterno = strtoupper(trim($data['paterno']));
            $materno = strtoupper(trim($data['materno']));
            $nombre = strtoupper(trim($data['nombre']));
            $sql .= " appater=? AND apmater =?
                          AND nombre LIKE CONCAT('%',?,'%')";
            $result = DB::select($sql, array($paterno,$materno,$nombre));

        }
        return $result;
    }
    /**
     * aver_liq_bas_lima
     */
    public static function getAveriaLiqBasLima( $array= array() )
    {
        $result= array();
        //1 Aver. Liq. TBA Lima (schedulle_sistemas.aver_liq_bas_lima)
        $sql = "SELECT telefono, ape_paterno AS appater, ape_materno AS apmater,
                nombre, inscripcion, mdf, armario, cable, terminal,
                numero_osiptel, fecha_reporte, fecha_de_liquidacion,
                tecnico_liquidacion, direccion_instalacion AS direccion,
                CONCAT(observacion_102,' ',otra_observacion) AS observ
                FROM schedulle_sistemas.aver_liq_bas_lima
                WHERE  inscripcion=? ";

        if (  trim($array['inscripcio'])!='' )
            $result = DB::select($sql, array( trim($array['inscripcio']) ));

        return $result;
    }
    /**
     * aver_liq_bas_prov_pedidos
     */
    public static function getAveriaLiqBasProv( $array= array() )
    {
        $result= array();
        //2 Aver.Liq.TBA Provincia(schedulle_sistemas.aver_liq_bas_prov_pedidos)
        $sql = "SELECT telefono, ape_paterno AS appater, ape_materno AS apmater,
                nombre, inscripcion AS inscripcio, mdf, direccion_instalacion
                AS direccion, '' AS armario, '' AS cable, '' AS terminal,
                correlativo, fecha_hora_boleta, fecha_hora_franqueo,
                comentario_de_boletin AS observ
                FROM schedulle_sistemas.aver_liq_bas_prov_pedidos
                WHERE inscripcion=? ";

        if (  trim($array['inscripcio'])!='' )
            $result = DB::select($sql, array( trim($array['inscripcio']) ));

        return $result;

    }
    /**
     * aver_liq_adsl_pais
     */
    public static function getAveriaLiqAdslPais( $array= array() )
    {
        $result= array();
        //3 Aver. Liq. ADSL (schedulle_sistemas.aver_liq_adsl_pais)
        $sql = "SELECT telefono, ape_paterno AS appater, ape_materno AS apmater,
                nombre, inscripcion AS inscripcio, mdf, direccion_instalacion
                AS direccion, mdf, nro_dsa AS armario, cable, nro_caja AS
                terminal, numero_osiptel, fecha_registro, fecha_liquidacion_,
                observacion_liquidacion
                FROM schedulle_sistemas.aver_liq_adsl_pais
                WHERE inscripcion=? ";
        if (  trim($array['inscripcio'])!='' )
            $result = DB::select($sql, array( trim($array['inscripcio']) ));

        return $result;
    }
    /**
     * aver_liq_catv_pais
     */
    public static function getAveriaLiqCatvPais( $array= array() )
    {
        $result= array();
        //4. Aver. Liq. CATV (schedulle_sistemas.aver_liq_catv_pais)
        $sql = "SELECT apellidopaterno AS appater, apellidomaterno AS apmater,
                nombres AS nombre, codigodelcliente AS codclie,
                codigodelservicio AS codservcms, CONCAT(tipodevia,' ',
                nombredelavia,' ',numero,' Piso: ',piso, ' Int: ',interior,
                ' Mzn: ',manzana,' Lt: ',lote) AS direccion, nodo, plano,lex,
                tap,borne, codigoreq, fecharegistro,fecha_liquidacion, tecnico,
                '' AS observ
                FROM schedulle_sistemas.aver_liq_catv_pais
                WHERE codigodelcliente=? ";

        if (  trim($array['codclicms'])!='' && trim($array['codclicms']) !='0' )
            $result = DB::select($sql, array( trim($array['codclicms']) ));

        return $result;
    }
    /**
     * prov_liq_bas_pais
     */
    public static function getProvisionLiqBasPais(  $array= array() )
    {
        $result= array();
        //5. Prov. Liq. TBA/ADSL (schedulle_sistemas.prov_liq_bas_pais)
        $sql = "SELECT telefono, direccion_instalacion AS direccion,
                tecnico, cod_cliente AS codclie, fecha_reg, desc_negocio,
                orden, solicitud, inscripcion AS inscripcio, fecha_liquidacion
                FROM schedulle_sistemas.prov_liq_bas_pais
                WHERE inscripcion=? ";

        if (  trim($array['inscripcio'])!='' )
            $result = DB::select($sql, array( trim($array['inscripcio']) ));

        return $result;
    }
    /**
     * prov_liq_catv_pais
     */
    public static function getProvisionLiqCatvPais( $array= array() )
    {
        $result= array();
        //6. Prov. Liq. CATV (schedulle_sistemas.prov_liq_catv_pais)
        $sql = "SELECT apellido_paterno AS appater, apellido_materno AS apmater,
                nombres AS nombre, codigo_del_cliente AS codclie,
                CONCAT(tipo_de_via,' ',nombre_de_la_via,' ',numero,' Piso: ',
                    piso, ' Int: ',interior, ' Mzn: ',manzana,' Lt: ',lote) AS
                direccion,nodo, plano,lex,tap,borne, codigo_req, fecha_registro,
                fecha_liquidacion, tecnico, '' AS observ
                FROM schedulle_sistemas.prov_liq_catv_pais
                WHERE codigo_del_cliente=? ";

        if (  trim($array['codclicms'])!='' && trim($array['codclicms']) !='0' )
            $result = DB::select($sql, array( trim($array['codclicms']) ));

        return $result;
    }
    /**
     * aver_pen_bas_lima
     */
    public static function getAveriaPenBasLima( $array= array() )
    {
        $result= array();
        //7. Aver. Pen. TBA Lima (schedulle_sistemas.aver_pen_bas_lima)
        $sql = "SELECT telefono, ape_paterno AS appater, ape_materno AS apmater,
                nombre, direccion_instalacion AS direccion, inscripcion AS
                inscripcio, mdf, armario, cable,  terminal, numero_osiptel,
                fecha_registro
                FROM schedulle_sistemas.aver_pen_bas_lima
                WHERE inscripcion=? ";

        if (  trim($array['inscripcio'])!='' )
            $result = DB::select($sql, array( trim($array['inscripcio']) ));

        return $result;
    }
    /**
     * aver_pen_bas_prov
     */
    public static function getAveriaPenBasProv( $array= array() )
    {
        $result= array();
        //8. Aver. Pen. TBA Provincia (schedulle_sistemas.aver_pen_bas_prov)
        $sql = "SELECT telefono, ape_paterno AS appater, ape_materno AS apmater,
                nombre, direccioninstalacion AS direccion,
                inscripcion AS inscripcio, mdf
                FROM schedulle_sistemas.aver_pen_bas_prov
                WHERE inscripcion=? ";

        if (  trim($array['inscripcio'])!='' )
            $result = DB::select($sql, array( trim($array['inscripcio']) ));

        return $result;
    }
    /**
     * aver_pen_adsl_pais
     */
    public static function getAveriaPenAdslPais( $array= array() )
    {
        $result= array();
        //9. Aver. Pen. ADSL (schedulle_sistemas.aver_pen_adsl_pais)
        $sql = "SELECT telefono, ape_paterno AS appater, ape_materno AS apmater,
                nombre, direccion_instalacion AS direccion,
                mdf, modalidad,  nrodsa AS armario, cable, nro_caja AS terminal,
                numero_osiptel, fecha_registro, inscripcion AS inscripcio
                FROM schedulle_sistemas.aver_pen_adsl_pais
                WHERE inscripcion=? ";

        if (  trim($array['inscripcio'])!='' )
            $result = DB::select($sql, array( trim($array['inscripcio']) ));

        return $result;
    }
    /**
     * aver_pen_catv_pais
     */
    public static function getAveriaPenCatvPais( $array= array() )
    {
        $result= array();
        //10. Aver. Pen. CATV (schedulle_sistemas.aver_pen_catv_pais)
        $sql = "SELECT  apellidopaterno AS appater, apellidomaterno
                AS apmater, nombres AS nombre, codigodelcliente AS codclie,
                CONCAT(tipodevia,' ',nombredelavia,' ',numero,' Piso: ',piso,
                ' Int: ',interior, ' Mzn: ',manzana,' Lt: ',lote) AS direccion,
                nodo, plano,lex,tap,borne, codigo_req, fecharegistro,
               fecha_liquidacion,  desc_motivo, codigodelservicio AS codservcms
                FROM schedulle_sistemas.aver_pen_catv_pais
                WHERE codigodelcliente=?";

        if (  trim($array['codclicms'])!='' && trim($array['codclicms']) !='0' )
            $result = DB::select($sql, array( trim($array['codclicms']) ));

        return $result;
    }
    /**
     * prov_pen_bas_pais
     */
    public static function getProvisionPenBasPais(  $array= array() )
    {
        $result= array();
        //11. Prov. Pen. TBA/ADSL (schedulle_sistemas.prov_pen_bas_pais)
        $sql = "SELECT telefono, dir_instal AS direccion,
                cod_cliente AS codclie, fecha_reg, desc_negocio,
                orden, solicitud, ninscripcion AS inscripcio
                FROM schedulle_sistemas.prov_pen_bas_pais
                WHERE ninscripcion=? ";
        if (  trim($array['inscripcio'])!='' )
            $result = DB::select($sql, array( trim($array['inscripcio']) ));

        return $result;
    }
    /**
     * prov_pen_catv_pais
     */
    public static function getProvisionPenCatvPais( $array= array() )
    {
        $result= array();
        //12. Prov. Pen. CATV (schedulle_sistemas.prov_pen_catv_pais)
        $sql = "SELECT apellido_paterno AS appater, apellido_materno AS apmater,
                nombres AS nombre,
                CONCAT(tipo_de_via,' ',nombre_de_la_via,' ',numero,' Piso: ',
                piso,
                ' Int: ',interior, ' Mzn: ',manzana,' Lt: ',lote) AS direccion,
                nodo, plano,lex,tap,borne, codigo_req, fecha_registro,
                codigo_del_cliente AS codclie
                FROM schedulle_sistemas.prov_pen_catv_pais
                WHERE codigo_del_cliente=? ";

        if (  trim($array['codclicms'])!='' && trim($array['codclicms']) !='0' )
            $result = DB::select($sql, array( trim($array['codclicms']) ));

        return $result;
    }
    /**
     * prov_liqui_macro
     */
    public static function getProvisionLiqMacro( $array= array() )
    {
        $result= array();
        //13. Pendientes Prov CATV - Andrea (webpsi_coc.prov_liqui_macro)
        $sql = "SELECT nombre,
                CONCAT(destipvia,' ',desnomvia,' ',numvia,' Piso: ',despis,
                ' Int: ',desint, ' Mzn: ',desmzn,' Lt: ',deslot) AS direccion,
                codnod, nroplano, fechorliq
                FROM webpsi_coc.prov_liqui_macro
                WHERE codcli=? ";

        if (  trim($array['codclicms'])!='' && trim($array['codclicms']) !='0' )
            $result = DB::select($sql, array( trim($array['codclicms']) ));

        return $result;
    }
    /**
     * fftt_directa
     */
    public static function getFfttDirecta( $array= array() )
    {
        $result= array();
        //14. Maestro FFTT Directa (webunificada_fftt.fftt_directa)
        $sql = "SELECT ParAlimentador as par, Caja, Inscripcion,
                Solicitud, EstadoPar, Negocio, Zonal, Ciudad, DescCiudad, MDF,
                DescMDF, Cable, Telefono, Circuito, DDN, Direccion, Cliente,
                posicionAdslGestel, segmentoGestel, sectorGestel, manzanaGestel,
                velocidadBajadaGestel, velocidadSubidaGestel,
                descripcionModalidadGestel, fecha_insert,X,Y
                FROM webunificada_fftt.fftt_directa
                WHERE inscripcion*1=(?)*1 ";

        if (  trim($array['inscripcio'])!='' )
            $result = DB::select($sql, array( trim($array['inscripcio']) ));

        return $result;
    }
    /**
     * fftt_secundaria
     */
    public static function getFfttsecundaria(  $array= array() )
    {
        $result= array();
        //15. Maestro FFTT Secundaria (webunificada_fftt.fftt_secundaria)
        $sql = "SELECT Pardistribuidor as par, Caja, Inscripcion,
                Solicitud, EstadoPar, Negocio, Zonal, Ciudad, DescCiudad, MDF,
                DescMDF,Armario, Bloque,  Telefono, Circuito, DDN, Direccion, 
                Cliente,
                posicionAdslGestel, segmentoGestel, sectorGestel, manzanaGestel,
                velocidadBajadaGestel, velocidadSubidaGestel,
                descripcionModalidadGestel, fecha_insert,X,Y
                FROM webunificada_fftt.fftt_secundaria
                WHERE inscripcion*1=(?)*1  ";

        if (  trim($array['inscripcio'])!='' )
            $result = DB::select($sql, array( trim($array['inscripcio']) ));

        return $result;
    }
}
