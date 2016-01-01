<?php
class GestionMovimiento extends Eloquent
{
    public $table = 'gestiones_movimientos';

    public function gestion()
    {
        return $this->belongsTo('Gestion');
    }

    public static function OfscUpdate($datos){
        DB::table('gestiones_movimientos')
            ->where('id', $datos['gestion_movimiento_id'])
            ->update(
                array(
                    'estado_ofsc_id'=>$datos['estado_ofsc_id'],
                    'envio_ofsc'=>$datos['envio_ofsc'],
                    'aid'=> $datos['aid']
                )
            );
            
        DB::table('ultimos_movimientos')
            ->where('gestion_id', $datos['gestion_id'])
            ->update(
                array(
                    'estado_ofsc_id'=>$datos['estado_ofsc_id'],
                    'envio_ofsc'=>$datos['envio_ofsc'],
                    'aid'=> $datos['aid']
                )
            );
    }

    public static function ActOfscAid($datos)
    {
        DB::table('gestiones_detalles')
            ->where('gestion_id', $datos['gestion_id'])
            ->update(
                array(
                    'aid' => $datos['aid']
                )
            );

        DB::table('gestiones_movimientos')
            ->where('id', $datos['gestion_movimiento_id'])
            ->update(
                array(
                    'estado_ofsc_id'=>'1',
                    'envio_ofsc'=>$datos['envio_ofsc'],
                    'aid'=> $datos['aid']
                )
            );
            
        DB::table('ultimos_movimientos')
            ->where('gestion_id', $datos['gestion_id'])
            ->update(
                array(
                    'estado_ofsc_id'=>'1',
                    'envio_ofsc'=>$datos['envio_ofsc'],
                    'aid' => $datos['aid']
                )
            );
    }

    public static function getValidaOfficetrack()
    {
        $gm = DB::table('actividad_quiebre')
                ->where('actividad_id', '=', Input::get('actividad_id'))
                ->where('quiebre_id', '=', Input::get('quiebre_id'))
                ->where('estado', '=', '1')
                ->count();

        return $gm;
    }

