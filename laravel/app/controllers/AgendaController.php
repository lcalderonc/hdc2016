<?php

class AgendaController extends BaseController
{
    
    protected $_ffttController;
    
    public function __construct(FfttController $FfttController)
    {
        $this->_ffttController = $FfttController;
    }
    
    /**
     * Listar registro de actividades con estado 1
     * POST agenda/agendaLibre
     *
     * @return Response
     */
    public function postLibre()
    {
        /**
         * Datos base para agendamiento
         */
        $diaMes = date("d");
        $diaSemana = date("N");
        $ini = date("Y/n/" . $diaMes);
        $fechaIni = date("Y-m-d");
        
        //Aumentandole 7 dias
        $fecha = new DateTime($ini);
        $fecha->add(new DateInterval('P7D'));
        $fechaFin = $fecha->format('Y-m-d');
        
        //Cabecera
        $idMes = date("m");
        
        //Mensaje de respuesta
        $agendaMsj = "";
        
        /**
         * Restriccion:
         * -----------
         * Agendamiento para trobas digitalizadas
         * en quiebres:
         *          - DIGITALIZACION
         *          - POST DIGIT
         * Solo si se encuentra el registro en dig_trobas
         */
        $agendarOrden = true;
        if ( Input::get('quiebre') !== null ) 
        {
            $ffttData = new stdClass();
            $aquiebre = trim(Input::get('quiebre'));
            if ($aquiebre=='DIGITALIZACION' or $aquiebre=='POST DIGIT')
            {
                $ffttData->fftt     = Input::get('fftt');
                $ffttData->tipoactu = Input::get('tipoactu');
                
                $ffttel = $this->_ffttController->getExplodefftt($ffttData);
                
                if ($ffttel["tipo"]=='catv') 
                {
                    //Buscar troba digitalizada
                    $trobadig = DB::table('geo_trobapunto AS gt')
                        ->join('dig_trobas AS dt', 'gt.id', '=', 'dt.troba_id')
                        ->where('gt.zonal', '=', 'LIM')
                        ->where('dt.fecha_fin', '!=', '0000-00-00')
                        ->where('gt.nodo', '=', $ffttel["nodo"])
                        ->where('gt.troba', '=', $ffttel["troba"])
                        ->select(
                                'gt.id', 'gt.zonal', 
                                'gt.nodo', 'gt.troba', 
                                'dt.fecha_inicio', 'dt.fecha_fin'
                            )
                        ->get();
                    
                    //Indice [0] => existe respuesta
                    if (isset($trobadig[0]))
                    {
                        $hoy = new DateTime($fechaIni);
                        $fin = new DateTime($trobadig[0]->fecha_fin);
                        $interval = $fin->diff($hoy);
                        $diasDiff = (int) $interval->format('%R%a');
                        
                        //Si menor a +3 dias el día base cambia
                        if ($diasDiff < 3)
                        {
                            $diasDiff *= -3;
                            $fechaIni = date(
                                    "Y-m-d", 
                                    strtotime(
                                        $trobadig[0]->fecha_fin 
                                        . " +3 day"
                                    )
                                );
                            $ini = date("Y/n/" . substr($fechaIni, -2));
                            $diaSemana = date("N", strtotime($fechaIni));
                            $diaMes = substr($fechaIni, -2);
                            
                            $fechaFin = date(
                                    "Y-m-d", 
                                    strtotime(
                                        $fechaIni 
                                        . " +7 day"
                                    )
                                );
                            
                            $idMes = date("m", strtotime($fechaIni));
                            
                            //Mostrar agenda solo si $fechaIni = hoy
                            if ($fechaIni != date("Y-m-d"))
                            {
                                $agendarOrden = false;
                                $agendaMsj = '<div class="alert alert-warning' 
                                        . ' alert-dismissable">
                                          <button type="button" class="close"' 
                                        . ' data-dismiss="alert"' 
                                        . ' aria-hidden="true">×</button>
                                          <h4><i class="icon fa' 
                                        . ' fa-warning"></i> Agenda' 
                                        . ' disponible el ' 
                                        . date("d/m/Y", strtotime($fechaIni)) 
                                        . '</h4></div>';
                            }
                            
                        }
                        
                    } else {
                        //No se puede agendar por fecha de digitalizacion
                        $agendarOrden = false;
                        $agendaMsj = '<div class="alert alert-warning' 
                                        . ' alert-dismissable">
                                          <button type="button" class="close"' 
                                        . ' data-dismiss="alert"' 
                                        . ' aria-hidden="true">×</button>
                                          <h4><i class="icon fa' 
                                        . ' fa-warning"></i> Troba no' 
                                        . ' digitalizada.</h4>
                                      </div>';
                    }
                    
                }
                
            }
        }

        $zona=Input::get('zona');
        $empresa=Input::get('empresa');
        $tipo=Input::get('tipo');//1 o 2
        $quiebreGrupoId=Input::get('quiebre_grupo');
        $query = "SELECT 
                IF(b.fecha_agenda IS NULL, 
                    DATE_ADD(?, 
                        INTERVAL IF(a.diaId < ?, 7-?+a.diaId, a.diaId-?)
                    DAY), 
                    b.fecha_agenda
                ) fecha, 
                a.*, 
                IF(b.ocupado IS NULL, 0, b.ocupado) ocupado, 
                a.capacidad - IF(b.ocupado IS NULL, 0, b.ocupado) libre
        FROM

            (SELECT 
                dia_id as diaId, chd.horario_id as horarioId, d.nombre, h.horario
                'hora', h.hora_inicio AS horaIni, h.hora_fin as horaFin,
                chd.capacidad, ch.empresa_id as empresaId, 
                ch.zonal_id as zonalId, chd.estado as estado,
                ch.quiebre_grupo_id
            FROM 
                capacidad_horario ch 
                JOIN capacidad_horario_detalle chd 
                    ON chd.capacidad_horario_id=ch.id
                JOIN horarios h ON chd.horario_id=h.id
                JOIN dias d ON chd.dia_id=d.id
            WHERE ch.quiebre_grupo_id=? AND ch.empresa_id=? 
            AND ch.zonal_id=? AND ch.horario_tipo_id=? and h.estado=1 
            AND chd.estado=1
            ) a

        LEFT JOIN

            (SELECT 
                gd.gestion_id AS id, 
                gm.fecha_agenda,q.quiebre_grupo_id,
                gm.gestion_id, gm.empresa_id, gm.zonal_id, 
                gm.horario_id, gm.dia_id, COUNT(*) ocupado 
            FROM gestiones_movimientos gm
            INNER JOIN gestiones_detalles gd ON gd.gestion_id=gm.gestion_id
            INNER JOIN quiebres q ON q.id=gd.quiebre_id AND q.estado=1
            INNER JOIN (
                SELECT max(g2.id) id
                FROM gestiones_movimientos g2
                GROUP BY g2.gestion_id
            ) gm2 ON gm2.id=gm.id
            WHERE gm.fecha_agenda BETWEEN ? AND ?
            AND gm.empresa_id=? 
            AND gm.zonal_id=?
            AND q.quiebre_grupo_id=?
            GROUP BY
                gm.empresa_id, gm.zonal_id,
                gm.horario_id, gm.dia_id,q.quiebre_grupo_id
            ) b
        ON a.empresaId=b.empresa_id AND a.zonalId=b.zonal_id
        AND a.diaId=b.dia_id AND a.horarioId=b.horario_id
        AND a.quiebre_grupo_id=b.quiebre_grupo_id
        ORDER BY hora,fecha";

        $horarios= DB::select(
            $query,
            array(
                $fechaIni,
                $diaSemana,
                $diaSemana,
                $diaSemana,
                $quiebreGrupoId,
                $empresa,
                $zona,
                $tipo,
                $fechaIni,
                $fechaFin,
                $empresa,
                $zona,
                $quiebreGrupoId
            )
        );

        $cantidadHorarios =Horario::where('horario_tipo_id', '=', $tipo)
                            ->where('estado','=',1)
                            ->get();

        $cantidadMinutos = HorarioTipo::where('id', '=', $tipo)->first();
        $cantidadMinutos = $cantidadMinutos->minutos;

        $nombreMeses = array(
                        "Enero", "Febrero", "Marzo",
                        "Abril", "Mayo", "Junio",
                        "Julio", "Agosto", "Septiembre",
                        "Octubre", "Noviembre", "Diciembre"
                    );
        $nombreDias = array(
                        "domingo", "lunes", "martes",
                        "mi&eacute;rcoles", "jueves",
                        "viernes", "s&aacute;bado",
                        "domingo", "lunes", "martes",
                        "mi&eacute;rcoles", "jueves", "viernes", 
                        "s&aacute;bado", "domingo", "lunes", "martes",
                        "mi&eacute;rcoles", "jueves", "viernes"
                    );

        //creando la cabecera
        
        $table = '<table id="horario" class="table table-bordered"><thead style="font-size: 15px !important;">';
        $table .= '<tr><th colspan=8>'
                . date("Y")
                . ' - '
                . $nombreMeses[$idMes - 1]
                . '</th></tr>';

        $table .= '<tr><th>Hora</th>';

        for ($i = 0; $i < 7; $i++) {
            $nuevafecha = strtotime ( "+$i day" , strtotime ( $fechaIni ) ) ;
            $nombreDia = $nombreDias[$diaSemana+$i];
            $numeroDia=$diaMes+$i;
            $table .= "<th>".$nombreDia."(" . date("d", $nuevafecha) . ")</th>";
        }

        $table .= '</tr></thead><tbody>';

        $numeroCeldaHora = 0;
        $numeroCeldaValor = 0;

        foreach ($cantidadHorarios as $value) {
            $horarioId=$value->id;
            $table .= '<tr><td title="'
                    . $numeroCeldaHora
                    . '" style="background:#49afcd">HORA</td>';
            $contDias = 0;

            $numeroCeldaValor++;

            foreach ($horarios as $data) {
                if ($horarioId==$data->horarioId) { 

                    $fec = new DateTime($ini);
                    $fec->add(new DateInterval('P' . $contDias . 'D'));
                    $fechaRes = $fec->format('Y-m-d');
                    $nombreDia = $nombreDias[$diaSemana+$contDias];
                    $codigoDia = substr($fechaRes, 8, 2);

                    $codigoDia = ($codigoDia == 0) ? 7 : $codigoDia;
                    $estado = "";
                    $cantLibres = (int) $data->libre;
                    /*$libres = "Total:"
                            . $data->capacidad
                            . "<br>Libres:"
                            . $cantLibres;*/
                    $libres = $cantLibres."/".$data->capacidad;
                    $hoy = date("Y-m-d");

                    if ($data->estado==0) {
                        $estado = "background:#f0e535;color:#000";//yellow
                    } elseif($data->ocupado>=$data->capacidad) {
                        $estado = "background:#f0e535;color:#000"; 
                    } elseif ($hoy == $fechaRes) {
                        //sumar la cantidad de hora en el intervalo
                        $hora = new DateTime();
                        $hora->add(new DateInterval('PT'.$cantidadMinutos.'M'));
                        $hora = $hora->format('H:i:s');

                        if ($hora >= $data->horaFin)
                            $estado = "background:#f0e535;color:#000";//yellow
                    }

                    //Imprimiendo estado del horario
                    $hora = $data->hora;
                    //$buscar=array(" ","-");
                    //$reemplazar=array("","");
                    //$hora = str_replace($buscar,$reemplazar,$hora);
                    $table = str_replace("HORA", $hora, $table);
                    
                    $table .= '<td title="'
                            . $numeroCeldaValor
                            . '" data-fec="'
                            . $fechaRes
                            . '" data-horario="'
                            . $data->horarioId
                            . '" data-dia="'
                            //. $codigoDia
                            .$data->diaId
                            . '" data-hora="'
                            . $data->hora
                            . '" data-total="'
                            . $data->capacidad
                            . '" style="'
                            . $estado . '">' . $libres . '</td>';
                    
                    $contDias++;
                    $numeroCeldaValor++;
                }
            }
            $table .= '</tr>';
            $numeroCeldaHora+=8;
        }
        $table .= '</tbody></table>';

        if ($agendarOrden)
        {
            return Response::json(
                array(
                    'rst'=>1,
                    'html'=>$table
                )
            );
        } else {
            return Response::json(
                array(
                    'rst'=>1,
                    'html'=>$agendaMsj
                )
            );
        }

    }
}
