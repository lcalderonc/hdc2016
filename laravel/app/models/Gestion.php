<?php 
class Gestion extends Eloquent {

    public $table = 'gestiones';

    public static function getGenerarID() {

        $sql = 'SELECT GenerarIDGestionCritico() as id';
        $res = DB::SELECT($sql);
        return $res[0]->id;
    }
    /**
     * si existe retorno 0, si no retorno 11
     */
    public static function getRowgestiones($codactu=''){
        $row=0;
        $query="SELECT count(averia) as row
                FROM  webpsi_coc.averias_criticos_final
                WHERE averia=?";
        
        $row = DB::select($query, array($codactu));
        if ($row['0']->row>0) {
            return 0;
        }
        $query="SELECT count(codigo_req) as row
                FROM  webpsi_coc.tmp_provision
                WHERE codigo_req=?";
        $row = DB::select($query, array($codactu));
        if ($row['0']->row>0) {
            return 0;
        }
        //codactu
        $query="SELECT count(codactu) as row
                FROM  gestiones_detalles
                WHERE codactu=?";
        $row = DB::select($query, array($codactu));
        if ($row['0']->row>0) {
            return 0;
        }
        return 1;
    }
    public static function getCargar($actucode=""){
        $filtro=array('','','','');
        $buscar=array("averia");
        $reemplazar=array("codigo_req");

        $querycompleto=true;
        $queryconestado=true;
        $queryadicional=false;// para las facilidades tecnicas
        $queryadicionalsql=array("","","");// para las facilidades tecnicas

        $post=array();$haspost=array();

        $post=Input::all();

        foreach($post as $k=>$value){
            $bk=array("slct_","txt_","chk_","rdb_","_modal");
            $rk=array("","","","","");
            $k=str_replace($bk,$rk,$k);
            $post[$k]=$value;
        }
        
        if ($actucode != '') {
            $post["buscar"] = $actucode;
            $post["tipo"] = "gd.averia";
        }

        Input::replace($post);

        if ( Input::get('bandeja') && Input::get('bandeja')==1 AND !Input::has('imagen') AND !Input::has('totalPSI') ) {
            $querycompleto=false;
        }

        if ( Input::get('usuario') ) {
            $filtro[0]='AND q.quiebre_grupo_id IN (  SELECT quiebre_grupo_id
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
                        )
                        ';
            $filtro[1]='AND q.quiebre_grupo_id IN (  SELECT quiebre_grupo_id
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
                        AND z.id IN (
                                SELECT zonal_id
                                FROM usuario_zonal
                                WHERE usuario_id="'.Auth::user()->id.'"
                                AND estado=1
                        )
                        ';
            $filtro[2]='AND q.quiebre_grupo_id IN (  SELECT quiebre_grupo_id
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
                        AND z.id IN (
                                SELECT zonal_id
                                FROM usuario_zonal
                                WHERE usuario_id="'.Auth::user()->id.'"
                                AND estado=1
                        )
                        ';
        }