    public static function getGestionMovimiento()
    {
        $query=' SELECT `gm`.`id`, `gd`.`codactu`, `gm`.`created_at` as `fecha_movimiento`, aux.estado as cierre,
                `a`.`nombre` as `actividad`, `q`.`nombre` as `quiebre`, `e`.`nombre` as `empresa`, `z`.`nombre` as `zonal`, 
                `g`.`nombre_cliente_critico`, `g`.`telefono_cliente_critico`, `g`.`celular_cliente_critico`, `m`.`nombre` as `motivo`, 
                `s`.`nombre` as `submotivo`, `es`.`nombre` as `estado`, `gm`.`observacion`, 
                CONCAT(u.apellido," ",u.nombre) AS usuario,
                IFNULL(gm.fecha_agenda,"") AS fecha_agenda,
                IFNULL(h.horario,"") AS hora_agenda,
                CONCAT(
                    IF( IFNULL(gm.fecha_agenda,"")="",
                        "",CONCAT(gm.fecha_agenda," / ")
                    ),
                    IFNULL(h.horario,"")
                ) AS fh_agenda,
                IFNULL(t.nombre_tecnico,"") AS tecnico,
                IFNULL(c.nombre,"") AS celula,
                (  SELECT GROUP_CONCAT(CONCAT_WS("^^",mo.created_at,ot.nombre,mo.observacion,u.nombre,u.apellido) SEPARATOR "|" )
                            FROM movimientos_observaciones mo
                            INNER JOIN observaciones_tipos ot ON mo.observacion_tipo_id=ot.id
                            INNER JOIN usuarios u ON u.id=mo.usuario_created_at
                            WHERE mo.gestion_movimiento_id=gm.id
                            GROUP BY mo.gestion_movimiento_id
                         ) AS observaciones,
                CONCAT(
                            "tr_",IFNULL(gm.id,"0")
                        ) AS id_detalle_table
                from `gestiones_movimientos` as `gm` 
                inner join `gestiones` as `g` on `g`.`id` = `gm`.`gestion_id` 
                inner join `gestiones_detalles` as `gd` on `gd`.`gestion_id` = `gm`.`gestion_id` 
                inner join `quiebres` as `q` on `q`.`id` = `gd`.`quiebre_id` 
                inner join `actividades` as `a` on `a`.`id` = `g`.`actividad_id` 
                inner join `empresas` as `e` on `e`.`id` = `gm`.`empresa_id` 
                inner join `zonales` as `z` on `z`.`id` = `gm`.`zonal_id` 
                inner join `motivos` as `m` on `m`.`id` = `gm`.`motivo_id` 
                inner join `submotivos` as `s` on `s`.`id` = `gm`.`submotivo_id` 
                inner join `estados` as `es` on `es`.`id` = `gm`.`estado_id` 
                inner join `usuarios` as `u` on `u`.`id` = `gm`.`usuario_created_at` 
                left join `horarios` as `h` on `h`.`id` = `gm`.`horario_id` 
                left join `tecnicos` as `t` on `t`.`id` = `gm`.`tecnico_id` 
                left join `celulas` as `c` on `c`.`id` = `gm`.`celula_id` 
                left join 
                    (SELECT gm.id movimiento_id,t.id,gm.estado_id,gm.created_at,t.fecha_recepcion,pt.estado,pt.estado_codigo,
                    (HOUR(t.fecha_recepcion)*3600+MINUTE(t.fecha_recepcion)*60+SECOND(t.fecha_recepcion)) - (HOUR(gm.created_at)*3600+MINUTE(gm.created_at)*60+SECOND(gm.created_at)) f
                    FROM psi.gestiones_movimientos gm
                    INNER JOIN psi.gestiones_detalles gd ON gd.gestion_id=gm.gestion_id
                    LEFT JOIN webpsi_officetrack.tareas t ON (t.task_id=gm.gestion_id AND SUBSTR(paso,6,6)="Cierre" AND gm.estado_id=9
                    AND ((HOUR(t.fecha_recepcion)*3600+MINUTE(t.fecha_recepcion)*60+SECOND(t.fecha_recepcion)) - (HOUR(gm.created_at)*3600+MINUTE(gm.created_at)*60+SECOND(gm.created_at))) BETWEEN 0 AND 270)
                    LEFT JOIN webpsi_officetrack.paso_tres pt ON pt.task_id=t.id 
                    WHERE gd.codactu="'.Input::get('codactu').'"
                    AND gm.estado_id=9
                    GROUP BY gm.id) AS aux on `aux`.`movimiento_id` = `gm`.`id` 
                WHERE `gd`.`codactu` = "'.Input::get('codactu').'"';

            $r = array();
            if (Input::get('first')) {
                $query.=' ORDER BY gm.created_at desc';
                $rquery = DB::select($query);
                $r= $rquery[0];
            } else {
                $r = DB::select($query);
            }

        return $r;
    }

    public static function getGestionMovimiento_ult(
                    $checkFecha=null,
                    $checkAveria=null,
                    $checkAveriaU=null,
                    $reporte=null,
                    $fechaIni=null,
                    $fechaFin=null,
                    $averia=null,
                    $averiau=null,
                    $pendiente=null
                    ){

        $filtro = "WHERE q.quiebre_grupo_id IN (  SELECT quiebre_grupo_id
                                                FROM quiebre_grupo_usuario
                                                WHERE estado=1
                                                AND usuario_id=".Auth::user()->id."
                                                ) 
                    AND q.id NOT IN (
                                    SELECT quiebre_id
                                    FROM quiebre_usuario_restringido
                                    WHERE usuario_id='".Auth::user()->id."'
                                    AND estado=1
                                )
                    AND z.id IN (
                                SELECT zonal_id
                                FROM usuario_zonal
                                WHERE usuario_id='".Auth::user()->id."'
                                AND estado=1
                        )
                    ";
        if ($checkFecha != null) {
            if ($checkFecha == 'on') {
                if ($fechaIni != "" && $fechaFin != "") {
                    if ($reporte == "act") {
                        $filtro .= " AND DATE(um.fecha_registro) BETWEEN '$fechaIni' AND '$fechaFin'";
                    } elseif ($reporte == "mov") {
                        $filtro .= " AND DATE(um.updated_at) BETWEEN '$fechaIni' AND '$fechaFin'";
                    } elseif ($reporte == "age") {
                        $filtro .= " AND DATE(um.fecha_agenda) BETWEEN '$fechaIni' AND '$fechaFin'";
                    } else {
                        $filtro .= " AND DATE(um.created_at) BETWEEN '$fechaIni' AND '$fechaFin'";
                    }
                }
            }
        }

