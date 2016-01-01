<?php
class UploadController extends BaseController
{
    protected $_uploadFolder = 'tmpfile';
    protected $_errorController;
    /**
     * Valida sesion activa
     */
    public function __construct(ErrorController $ErrorController)
    {
        $this->beforeFilter('auth');
        $this->_errorController = $ErrorController;
    }
    public function postCargartmp($contenido, $eliminar)
    {
        $data = "";

        $nombreArchivo = $_FILES['archivo']['name'];
        $extArchivo = end((explode(".", $nombreArchivo)));

        $tipoArchivo = $_FILES['archivo']['type'];

        $tamanoArchivo = $_FILES['archivo']['size'];

        $tmpArchivo = $_FILES['archivo']['tmp_name'];

        //Nuevo nombre de archivo MD5
        $archivoNuevo=md5(date("YmdHis") . $nombreArchivo) . "." . $extArchivo;

        $file = $this->_uploadFolder . '/' . $nombreArchivo;

        if (!move_uploaded_file($tmpArchivo, $file)) {
            $m="Ocurrio un error al subir el archivo. No pudo guardarse.";
            $return = array(
                'upload' => FALSE,
                'msg' => $m,
                'error' => $_FILES['archivo']
            );
        } else {
            //Retornar contenido
            if ($contenido == 1) {
                $data = Helpers::fileToJsonAddress($file);
            }
            //Eliminar archivo cargado
            if ($eliminar == 1) {
                unlink($file);
            }

            $return = array(
                'upload' => TRUE,
                'data' => $data
            );
        }

        return json_encode($return);
    }
    public function postConsultarquiebre()
    {
        $apellidos=$nombre=$tipocalle=$nomcalle=$numcalle='';
        $codsrv = Input::get('codsrv');
        $codcli = Input::get('codcli');
        $servicio = Input::get('servicio');//provision o averia

        $query ="SELECT CONCAT(appater, ' ',apmater) apellidos, nombre
                FROM webpsi_coc.tb_lineas_servicio_total
                WHERE codclicms=?";

        $res = DB::select($query, array($codcli));

        if (count($res)) {
            $apellidos=$res[0]->apellidos;
            $nombre=$res[0]->nombre;
            # code...
        }


        if ($servicio=='provision') {
            $query="SELECT tipocalle, nomcalle, numcalle
                    FROM webpsi_coc.tb_lineas_servicio_total
                    WHERE codclicms=? AND codservcms=?";
            unset($res);

            $res = DB::select($query, array($codcli,$codsrv));

            if (count($res)) {
                $tipocalle=$res[0]->tipocalle;
                $nomcalle=$res[0]->nomcalle;
                $numcalle=$res[0]->numcalle;
            }
            return Response::json(
                array(
                    'apellidos'=>$apellidos,
                    'nombre'=>$nombre,
                    'tipocalle'=>$tipocalle,
                    'nomcalle'=>$nomcalle,
                    'numcalle'=>$numcalle
                )
            );
        } elseif ($servicio=='averia')
            return Response::json(
                array(
                    'apellidos'=>$apellidos,
                    'nombre'=>$nombre
                )
            );
    }
    public function postCargarquiebre()
    {
        if (Input::has('data') ) {

            $data = Input::get('data');

            $obj=json_decode($data);
            set_time_limit(0);
            $i=0;
            if (is_object($obj) ) {
                foreach ($obj->filaExcel as $key => $value) {
                    if (is_object($value) && isset($value->codigoReq) ) {
                        $select="SELECT codigo_req
                                 FROM webpsi_coc.tmp_provision
                                 WHERE codigo_req=? ";
                        $rows=DB::select($select, array($value->codigoReq));
                        if (count($rows)==0) {
                            //convertir a tipo fecha
                            $fechaReg=str_replace("'", "", $value->fechaReg);
                            $dia= substr($fechaReg, 0, 2);
                            $mes= substr($fechaReg, 3, 2);
                            $anio= substr($fechaReg, 6, 4);
                            $hora= substr($fechaReg, 11, 5);
                            $fechaReg="$anio-$mes-$dia $hora:00";

                            $sql="INSERT INTO webpsi_coc.tmp_provision
                                (origen,zonal,ciudad,mdf,codigo_req, telefono_codclientecms,
                                nomcliente, telefono,contrata,fecha_reg,
                                direccion,distrito,obs_dev,quiebre,
                                eecc_final,fecha_data_fuente)
                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,now()) ";
                            //INSERTAR
                            try {
                                DB::insert(
                                    $sql,
                                    array(
                                    'pen_prov_adsl',
                                    str_replace("'", "", $value->zonal),
                                    str_replace("'", "", $value->ciudad),
                                    str_replace("'", "", $value->mdf),
                                    str_replace("'", "", $value->codigoReq),
                                    str_replace("'", "", $value->telefono),
                                    str_replace("'", "", $value->nomCliente),
                                    str_replace("'", "", $value->telefono),
                                    str_replace("'", "", $value->contrata),
                                    $fechaReg,
                                    str_replace("'", "", $value->direccion),
                                    str_replace("'", "", $value->distrito),
                                    str_replace("'", "", $value->observacion),
                                    'DEL-ROUTER',
                                    str_replace("'", "", $value->contrata)
                                    )
                                );
                            } catch (Exception $exc) {
                                $this->_errorController->saveError($exc);
                                return Response::json(
                                    array(
                                        'estado'=>false,
                                        'msj'=>'Ocurrio un error'
                                    )
                                );
                            }
                            $i++;
                        }
                    }

                }
            }
            return Response::json(
                array(
                    'estado'=>true,
                    'msj' => 'Se cargaron '.$i.' registros'
                )
            );

        } else {
            return Response::json(
                array(
                    'estado'=>true,
                    'msj'=>'No se recibieron datos validos'
                )
            );
        }
    }
    public function postCargar()
    {
        $data = json_decode(Input::get('data'));
        $servicio = Input::get('servicio');
        $quiebre = Input::get('quiebre');
        $quiebres = Quiebre::find($quiebre);
        $quiebre = $quiebres->apocope;
        $apellidos = Input::get('apellidos');
        $nombre = Input::get('nombre');

        $fechorreg=$data->fechorreg;
        $var = explode("/", substr($fechorreg, 0, 10));
        $var=array($var['1'],$var['0'],$var['2']);
        $fechorreg = implode("/", $var).substr($fechorreg, 10);
        $date = new DateTime($fechorreg);
        $fechorreg = $date->format('Y-m-d h:i:s');

        $codcli=$data->codcli;
        $codnod=$data->codnod;
        $coddtt=$data->coddtt;
        $nombreCliente = $apellidos.', '.$nombre;
        $codordtrab = $data->codordtrab;
        $tipreq=$data->tipreq;
        $codmotv = $data->codmotv;
        $nroplano = $data->nroplano;
        $codofcadm=$data->codofcadm;
        $desdtt=$data->desdtt;

        $query="SELECT eecc_critico
                FROM webpsi_fftt.nodos_eecc_regiones
                WHERE nodo=? LIMIT 1";

        $res = DB::select($query, array($codnod));
        if (count($res)) {
            $eeccCritico=$res[0]->eecc_critico;
        }

        if ($servicio == 'averia') {

            $codreqatn=$data->codreqatn;

            $destipvia=$data->destipvia;
            $desnomvia=$data->desnomvia;
            $numvia=$data->numvia;
            $destipurb=$data->destipurb;
            $desurb=$data->desurb;
            $despis=$data->despis;
            $desint=$data->desint;
            $desmzn=$data->desmzn;
            $deslot=$data->deslot;
            $codclasrv=$data->codclasrv;

            $codmotivoReqCatv=$tipreq.'|'.$codmotv;

            $fftt=$codnod.'|'.$nroplano;
            $desnomctr=$data->desnomctr;

            $direccionInstalacion = $destipvia.' '.$desnomvia.' '.$numvia.' '.
                            $destipurb.' '.$desurb.', Piso: '.$despis.
                            ' Int: '.$desint.' Mzn: '.$desmzn.' Lt: '.$deslot;
            $sql="INSERT INTO webpsi_coc.averias_criticos_final
            (tipo_averia,horas_averia,fecha_registro,ciudad,averia,inscripcion,
            fono1, telefono, mdf,observacion_102, segmento, area_,
            direccion_instalacion,codigo_distrito, nombre_cliente,orden_trabajo,
            veloc_adsl, clase_servicio_catv, codmotivo_req_catv,
            total_averias_cable, total_averias_cobre,total_averias, fftt, llave,
            dir_terminal, fonos_contacto, contrata, zonal, wu_nagendas,
            wu_nmovimientos, wu_fecha_ult_agenda, total_llamadas_tecnicas,
            total_llamadas_seguimiento, llamadastec15dias, llamadastec30dias,
            quiebre, lejano, distrito, eecc_zona, zona_movistar_uno, paquete,
            data_multiproducto, averia_m1, fecha_data_fuente,
            telefono_codclientecms, rango_dias, sms1, sms2, area2,
            velocidad_caja_recomendada, tipo_servicio, tipo_actuacion,
            eecc_final, microzona)
            VALUES
            ('aver-catv-pais',TIMESTAMPDIFF(HOUR, DATE_FORMAT('$fechorreg',
                '%Y-%m-%d %h:%i:%s'), NOW()),
            DATE_FORMAT('$fechorreg','%Y-%m-%d %h:%i:%s'),'','$codreqatn',
            '$codcli','','',
            '$codnod','','','','$direccionInstalacion','$coddtt',
            '$nombreCliente','$codordtrab','','$codclasrv','$codmotivoReqCatv',
            '','','','$fftt','',NULL, '','$desnomctr','$codofcadm','','','',
            NULL,NULL,'','', '$quiebre','','$desdtt','','','','', '','',
            '$codcli','','','','', NULL,'catv','AVERIA','$eeccCritico','') ";

            try {
                DB::insert($sql);
            }
            catch(\Exception $exc) {
                $this->_errorController->saveError($exc);
                return Response::json(
                    array(
                        'estado'=>false,
                        'msj'=>'archivo ya existe'
                    )
                );
            }
            $sqlD="INSERT INTO webpsi_coc.averias_criticos_final_historico
            (tipo_averia,horas_averia,fecha_registro,ciudad,averia,inscripcion,
            fono1,telefono, mdf,observacion_102,segmento,area_,
            direccion_instalacion,codigo_distrito, nombre_cliente,orden_trabajo,
            veloc_adsl,clase_servicio_catv,codmotivo_req_catv,
            total_averias_cable,total_averias_cobre,total_averias,fftt,llave,
            dir_terminal, fonos_contacto,contrata,zonal,wu_nagendas,
            wu_nmovimientos,wu_fecha_ult_agenda, total_llamadas_tecnicas,
            total_llamadas_seguimiento,llamadastec15dias,llamadastec30dias,
            quiebre,lejano,distrito,eecc_zona,zona_movistar_uno,paquete,
            data_multiproducto, averia_m1,fecha_data_fuente,
            telefono_codclientecms,rango_dias,sms1,sms2,area2,
            velocidad_caja_recomendada,tipo_servicio,tipo_actuacion,eecc_final,
            microzona, fecha_cambio, fecha_subida)
            VALUES
            ('aver-catv-pais',TIMESTAMPDIFF(HOUR, DATE_FORMAT('$fechorreg',
                '%Y-%m-%d %h:%i:%s'), NOW()),
            DATE_FORMAT('$fechorreg','%Y-%m-%d %h:%i:%s'), '','$codreqatn',
            '$codcli','','', '$codnod','','','','$direccionInstalacion',
            '$coddtt', '$nombreCliente','$codordtrab','','$codclasrv',
            '$codmotivoReqCatv', '','','','$fftt','',NULL, '','$desnomctr',
            '$codofcadm','','','', NULL,NULL,'','', '$quiebre','','$desdtt',
            '','','','', '','','$codcli','','','','', NULL,'catv','AVERIA',
            '$eeccCritico','',DATE_FORMAT('$fechorreg','%Y-%m-%d %h:%i:%s'),
            DATE_FORMAT('$fechorreg','%Y-%m-%d %h:%i:%s')) ";

            try {
                DB::insert($sqlD);
            }
            catch(\Exception $exc) {
                $this->_errorController->saveError($exc);
                return Response::json(
                    array(
                        'estado'=>false,
                        'msj'=>'archivo ya existe'
                    )
                );
            }
        } elseif ($servicio=='provision') {
            $tipocalle = Input::get('tipocalle');
            $nomcalle = Input::get('nomcalle');
            $numcalle = Input::get('numcalle');
            $piso = Input::get('piso');
            $interior = Input::get('interior');
            $manzana = Input::get('manzana');
            $lote = Input::get('lote');

            $coddpt = $data->coddpt;
            $codpvc = $data->codpvc;
            $codreq = $data->codreq;
            $indvip = $data->indvip;
            $codctr = $data->codctr;

            $distrito = $coddtt.$codpvc.$coddpt;

            $direccion = $tipocalle.' '.$nomcalle.' '.$numcalle.', Piso: '.
                    $piso.' Int: '.$interior.' Mzn: '.$manzana.' Lt: '.$lote;

            $tipoMotivo = $tipreq.'|'.$codmotv;

            $fftt= $codnod.'|'.$nroplano;

            $sql="  INSERT INTO webpsi_coc.tmp_provision
            (origen, horas_pedido, fecha_Reg, ciudad, codigo_req,
            codigo_del_cliente, fono1, telefono, mdf, obs_dev, codigosegmento,
            estacion, direccion, distrito, nomcliente, orden, veloc_adsl,
            servicio, tipo_motivo, tot_aver_cab, tot_aver_cob, tot_averias,
            fftt, llave, dir_terminal, fonos_contacto, contrata, zonal,
            wu_nagendas, wu_nmovimient, wu_fecha_ult_age, tot_llam_tec,
            tot_llam_seg, llamadastec15d, llamadastec30d, quiebre, lejano,
            des_distrito, eecc_zon, zona_movuno, paquete, data_multip, aver_m1,
            fecha_data_fuente, telefono_codclientecms, rango_dias, sms1, sms2,
            area2, veloc_caja_recomen, tipo_servicio, tipo_actuacion,
            eecc_final, microzona)
            VALUES
            ('pen_prov_catv',TIMESTAMPDIFF(HOUR, DATE_FORMAT('$fechorreg',
               '%Y-%m-%d %h:%i:%s'), NOW()),
            DATE_FORMAT('$fechorreg','%Y-%m-%d %h:%i:%s'), '', '$codreq',
            '$codcli', '', '', '$codnod', '', '$indvip', 'TELEDIF',
            '$direccion', '$distrito', '$nombreCliente', '$codordtrab', '', '',
            '$tipoMotivo', NULL, NULL, NULL, '$fftt', '', NULL, '', '$codctr',
            '$codofcadm', NULL, NULL, '', NULL, NULL, NULL, NULL, '$quiebre',
            '', '$desdtt', '', NULL, NULL, NULL, '', NULL, '$codcli',
            '', '', '', 'EN CAMPO', NULL, NULL, '', '$eeccCritico', '')   ";
            try {
                DB::insert($sql);
            }
            catch(\Exception $exc) {
                $this->_errorController->saveError($exc);
                return Response::json(
                    array(
                        'estado'=>false,
                        'msj'=>'no se cargo archivo'
                        )
                );
            }
        }
        return Response::json(
            array(
                'estado'=>true,
                'msj'=>'Se realizo la carga con exito'
            )
        );
        //echo json_encode(array('estado'=>true,'data'=>''));
    }
    /**
     * actualizacion de quiebres masivos
     */
    public function postActualizaquiebremasivo()
    {
        $usuarioId = Auth::user()->id;
        $forzar= Input::get('forzar', 0);
        $quiebre= Input::get('quiebre', '');
        $quiebreId= Input::get('quiebre_id');
        $empresa= Input::get('empresa', '');
        $empresaId= Input::get('empresa_id');
        $arrayInfo=array();
        $val = "";

        //validar archivo
        if (Input::hasFile('archivoTmp')) {
            if ( Input::file('archivoTmp')->isValid() ) {
                $file = Input::file('archivoTmp');
                $tmpArchivo = $file->getRealPath();
                $file = file($tmpArchivo);
                $con=0;
                foreach ($file as $f) {
                    $con++;
                    if($con>1) $val.="','".trim($f);
                }
                $val=substr($val, 3);
            } else {
                return Response::json(
                    array(
                        'estado'=>'0',
                        'msj'=>'Archivo no valido'
                    )
                );
            }
            $averias=str_replace("','", ",", $val);
        } else {
            return Response::json(
                array(
                    'estado'=>'0',
                    'msj'=>'Archivo no valido'
                )
            );
        }

        $arrayAveria = explode(',', $averias);
        //recorrer averias
        foreach ($arrayAveria as $averia) {
            $respuesta = Helpers::actualizarQuiebre($averia, $quiebreId, $quiebre, $empresaId, $empresa, $forzar);
            $row = array(
                'codigo'=>$averia,
                'quiebre'=>$quiebre,
                'contrata'=>$empresa,
                'estado'=>$respuesta
                );
            array_push($arrayInfo, $row);
        }
        $query = 'INSERT INTO bitacora_registros (usuario_id,accion,data)
                    values (?, ?,?)';
        try {
            DB::insert(
                $query,
                array($usuarioId, 'Quiebre Masivo',$averias)
            );
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            return Response::json(
                array(
                    'estado'=>'0',
                    'msj'=>'Ocurrio un error en la carga'
                )
            );
        }
        return Response::json(
            array(
                'estado'=>'1',
                'msj' => 'Se realizó con éxito',
                'txt' => $arrayInfo,
            )
        );
    }
    /**
     * actualizacion de quiebres masivos
     */
    public function postActualizaquiebreindividual()
    {
        $usuarioId = Auth::user()->id;
        $averia= Input::get('averia');
        $forzar= Input::get('forzar');
        $quiebre= Input::get('quiebre', '');
        $quiebreId= Input::get('quiebre_id');
        $empresa= Input::get('empresa', '');
        $empresaId= Input::get('empresa_id');

        $respuesta = Helpers::actualizarQuiebre($averia, $quiebreId, $quiebre, $empresaId, $empresa, $forzar);
        $query = 'INSERT INTO bitacora_registros (usuario_id,accion,data)
                    values (?, ?,?)';
        try {
            DB::insert(
                $query,
                array($usuarioId, 'Quiebre Individual',$averia)
            );
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            return Response::json(
                array(
                    'estado'=>'0',
                    'msj'=>'Ocurrio un error en la carga'
                )
            );
        }
        return Response::json(
            array(
                'estado'=>'1',
                'msj'=>$averia.': '.$respuesta
            )
        );
    }
}