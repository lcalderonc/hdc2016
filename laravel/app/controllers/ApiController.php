<?php

class ApiController extends \BaseController
{
    protected $_OfficetrackController;
    protected $_errorController;

    public function __construct(OfficetrackController $OfficetrackController)
    {
        //$this->beforeFilter('auth');
        $this->_OfficetrackController = $OfficetrackController ;
        //$this->_errorController = $ErrorController;
    }
    /**
     * obtiene1 si el tecnico tiene tareas hacignadas con el estado "2",
     * agendado con tecnico
     * POST /api/reenviarot
     * @param  int  gestion_id
     * @return Response
     */
    public function postTareatecnico()
    {
        if ( Input::has('gestion_id') && Input::has('carnet') ) {
            $gestionId = Input::get('gestion_id', '');
            $carnet = Input::get('carnet', '');
            $tecnico = Tecnico::where('carnet', $carnet)->first();
            $tecnicoId = $tecnico->id;

            $sql="SELECT id
                    FROM ultimos_movimientos  um
                    WHERE estado_id=2 AND tecnico_id = ? AND gestion_id = ? ";
            $result = DB::select($sql, array( $tecnicoId, $gestionId ));
            if (count($result)>0)
                $i=1;
            else
                $i=0;

            return Response::json(
                array(
                'rst'=>1,
                'cantidad'=>$i,
                )
            );
        } else {
            return Response::json(
                array(
                'rst'=>0,
                'error'=>'parametros incorrectos',
                )
            );
        }
    }
    /**
     * reenviar tarea de officetrack
     * POST /api/reenviarot
     * @param  int  gestion_id
     * @return Response
     */
    public function postReenviarot()
    {
        if ( Input::has('gestion_id') ) {

            Input::replace(
                array(
                    'buscar' => Input::get('gestion_id'),
                    'tipo' => 'g.id'
                )
            );
            $result = Gestion::getCargar();
            $resultDos = array();
            if ($result['rst'] =='1') {
                $datos = $result['datos'][0];

                $resultDos['fh_agenda'] =$datos->fh_agenda;
                $resultDos['codactu']= trim($datos->codactu.' ');
                $resultDos['fecha_registro'] =trim($datos->fecha_registro.' ');
                $resultDos['nombre_cliente'] =trim($datos->nombre_cliente.' ');
                $resultDos['act_codmotivo_req_catv'] =
                                    trim($datos->codmotivo_req_catv.' ');
                $resultDos['orden_trabajo'] =trim($datos->orden_trabajo.' ');
                $resultDos['fftt'] =trim($datos->fftt.' ');
                $resultDos['dir_terminal'] =trim($datos->dir_terminal.' ');
                $resultDos['inscripcion'] =trim($datos->inscripcion.' ');
                $resultDos['mdf'] =trim($datos->mdf.' ');
                $resultDos['segmento'] =trim($datos->segmento.' ');
                $resultDos['clase_servicio_catv'] =
                                    trim($datos->clase_servicio_catv.' ');
                $resultDos['total_averias'] =trim($datos->total_averias.' ');
                $resultDos['zonal'] =trim($datos->zonal.' ');
                $resultDos['llamadastec15dias'] =
                                    trim($datos->llamadastec15dias.' ');
                $resultDos['quiebre'] =trim($datos->quiebre.' ');
                $resultDos['lejano'] =trim($datos->lejano.' ');
                $resultDos['distrito'] =trim($datos->distrito.' ');
                $resultDos['averia_m1'] =trim($datos->averia_m1.' ');
                $resultDos['telefono_codclientecms'] =
                                    trim($datos->telefono_codclientecms.' ');
                $resultDos['area2'] =trim($datos->area2.' ');
                $resultDos['eecc_final'] =trim($datos->eecc_zona.' ');
                $resultDos['gestion_id'] =trim($datos->id.' ');
                $resultDos['estado'] =trim($datos->estado.' ');
                $resultDos['velocidad'] =trim($datos->veloc_adsl.' ');
                $resultDos['cr_observacion'] =trim($datos->observacion.' ');
                //$resultDos['reenviar'] =trim( $datos->reenviar.' ');
                $resultDos['actividad'] =trim($datos->actividad.' ');
                $resultDos['tecnico_id'] =trim($datos->tecnico_id.' ');
                $resultDos['coordinado2'] =trim($datos->coordinado.' ');
                $resultDos['direccion_instalacion'] = trim(
                    $datos->direccion_instalacion.' '
                );

                $resultTres = Helpers::ruta(
                    'officetrack/procesarot', 'POST', $resultDos, false
                );
                return Response::json($resultTres);

            } else {
                return Response::json($result);
            }
        }
    }
    /**
     * devolver la distancia de la tarea al punto x, y
     * POST /api/distanciaactu
     * @param  int  gestion_id
     * @param  int  actu
     * @param  int  x
     * @param  int  y
     * @return Response
     */
    public function postDistanciaactu()
    {
        if ( (Input::has('gestion_id') ||
            Input::has('actu') ) &&
            Input::has('x') &&
            Input::has('y') ) {

            $gestionId = Input::get('gestion_id', '');
            $actu = Input::get('actu', '');
            $x = Input::get('x');
            $y = Input::get('y');
            $distancia = 0;
            //query
            if ($gestionId!=='') {
                $gestionDetalle = GestionDetalle::Where(
                    'gestion_id',
                    $gestionId
                )->first();
                $tabla = 'gestiones_detalles';
                $gestionX = $gestionDetalle->x;
                $gestionY = $gestionDetalle->y;
            } elseif ($actu!=='') {
                $tmptable = new Tmptable();
                $tmpdata = $tmptable->getAveria($actu);
                if (isset($tmpdata[0])) {
                    //return averia x,y
                    $tabla = 'tmp_averia';
                    $gestionX = $tmpdata[0]->x;
                    $gestionY = $tmpdata[0]->y;
                } else {
                    $tmpdata = $tmptable->getProvision($actu);
                    if (isset($tmpdata[0])) {
                        //return averia x,y
                        $tabla = 'tmp_provision';
                        $gestionX = $tmpdata[0]->x;
                        $gestionY = $tmpdata[0]->y;
                    } else {
                        return Response::json(
                            array(
                            'rst'=>0,
                            'error'=>'no se encontro x,y para este codigo',
                            )
                        );
                    }
                }

            }
            //distancia
            $distancia = sqrt(
                pow(($gestionX - $x), 2) +
                pow(($gestionY - $y), 2)
            );
            //metros
            $distancia=$distancia*100000;

            return Response::json(
                array(
                'rst'=>1,
                'tabla' => $tabla,
                'distancia'=>$distancia,
                )
            );

        } else {
            return Response::json(
                array(
                'rst'=>0,
                'error'=>'parametros incorrectos',
                )
            );
        }
    }
    /**
     * obtener la cantidad de visitas en una tarea,
     * retorna 0 si tiene multiplo de 3 ausentes
     * POST /api/estadovisitas
     * PROVISION: DT02 (cliente ausente)
     * AVERIAS: 7705 (casa cerrada)
     * @param  int  gestion_id
     * @return Response
     */
    public function postEstadovisitas()
    {
        $cantidad='';
        $gestionId=Input::get('gestion_id', '');
        try {
            $rst=Api::getCantVisitas($gestionId);
        } catch (Exception $e) {
            return Response::json(
                array(
                'rst'=>0,
                'error'=>'ocurrio un error en la consulta',
                )
            );
        }
        if (count($rst)>0)
            $cantidad = $rst[0]->final;

        return Response::json(
            array(
            'rst'=>1,
            'cantidad'=> $cantidad,
            )
        );
    }
    /**
     * POST /api/consulta
     */
    public function postConsulta()
    {
        if (Input::has('telefonoOrigen')) {
            $telefono = Input::get('telefonoOrigen');
            
            $resultado = array();

            $sql="  SELECT DISTINCT(e.tipo_evento) as evento
                    FROM eventos e
                    LEFT JOIN evento_consulta ec ON ec.id=e.`evento_id` 
                          AND e.`tipo_evento`=1
                    LEFT JOIN tecnicos t
                           ON t.id=e.persona_id
                          AND e.tipo_persona=2
                          AND t.estado=1
                    LEFT JOIN usuarios u
                           ON u.id=e.persona_id
                          AND e.tipo_persona=1
                          AND u.estado=1
                    WHERE  (t.celular=? OR u.celular=? )
                    AND ec.id IS NOT NULL AND e.estado=1 ";
            $datos=DB::select($sql,array($telefono,$telefono));
            if ( count($datos)>0 ) {
                if ( $datos[0]->evento==1 ) {
                    $array = Helpers::ruta(
                        'api/getactuaux', 'POST', Input::all(), false
                    );
                    if ($array->rst==1) {
                        $resultado[] = $array->datos;
                    }
                }
            }
            $sql ="  SELECT e.tipo_evento as evento, em.metodo, em.nombre
                    FROM eventos e
                    LEFT JOIN evento_metodo em ON em.id=e.`evento_id` 
                          AND e.`tipo_evento`=2 AND em.`consulta`= 1
                    LEFT JOIN tecnicos t
                           ON t.id=e.persona_id
                          AND e.tipo_persona=2
                          AND t.estado=1
                    LEFT JOIN usuarios u
                           ON u.id=e.persona_id
                          AND e.tipo_persona=1
                          AND u.estado=1
                    WHERE  (t.celular=? OR u.celular=? )
                    AND em.id IS NOT NULL AND e.estado=1 ";
            $datos=DB::select($sql,array($telefono,$telefono));
            foreach ($datos as $data) {
                $inputDos = $input = Input::all();
                $input['nombreevento'] = $data->nombre;
                $array = Helpers::ruta(
                    'api/eventometodo', 'POST', $input, false
                );
                if ($array->rst==1) {
                    $resultado[] = $array->datos;
                }
            }
            if (count($resultado)>0) {
                return Response::json($resultado);
            } else {
                return Response::json(
                    array(
                        'rst'=>1,
                        'datos'=> '',
                        'msj'=>'Ud no cuenta con permisos.',
                    )
                );
            }
        } else {
            return Response::json(
                    array(
                        'rst'=>1,
                        'datos'=> '',
                        'msj'=>"Ud debe ingresar elefono 'telefonoOrigen'",
                    )
                );
        }

    }