        if ($checkAveria != null) {
            if ($checkAveria == 'on') {
                $averia = str_replace(",", "','", $averia);
                $filtro .= " AND um.codactu IN ('".$averia."') ";
            }
        }

        if ($checkAveriaU != null) {
            if ($checkAveriaU == 'on') {
                $filtro .= " AND um.codactu = '".$averiau."' ";
            }
        }
        $query2="";
        $agrupar=" GROUP BY um.gestion_id ";
        if($pendiente!=null and $pendiente==1){
            $filtro="";
            $agrupar="";
            $query2="   INNER JOIN (
                            SELECT acf.averia idunico
                            FROM webpsi_coc.averias_criticos_final acf
                            UNION 
                            SELECT p.codigo_req idunico
                            FROM webpsi_coc.tmp_provision p
                        ) red ON red.idunico=um.codactu
                        WHERE q.quiebre_grupo_id IN (  SELECT quiebre_grupo_id
                                                FROM quiebre_grupo_usuario
                                                WHERE estado=1
                                                AND usuario_id=".Auth::user()->id."
                                                ) 
                        AND q.id NOT IN (
                                    SELECT quiebre_id
                                    FROM quiebre_usuario_restringido
                                    WHERE usuario_id='".Auth::user()->id."'
                                    AND estado=1
                                )
                        AND z.id IN (
                                SELECT zonal_id
                                FROM usuario_zonal
                                WHERE usuario_id='".Auth::user()->id."'
                                AND estado=1
                        )
                        GROUP BY um.gestion_id
                        UNION
                        SELECT 
                            '' gestion_id,'' id_atc, a.quiebre,a.eecc_final empresa,a.tipo_averia,a.horas_averia,a.fecha_registro,a.ciudad,
                          a.averia codactu,a.inscripcion,a.fono1,a.telefono,a.mdf,'' obs_gestion,a.segmento,a.area_ area,a.direccion_instalacion,a.codigo_distrito,
                          a.nombre_cliente,a.orden_trabajo,a.veloc_adsl,a.clase_servicio_catv,a.codmotivo_req_catv,a.total_averias_cable,a.total_averias_cobre,
                          a.total_averias,a.fftt,gt.nodo,gt.troba,if(dt.fecha_fin='0000-00-00','',dt.fecha_fin) f_apagon,
                          if(dt.fecha_fin='0000-00-00','',DATEDIFF(date(now()),dt.fecha_fin)) dias_transcurrido,IF(dt.est_seguim='A','Activo','Inactivo') estadof,
                            a.llave,a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal,a.wu_nagendas,a.wu_nmovimientos,a.wu_fecha_ult_agenda,
                          a.total_llamadas_tecnicas,a.total_llamadas_seguimiento,a.llamadastec15dias,a.llamadastec30dias,a.lejano,a.distrito,a.eecc_zona,
                          a.zona_movistar_uno,a.paquete,a.data_multiproducto,a.averia_m1,a.fecha_data_fuente,a.telefono_codclientecms,a.rango_dias,a.sms1,a.sms2,
                          a.area2,a.microzona,a.tipo_actuacion,'' x, '' y,'Averia' actividad,a.nombre_cliente nombre_cliente_critico,a.telefono telefono_cliente_critico,
                          '' celular_cliente_critico, 0 n_evento,'' empresa_movimiento,'' estado,'' motivo, '' submotivo, '' celula, '' tecnico, '' dia, '' fecha_agenda,
                          '' horario,'' fecha_agenda_inicial, '' fecha_agenda_final, '' cant_agendas, '' fecha_tecnico_en_sitio,'' hora_tecnico_en_sitio, '' obs_movimiento, '' fecha_consolidacion, '' flag_tecnico, '' coordinado,
                          '' fecha_movimiento, '' hora_movimiento, '' usuario_movimiento, '' fecha_gestion, '' hora_gestion, '' usuario_gestion,'' estado_legado,'' fec_liq_legado,'' contrata_legado,
                          '' estado_officetrack,'' estado_final_ot
                        FROM
                            webpsi_coc.averias_criticos_final a
                            INNER JOIN quiebres q ON q.apocope=a.quiebre
                            LEFT JOIN geo_trobapunto gt ON concat(gt.nodo,'|',gt.troba)=substr(a.fftt,1,7)
                            LEFT JOIN dig_trobas dt ON dt.troba_id=gt.id
                            LEFT JOIN zonales z ON z.abreviatura=a.zonal
                         WHERE q.quiebre_grupo_id IN (  SELECT quiebre_grupo_id
                                                        FROM quiebre_grupo_usuario
                                                        WHERE estado=1
                                                        AND usuario_id=".Auth::user()->id."
                                                        ) 
                        AND q.id NOT IN (
                                    SELECT quiebre_id
                                    FROM quiebre_usuario_restringido
                                    WHERE usuario_id='".Auth::user()->id."'
                                    AND estado=1
                                )
                        AND z.id IN (
                                SELECT zonal_id
                                FROM usuario_zonal
                                WHERE usuario_id='".Auth::user()->id."'
                                AND estado=1
                        )
                        WHERE a.averia NOT IN (SELECT codactu FROM ultimos_movimientos)
                        UNION
                        SELECT 
                            '' gestion_id,'' id_atc, a.quiebre,a.eecc_final empresa,a.origen tipo_averia,a.horas_pedido horas_averia,a.fecha_Reg fecha_registro,a.ciudad,
                          a.codigo_req codactu,a.codigo_del_cliente inscripcion,a.fono1,a.telefono,a.mdf,'' obs_gestion,a.codigosegmento segmento,a.estacion area,a.direccion direccion_instalacion,a.distrito codigo_distrito,
                          a.nomcliente nombre_cliente,a.orden orden_trabajo,a.veloc_adsl,a.servicio clase_servicio_catv,a.tipo_motivo codmotivo_req_catv,a.tot_aver_cab total_averias_cable,a.tot_aver_cob total_averias_cobre,
                          a.tot_averias total_averias,a.fftt,gt.nodo,gt.troba,if(dt.fecha_fin='0000-00-00','',dt.fecha_fin) f_apagon,
                          if(dt.fecha_fin='0000-00-00','',DATEDIFF(date(now()),dt.fecha_fin)) dias_transcurrido,IF(dt.est_seguim='A','Activo','Inactivo') estadof,
                            a.llave,a.dir_terminal,a.fonos_contacto,a.contrata,a.zonal,a.wu_nagendas,a.wu_nmovimient wu_nmovimientos,a.wu_fecha_ult_age wu_fecha_ult_agenda,
                          a.tot_llam_tec total_llamadas_tecnicas,a.tot_llam_seg total_llamadas_seguimiento,a.llamadastec15d llamadastec15dias,a.llamadastec30d llamadastec30dias,a.lejano,a.des_distrito distrito,a.eecc_zon eecc_zona,
                          a.zona_movuno zona_movistar_uno,a.paquete,a.data_multip data_multiproducto,a.aver_m1 averia_m1,a.fecha_data_fuente,a.telefono_codclientecms,a.rango_dias,a.sms1,a.sms2,
                          a.area2,a.microzona,a.tipo_actuacion,'' x, '' y,'Provision' actividad,a.nomcliente nombre_cliente_critico,a.telefono_codclientecms telefono_cliente_critico,
                          a.fono1 celular_cliente_critico, 0 n_evento,'' empresa_movimiento,'' estado,'' motivo, '' submotivo, '' celula, '' tecnico, '' dia, '' fecha_agenda,
                          '' horario,'' fecha_agenda_inicial, '' fecha_agenda_final, '' cant_agendas, '' fecha_tecnico_en_sitio,'' hora_tecnico_en_sitio, '' obs_movimiento, '' fecha_consolidacion, '' flag_tecnico, '' coordinado,
                          '' fecha_movimiento, '' hora_movimiento, '' usuario_movimiento, '' fecha_gestion, '' hora_gestion, '' usuario_gestion,'' estado_legado,'' fec_liq_legado,'' contrata_legado,
                          '' estado_officetrack, '' estado_final_ot
                        FROM
                            webpsi_coc.tmp_provision a
                            INNER JOIN quiebres q ON q.apocope=a.quiebre
                            LEFT JOIN geo_trobapunto gt ON concat(gt.nodo,'|',gt.troba)=substr(a.fftt,1,7)
                            LEFT JOIN dig_trobas dt ON dt.troba_id=gt.id
                            LEFT JOIN zonales z ON z.abreviatura=a.zonal
                         WHERE q.quiebre_grupo_id IN (  SELECT quiebre_grupo_id
                                                        FROM quiebre_grupo_usuario
                                                        WHERE estado=1
                                                        AND usuario_id=".Auth::user()->id."
                                                        ) 
                        AND q.id NOT IN (
                                    SELECT quiebre_id
                                    FROM quiebre_usuario_restringido
                                    WHERE usuario_id='".Auth::user()->id."'
                                    AND estado=1
                                )
                        AND z.id IN (
                                SELECT zonal_id
                                FROM usuario_zonal
                                WHERE usuario_id='".Auth::user()->id."'
                                AND estado=1
                        )
                        WHERE a.codigo_req NOT IN (SELECT codactu FROM ultimos_movimientos)";
        }

        $sql="  SELECT 
                um.gestion_id,um.id_atc,q.nombre quiebre,e.nombre empresa,um.tipo_averia,um.horas_averia,um.fecha_registro,um.ciudad,um.codactu,
                um.inscripcion,um.fono1,um.telefono,um.mdf,um.observacion obs_gestion,um.segmento,um.area,um.direccion_instalacion,um.codigo_distrito,um.nombre_cliente,
                um.orden_trabajo,um.veloc_adsl,um.clase_servicio_catv,um.codmotivo_req_catv,um.total_averias_cable,um.total_averias_cobre,um.total_averias,
                um.fftt,gt.nodo,gt.troba,if(dt.fecha_fin='0000-00-00','',dt.fecha_fin) f_apagon,
                if(dt.fecha_fin='0000-00-00','',DATEDIFF(date(now()),dt.fecha_fin)) dias_transcurrido,IF(dt.est_seguim='A','Activo','Inactivo') estadof,
                um.llave,um.dir_terminal,um.fonos_contacto,um.contrata,um.zonal,um.wu_nagendas,um.wu_nmovimientos,um.wu_fecha_ult_agenda,
                um.total_llamadas_tecnicas,um.total_llamadas_seguimiento,um.llamadastec15dias,um.llamadastec30dias,um.lejano,um.distrito,um.eecc_zona,
                um.zona_movistar_uno,um.paquete,um.data_multiproducto,um.averia_m1,um.fecha_data_fuente,um.telefono_codclientecms,um.rango_dias,um.sms1,um.sms2,um.area2,
                um.microzona,um.tipo_actuacion,ifnull(um.x,'') x,ifnull(um.y,'') y,a.nombre actividad,um.nombre_cliente_critico,um.telefono_cliente_critico,um.celular_cliente_critico,um.n_evento,
                e2.nombre empresa_movimiento,es.nombre estado,m.nombre motivo,sm.nombre submotivo,
                c.nombre celula,CONCAT(ape_paterno,' ',ape_materno,' ',nombres) tecnico,d.nombre dia,um.fecha_agenda,CONCAT(h.hora_inicio,'-',h.hora_fin) horario,
                substr( min(concat(gm.id,',',gm.fecha_agenda) ), locate(',',min( concat(gm.id,',',gm.fecha_agenda) )) +1 ) fecha_agenda_inicial, um.fecha_agenda fecha_agenda_final, count(distinct(gm.fecha_agenda)) cant_agendas,
                date(um.tecnico_en_sitio) fecha_tecnico_en_sitio,time(um.tecnico_en_sitio) hora_tecnico_en_sitio,um.observacion_m obs_movimiento,
                um.fecha_consolidacion,um.flag_tecnico,IF(um.coordinado=1,'SI','NO') coordinado,date(um.updated_at) fecha_movimiento,time(um.updated_at) hora_movimiento ,
                CONCAT(u2.nombre,' ',u2.apellido) usuario_movimiento,date(um.created_at) fecha_gestion,time(um.created_at) hora_gestion,CONCAT(u.nombre,' ',u.apellido) usuario_gestion,
                um.estado_legado,um.fec_liq_legado,um.contrata_legado,um.estado_officetrack,um.estado_final_ot
                FROM ultimos_movimientos um 
                INNER JOIN gestiones_movimientos gm ON um.gestion_id=gm.gestion_id
                INNER JOIN quiebres q ON q.id=um.quiebre_id
                INNER JOIN empresas e ON e.id=um.empresa_id
                INNER JOIN empresas e2 ON e2.id=um.empresa_m_id
                INNER JOIN zonales z ON z.id=um.zonal_id
                INNER JOIN actividades a ON a.id=um.actividad_id
                INNER JOIN estados es ON es.id=um.estado_id
                INNER JOIN motivos m ON m.id=um.motivo_id
                INNER JOIN submotivos sm ON sm.id=um.submotivo_id
                INNER JOIN usuarios u ON u.id=um.usuario_created_at
                INNER JOIN usuarios u2 ON u2.id=um.usuario_updated_at
                LEFT JOIN horarios h ON h.id=um.horario_id
                LEFT JOIN dias d ON d.id=um.dia_id
                LEFT JOIN tecnicos t ON t.id=um.tecnico_id
                LEFT JOIN celulas c ON c.id=um.celula_id
                LEFT JOIN geo_trobapunto gt ON concat(gt.nodo,'|',gt.troba)=substr(um.fftt,1,7)
                LEFT JOIN dig_trobas dt ON dt.troba_id=gt.id
                 ".$filtro.$agrupar.$query2;
                 //echo $sql;
        $reporte = DB::select($sql);

        return $reporte;
    }

    public static function getGestionMovimiento_coc(
                    $checkFecha = null,
                    $checkAveria = null,
                    $reporte = null,
                    $fechaIni = null,
                    $fechaFin = null,
                    $averia = null,
                    $detalle = null
                    ) {
        $filtro = ' WHERE q.quiebre_grupo_id IN (  SELECT quiebre_grupo_id
                                                    FROM quiebre_grupo_usuario
                                                    WHERE estado=1
                                                    AND usuario_id='.Auth::user()->id.'
                                                ) 
                    AND q.id NOT IN (
                                    SELECT quiebre_id
                                    FROM quiebre_usuario_restringido
                                    WHERE usuario_id="'.Auth::user()->id.'"
                                    AND estado=1
                                )
                    AND gd.zonal_id IN (
                                SELECT zonal_id
                                FROM usuario_zonal
                                WHERE usuario_id="'.Auth::user()->id.'"
                                AND estado=1
                        )';
        if ($checkFecha != null) {
            if ($checkFecha == 'on') {
                if ($fechaIni != "" && $fechaFin != "") {
                    if ($reporte == "act") {
                        $filtro .= " AND DATE(gd.fecha_registro) BETWEEN '$fechaIni' AND '$fechaFin'";
                    } elseif ($reporte == "mov") {
                        $filtro .= " AND DATE(gm.created_at) BETWEEN '$fechaIni' AND '$fechaFin'";
                    } elseif ($reporte == "age") {
                        $filtro .= " AND DATE(gm.fecha_agenda) BETWEEN '$fechaIni' AND '$fechaFin'";
                    } else {
                        $filtro .= " AND DATE(g.created_at) BETWEEN '$fechaIni' AND '$fechaFin'";
                    }
                }
            }
        }

        if ($checkAveria != null) {
            if ($checkAveria == 'on') {
                $averia = str_replace(",", "','", $averia);
                $filtro .= " AND gd.codactu IN ('$averia')";
            }
        }

        $campoAdic = '';
        $leftAdic = '';
        $groupAdic = '';

        if ($detalle != null and $detalle != "0") {
            $campoAdic = ' ,IFNULL( GROUP_CONCAT( CONCAT(ot.nombre," => ",mo.observacion) SEPARATOR "|"),"" ) AS detalle';
            $leftAdic = ' LEFT JOIN movimientos_observaciones mo ON gm.id=mo.gestion_movimiento_id
                        LEFT JOIN observaciones_tipos ot ON ot.id=mo.observacion_tipo_id ';
            $groupAdic = ' GROUP BY gm.id ';
        }

        $query = '  SELECT  g.id,g.id_atc,e.nombre AS eecc_final,
                    (CASE gm.id WHEN (  SELECT MAX(gm2.id)
                                        FROM gestiones_movimientos gm2
                                        WHERE gm2.gestion_id= g.id
                                    )
                    THEN "X" ELSE "" END) AS ultimo_movimiento,
                    a.nombre AS tipo_actividad,
                    gd.codactu AS averia,gd.codmotivo_req_catv,
                    q.apocope AS quiebre,g.nombre_cliente_critico,
                    g.telefono_cliente_critico,g.celular_cliente_critico,
                    gm.observacion,IFNULL(h.horario,"") horario,
                    IFNULL(d.nombre,"") AS dias,
                    IFNULL(gm.fecha_agenda,"") AS fecha_agenda,
                    e.nombre,gd.zonal,m.nombre AS motivo,
                    s.nombre AS submotivo,es.nombre AS estado,
                    IFNULL(t.nombre_tecnico,"") AS tecnico,
                    gd.fecha_registro,g.created_at AS fecha_creacion,
                    gm.created_at AS fecha_movimiento,
                    gm.coordinado,CONCAT(u.apellido," ",u.nombre) AS usuario,
                    es.id AS e_id,
                    IFNULL(gm.fecha_consolidacion,"") AS fecha_consolidacion,
                    gd.tipo_averia,gd.averia_m1,
                    IF( IFNULL(l.penalizable,"*")="*","",
                        IF( IFNULL(l.penalizable,"*")="","No","Si" )
                    ) AS penalizable, IFNULL(l.penalizable,"") AS penalizabledes,
                    gd.mdf,
                    IFNULL(
                            IF(a.id IN (1,3),
                                    (   SELECT fecha_cambio
                                        FROM webpsi_coc.`averias_criticos_final_historico` ach
                                        WHERE ach.averia=gd.codactu
                                        LIMIT 1),
                                    (   SELECT fecha_cambio
                                        FROM webpsi_coc.`tmp_provision_historico` pch
                                        WHERE pch.codigo_req=gd.codactu
                                        LIMIT 1)
                            ),
                            ""
                    ) AS fecha_cambio '.$campoAdic.', gd.fftt
                    , gd.estado_legado, gd.fec_liq_legado 
                    FROM gestiones g
                    INNER JOIN gestiones_detalles gd ON g.id=gd.gestion_id
                    INNER JOIN gestiones_movimientos gm ON g.id=gm.gestion_id
                    INNER JOIN actividades a ON a.id=g.actividad_id
                    INNER JOIN quiebres q ON q.id=gd.quiebre_id
                    INNER JOIN empresas e ON e.id=gm.empresa_id
                    INNER JOIN estados es ON es.id=gm.estado_id
                    INNER JOIN motivos m ON m.id=gm.motivo_id
                    INNER JOIN submotivos s ON s.id=gm.submotivo_id
                    INNER JOIN usuarios u ON u.id= gm.usuario_created_at
                    LEFT JOIN tecnicos t ON t.id=gm.tecnico_id
                    LEFT JOIN horarios h ON h.id=gm.horario_id
                    LEFT JOIN dias d ON d.id=gm.dia_id
                    LEFT JOIN liquidados l ON l.gestion_movimiento_id=gm.id
                    '.$leftAdic.'
                    '.$filtro.'
                    '.$groupAdic.'
                    ORDER BY g.id DESC,gm.id DESC';