        if ( Input::get('buscar')!='' ) {
            $filtro[0]=' AND '.Input::get('tipo').
                    ' ="'.Input::get('buscar').'" ';
            $filtro[0]=str_replace("averia", "codactu", $filtro[0]);
            if ( Input::get('tipo')=="gd.averia" ) {
                $filtro[1]=' AND '.Input::get('tipo').
                         ' ="'.Input::get('buscar').'" ';
            } else {
                $filtro[1]=" AND 1=0 ";
            }

            $filtro[2]=str_replace($buscar, $reemplazar, $filtro[1]);
        } elseif ( Input::get('buscar_codactu') ) {
            $bcodactu=str_replace(',','","',Input::get('buscar_codactu'));
            $filtro[0]=' AND '.Input::get('tipo').
                    ' IN ("'.$bcodactu.'") ';
            $filtro[0]=str_replace("averia", "codactu", $filtro[0]);
            $queryconestado=false;
        } else {
            if ( Input::get('actividad') ) {
                $actividad=implode(",", Input::get('actividad'));
                $filtro[0].=" AND a.id IN (".$actividad.")";
                $filtro[1].=" AND 1 IN (".$actividad.")";
                $filtro[2].=" AND 2 IN (".$actividad.")";
            }

            if ( Input::get('estado') ) {
                $estado=implode(",", Input::get('estado'));
                $posestado= strpos($estado,"-1");
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

            if ( Input::get('empresa') ) {
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

            if ( trim( Input::get('legado') )!='' ) { 
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

            if ( trim( Input::get('coordinado') )!='' ) {
                $coordinado=Input::get('coordinado');
                $filtro[0].=" AND gm.coordinado=".$coordinado;
                $queryconestado=false;
                $filtro[1].=" AND 1=0";
                $filtro[2].=" AND 1=0";
            }

            if ( trim( Input::get('fecha_agenda') )!='' ) {
                $fechaAgenda=explode(" - ", Input::get('fecha_agenda'));
                $filtro[0].='   AND gm.fecha_agenda 
                                BETWEEN "'.$fechaAgenda[0].'" 
                                AND "'.$fechaAgenda[1].'" ';
                $queryconestado=false;
                $filtro[1].=" AND 1=0";
                $filtro[2].=" AND 1=0";
            }

            if( Input::get('nodo') and Input::get('mdf') ){
                $mdffinal=implode('","', Input::get('mdf')).'","'.implode('","', Input::get('mdf'));
                $filtro[0].=' AND gd.mdf IN ("'.$mdffinal.'")';
                $filtro[1].=' AND gd.mdf IN ("'.$mdffinal.'")';
                $filtro[2].=' AND gd.mdf IN ("'.$mdffinal.'")';
            }
            elseif( Input::get('nodo') ){
                $mdffinal=implode('","', Input::get('nodo'));
                $filtro[0].=' AND gd.mdf IN ("'.$mdffinal.'")';
                $filtro[1].=' AND gd.mdf IN ("'.$mdffinal.'")';
                $filtro[2].=' AND gd.mdf IN ("'.$mdffinal.'")';
            }
            elseif( Input::get('mdf') ){
                $mdffinal=implode('","', Input::get('mdf'));
                $filtro[0].=' AND gd.mdf IN ("'.$mdffinal.'")';
                $filtro[1].=' AND gd.mdf IN ("'.$mdffinal.'")';
                $filtro[2].=' AND gd.mdf IN ("'.$mdffinal.'")';
            }

            if ( Input::get('zonal') ) {
                $zonal=implode('","', Input::get('zonal'));
                $filtro[0].=' AND CONCAT(gd.zonal,"|",gd.zonal_id) IN ("'.$zonal.'")';
                $filtro[1].=' AND CONCAT(gd.zonal,"|",z.id) IN ("'.$zonal.'")';
                $filtro[2].=' AND CONCAT(gd.zonal,"|",z.id) IN ("'.$zonal.'")';
            }

            if ( Input::has('troba') ){
                $troba=implode('","', Input::get('troba'));
                $queryadicional=true;
                $queryadicionalsql[0]=' INNER JOIN (
                                            SELECT gestion_id
                                            FROM gestiones_fftt
                                            WHERE fftt_tipo_id=6
                                            AND nombre IN ("'.$troba.'")
                                         ) adic ON adic.gestion_id=g.id ';
                $queryadicionalsql[1]=' AND gd.tipo_averia LIKE "%catv%"
                                        AND SUBSTRING_INDEX(SUBSTRING_INDEX(gd.fftt,"|",3),"|",-1) IN ("'.$troba.'") ';
                $queryadicionalsql[2]=' AND gd.origen LIKE "%catv%"
                                        AND SUBSTRING_INDEX(SUBSTRING_INDEX(gd.fftt,"|",2),"|",-1) IN ("'.$troba.'") ';
            }

            if ( Input::has('estado_ofsc') ){
                $queryconestado=false;
                $estado_ofsc=implode('","', Input::get('estado_ofsc'));
                $filtro[0].=' AND gm.estado_ofsc_id IN ("'.$estado_ofsc.'")';
               // $filtro[1].=' AND gm.estado_ofsc_id IN ("'.$estado_ofsc.'")';
               //$filtro[2].=' AND gm.estado_ofsc_id IN ("'.$estado_ofsc.'")';
            }
            
        }
        /*(
            SELECT CAST(CONCAT("Tipo:",ot.nombre," | Obs:",go.observacion,
                " | Usu:",u.apellido,", ",u.nombre) AS CHAR)
            FROM movimientos_observaciones go
            INNER JOIN observaciones_tipos ot ON ot.id=go.observacion_tipo_id
            INNER JOIN usuarios u ON u.id=go.usuario_created_at
            WHERE go.gestion_movimiento_id=gm.id
            ORDER BY go.id DESC
            LIMIT 1
            )*/ 
        $querydetallegestion[0]='
            ,e.id AS empresa_id,
            IFNULL(t.id,"") AS tecnico_id,gm.coordinado,
            IFNULL(gm.celula_id,"") AS celula_id,es.id AS estado_id,
            q.id AS quiebre_id,q.quiebre_grupo_id,a.id AS actividad_id,
            g.nombre_cliente_critico,g.celular_cliente_critico,
            g.telefono_cliente_critico, gd.zonal_id,
            gd.tipo_averia,
            gd.horas_averia,
            
            gd.ciudad,
            gd.inscripcion,
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
            gd.tipo_actuacion,IFNULL(gm.horario_id,"") AS horario_id, 
            IFNULL(h.horario,"") hora_agenda,IFNULL(gm.dia_id,"") AS dia_id,
            IFNULL(gd.fec_liq_legado,"") fec_liq_legado,
            IFNULL(gd.contrata_legado,"") contrata_legado,
            gd.actividad_tipo_id,
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
            "" AS ult_obs_movimiento_detalle,
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
            ) AS fecha_cambio,
            um.codmotivo_req_catv AS CodReqMotivo,
            IFNULL(c.nombre,"") AS celula,IFNULL(um.aid,"") AS aid,
            IFNULL(um.estado_ofsc_id,"") AS estado_ofsc_id';
        $querydetallegestion[1]='';
        $querydetalleaveria[0]='
            ,e.id AS empresa_id,
            "" AS tecnico_id,"0" AS coordinado,
            "" AS celula_id, "-1" AS estado_id,
            q.id AS quiebre_id,q.quiebre_grupo_id, "1" AS actividad_id,
            gd.nombre_cliente AS nombre_cliente_critico,
            gd.fono1 AS celular_cliente_critico,
            gd.telefono AS telefono_cliente_critico, 
            IFNULL(z.id,"") AS zonal_id,
            gd.tipo_averia,
            gd.horas_averia,
            gd.ciudad,
            gd.inscripcion,
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
            gd.tipo_actuacion,"" AS horario_id, 
            "" AS hora_agenda, "" AS dia_id,
            "" AS fec_liq_legado,
            "" AS contrata_legado,
            gd.actividad_tipo_id,
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
            IFNULL(
                    (SELECT fecha_cambio
                    FROM webpsi_coc.`averias_criticos_final_historico` ach
                    WHERE ach.averia=gd.averia
                    LIMIT 1)
                    ,
                        ""
            ) AS fecha_cambio,
            gd.codmotivo_req_catv AS CodReqMotivo,
            "" AS celula, "" AS aid, "" AS estado_ofsc_id';
        $querydetalleaveria[1]=' LEFT JOIN zonales z ON z.abreviatura=gd.zonal ';
        $querydetalleprovision[0]='
            ,e.id AS empresa_id,
            "" AS tecnico_id,"0" AS coordinado,
            "" AS celula_id, "-1" AS estado_id,
            q.id AS quiebre_id,q.quiebre_grupo_id, "2" AS actividad_id,
            gd.nomcliente AS nombre_cliente_critico,
            gd.fono1 AS celular_cliente_critico,
            gd.telefono_codclientecms AS telefono_cliente_critico,
            IFNULL(z.id,"") AS zonal_id,
            gd.origen AS tipo_averia,
            gd.horas_pedido AS horas_averia,
            
            gd.ciudad,
            gd.codigo_del_cliente AS inscripcion,
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
            gd.tipo_actuacion,"" AS horario_id, 
            "" AS hora_agenda,"" AS dia_id,
            "" AS fec_liq_legado,
            "" AS contrata_legado,
            gd.actividad_tipo_id,
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
            IFNULL(
                    (SELECT fecha_cambio
                    FROM webpsi_coc.`tmp_provision_historico` pch
                    WHERE pch.codigo_req=gd.codigo_req
                    LIMIT 1)
                    ,
                    ""
                ) AS fecha_cambio,
            gd.tipo_motivo AS CodReqMotivo,
            "" AS celula,"" AS aid, "" AS estado_ofsc_id';
        $querydetalleprovision[1]=' LEFT JOIN zonales z ON z.abreviatura=gd.zonal ';

        if($querycompleto==false){
            $querydetallegestion[0]='';
            //$querydetallegestion[1]='';
            $querydetalleaveria[0]='';
            //$querydetalleaveria[1]='';
            $querydetalleprovision[0]='';
            //$querydetalleprovision[1]='';
        }

        $dqueryconestado='';
        if ( $queryconestado==true ) {
            $dqueryconestado=
            ' UNION '.
            'SELECT "" AS id,gd.averia AS codactu,gd.quiebre,gd.mdf,
            gd.eecc_final AS empresa,"" AS fecha_agenda,
            "" AS tecnico,"Temporal" AS estado,
            "Averia" AS actividad,gd.fecha_registro,
            gd.xcoord coord_x, gd.ycoord coord_y, "" x_inicio, "" y_inicio,
            "1" AS existe,
            "0" AS transmision,
            "" AS cierre_estado,
            "" AS fh_agenda,
            "" as estado_ofsc
            '.$querydetalleaveria[0].'
            FROM webpsi_coc.averias_criticos_final gd
            LEFT JOIN webpsi_coc.averias_criticos_final_historico acfh
                on gd.averia=acfh.averia
            LEFT JOIN quiebres q ON q.apocope=gd.quiebre
            LEFT JOIN empresas e ON e.nombre=gd.eecc_final
            '.$querydetalleaveria[1].'
            WHERE gd.averia NOT IN (    
                                    SELECT gd2.codactu 
                                    FROM gestiones_detalles gd2
                                    )
            '.$queryadicionalsql[1].$filtro[1].
            ' UNION '.
            'SELECT "" AS id,gd.codigo_req AS codactu,gd.quiebre,gd.mdf,
            gd.eecc_final AS empresa,"" AS fecha_agenda,
            "" AS tecnico,"Temporal" AS estado,
            "Provision" AS actividad,gd.fecha_Reg AS fecha_registro,
            gd.xcoord coord_x, gd.ycoord coord_y, "" x_inicio, "" y_inicio,
            "1" AS existe,
            "0" AS transmision,
            "" AS cierre_estado,
            "" AS fh_agenda,
            ""  as estado_ofsc
            '.$querydetalleprovision[0].'
            FROM webpsi_coc.tmp_provision gd
            LEFT JOIN webpsi_coc.tmp_provision_historico tph 
                ON (gd.codigo_req=tph.codigo_req)               
            LEFT JOIN quiebres q ON q.apocope=gd.quiebre
            LEFT JOIN empresas e ON e.nombre=gd.eecc_final
            '.$querydetalleprovision[1].'
            WHERE gd.codigo_req NOT IN (    
                                        SELECT gd2.codactu 
                                        FROM gestiones_detalles gd2
                                        )
            '.$queryadicionalsql[2].$filtro[2];
        }

        $queryGestion=
        'SELECT *
        FROM (  
            SELECT  g.id,gd.codactu,q.apocope AS quiebre,gd.mdf,
            e.nombre AS empresa,IFNULL(gm.fecha_agenda,"") AS fecha_agenda,
            IFNULL(t.nombre_tecnico,"") AS tecnico,es.nombre AS estado,
            a.nombre AS actividad,gd.fecha_registro,
                            um.x coord_x, um.y coord_y, um.x_inicio, um.y_inicio,
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
                IF( IFNULL(gm.fecha_agenda,"")="",
                    "",CONCAT(gm.fecha_agenda," / ")
                ),
                IFNULL(h.horario,"")
            ) AS fh_agenda,
            eo.nombre as estado_ofsc
            '.$querydetallegestion[0].'
            FROM gestiones g
            '.$queryadicionalsql[0].' 
            INNER JOIN gestiones_detalles gd ON g.id=gd.gestion_id
            INNER JOIN gestiones_movimientos gm 
                ON (g.id=gm.gestion_id AND 
                    gm.id IN (  SELECT MAX(gm2.id)
                                FROM gestiones_movimientos gm2
                                WHERE gm2.gestion_id=g.id
                             )
                   )
            LEFT JOIN estados_ofsc eo ON gm.estado_ofsc_id=eo.id 
            INNER JOIN actividades a ON a.id=g.actividad_id
            INNER JOIN quiebres q ON q.id=gd.quiebre_id
            INNER JOIN empresas e ON e.id=gm.empresa_id
            INNER JOIN estados es ON es.id=gm.estado_id
            INNER JOIN ultimos_movimientos um ON um.gestion_id=g.id
            INNER JOIN usuarios u ON u.id=um.usuario_updated_at
            LEFT JOIN celulas c ON c.id=um.celula_id
            LEFT JOIN tecnicos t ON t.id=gm.tecnico_id
            LEFT JOIN horarios h ON h.id=gm.horario_id
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

         $queryGestion2=$queryGestion;
         if( Input::get('draw') ){
            if ( Input::get('order') ){
                $inorder=Input::get('order');
                $incolumns=Input::get('columns');
                $queryGestion2.=' ORDER BY '.$incolumns[ $inorder[0]['column'] ]['name'].' '.$inorder[0]['dir'];
            }
            $queryGestion2.=' LIMIT '.Input::get('start').','.Input::get('length');
         }
         DB::connection()->disableQueryLog();
         //echo   $queryGestion;
        try {
        $gestion2= DB::select($queryGestion2);
        $gestion= DB::select($queryGestion);
            $retorno =  array(
                            'rst'=>1,
                            'datos'=>$gestion//,'sql'=>$queryGestion
                        );

            $retorno["recordsTotal"]=count($gestion);
            $retorno["recordsFiltered"]=count($gestion);
            if( Input::get('draw') ){
                $retorno["draw"]=Input::get('draw');
                $retorno["data"]=$gestion2;
            }
            return $retorno;
        } catch (Exception $exc) {
            //$this->_errorController->saveError($exc);
            return array(
                    'rst'=>2,
                    'datos'=>$exc
                
            );
        }
    }
}
?>