    public function postGetactuaux()
    {
        if (Input::has('telefonoOrigen')) {
            $telefonoOrigen = Input::get('telefonoOrigen');
            $r= Api::permisosGetactu($telefonoOrigen);
            //enviar a celular
            $datos = json_encode($r['datos']);
            //strlen($datos)/140 +1;
            $datos=str_replace(array('[',']','{','}',':','"',"'"), array('','','','','=','',''), $datos);
            $contador = ceil(strlen($datos)/135);

            Sms::enviar($telefonoOrigen, $r['msj'], '397');
            if ($r['rst']=='1') {
                $inicio=0;
                $i=0;
                while ( $contador> $i) {
                    $i++;
                    $msj=substr($datos, $inicio, 135);
                    Sms::enviar($telefonoOrigen, "(".$i.")".$msj, '397');
                    //sleep(1);
                    $inicio +=135;
                }
            }

        } else {
            return Response::json(
                array(
                    'rst'=>1,
                    'datos'=> '',
                    'msj'=>'No se encontraron registros, verifique su '.
                    'dato ingresado e intente nuevamente.',
                )
            );
        }

        return Response::json($r);
    }
    /**
     * POST /api/eventoconsulta
     */
    public function postEventoconsulta()
    {
        if ( Input::has('nombreevento') ) {
            $nombre=Input::get('nombreevento');
            $r= Api::eventoConsulta($nombre);
            return Response::json($r);
        } else {
            return Response::json(
                array(
                    'rst'=>1,
                    'datos'=> '',
                    'msj'=>'Usted debe ingresar el nombre del evento',
                )
            );
        }
    }
    /**
     * POST /api/eventometodo
     *
     * nombreevento : tabla psi.evento_metodo , campo: nombre
     * 1. bandeja   : buscar ->31786681 ,tipo->(gd.averia,g.id,gd.telefono)
     * 2. activacion: asunto->activacion, serieDeco->
     * 2. refresh   : asunto->refresh, serieDeco->
     */
    public function postEventometodo()
    {
        if (Input::has('telefonoOrigen') && Input::has('nombreevento')) {
            $r='';
            $nombre=Input::get('nombreevento');
            $telefono=Input::get('telefonoOrigen');
            if ($nombre=='bandeja') {
                if ( Input::has('buscar') && Input::has('tipo')) {
                    $r= Api::eventoMetodo($nombre, $telefono);
                } else {
                    $r="debe ingresar 'buscar' y 'tipo'";
                }

            } elseif ($nombre=='activacion' || $nombre=='refresh') {
                $r= Api::eventoMetodo($nombre, $telefono);
            } elseif ($nombre=='consulta') {
                $r= Api::eventoMetodo($nombre, $telefono);
            } elseif ($nombre=='webservice') {
                $r= Api::eventoMetodo($nombre, $telefono);
            } else {
                $r= Api::eventoMetodo($nombre, $telefono);
            }

            if ( isset($r['msj']) ) {
                Sms::enviar($telefono, $r['msj'], '397');
            }
            return Response::json($r);
        } else {
            return Response::json(
                array(
                    "rst"=> 1,
                    "datos"=> "",
                    "msj"=> "Usted debe ingresar nombre del evento 'nombreevento'",
                )
            );
        }
        /*
        if (Input::has('telefonoOrigen') && Input::has('nombreevento') ) {
            $nombre=Input::get('nombreevento');
            $telefono = Input::get('telefonoOrigen');
            $r= Api::eventoMetodo($nombre, $telefono);
            //enviar mensaje cuando el metodo llamado devuelve $r['retorno']
            if ( isset($r['retorno']) ) {
                Sms::enviar($telefono, $r['msj'], '397');
            }
            return Response::json($r);
        } else {
            Sms::enviar(Input::get('telefonoOrigen'), 'no ingreso telefono o nombre evento', '397');
            return Response::json(
                array(
                    "rst"=> 1,
                    "datos"=> "",
                    "msj"=> "Usted debe ingresar un telefono 'telefonoOrigen' y el nombre del evento 'nombreevento'",
                )
            );
        }*/
    }
    /**
     * obtener datos de una gestion segun codigo de actuacion,
     * POST /api/getactu
     * @param  int  gestion_id
     * @return Response
     */
    public function postGetactu()
    {
        $inscripcion='';
        $result=$data= array();
        //la busqueda concluye:
        //si se tiene mas de un registro
        //si se busca por apellidos y/o nombre
        $result = Api::getMaestro(Input::all());
        if ( count($result)>1 ||
            ( ( Input::has('paterno') && Input::has('materno') ) ||
                Input::has('nombre') )
        ) {
            $data['maestro']= $result;
            return Response::json(
                array(
                'rst'=>1,
                'datos'=> $data,
                )
            );
        } elseif ( count($result) ==1 ) {
            $inscripcion['inscripcio'] = trim($result[0]->inscripcio);
            $codclicms['codclicms'] = trim($result[0]->codclicms);
            $data['maestro']= $result;
        }
        $result= Api::getAveriaLiqBasLima($inscripcion);
        if (count($result)>0)
            $data['aver_liq_bas_lima']= $result;
        $result = Api::getAveriaLiqBasProv($inscripcion);
        if (count($result)>0)
            $data['aver_liq_bas_prov_pedidos']= $result;
        $result = Api::getAveriaLiqAdslPais($inscripcion);
        if (count($result)>0)
            $data['aver_liq_adsl_pais']= $result;
        $result = Api::getAveriaLiqCatvPais($codclicms);
        if (count($result)>0)
            $data['aver_liq_catv_pais']= $result;
        $result = Api::getProvisionLiqBasPais($inscripcion);
        if (count($result)>0)
            $data['prov_liq_bas_pais'] =$result;
        $result = Api::getProvisionLiqCatvPais($codclicms);
        if (count($result)>0)
            $data['prov_liq_catv_pais'] =$result;
        $result = Api::getAveriaPenBasLima($inscripcion);
        if (count($result)>0)
            $data['aver_pen_bas_lima'] =$result;
        $result = Api::getAveriaPenBasProv($inscripcion);
        if (count($result)>0)
            $data['aver_pen_bas_prov'] =$result;
        $result = Api::getAveriaPenAdslPais($inscripcion);
        if (count($result)>0)
            $data['aver_pen_adsl_pais'] =$result;
        $result = Api::getAveriaPenCatvPais($codclicms);
        if (count($result)>0)
            $data['aver_pen_catv_pais'] =$result;
        $result = Api::getProvisionPenBasPais($inscripcion);
        if (count($result)>0)
            $data['prov_pen_bas_pais'] =$result;
        $result = Api::getProvisionPenCatvPais($codclicms);
        if (count($result)>0)
            $data['prov_pen_catv_pais'] =$result;
        $result = Api::getProvisionLiqMacro($codclicms);
        if (count($result)>0)
            $data['prov_liqui_macro'] =$result;
        $result = Api::getFfttDirecta($inscripcion);
        if (count($result)>0)
            $data['fftt_directa'] =$result;
        $result = Api::getFfttsecundaria($inscripcion);
        if (count($result)>0)
            $data['fftt_secundaria'] =$result;
        return Response::json(
            array(
            'rst'=>1,
            'datos'=> $data,
            )
        );
    }
    /**
     * obtener datos de una gestion,
     * POST /api/obteneractu
     * @param  int  gestion_id
     * @return Response
     */
    public function postObteneractu()
    {
        $gestionId=Input::get('gestion_id', '');
        try {
            $gestionDetalle = GestionDetalle::Where(
                'gestion_id',
                $gestionId
            )->first();
        } catch (Exception $e) {
            return Response::json(
                array(
                'rst'=>0,
                'error'=>'ocurrio un error en la consulta',
                )
            );
        }
        return Response::json(
            array(
            'rst'=>1,
            'datos'=> $gestionDetalle,
            )
        );
    }
    /**
     * metodo para cambiar x, y
     * POST /api/actualizardireccion
     * @param  int  gestion_id
     * @param  int  carnet
     * @param  int  x
     * @param  int  y
     * @param  int  direccion
     * @param  int  referencia
     * @return Response
     */
    public function postActualizardireccion()
    {
        $gestionId = Input::get('gestion_id', '');
        $carnet = Input::get('carnet', '');
        $x = Input::get('x', '');
        $y = Input::get('y', '');
        $direccion = Input::get('direccion', '');
        $referencia = Input::get('referencia', '');
        $fechaHora = date("Y-m-d H:i:s");
        try {
            //Iniciar transaccion
            DB::beginTransaction();

            //Guardar direccion previa
            UltimoMovimiento::actualizar_direccion(
                $x, $y, $direccion, $gestionId
            );

            $tecnico = Tecnico::where('carnet_tmp', '=', $carnet)->first();
            $tecnico_id = $tecnico->id;
            //INSERT en cambios_direcciones
            $sql = "INSERT INTO cambios_direcciones
                   (gestion_id, tipo_usuario, usuario_id,
                   coord_x, coord_y, direccion, referencia, created_at)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $data = array(
                $gestionId, 'tec', $tecnico_id,
                $x, $y, $direccion, $referencia,
                $fechaHora
            );
            DB::insert($sql, $data);
            $sql = "UPDATE ultimos_movimientos
                   SET x = ?, y = ?, direccion_instalacion = ?
                   WHERE gestion_id = ?";
            $data = array(
                $x, $y, $direccion . " ref.: " . $referencia, $gestionId
            );
            DB::update($sql, $data);
            //UPDATE gestiones_detalles
            $sql = "UPDATE gestiones_detalles
                   SET x = ?, y = ?, direccion_instalacion = ?
                   WHERE gestion_id = ?";
            $data = array(
                $x, $y, $direccion . " ref.: " . $referencia, $gestionId
            );
            DB::update($sql, $data);
            DB::commit();
            return Response::json(
                array(
                    'estado'=>true,
                    'msg'=> "Direccion actualizada",
                )
            );
        } catch (PDOException $error) {
            DB::rollback();
            return Response::json(
                array(
                    'estado'=>false,
                    'msg'=> $error->getMessage()
                )
            );
        }
    }
}
