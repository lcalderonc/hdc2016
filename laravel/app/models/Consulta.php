<?php

class Consulta extends \Eloquent
{

    public static function getUltimoMovimiento()
    {
        $filtro=array('','','','');
            if ( Input::get('usuario') ) {
                $filtro[0]='AND qg.id IN (  SELECT quiebre_grupo_id
                                        FROM quiebre_grupo_usuario
                                        WHERE estado=1
                                        AND usuario_id='.Auth::user()->id.'
                                    )';
                $filtro[1]='AND qg.id IN (  SELECT quiebre_grupo_id
                                        FROM quiebre_grupo_usuario
                                        WHERE estado=1
                                        AND usuario_id='.Auth::user()->id.'
                                    )';
                $filtro[2]='AND qg.id IN (  SELECT quiebre_grupo_id
                                        FROM quiebre_grupo_usuario
                                        WHERE estado=1
                                        AND usuario_id='.Auth::user()->id.'
                                    )';
            }

        if ( Input::get('cliente') ) {
            $cliente=Input::get('cliente');
            $filtro[0].=" AND gd.inscripcion ='".$cliente."' ";
            $filtro[1].=" AND gd.inscripcion ='".$cliente."' ";
            $filtro[2].=" AND gd.codigo_del_cliente ='".$cliente."' ";
        }

        if ( Input::get('codactu') ) {
            $codactu=Input::get('codactu');
            $filtro[0].=" AND gd.codactu ='".$codactu."' ";
            $filtro[1].=" AND gd.averia ='".$codactu."' ";
            $filtro[2].=" AND gd.codigo_req ='".$codactu."' ";
        }

        if ( Input::get('actividad') ) {
            $actividad=implode(",", Input::get('actividad'));
            $filtro[0].=" AND a.id IN (".$actividad.")";
            $filtro[1].=" AND 1 IN (".$actividad.")";
            $filtro[2].=" AND 2 IN (".$actividad.")";
        }

        if ( Input::get('estado') ) {
            $estado=implode(",", Input::get('estado'));
            $posestado= strpos($estado, "-1");
            if($posestado===false){
               $queryconestado=false;
            }
            $filtro[0].=" AND es.id IN (".$estado.")";
            $filtro[1].=" AND -1 IN (".$estado.")";
            $filtro[2].=" AND -1 IN (".$estado.")";
        }

        if ( Input::get('quiebre') ) {
            $quiebre=implode(",", Input::get('quiebre'));
            $filtro[0].=" AND q.id IN (".$quiebre.")";
            $filtro[1].=" AND q.id IN (".$quiebre.")";
            $filtro[2].=" AND q.id IN (".$quiebre.")";
        }

        if ( Input::get('empresa')!='' ) {
            $empresa=implode(",", Input::get('empresa'));
            $filtro[0].=" AND e.id IN (".$empresa.")";
            $filtro[1].=" AND e.id IN (".$empresa.")";
            $filtro[2].=" AND e.id IN (".$empresa.")";
        }

        if ( Input::get('celula') ) {
            $celula=implode(",", Input::get('celula'));
            $filtro[0].=" AND t.id IN ( 
                                    SELECT DISTINCT(ct.tecnico_id) 
                                    FROM celula_tecnico ct 
                                    WHERE ct.celula_id IN (".$celula.")
                              )";
            $queryconestado=false;
            $filtro[1].=" AND 1=0";
            $filtro[2].=" AND 1=0";
        }

        if ( Input::get('tecnico') ) {
            $tecnico=implode(",", Input::get('tecnico'));
            $filtro[0].=" AND t.id IN (".$tecnico.")";
            $queryconestado=false;
            $filtro[1].=" AND 1=0";
            $filtro[2].=" AND 1=0";
        }

        if ( Input::get('legado') ) { 
            $legado=Input::get('legado');
            $filtro[1].=" AND 1=".$legado;
            $filtro[2].=" AND 1=".$legado;
            $filtro[3].=" AND existe=".$legado;
        }

        if ( Input::get('transmision') ) { 
            $transmision=implode('","', Input::get('transmision'));
            $queryconestado=false;
            $filtro[1].=" AND 1=0";
            $filtro[2].=" AND 1=0";
            $filtro[3].=' AND transmision IN ("'.$transmision.'")';
        }

        if ( Input::get('cierre_estado') ) { 
            $cierreEstado=implode('","', Input::get('cierre_estado'));
            $queryconestado=false;
            $filtro[1].=" AND 1=0";
            $filtro[2].=" AND 1=0";
            $filtro[3].=' AND cierre_estado IN ("'.$cierreEstado.'")';
        }

        if ( Input::get('coordinado') ) {
            $coordinado=Input::get('coordinado');
            $filtro[0].=" AND um.coordinado=".$coordinado;
            $queryconestado=false;
            $filtro[1].=" AND 1=0";
            $filtro[2].=" AND 1=0";
        }

        if ( Input::get('fecha_agenda') ) {
            $fechaAgenda=explode(" - ", Input::get('fecha_agenda'));
            $filtro[0].='   AND um.fecha_agenda 
                            BETWEEN "'.$fechaAgenda[0].'" 
                            AND "'.$fechaAgenda[1].'" ';
            $queryconestado=false;
            $filtro[1].=" AND 1=0";
            $filtro[2].=" AND 1=0";
        }

            $querydetallegestion[0]='
                ,IF(um.coordinado=0,"No","Si") AS coordinado,
                IFNULL(c.nombre,"") AS celula,
                g.nombre_cliente_critico,g.celular_cliente_critico,
                g.telefono_cliente_critico, gd.zonal_id,
                gd.tipo_averia,
                gd.horas_averia,
                gd.ciudad,
                gd.inscripcion,gd.mdf,
                gd.observacion,
                gd.segmento,gd.area,
                gd.direccion_instalacion,
                gd.codigo_distrito,gd.nombre_cliente,
                gd.orden_trabajo,gd.veloc_adsl,
                gd.clase_servicio_catv,
                gd.codmotivo_req_catv,
                gd.total_averias_cable,
                gd.total_averias_cobre,
                gd.total_averias,gd.fftt,gd.llave,
                gd.dir_terminal,gd.fonos_contacto,
                gd.contrata,gd.zonal,
                IFNULL(gd.wu_nagendas,"0") wu_nagendas,
                IFNULL(gd.wu_nmovimientos,"0") wu_nmovimientos,
                gd.wu_fecha_ult_agenda,
                gd.total_llamadas_tecnicas,
                gd.total_llamadas_seguimiento,
                gd.llamadastec15dias,gd.llamadastec30dias,
                gd.lejano,gd.distrito,gd.eecc_zona,
                gd.zona_movistar_uno,
                IFNULL(gd.paquete,"") AS paquete,
                gd.data_multiproducto,gd.averia_m1,
                gd.fecha_data_fuente,gd.telefono_codclientecms,
                gd.rango_dias,gd.sms1,gd.sms2,gd.area2,gd.microzona,
                gd.tipo_actuacion';
            $querydetallegestion[1]='';
            $querydetalleaveria[0]='
                ,"No" AS coordinado,
                "" AS celula,
                gd.nombre_cliente AS nombre_cliente_critico,
                gd.fono1 AS celular_cliente_critico,
                gd.telefono AS telefono_cliente_critico, 
                IFNULL(z.id,"") AS zonal_id,
                gd.tipo_averia,
                gd.horas_averia,
                gd.ciudad,
                gd.inscripcion,gd.mdf,
                gd.observacion_102 AS observacion,
                gd.segmento,gd.area_ AS area,
                gd.direccion_instalacion,
                gd.codigo_distrito,gd.nombre_cliente,
                gd.orden_trabajo,gd.veloc_adsl,
                gd.clase_servicio_catv,
                gd.codmotivo_req_catv,
                gd.total_averias_cable,
                gd.total_averias_cobre,
                gd.total_averias,gd.fftt,gd.llave,
                gd.dir_terminal,gd.fonos_contacto,
                gd.contrata,gd.zonal,
                IFNULL(gd.wu_nagendas,"0") wu_nagendas,
                IFNULL(gd.wu_nmovimientos,"0") wu_nmovimientos,
                gd.wu_fecha_ult_agenda,
                gd.total_llamadas_tecnicas,
                gd.total_llamadas_seguimiento,
                gd.llamadastec15dias,gd.llamadastec30dias,
                gd.lejano,gd.distrito,gd.eecc_zona,
                gd.zona_movistar_uno,
                IFNULL(gd.paquete,"") AS paquete,
                gd.data_multiproducto,gd.averia_m1,
                gd.fecha_data_fuente,gd.telefono_codclientecms,
                gd.rango_dias,gd.sms1,gd.sms2,gd.area2,gd.microzona,
                gd.tipo_actuacion';
            $querydetalleaveria[1]=' LEFT JOIN zonales z ON z.abreviatura=gd.zonal ';
            $querydetalleprovision[0]='
                ,"No" AS coordinado,
                "" AS celula,
                gd.nomcliente AS nombre_cliente_critico,
                gd.fono1 AS celular_cliente_critico,
                gd.telefono_codclientecms AS telefono_cliente_critico,
                IFNULL(z.id,"") AS zonal_id,
                gd.origen AS tipo_averia,
                gd.horas_pedido AS horas_averia,
                gd.ciudad,
                gd.codigo_del_cliente AS inscripcion,gd.mdf,
                gd.obs_dev AS observacion,
                gd.codigosegmento AS segmento,
                gd.estacion AS area,
                gd.direccion AS direccion_instalacion,
                gd.distrito AS codigo_distritoa,
                gd.nomcliente AS nombre_cliente,
                gd.orden AS orden_trabajo,gd.veloc_adsl,
                gd.servicio AS clase_servicio_catv,
                gd.tipo_motivo AS codmotivo_req_catv,
                gd.tot_aver_cab AS total_averias_cable,
                gd.tot_aver_cob AS total_averias_cobre,
                gd.tot_averias AS total_averias,gd.fftt,
                gd.llave,gd.dir_terminal,gd.fonos_contacto,
                gd.contrata,gd.zonal,IFNULL(gd.wu_nagendas,"0") wu_nagendas,
                IFNULL(gd.wu_nmovimient,"0") AS wu_nmovimientos,
                gd.wu_fecha_ult_age AS wu_fecha_ult_agenda,
                gd.tot_llam_tec AS total_llamadas_tecnicas,
                gd.tot_llam_seg AS total_llamadas_seguimiento,
                gd.llamadastec15d AS llamadastec15dias,
                gd.llamadastec30d AS llamadastec30dias,
                gd.lejano,gd.des_distrito AS distrito,gd.eecc_zon AS eecc_zona,
                gd.zona_movuno AS zona_movistar_uno,
                IFNULL(gd.paquete,"") AS paquete,
                gd.data_multip AS data_multiproducto,
                gd.aver_m1 AS averia_m1,gd.fecha_data_fuente,
                gd.telefono_codclientecms,gd.rango_dias,gd.sms1,
                gd.sms2,gd.area2,gd.microzona,
                gd.tipo_actuacion';
            $querydetalleprovision[1]=' LEFT JOIN zonales z ON z.abreviatura=gd.zonal ';

            $dqueryconestado=
                ' UNION '.
                'SELECT "" AS id,gd.averia AS codactu,gd.quiebre,
                gd.eecc_final AS empresa,"" AS fecha_agenda,
                "" AS tecnico,"Temporal" AS estado,
                "Averia" AS actividad,gd.fecha_registro,
                "1" AS existe,
                "0" AS transmision,
                "" AS cierre_estado,
                "" AS fh_agenda,
                "" AS f_agenda,
                "" AS h_agenda,
                "" AS f_consolidacion,
                "" AS f_ingreso,
                "" AS h_ingreso,
                "" AS f_movimiento,
                "" AS h_movimiento,
                "" AS f_liquidacion,
                "" AS h_liquidacion,
                "" AS estado_legado,
                "" AS usuario_ultimo_movimiento,
                "" AS ult_obs_movimiento,
                "" AS ult_obs_movimiento_detalle,
                gd.codmotivo_req_catv AS CodReqMotivo
                '.$querydetalleaveria[0].'
                FROM webpsi_coc.averias_criticos_final gd
                LEFT JOIN webpsi_coc.averias_criticos_final_historico acfh
                    on gd.averia=acfh.averia
                LEFT JOIN quiebres q ON q.apocope=gd.quiebre
                LEFT JOIN quiebre_grupos qg ON qg.id=q.quiebre_grupo_id
                LEFT JOIN empresas e ON e.nombre=gd.eecc_final
                '.$querydetalleaveria[1].'
                WHERE gd.averia NOT IN (    
                                        SELECT gd2.codactu 
                                        FROM gestiones_detalles gd2
                                        )
                '.$filtro[1].
                ' UNION '.
                'SELECT "" AS id,gd.codigo_req AS codactu,gd.quiebre,
                gd.eecc_final AS empresa,"" AS fecha_agenda,
                "" AS tecnico,"Temporal" AS estado,
                "Provision" AS actividad,gd.fecha_Reg AS fecha_registro,
                "1" AS existe,
                "0" AS transmision,
                "" AS cierre_estado,
                "" AS fh_agenda,
                "" AS f_agenda,
                "" AS h_agenda,
                "" AS f_consolidacion,
                "" AS f_ingreso,
                "" AS h_ingreso,
                "" AS f_movimiento,
                "" AS h_movimiento,
                "" AS f_liquidacion,
                "" AS h_liquidacion,
                "" AS estado_legado,
                "" AS usuario_ultimo_movimiento,
                "" AS ult_obs_movimiento,
                "" AS ult_obs_movimiento_detalle,
                gd.tipo_motivo AS CodReqMotivo 
                '.$querydetalleprovision[0].'
                FROM webpsi_coc.tmp_provision gd
                LEFT JOIN webpsi_coc.tmp_provision_historico tph 
                    ON (gd.codigo_req=tph.codigo_req)               
                LEFT JOIN quiebres q ON q.apocope=gd.quiebre
                LEFT JOIN quiebre_grupos qg ON qg.id=q.quiebre_grupo_id
                LEFT JOIN empresas e ON e.nombre=gd.eecc_final
                '.$querydetalleprovision[1].'
                WHERE gd.codigo_req NOT IN (    
                                            SELECT gd2.codactu 
                                            FROM gestiones_detalles gd2
                                            )
                '.$filtro[2];

            $queryGestion=
            'SELECT *
            FROM (  
                SELECT  g.id,gd.codactu,q.apocope AS quiebre,
                e.nombre AS empresa,IFNULL(um.fecha_agenda,"") AS fecha_agenda,
                IFNULL(t.nombre_tecnico,"") AS tecnico,es.nombre AS estado,
                a.nombre AS actividad,gd.fecha_registro,
                IFNULL( 
                    IF(a.id IN (1,3),
                        (SELECT 1 
                        FROM schedulle_sistemas.pen_pais_total ppt 
                        WHERE ppt.averia=gd.codactu 
                        LIMIT 1),
                        (SELECT 1 
                        FROM schedulle_sistemas.tmp_gaudi_total tgt 
                        WHERE tgt.DATA17=gd.codactu 
                        LIMIT 1)
                    ),
                    "0"
                ) AS existe,
                IFNULL(
                    ta.paso,
                    IF(g.n_evento=1,"1","0")
                ) AS transmision,
                IFNULL(
                    (SELECT estado
                    FROM webpsi_officetrack.paso_tres pt
                    WHERE pt.task_id=ta.id
                    LIMIT 1),
                    ""
                ) cierre_estado,
                CONCAT( 
                    IF( IFNULL(um.fecha_agenda,"")="",
                        "",CONCAT(um.fecha_agenda," / ")
                    ),
                    IFNULL(h.horario,"")
                ) AS fh_agenda,
                IFNULL(um.fecha_agenda,"") AS f_agenda,
                IFNULL(h.horario,"") AS h_agenda,
                DATE(um.fecha_consolidacion) AS f_consolidacion,
                DATE(g.created_at) AS f_ingreso,
                time(g.created_at) AS h_ingreso,
                DATE(um.updated_at) AS f_movimiento,
                time(um.updated_at) AS h_movimiento,
                IFNULL(DATE(um.fec_liq_legado),"") AS f_liquidacion,
                IFNULL(time(um.fec_liq_legado),"") AS h_liquidacion,
                IFNULL(um.estado_legado,"") AS estado_legado,
                CONCAT(u.apellido,", ",u.nombre) AS usuario_ultimo_movimiento,
                um.observacion_m AS ult_obs_movimiento,
                /*(
                SELECT CAST(CONCAT("Tipo:",ot.nombre," | Obs:",go.observacion,
                    " | Usu:",u.apellido,", ",u.nombre) AS CHAR) 
                FROM movimientos_observaciones go
                INNER JOIN observaciones_tipos ot ON ot.id=go.observacion_tipo_id
                INNER JOIN usuarios u ON u.id=go.usuario_created_at
                WHERE go.gestion_movimiento_id=gm.id
                ORDER BY go.id DESC
                LIMIT 1
                )*/ "" AS ult_obs_movimiento_detalle,
                um.codmotivo_req_catv AS CodReqMotivo 
                '.$querydetallegestion[0].'
                FROM gestiones g
                INNER JOIN ultimos_movimientos um ON um.gestion_id=g.id
                INNER JOIN gestiones_detalles gd ON g.id=gd.gestion_id
                INNER JOIN actividades a ON a.id=g.actividad_id
                INNER JOIN quiebres q ON q.id=gd.quiebre_id
                INNER JOIN quiebre_grupos qg ON qg.id=q.quiebre_grupo_id
                INNER JOIN empresas e ON e.id=um.empresa_id
                INNER JOIN estados es ON es.id=um.estado_id
                INNER JOIN usuarios u ON u.id=um.usuario_created_at
                LEFT JOIN tecnicos t ON t.id=um.tecnico_id
                LEFT JOIN celulas c ON c.id=um.celula_id
                LEFT JOIN horarios h ON h.id=um.horario_id
                LEFT JOIN webpsi_officetrack.tareas ta 
                    ON (ta.task_id=g.id AND 
                        ta.id IN (  SELECT MAX(ta2.id)
                                    FROM webpsi_officetrack.tareas ta2
                                    WHERE ta2.task_id=g.id
                                 )
                       )
                WHERE 1=1 '.
                $filtro[0].
                $dqueryconestado.
            ') q1 
             WHERE 1=1 '.$filtro[3];
            //echo $queryGestion;
            $gestion= DB::select($queryGestion);

        return $gestion;
    }
    public static function getCargar($buscar,$tipo)
    {
        //codclicms
        //telefono
        $sql = "SELECT telefono,codclie,codclicms,codservcms,inscripcio,appater,
                        apmater,nombre,mdf,tipopaq,modalidad,veloc,nodotroba,
                        nomcalle,numcalle,tipocalle
                FROM webpsi_coc.tb_lineas_servicio_total t 
                WHERE ".$tipo."='".$buscar."' ";

        $result = DB::select($sql);
       // $result = array();
        return $result;
    }

    public static function getCargarNombre()
    {
        $sql = "SELECT telefono,codclie,codclicms,codservcms,inscripcio,appater,
                        apmater,nombre,mdf,tipopaq,modalidad,veloc,nodotroba,
                        nomcalle,numcalle,tipocalle
                FROM webpsi_coc.tb_lineas_servicio_total t 
                WHERE appater='".Input::get('paterno')."'
                AND apmater='".Input::get('materno')."'
                AND nombre like '%".Input::get('nombre')."%' ";

        $result = DB::select($sql);
       // $result = array();
        return $result;
    }

    public static function getMovimientos()
    {
        $gm = DB::table('gestiones_movimientos AS gm')
                ->join(
                    'gestiones AS g',
                    'g.id', '=', 'gm.gestion_id'
                )
                ->join(
                    'gestiones_detalles AS gd',
                    'gd.gestion_id', '=', 'gm.gestion_id'
                )
                ->join(
                    'quiebres AS q',
                    'q.id', '=', 'gd.quiebre_id'
                )
                ->join(
                    'actividades AS a',
                    'a.id', '=', 'g.actividad_id'
                )
                ->join(
                    'empresas AS e',
                    'e.id', '=', 'gm.empresa_id'
                )
                ->join(
                    'zonales AS z',
                    'z.id', '=', 'gm.zonal_id'
                )
                ->join(
                    'motivos AS m',
                    'm.id', '=', 'gm.motivo_id'
                )
                ->join(
                    'submotivos AS s',
                    's.id', '=', 'gm.submotivo_id'
                )
                ->join(
                    'estados AS es',
                    'es.id', '=', 'gm.estado_id'
                )
                ->join(
                    'usuarios AS u',
                    'u.id', '=', 'gm.usuario_created_at'
                )
                ->leftJoin(
                    'horarios AS h',
                    'h.id', '=', 'gm.horario_id'
                )
                ->leftJoin(
                    'tecnicos AS t',
                    't.id', '=', 'gm.tecnico_id'
                )
                ->leftJoin(
                    'celulas AS c',
                    'c.id', '=', 'gm.celula_id'
                )
                ->select(
                    'gm.id', 'gd.codactu',
                    'gm.created_at AS fecha_movimiento',
                    'a.nombre AS actividad', 'q.nombre AS quiebre',
                    'e.nombre AS empresa', 'z.nombre AS zonal',
                    'g.nombre_cliente_critico', 'g.telefono_cliente_critico',
                    'g.celular_cliente_critico', 'm.nombre AS motivo',
                    's.nombre AS submotivo', 'es.nombre AS estado',
                    'gm.observacion',
                    DB::raw(
                        'CONCAT(u.apellido," ",u.nombre) AS usuario,
                        IFNULL(gm.fecha_agenda,"") AS fecha_agenda,
                        IFNULL(h.horario,"") AS hora_agenda,
                        CONCAT(
                            IF( IFNULL(gm.fecha_agenda,"")="",
                                "",CONCAT(gm.fecha_agenda," / ")
                            ),
                            IFNULL(h.horario,"")
                        ) AS fh_agenda,
                        IFNULL(t.nombre_tecnico,"") AS tecnico,
                        IFNULL(c.nombre,"") AS celula'
                    ),
                    DB::raw(
                        '(  SELECT GROUP_CONCAT(CONCAT_WS("^^",mo.created_at,ot.nombre,mo.observacion,u.nombre,u.apellido) SEPARATOR "|" )
                            FROM movimientos_observaciones mo
                            INNER JOIN observaciones_tipos ot ON mo.observacion_tipo_id=ot.id
                            INNER JOIN usuarios u ON u.id=mo.usuario_created_at
                            WHERE mo.gestion_movimiento_id=gm.id
                            GROUP BY mo.gestion_movimiento_id
                         ) AS observaciones'
                    ),
                    DB::raw(
                        'CONCAT(
                            "tr_",IFNULL(gm.id,"0")
                        ) AS id_detalle_table'
                    )
                )
                ->where(
                    function ($query) {
                        if ( Input::get('codactu') ) {
                            $query->where(
                                'gd.codactu', '=', Input::get('codactu')
                            );
                            //$query;
                        }
                        else{
                            $query->where(
                                'gd.codactu', '=', 'consulta'
                            );
                        }
                    }
                );

        $r = array();
        if ( Input::get('first') ) {
            $gm->orderBy('gm.created_at', 'desc');
            $r = $gm->first();
        } else {
            $r = $gm->get();
        }

        return $r;
    }
}