//echo $query;
        $reporte = DB::select($query);

        return $reporte;
    }

    public static function getControlarCupos($data)
    {
        $sql_capacidad = "SELECT
                                ch.horario_tipo_id, cd.capacidad, cd.horario_id
                        FROM
                                capacidad_horario ch,
                                capacidad_horario_detalle cd,
                                quiebre_grupos qg,
                                quiebres q
                        WHERE
                                ch.id=cd.capacidad_horario_id
                                AND ch.zonal_id={$data->zonal_id}
                                AND ch.empresa_id={$data->empresa_id}
                                AND cd.horario_id={$data->horario_id}
                                AND cd.dia_id={$data->dia_id}
                                AND ch.estado=1
                                AND cd.estado=1
                                AND qg.id=q.quiebre_grupo_id
                                AND qg.id=ch.quiebre_grupo_id
                                AND q.id={$data->quiebre_id}";
        $data["cupos"] = DB::select($sql_capacidad);

        $sql_ocupado = "SELECT
                                gm.id
                        FROM
                                gestiones g
                                INNER JOIN gestiones_movimientos gm
                                    ON (g.id=gm.gestion_id AND
                                        gm.id IN
                                            (  SELECT MAX(gm2.id)
                                                FROM gestiones_movimientos gm2
                                                WHERE gm2.gestion_id=g.id
                                             )
                                       )
                        WHERE
                                gm.empresa_id={$data->empresa_id}
                                AND gm.zonal_id={$data->zonal_id}
                                AND gm.horario_id={$data->horario_id}
                                AND gm.dia_id={$data->dia_id}
                                AND gm.estado=1
                                AND gm.fecha_agenda = '{$data->fecha_agenda}'
                                AND (gm.estado_id = 2 OR gm.estado = 3)
                                ORDER BY gm.id";
        $data["ocupado"] = DB::select($sql_ocupado);

        return $data;
    }
    
    /**
     * Retorna datos de una orden agendada con tecnico asignado
     * 
     * @param array $data
     *      ->tecnico_id
     *      ->horario_id
     *      ->dia_id
     *      ->fecha_agenda
     */
    public static function getTecnicoHorario($data){
        $sql_asignado = "SELECT 
                            g.id, g.id_atc, gd.codactu, 
                            ac.nombre, um.estado_id
                        FROM 
                            gestiones g
                            INNER JOIN gestiones_detalles gd 
                                ON g.id=gd.gestion_id
                            INNER JOIN actividades ac 
                                ON g.actividad_id=ac.id
                            INNER JOIN ultimos_movimientos um 
                                ON g.id=um.gestion_id 
                            INNER JOIN horarios h 
                                ON (h.id=um.horario_id AND h.multiple=0)
                        WHERE tecnico_id = {$data->tecnico_id}
                            AND horario_id = {$data->horario_id}
                            AND dia_id = {$data->dia_id}
                            AND fecha_agenda = '{$data->fecha_agenda}'
                            AND (
                                um.estado_id <> 6 
                                AND um.estado_id <> 14 
                                AND um.estado_id <> 15
                            )";
        
        $datos["asignado"] = DB::select($sql_asignado);
        return $datos;
    }
}
