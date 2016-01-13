<?php

class ReporteController extends BaseController
{
    public function postAgendamientomesca()
    {
        $fechaIni="";
        $fechaFin="";

        if ( Input::get('fecha_agenda') ) {
            $fechaAgenda=explode(" - ", Input::get('fecha_agenda'));
            $fechaIni=$fechaAgenda[0]; //Fecha inicio
            $fechaFin=$fechaAgenda[1]; //Fecha final
        }
        $fechaIniaux=$fechaIni; // Fecha inicial auxiliar para aumento

        $pos=0;

        //Meses Texto
        $cabeceraMes= array('Enero','Febrero','Marzo','Abril',
                            'Mayo','Junio','Julio','Agosto',
                            'Setiembre','Octubre','Noviembre','Diciembre');

        $contador=0;
        $cabecera=array();
        $cols="";
        $leftJoin="";

        while($fechaIniaux<=$fechaFin){
            $contador++; //nro dimamico para columnas
            /*************************CABECERA*********************************/
            $mes=date("m",strtotime(date($fechaIniaux))); // Obteniendo Mes
            $cabecera[]=$cabeceraMes[ ($mes-1) ]; // Capturando Mes Texto
            /******************************************************************/
            /*************************DATOS DINAMICOS**************************/
            $cols.= " ,count(gm".$contador.".fecha_agenda) f".$contador;
            $leftJoin.= " LEFT JOIN gestiones_movimientos gm".$contador." 
                          ON gm".$contador.".id=gm.id 
                          AND MONTH(gm".$contador.".fecha_agenda)='".$mes."'";
            /******************************************************************/
            $fechaIniaux=date(
                            'Y-m-d', 
                            strtotime(
                                date($fechaIniaux)." +1 month"
                            )
                         );// Aumentando de mes en mes
        }

        $query="   SELECT gd.codactu,gd.fecha_registro,gd.created_at fecha_psi,
                    count(gm.id) total,count(gm.fecha_agenda) total_programados
                    $cols
                    FROM gestiones_detalles gd
                    JOIN gestiones_movimientos gm ON gd.gestion_id=gm.gestion_id 
                    /* PARTE DINAMICA VERTICAL=> definir cual será el recorrido , L-D, días, meses, trimestres, horarios, tecnicos(otros) */
                    /* PARTE ESTATICA HORIZONTAL=> codactu, quiebres, empresa, motivo - submitivo - estados */
                    $leftJoin
                    /*********************************************************************************************/
                    WHERE 
                     DATE(fecha_registro) BETWEEN '$fechaIni' and '$fechaFin'
                    -- DATE(created_at) BETWEEN '2015-11-01' and '2015-11-30'
                    GROUP BY gd.gestion_id
                ";
        $rquery=DB::select($query);
        //echo $query;
        return Response::json(
            array(
                        'rst'=>1,
                        'datos'=>$rquery,
                        'cabecera'=>$cabecera
                    )
        );

    }
    /*
    Demo con Paginación Ajax
    */
    public function postAgendamientomescapag()
    {
        $fechaIni="";
        $fechaFin="";

        if ( Input::get('fecha_agenda') ) {
            $fechaAgenda=explode(" - ", Input::get('fecha_agenda'));
            $fechaIni=$fechaAgenda[0]; //Fecha inicio
            $fechaFin=$fechaAgenda[1]; //Fecha final
        }
        $fechaIniaux=$fechaIni; // Fecha inicial auxiliar para aumento

        $pos=0;

        //Meses Texto
        $cabeceraMes= array('Enero','Febrero','Marzo','Abril',
                            'Mayo','Junio','Julio','Agosto',
                            'Setiembre','Octubre','Noviembre','Diciembre');

        $contador=0;
        $cabecera=array();
        $cols="";
        $leftJoin="";

        while($fechaIniaux<=$fechaFin){
            $contador++; //nro dimamico para columnas
            /*************************CABECERA*********************************/
            $mes=date("m",strtotime(date($fechaIniaux))); // Obteniendo Mes
            $cabecera[]=$cabeceraMes[ ($mes-1) ]; // Capturando Mes Texto
            /******************************************************************/
            /*************************DATOS DINAMICOS**************************/
            $cols.= " ,count(gm".$contador.".fecha_agenda) f".$contador;
            $leftJoin.= " LEFT JOIN gestiones_movimientos gm".$contador." 
                          ON gm".$contador.".id=gm.id 
                          AND MONTH(gm".$contador.".fecha_agenda)='".$mes."'";
            /******************************************************************/
            $fechaIniaux=date(
                            'Y-m-d', 
                            strtotime(
                                date($fechaIniaux)." +1 month"
                            )
                         );// Aumentando de mes en mes
        }

        /* Preparación para la paginación */
        $count = "  SELECT count(id) t
                    FROM gestiones_detalles
                    WHERE DATE(fecha_registro) 
                          BETWEEN '$fechaIni' AND '$fechaFin' ";
         /*********************************************************************/

        $query="   SELECT gd.codactu,gd.fecha_registro,gd.created_at fecha_psi,
                    count(gm.id) total,count(gm.fecha_agenda) total_programados
                    $cols
                    FROM gestiones_detalles gd
                    JOIN gestiones_movimientos gm ON gd.gestion_id=gm.gestion_id
                    /* PARTE DINAMICA VERTICAL=> definir cual será el recorrido , L-D, días, meses, trimestres, horarios, tecnicos(otros) */
                    /* PARTE ESTATICA HORIZONTAL=> codactu, quiebres, empresa, motivo - submitivo - estados */
                    $leftJoin
                    /*********************************************************************************************/
                    WHERE 
                     DATE(fecha_registro) BETWEEN '$fechaIni' and '$fechaFin'
                    -- DATE(created_at) BETWEEN '2015-11-01' and '2015-11-30'
                    GROUP BY gd.gestion_id
                ";

        $retorno=array(
                        'rst'=>1,
                        'cabecera'=>$cabecera
                    );

        if( Input::get('draw') ){
            if ( Input::get('order') ){
                $inorder=Input::get('order');
                $incolumns=Input::get('columns');
                $query.=    ' ORDER BY '.
                            $incolumns[ $inorder[0]['column'] ]['name'].' '.
                            $inorder[0]['dir'];
            }
            $query.=' LIMIT '.Input::get('start').','.Input::get('length');

            $retorno["draw"]=Input::get('draw');
        }

        /****************Ejecución de Queys********************************/
        $rcount= DB::select($count);
        $rquery=DB::select($query);
        /******************************************************************/

        $retorno["data"]=$rquery;
        $retorno["recordsTotal"]=$rcount[0]->t;
        $retorno["recordsFiltered"]=$rcount[0]->t;

         //DB::connection()->disableQueryLog();

        //echo $query;
        return Response::json(
            $retorno
        );

    }
    /**
     * Store a newly created resource in storage.
     * POST /lista/estadosdigitalizacioncrear
     *
     * @return Response
     */
    public function postEstadosdigitalizacioncrear()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            //validar la data y crear nueva gestion
            if (Input::has('ed_motivo_id') && Input::has('observacion') &&
                Input::has('ed_id')) {
                $data=array(
                    Input::get('observacion'),
                    Input::get('ed_id'),
                    Auth::user()->id,
                    Input::get('ed_motivo_id'),
                    1
                    );
                $datos=EstadosDigitalizacion::getEstadosDigitalizacionCrear($data);
                return Response::json(
                    array('rst'=>1,
                        'datos'=>$datos,
                        'msj'=>"Se inserto registro exitosamente"
                        )
                );
            }
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /lista/estadosdigitalizaciongestiones
     *
     * @return Response
     */
    public function postEstadosdigitalizaciongestiones()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            if (Input::has('estado_id')) {
                $id = Input::get('estado_id');
                $datos = EstadosDigitalizacion::getEstadosDigitalizacionGestiones($id);
                return Response::json(array('rst'=>1,'datos'=>$datos));
            }
        }
    }
    /**
     * mostrar distinct de la vista vistaEstadoDigitalizacion
     */
    public function postListar()
    {
        $rst = EstadosDigitalizacion::getListaProyecto();
        return Response::json(
            array(
                    'rst'=>1,
                    'datos'=>$rst
                )
        );
    }
    /**
     * cargar los registros de estados_digitalizacion a gestionar
     */
    public function postEstadodigitalizacioncargar()
    {
        $data=array();
        //Input::get('proyecto', )
        if (Input::has('proyecto')) {
            $proyecto = Input::get('proyecto');
            $data = EstadosDigitalizacion::getEstadosDigitalizacion($proyecto);
        }

        return Response::json(
            array(
                'rst'=>1,
                'msj' => 'Se cargaron '+count($data)+' registros',
                'datos'=>$data
            )
        );
    }
    /**
     * estadodigitalizacionexcel
     * generar excel de la vista vistaEstadoDigitalizacion
     */
    public function postEstadodigitalizacionexcel()
    {
        //recibir los parametros y enviarlos al modelo, ahi ejecutar el query
        if (Input::has('proyecto')) {
            $proyecto = Input::get('proyecto');
        }
        $rst = EstadosDigitalizacion::getVistaEstadosDigitalizacion($proyecto);
        

        $filename = Helpers::convert_to_file_excel("estadodigitalizacion");

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-Transfer-Encoding: binary");
        header('Pragma: public');

        $n = 1;
        foreach ($rst as $data) {
            // Encabezado
            if ($n == 1) {
                foreach ($data as $key=>$val) {
                    echo $key . "\t";
                }
                echo $val . "\r\n";
            }
            //Datos
            foreach ($data as $val) {
                $val = str_replace(
                    array("\r\n", "\n", "\n\n", "\t", "\r"),
                    array("", "", "", "", ""),
                    $val
                );
                echo $val . "\t";
            }
            echo "\r\n";
            $n++;
        }
    }
    public function postEstadodigitalizacion()
    {
        //validar archivo
        if (Input::hasFile('archivo')) {
            if ( Input::file('archivo')->isValid() ) {
                $file = Input::file('archivo');
                $tmpArchivo = $file->getRealPath();
                $data = Helpers::fileToJsonAddress($tmpArchivo, 0);
            } else {
                return Response::json(
                    array(
                        'estado'=>'0',
                        'msj'=>'Archivo no valido'
                    )
                );
            }
        } else {
            return Response::json(
                array(
                    'estado'=>'0',
                    'msj'=>'Archivo no valido'
                )
            );
        }
        set_time_limit(0);
        $i=0;
        //recorre filas
        foreach ($data as $key => $value) {
            $row = explode("\t", $value);
            //recorre columnas
            if ($key>0) {
                $estados=EstadosDigitalizacion::where('cliente_cms', '=', $row[1])
                                    ->where('servicio_cms', '=', $row[2])
                                    ->first();
                if (count($estados)==0) {
                    //INSERTAR
                    try {
                        $estado = EstadosDigitalizacion::nuevo($row);
                    } catch (Exception $e) {
                        Log::error($e);
                        return "";
                    }
                    $i++;
                }
            }
        }
        return Response::json(
            array(
                'rst'=>1,
                'msj' => 'Se cargaron '.$i.' registros',
                //'datos'=>EstadosDigitalizacion::all()
            )
        );
    }
    public function postMovimientoult()
    {
        //recibir los parametros y enviarlos al modelo, ahi ejecutar el query
        $averia = null;
        if (Input::hasFile('file_averia')) {

            if ( Input::file('file_averia')->isValid() ) {
                $file = Input::file('file_averia');
                $tmpArchivo = $file->getRealPath();
                $file = file($tmpArchivo);
                $con=0;
                $averia="";
                foreach ($file as $f) {
                    $con++;
                    if($con>1) $averia.=",".trim($f);
                }
                $averia=substr($averia, 1);
            }
        }
        $averiau= Input::get('txt_cod_actu');
        $checkFecha = Input::get('check_fecha');
        $reporte = Input::get('slct_reporte');
        $checkAveria = Input::get('check_averia');
        $checkAveriaU = Input::get('check_averia_u');
        $fechaIni = Input::get('fecha_ini');
        $fechaFin = Input::get('fecha_fin');
        $detalle =  Input::get('slct_detalle_observacion');
        $pendiente = Input::get('slct_pendiente');
        $checkDetalleAgenda = false;

        $total = GestionMovimiento::getGestionMovimiento_ult(
            $checkFecha,
            $checkAveria,
            $checkAveriaU,
            $reporte,
            $fechaIni,
            $fechaFin,
            $averia,
            $averiau,
            $pendiente
        );

        $output="<table>";
        $con=0;
        foreach ($total as $t) {
            $con++;
            if($con==1){
                $output.="<tr>";
                foreach ($t as $key => $value) {
                    $output.="<th>";
                    $output.=$key;
                    $output.="</th>";
                }
                $output.="</tr>";
            }

            $output.="<tr>";
                foreach ($t as $key => $value) {
                    $output.="<td>";
                    $output.=$value;
                    $output.="</td>";
                }
            $output.="</tr>";
        }
        $output .= "</table>";

        $filename = Helpers::convert_to_file_excel('reporte_ult_movimientos');

        $headers = array(
            'Pragma' => 'public',
            'Expires' => 'public',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Cache-Control' => 'private',
            'Content-Type' => 'application/vnd.ms-excel',
            'charset' => 'utf-8',
            'Content-Disposition' => 'attachment; filename='.$filename,
            'Content-Transfer-Encoding' => ' binary'
        );
        return Response::make($output, 200, $headers);
    }
    /**
     * retornar reporte en formato xls
     * POST reporte/movimiento
     *
     * @return file xls
     */
    public function postMovimiento()
    {
        //recibir los parametros y enviarlos al modelo, ahi ejecutar el query
        $averia = null;
        if (Input::hasFile('file_averia')) {

            if ( Input::file('file_averia')->isValid() ) {
                $file = Input::file('file_averia');
                $tmpArchivo = $file->getRealPath();
                $file = file($tmpArchivo);
                $con=0;
                $averia="";
                foreach ($file as $f) {
                    $con++;
                    if($con>1) $averia.=",".trim($f);
                }
                $averia=substr($averia, 1);
            }
        }
        $checkFecha = Input::get('check_fecha');
        $reporte = Input::get('slct_reporte');
        $checkAveria = Input::get('check_averia');
        $fechaIni = Input::get('fecha_ini');
        $fechaFin = Input::get('fecha_fin');
        $detalle =  Input::get('slct_detalle_observacion');
        $checkDetalleAgenda = false;

        //Reporte por fecha de agenda incluyendo detalle de movimientos
        if ($reporte == 'adt')
        {
            $averia = "";
            $cliente = GestionMovimiento::getGestionMovimiento_coc(
                $checkFecha,
                $checkAveria,
                'age',
                $fechaIni,
                $fechaFin,
                $averia,
                $detalle
            );

            foreach ($cliente as $row) {
                $averia .= "," . $row->averia;
            }

            $checkDetalleAgenda = true;
            $averia=substr($averia, 1);
            $checkFecha = null;
            $checkAveria = true;
        }

        $cliente = GestionMovimiento::getGestionMovimiento_coc(
            $checkFecha,
            $checkAveria,
            $reporte,
            $fechaIni,
            $fechaFin,
            $averia,
            $detalle
        );

        $outputcab="";
        $outputdet="";
        if ($detalle==1) {
            $outputcab="<td>DETALLE OBSERVACIÓN</td>";
        }

        $output = "<head>
                    <meta charset='UTF-8'>
                    </head><table><tr>";
        $output.="<td>Id_atc</td>
                <td>Tipo Actuacion</td>
                <td>Averia</td>
                <td>Quiebre</td>
                <td>nombre_contacto</td>
                <td>Cod Cliente</td>
                <td>celular_contacto</td>
                <td>observacion</td>
                <td>Fecha Agenda</td>
                <td>horario</td>
                <td>dia</td>
                <td>Empresa</td>
                <td>Zonal</td>
                <td>Motivo</td>
                <td>Submotivo</td>
                <td>Estado</td>
                <td>Tecnico</td>
                <td>Fecha Registro Actuacion</td>
                <td>Hora Registro Actuacion</td>
                <td>Fecha Creacion ATC</td>
                <td>Hora Creacion ATC</td>
                <td>Fecha Creacion Movimiento</td>
                <td>Hora Creacion Movimiento</td>
                <td>Fecha Consolidacion</td>
                <td>Ultimo Movimiento</td>
                <td>Tipo Averia</td>
                <td>Averia_m1</td>
                <td>Penalizable</td>
                <td>Desc. Penalizable</td>
                <td>Usuario</td>
                <td>codmotivo_req_catv</td>
                <td>Fecha_Cambio</td>
                <td>MDF/NODO</td>
                <td>fftt</td>".$outputcab."
                <td>Estado Legados</td>
                <td>Fec.Liq.Legados</td>
                <td>Estado OFSC</td>
                <td>Aid</td>
                <td>Envio OFSC</td>
            </tr>";

        foreach ($cliente as $row) {
            $fechaAgenda = $row->fecha_agenda;
            $horario = $row->horario;
            $dia = $row->dias;
            $fechaConsolidacion = $row->fecha_consolidacion;

            if ($detalle==1) {
                $outputdet="<td>".str_replace("|", "<br>", $row->detalle)."</td>";
            }
            /*$fechaAgenda = (($row->id==1 || $row->id==8 || $row->id==9 || $row->id==10 || $row->id==20) && ($row->m_id!=5))? $row->fecha_agenda:'';
            $horario = (($row->id==1 || $row->id==8 || $row->id==9 || $row->id==10 || $row->id==20) && ($row->m_id!=5))? $row->horario:'';
            $dia = (($row->id==1 || $row->id==8 || $row->id==9 || $row->id==10 || $row->id==20) && ($row->m_id!=5))? $row->dias:'';
            $fechaConsolidacion = ($row->id==3 || $row->id==19)? $row->fecha_consolidacion:'';*/

            //if ($fechaAgenda!="")
               // $fechaAgenda = Helpers::convert_to_date($fechaAgenda, 'date');

            //if ($fechaConsolidacion!="")
                //$fechaConsolidacion = Helpers::convert_to_date($fechaConsolidacion, 'date');

            $penalizable = $row->penalizable;
            $descPenalizable = $row->penalizabledes;
            /*if ($row->id=="3" || $row->id=="19") {
                $penalizable = ($row->penalizable=="")? 'no':'si';
                $descPenalizable = $row->penalizable;
            } else {
                $penalizable = "";
                $descPenalizable = "";
            }*/

            $fechaMov = Helpers::convert_to_date($row->fecha_movimiento, 'datetime');
            $fechaRegistro = Helpers::convert_to_date($row->fecha_registro, 'datetime');
            $fechaCreacion = Helpers::convert_to_date($row->fecha_creacion, 'datetime');
            $fechaMov= explode(" ", $fechaMov);
            $fechaRegistro= explode(" ", $fechaRegistro);
            $fechaCreacion= explode(" ", $fechaCreacion);

            $output .= "<tr><td>".$row->id_atc."</td><td>".$row->tipo_actividad.
            "</td><td>".$row->averia."</td><td>".$row->quiebre."</td><td>".
            $row->nombre_cliente_critico."</td><td>".
            $row->telefono_cliente_critico."</td><td>".
            $row->celular_cliente_critico."</td><td>".$row->observacion.
            "</td><td>".$fechaAgenda."</td><td>".$horario."</td><td>".$dia.
            "</td><td>".$row->nombre."</td><td>".$row->zonal."</td><td>".
            $row->motivo."</td><td>".$row->submotivo."</td><td>".$row->estado.
            "</td><td>".$row->tecnico."</td><td>".$fechaRegistro[0]."</td><td>".$fechaRegistro[1]."</td><td>".
            $fechaCreacion[0]."</td><td>".$fechaCreacion[1]."</td><td>".$fechaMov[0]."</td><td>".$fechaMov[1]."</td><td>".
            $fechaConsolidacion."</td><td>".$row->ultimo_movimiento."</td><td>".
            $row->tipo_averia."</td><td>".$row->averia_m1."</td><td>".
            $penalizable."</td><td>".$descPenalizable."</td><td>".$row->usuario.
            "</td><td>".$row->codmotivo_req_catv."</td><td>".$row->fecha_cambio.
            "</td>"."<td>".$row->mdf."</td>"."<td>".$row->fftt."</td>"
                    . $outputdet
                    . "<td>" . $row->estado_legado . "</td>"
                    . "<td>" . $row->fec_liq_legado . "</td>"
                    . "<td>" . $row->estado_ofsc . "</td>"
                    . "<td>" . $row->aid . "</td>"
                    . "<td>" . $row->envio_ofsc . "</td>"
                    . "</tr>";

        }
        $output .= "</table>";

        $filename = Helpers::convert_to_file_excel('reporte_movimientos');

        $headers = array(
            'Pragma' => 'public',
            'Expires' => 'public',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Cache-Control' => 'private',
            'Content-Type' => 'application/vnd.ms-excel',
            'charset' => 'utf-8',
            'Content-Disposition' => 'attachment; filename='.$filename,
            'Content-Transfer-Encoding' => ' binary'
        );
        return Response::make($output, 200, $headers);
    }
    /**
     * Listar registro de actividades con estado 1
     * POST reporte/cruce
     *
     * @return Response
     */
    public function postCruce()
    {
        //recibir los parametros y enviarlos al modelo, ahi ejecutar el query
        $fechaIni = Input::get('fecha_ini');
        $fechaFin = Input::get('fecha_fin');

        $reporte = Tarea::reporte_cruce_finalizado(
            $fechaIni,
            $fechaFin
        );
        $filename = Helpers::convert_to_file_excel('cruce_finalizado');

        header('Content-Type: application/octet-stream; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-Transfer-Encoding: binary");
        header('Pragma: public');
        $n = 1;
        foreach ($reporte as $data) {
            //Encabezado
            if ($n == 1) {
                foreach ($data as $key=>$val) {
                    echo $key . "\t";
                }
                echo $val . "\r\n";
            }
            //Datos
            foreach ($data as $val) {
                $val = str_replace(
                    array("\r\n", "\n", "\n\n", "\t", "\r"),
                    array("", "", "", "", ""),
                    $val
                );
                echo $val . "\t";
            }
            echo "\r\n";
            $n++;
        }
    }
    /**
     * Listar registro de actividades con estado 1
     * POST reporte/critico
     *
     * @return Response
     */
    public function postCritico()
    {
        //recibir los parametros y enviarlos al modelo, ahi ejecutar el query
        if ( Input::has('averia') ) {
            $averia = Input::get('averia');
            $reporte = Critico::critico_averia();
            $filename = 'averias_criticos';
        } elseif ( Input::has('provision')) {
            $provision = Input::get('provision');
            $reporte = Critico::critico_provision();
            $filename = 'provision_criticos';
        } elseif (Input::get('actividad')<>'') {
            $actividad = Input::get('actividad');//1 averia 2 provision
            $fechaIni = Input::get('fecha_ini');
            $fechaFin = Input::get('fecha_fin');
            if ($actividad=='1') {//averia
                $reporte = Critico::critico_averia_historico(
                    $fechaIni,
                    $fechaFin
                );
                $filename = 'averias_criticos_historico';
            } else {//provision
                $reporte = Critico::critico_provision_historico(
                    $fechaIni,
                    $fechaFin
                );
                $filename = 'provision_criticos_historico';
            }
        }

        $filename = Helpers::convert_to_file_excel($filename);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-Transfer-Encoding: binary");
        header('Pragma: public');

        $n = 1;
        foreach ($reporte as $data) {
            //Encabezado
            if ($n == 1) {
                foreach ($data as $key=>$val) {
                    echo $key . "\t";
                }
                echo $val . "\r\n";
            }
            //Datos
            foreach ($data as $val) {
                $val = str_replace(
                    array("\r\n", "\n", "\n\n", "\t", "\r"),
                    array("", "", "", "", ""),
                    $val
                );
                echo $val . "\t";
            }
            echo "\r\n";
            $n++;
        }
    }
    /**
     * reporte en excel
     * POST reporte/digitalizacion
     *
     * @return Response
     */
    public function postDigitalizacion()
    {
        //recibir los parametros y enviarlos al modelo, ahi ejecutar el query
        if ( Input::has('averia') ) {
            $averia = Input::get('averia');
            $reporte = Critico::digitalizacion_averia();
            $filename = 'averias_digitalizacion';
        } elseif ( Input::has('provision')) {
            $provision = Input::get('provision');
            $reporte = Critico::digitalizacion_provision();
            $filename = 'provision_digitalizacion';
        } elseif (Input::get('actividad')<>'') {
            return '';
        }

        $filename = Helpers::convert_to_file_excel($filename);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-Transfer-Encoding: binary");
        header('Pragma: public');

        $n = 1;
        foreach ($reporte as $data) {
            // Encabezado
            if ($n == 1) {
                foreach ($data as $key=>$val) {
                    echo $key . "\t";
                }
                echo $val . "\r\n";
            }
            //Datos
            foreach ($data as $val) {
                $val = str_replace(
                    array("\r\n", "\n", "\n\n", "\t", "\r"),
                    array("", "", "", "", ""),
                    $val
                );
                echo $val . "\t";
            }
            echo "\r\n";
            $n++;
        }
    }
    /**
     * Listar registro de actividades con estado 1
     * POST reporte/tecnicoofficetrack
     *
     * @return Response
     */
    public function postTecnicoofficetrack()
    {
        //recibir los parametros y enviarlos al modelo, ahi ejecutar el query
        //1 dia, 2 rango de fechas
        $tipoRepor = Input::get('tipo_repo');
        $tecnicos = Input::get('tecnicos');
        $fecha = Input::get('fecha');
        $table ='';
        if ($tipoRepor == "1") {

            $reporte = Tecnico::asisTecnicos($fecha, $tecnicos);
            if (count($reporte)>0) {
                if ( Input::has('excel') ) {
                    $filename = 'asistencia_tecnicos_por_dia';
                } else {
                    $h = array();
                    foreach ($reporte as $row) {
                        foreach ($row as $key=>$val) {
                            if ( !in_array($key, $h) ) {
                                $h[] = $key;
                            }
                        }
                    }
                    $table = "<thead></thead>";
                    $cabecera = "<tr>";
                    foreach ($h as $key) {
                        $key = ucwords($key);
                        $cabecera .= '<th>'.$key.'</th>';
                    }
                    $cabecera.='</tr>';
                    $table="<thead>".$cabecera."</thead>";;
                    $table ."<tbody id='tb_reporte'>";
                    $imgHabilitado='img/admin/datatable/estado_habilitado.png';
                    $imgInhabilitado='img/admin/datatable/estado_deshabilitado.png';
                    foreach ($reporte as $row) {
                        $table .= "<tr>";
                            foreach ($row as $field) {
                                if ($field== "Activo") {
                                    $field ="<img src=$imgHabilitado>";
                                } elseif ($field== "Inactivo") {
                                    $field ="<img src=$imgInhabilitado>";
                                }
                                $table .= "<td class='td_res_grupal2'>".$field."</td>";
                            }
                        $table .="</tr>";
                    }
                    $table .="</tbody><tfoot>".$cabecera."</tfoot>";
                }
            }

        } elseif ($tipoRepor == "2") {
            list($fechaIni,$fechaFin) = explode(" - ", $fecha);
            $reporte = Tecnico::asisTecnicosRango($fechaIni, $fechaFin, $tecnicos);
            if (count($reporte)>0) {
                if ( Input::has('excel') ) {
                    $filename = 'asistencia_tecnicos_por_rango_fecha';
                } else {
                    $h = array();
                    foreach ($reporte as $row) {
                        foreach ($row as $key=>$val) {
                            if ( !in_array($key, $h) ) {
                                $h[] = $key;
                            }
                        }
                    }
                    $table = "<thead></thead>";
                    $cabecera = "<tr>";
                    foreach ($h as $key) {
                        $key = ucwords($key);
                        $cabecera .= '<th>'.$key.'</th>';
                    }
                    $cabecera.='</tr>';
                    $table="<thead>".$cabecera."</thead>";;
                    $table ."<tbody id='tb_reporte'>";
                    foreach ($reporte as $row) {
                        $table .= "<tr>";
                            foreach ($row as $field) {
                                $table .= "<td class='td_res_grupal2'>".$field."</td>";
                            }
                        $table .="</tr>";
                    }
                    $table .="</tbody><tfoot>".$cabecera."</tfoot>";
                }
            }
        }
        if ( Input::has('excel') ) {
            if (count($reporte)>0) {
                $filename = Helpers::convert_to_file_excel($filename);

                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . $filename);
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header("Content-Transfer-Encoding: binary");
                header('Pragma: public');

                $n = 1;
                foreach ($reporte as $data) {
                    //Encabezado
                    if ($n == 1) {
                        foreach ($data as $key=>$val) {
                            echo $key . "\t";
                        }
                        echo $val . "\r\n";
                    }
                    //Datos
                    foreach ($data as $val) {
                        $val = str_replace(
                            array("\r\n", "\n", "\n\n", "\t", "\r"),
                            array("", "", "", "", ""),
                            $val
                        );
                        echo $val . "\t";
                    }
                    echo "\r\n";
                    $n++;
                }
            } else {
                return Response::json(
                    array(
                        'rst'=>1,
                        'datos'=>$table
                    )
                );
            }
        } else {
            return Response::json(
                array(
                    'rst'=>1,
                    'datos'=>$table
                )
            );
        }
    }
    /**
     * reponder llamadas ajax de reportes estados de officetrack
     * POST reporte/estadoofficetrack
     *
     * @return Response
     */
    public function postEstadosot()
    {
        //recibir los parametros y enviarlos al modelo, ahi ejecutar el query

        $accion = Input::get('accion');
        if ($accion=='tecnicosofficetrack') {
            $reporte = Tecnico::getTecnicosOfficetrackAll();
        } elseif ($accion=='pendientes') {
            $fechaAgen = Input::get('fecha_agen');
            $empresaId = Input::get('empresaId');
            $celulaId = Input::get('celulaId');
            $estados = Input::get('estados');
            $carnets = Input::get('carnets');
            $pendientes ='';
            $tecnico = Tecnico::getTecnico($empresaId, $celulaId);
            if ( count($tecnico)>0 ){
                $reporte = Tarea::getAgendasAll($fechaAgen, $estados, $carnets);
            }
        } elseif ($accion=='tecnicosot') {
            $fechaIni = Input::get('fechaIni');
            $fechaFin = Input::get('fechaFin');
            $empresaId = Input::get('empresaId');
            $celulaId = Input::get('celulaId');
            //$carnets = Input::get('carnets');
            $reporte= Tarea::getTareas($fechaIni, $fechaFin. " 23:59:59 ", $empresaId, $celulaId);
        }
        if (Input::get("excel")=='1') {
            $filename=$accion;
            $filename = Helpers::convert_to_file_excel($filename);
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header("Content-Transfer-Encoding: binary");
            header('Pragma: public');

            $n = 1;
            foreach ($reporte as $data) {
                //Encabezado
                if ($n == 1) {
                    foreach ($data as $key=>$val) {
                        echo $key . "\t";
                    }
                    echo $val . "\r\n";
                }
                //Datos
                foreach ($data as $val) {
                    $val = str_replace(
                        array("\r\n", "\n", "\n\n", "\t", "\r"),
                        array("", "", "", "", ""),
                        $val
                    );
                    echo $val . "\t";
                }
                echo "\r\n";
                $n++;
            }
        }
        else
            return Response::json(array('rst' => 1, 'datos' => $reporte));
    }

    public function postTecnicoprogramadoexcel()
    {
        $fechaIni=date('Y-m-d');
        $fechaFin=date('Y-m-d', strtotime("+6 days"));
        $empresa=implode(",", Input::get('slct_empresa'));
        if ( Input::get('fecha_agenda') ) {
            $fechaAgenda=explode(" - ", Input::get('fecha_agenda'));
            $fechaIni=$fechaAgenda[0];
            $fechaFin=$fechaAgenda[1];
        }

        $quiebre_grupo=implode(",", Input::get('slct_grupo_quiebre'));

        $query3=
        '   SELECT CONCAT_WS(" ",t.ape_paterno,t.ape_materno,t.nombres) tecnico,c.nombre celula
                ,e.nombre empresa,gd.fftt
                ,concat(
                    substr(h.hora_inicio,1,5),
                    " - ",
                    substr(h.hora_fin,1,5)
                ) hora,gm.fecha_agenda,gd.codactu,gm.observacion
                ,GROUP_CONCAT(CONCAT(ot.nombre," => ",mo.observacion) SEPARATOR "<br>") detalle_observacion
                ,m.nombre Motivo
                ,sm.nombre SubMotivo
                ,es.nombre Estado
            FROM gestiones g
            INNER JOIN gestiones_detalles gd ON gd.gestion_id=g.id
            INNER JOIN quiebres q ON q.id=gd.quiebre_id
            INNER JOIN quiebre_grupos qg ON qg.id=q.quiebre_grupo_id
            INNER JOIN gestiones_movimientos gm ON gm.gestion_id=g.id
                    AND gm.id IN (SELECT max(gm2.id) FROM gestiones_movimientos gm2 WHERE gm2.gestion_id=g.id)
            INNER JOIN estados es ON es.id=gm.estado_id
            INNER JOIN motivos m ON m.id=gm.motivo_id
            INNER JOIN submotivos sm ON sm.id=gm.submotivo_id
            INNER JOIN empresas e ON e.id=gm.empresa_id
            INNER JOIN dias d ON d.id=gm.dia_id
            INNER JOIN horarios h ON h.id=gm.horario_id
            LEFT JOIN movimientos_observaciones mo ON gm.id=mo.gestion_movimiento_id
            LEFT JOIN observaciones_tipos ot ON mo.observacion_tipo_id=ot.id
            LEFT JOIN tecnicos t ON t.id=gm.tecnico_id
            LEFT JOIN celulas c ON c.id=gm.celula_id
            WHERE gm.fecha_agenda BETWEEN "'.$fechaIni.'" AND "'.$fechaFin.'"
            AND qg.id IN ('.$quiebre_grupo.')
            AND gm.empresa_id IN ('.$empresa.')
            GROUP BY gm.id';

        $rquery3= DB::select($query3);

        $filename = Helpers::convert_to_file_excel('Detalle_Programacion');

        header('Content-Type: application/octet-stream; charset=utf-8;');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Content-Transfer-Encoding: binary");
        header('Pragma: public');

        $n = 1;
        foreach ($rquery3 as $data) {
            //Encabezado
            if ($n == 1) {
                foreach ($data as $key=>$val) {
                    echo $key . "\t";
                }
                echo $val . "\r\n";
            }
            //Datos
            foreach ($data as $val) {
                $val = str_replace(
                    array("\r\n", "\n", "\n\n", "\t", "\r"),
                    array("", "", "", "", ""),
                    $val
                );
                echo $val . "\t";
            }
            echo "\r\n";
            $n++;
        }

    }

    public function postBandejaexcel()
    {
        $gestion=Gestion::getCargar();

        if( Input::has('imagen') ){
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()
                        ->setCreator("Jorge Salcedo")
                        ->setLastModifiedBy("Jorge Salcedo")
                        ->setTitle("Office 2007 XLSX Test Document")
                        ->setSubject("Office 2007 XLSX Test Document")
                        ->setDescription("Reporte de Problemas")
                        ->setKeywords("office 2007 openxml php")
                        ->setCategory("Test result file");
            $az=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD',
            'AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ','BA','BB','BC','BD','BE',
            'BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS','BT','BU','BV','BW','BX','BY','BZ','CA','CB','CC','CD','CE','CF',
            'CG','CH','CI','CJ','CK','CL','CM','CN','CO','CP','CQ','CR','CS','CT','CU','CV','CW','CX','CY','CZ','DA','DB','DC','DD','DE','DF','DG',
            'DH','DI','DJ','DK','DL','DM','DN','DO','DP','DQ','DR','DS','DT','DU','DV','DW','DX','DY','DZ');
            $objPHPExcel->getDefaultStyle()->getFont()->setName('Bookman Old Style');
            $objPHPExcel->getDefaultStyle()->getFont()->setSize(8);

            $valorinicial=1;$azpos=0;
            foreach ($gestion['datos'] as $data) {
                //Encabezado
                if ($valorinicial == 1) {
                    foreach ($data as $key=>$val) {
                        $objPHPExcel->getActiveSheet()->setCellValue($az[$azpos].$valorinicial,$key);$azpos++;
                    }
                }
                $valorinicial++;
                $azpos=0;
                //Datos
                foreach ($data as $val) {
                    $val = str_replace(
                        array("\r\n", "\n", "\n\n", "\t", "\r"),
                        array("", "", "", "", ""),
                        $val
                    );
                    $objPHPExcel->getActiveSheet()->setCellValue($az[$azpos].$valorinicial,$val);$azpos++;
                }
            }
            $objPHPExcel->getActiveSheet()->setTitle('PSI');
            $objPHPExcel->setActiveSheetIndex(0);

            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            $objWriter->save('reporte/u743/PSI_'.date("Y-m-d_H-i-s").'.xlsx');

            $zipfile = new Zipfile();
            $zipfile->add_dir("reporte/");
            $ruta="reporte/u743/";
            
            $zipfile->carpeta=array();
            $zipfile->archivo=array();
            $zipfile->listar_directorios_ruta($ruta);

            $carpeta=$zipfile->carpeta;
            $archivo=$zipfile->archivo;

                for($i=0;$i<count($carpeta);$i++){
                    $zipfile->add_dir( str_replace("reporte/u743/","reporte/",$carpeta[$i]) );
                    //$zipfile->add_dir( $carpeta[$i] );
                }

                for($i=0;$i<count($archivo);$i++){
                    $zipfile->add_file($archivo[$i],str_replace("reporte/u743/","reporte/",$archivo[$i]) );
                    //$zipfile->add_file($archivo[$i],$archivo[$i] );
                }
             
            header("Content-type: application/octet-stream");
            header("Content-disposition: attachment; filename=bandeja.zip");
            echo $zipfile->file();

            exit;
        }
        else{
        $filename = Helpers::convert_to_file_excel('Bandeja_'.date("Ymd_His"));
            header('Content-Type: application/octet-stream; charset=utf-8;');
            header('Content-Disposition: attachment; filename=' . $filename);
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header("Content-Transfer-Encoding: binary");
            header('Pragma: public');
        $n = 1;
            foreach ($gestion['datos'] as $data) {
                //Encabezado
                if ($n == 1) {
                    foreach ($data as $key=>$val) {
                        echo $key . "\t";
                    }
                    echo "\r\n";
                }
                //Datos
                foreach ($data as $val) {
                    $val = str_replace(
                        array("\r\n", "\n", "\n\n", "\t", "\r"),
                        array("", "", "", "", ""),
                        $val
                    );
                    echo $val . "\t";
                }
                echo "\r\n";
                $n++;
            }
        }//

    }

    public function postTecnicoprogramado()
    {
        $empresa=implode(",", Input::get('empresa'));
        $zonal=Input::get('zonal');

        $fechaIni=date('Y-m-d');
        $fechaFin=date('Y-m-d', strtotime("+6 days"));

        if ( Input::get('fecha_agenda') ) {
            $fechaAgenda=explode(" - ", Input::get('fecha_agenda'));
            $fechaIni=$fechaAgenda[0];
            $fechaFin=$fechaAgenda[1];
        }
        $fechaIniaux=$fechaIni;

        $pos=0;
        for($i=0;$i<count($zonal);$i++){
            $pos=strrpos($zonal[$i], '|', 1);
            $zonal[$i]=substr($zonal[$i], ($pos+1));
        }
        $zonal=implode(",", $zonal);
        $quiebre_grupo=implode(",", Input::get('grupo_quiebre'));
        /*$tecnico='';
        if( Input::get('tecnico') ){
        $tecnico=implode( ",",Input::get('tecnico') );
        }*/

        $query1='   SELECT chd.capacidad_horario_id,chd.horario_id
                    ,concat(
                        substr(h.hora_inicio,1,5),
                        "<br>",
                        substr(h.hora_fin,1,5)
                    ) hora
                    FROM capacidad_horario_detalle chd
                    INNER JOIN horarios h ON h.id=chd.horario_id
                    WHERE capacidad_horario_id
                        IN (
                            SELECT id
                            FROM capacidad_horario
                            WHERE empresa_id IN ('.$empresa.')
                            AND zonal_id IN ('.$zonal.')
                            AND quiebre_grupo_id IN ('.$quiebre_grupo.')
                            AND estado=1
                        )
                    AND h.estado=1
                    GROUP BY horario_id
                    ORDER BY capacidad_horario_id,horario_id
                ';
        $rquery1=DB::select($query1);


        $contador=0;
        $contadorDinamico=0;
        $queryDinamico="";
        $cabeceraDinamico="";
        $leftJoinDinamico="";
        $arrayfechacabecera=array();
        $arrayhoracabecera=array();
        while($fechaIniaux<=$fechaFin){
            $arrayfechacabecera[$contador]=$fechaIniaux;
            $contador++;
            $cabeceraDinamico="";
            $leftJoinDinamico="";
            $contadorDinamico=0;
            $queryDinamico.=
                '
                LEFT JOIN
                (
                    SELECT IFNULL(gm.tecnico_id,0) t'.$contador.',IFNULL(gm.celula_id,0) c'.$contador.',q.id q'.$contador.'
                    cabeceraDinamico
                    FROM gestiones g
                    INNER JOIN ultimos_movimientos um ON um.gestion_id=g.id
                    INNER JOIN gestiones_detalles gd ON gd.gestion_id=g.id
                    INNER JOIN quiebres q ON q.id=gd.quiebre_id
                    INNER JOIN quiebre_grupos qg ON qg.id=q.quiebre_grupo_id
                    INNER JOIN gestiones_movimientos gm ON gm.gestion_id=g.id
                            AND gm.id IN (
                                SELECT max(gm2.id)
                                FROM gestiones_movimientos gm2
                                INNER JOIN estado_motivo_submotivo ems ON ems.estado_id=gm2.estado_id
                                    AND CONCAT(ems.req_tecnico,"_",ems.req_horario)!="0_0"
                                WHERE gm2.gestion_id=g.id
                            )
                    LEFT JOIN (
                        SELECT gms.gestion_id,GROUP_CONCAT(gms.id SEPARATOR ",") ids,count(gms.id) cant
                        FROM gestiones_movimientos gms
                        INNER JOIN movimientos_observaciones mos ON gms.id=mos.gestion_movimiento_id
                        WHERE gms.fecha_agenda="'.$fechaIniaux.'"
                        AND DATE(mos.created_at)="'.$fechaIniaux.'"
                        AND gms.empresa_id IN ('.$empresa.')
                        GROUP BY gms.gestion_id
                    ) obs ON obs.gestion_id=gm.gestion_id
                    INNER JOIN dias d ON d.id=gm.dia_id
                    INNER JOIN horarios h ON h.id=gm.horario_id
                    INNER JOIN estados es ON es.id=um.estado_id
                    LEFT JOIN tecnicos t ON t.id=gm.tecnico_id
                    LEFT JOIN celulas c ON c.id=gm.celula_id AND c.zonal_id IN ('.$zonal.')
                    leftJoinDinamico
                    WHERE gm.fecha_agenda="'.$fechaIniaux.'"
                    AND qg.id IN ('.$quiebre_grupo.')
                    AND gm.empresa_id IN ('.$empresa.')
                    AND gd.zonal_id IN ('.$zonal.')
                    GROUP BY q.id,gm.tecnico_id,gm.celula_id
                ) t'.$contador. ' ON t.celula_id=t'.$contador.'.c'.$contador.
                                ' AND t.tecnico_id=t'.$contador.'.t'.$contador.
                                ' AND t.quiebre_id=t'.$contador.'.q'.$contador;
            foreach ($rquery1 as $r) {
                $arrayhoracabecera[$contadorDinamico]=$r->hora;
                $contadorDinamico++;
                $cabeceraDinamico.=',IFNULL(count(h'.$contadorDinamico.'.id),0) h'.$contadorDinamico.'_'.$contador;
                $cabeceraDinamico.=',GROUP_CONCAT( IF(h'.$contadorDinamico.'.id IS NULL,NULL,gd.codactu) ) a'.$contadorDinamico.'_'.$contador;
                $cabeceraDinamico.=',GROUP_CONCAT( IF(h'.$contadorDinamico.'.id IS NULL,
                                                        NULL,
                                                        IF(date(um.f_cierre)=current_date(),um.estado_final_ot,es.nombre)
                                                      )
                                                 ) esfiot'.$contadorDinamico.'_'.$contador;
                $cabeceraDinamico.=',GROUP_CONCAT( IF(h'.$contadorDinamico.'.id IS NULL,NULL,gd.mdf) ) mdf'.$contadorDinamico.'_'.$contador;
                $cabeceraDinamico.=',GROUP_CONCAT( IF(h'.$contadorDinamico.'.id IS NULL,NULL,IFNULL( gd.estado_legado,"" )) ) legado'.$contadorDinamico.'_'.$contador;
                $cabeceraDinamico.=',GROUP_CONCAT( IF(h'.$contadorDinamico.'.id IS NULL,NULL,CONCAT(IFNULL( gd.y,"" ),",",IFNULL( gd.x,"" )) ) ) xy'.$contadorDinamico.'_'.$contador;
                $cabeceraDinamico.=',SUM( IF(h'.$contadorDinamico.'.id IS NULL,0,obs.cant) ) s'.$contadorDinamico.'_'.$contador;
                // Para la segunda tabla
                $cabeceraDinamico.=',GROUP_CONCAT( IF(h'.$contadorDinamico.'.id IS NULL,NULL,CONCAT(IFNULL( um.cnt_hd,"0" ), ",", IFNULL( um.cnt_sd,"0" ), ",", IFNULL( um.cnt_pv,"0" ), ",", IFNULL( um.cnt_pt,"0" )) ) ) decos'.$contadorDinamico.'_'.$contador;
                ////////////////////////
                $leftJoinDinamico.='
                                    LEFT JOIN horarios h'.$contadorDinamico.' ON h'.$contadorDinamico.'.id=gm.horario_id AND h'.$contadorDinamico.'.id='.$r->horario_id;
            }
            $buscar=array('cabeceraDinamico','leftJoinDinamico');
            $reemplazar=array($cabeceraDinamico,$leftJoinDinamico);
            $queryDinamico=str_replace($buscar, $reemplazar, $queryDinamico);

            $fechaIniaux=date('Y-m-d', strtotime(date($fechaIniaux)." +1 days"));
        }



        $query2=
        'SELECT *
        FROM
        (
            SELECT *
            FROM
            (
                SELECT  qg.nombre grupo_quiebre,q.id quiebre_id,q.nombre quiebre,GROUP_CONCAT(gd.codactu) ids,
                        IFNULL(gm.tecnico_id,0) tecnico_id,IFNULL(gm.celula_id,0) celula_id,count(q.id) total,
                        CONCAT_WS(" ",t.ape_paterno,t.ape_materno,t.nombres) tecnico,IFNULL(c.nombre,"") celula,e.nombre empresa,
                        uc.coord_x tecx, uc.coord_y tecy
                        ,GROUP_CONCAT( CONCAT(gd.codactu,"_",IFNULL(gd.y,""),",",IFNULL(gd.x,""),"_",IFNULL(um.estado_legado,"") )
                                     ORDER BY h.horario ASC SEPARATOR "|" ) xyfinales,
                        t.carnet_tmp, t.celular, DATE_FORMAT(uc.fecha_hora, \'%d/%m/%Y %H:%i:%s\') ultima,
                        GROUP_CONCAT( CONCAT(gd.codactu,"_",IFNULL(um.y_inicio,""),",",IFNULL(um.x_inicio,""),",",IFNULL(um.f_inicio,"") )
                                     ORDER BY h.horario ASC SEPARATOR "|" ) xyinicios
                FROM gestiones g
                INNER JOIN gestiones_detalles gd ON gd.gestion_id=g.id
                INNER JOIN quiebres q ON q.id=gd.quiebre_id
                INNER JOIN quiebre_grupos qg ON qg.id=q.quiebre_grupo_id
                INNER JOIN gestiones_movimientos gm ON gm.gestion_id=g.id
                        AND gm.id IN (
                            SELECT max(gm2.id)
                            FROM gestiones_movimientos gm2
                            INNER JOIN estado_motivo_submotivo ems ON ems.estado_id=gm2.estado_id
                                AND CONCAT(ems.req_tecnico,"_",ems.req_horario)!="0_0"
                            WHERE gm2.gestion_id=g.id
                        )
                INNER JOIN empresas e ON e.id=gm.empresa_id
                INNER JOIN dias d ON d.id=gm.dia_id
                INNER JOIN horarios h ON h.id=gm.horario_id
                INNER JOIN ultimos_movimientos um ON um.gestion_id=g.id
                LEFT JOIN tecnicos t ON t.id=gm.tecnico_id
                LEFT JOIN celulas c ON c.id=gm.celula_id AND c.zonal_id IN ('.$zonal.')
                LEFT JOIN webpsi_officetrack.ultimas_coordenadas uc 
                    ON uc.carnet=t.carnet_tmp
                WHERE gm.fecha_agenda BETWEEN "'.$fechaIni.'" AND "'.$fechaFin.'"
                AND qg.id IN ('.$quiebre_grupo.')
                AND gm.empresa_id IN ('.$empresa.')
                AND gd.zonal_id IN ('.$zonal.')
                GROUP BY q.id,gm.tecnico_id,gm.celula_id
            ) t
            '.$queryDinamico.'
        ) f
        ORDER BY f.grupo_quiebre,f.quiebre,f.total desc';
        //echo $query2;
        $rquery2= DB::select($query2);

        return Response::json(
            array(
                        'rst'=>1,
                        'datos'=>$rquery2,
                        'fechacabecera'=>$arrayfechacabecera,
                        'horacabecera'=>$arrayhoracabecera,
                        'iconos'=>  Helpers::iconArray()
                    )
        );

    }


}
