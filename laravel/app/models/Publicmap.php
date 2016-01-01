<?php

class Publicmap extends Eloquent {
    /**
     * @param filtro_fec array(0,1) , el primer parametro es un simbolo =,<,>,<>
     * el segundo es el valor de la fecha:
     * <>'', ='', <'', >''
     */
    public function getRutaTecnico($carnet, $gestion_id, $filtro_fec = array()) {

        try {
            $tecnico = DB::table('tecnicos')
                    ->where('estado', '=', 1)
                    ->where('carnet', '=', $carnet)
                    ->orWhere('carnet_tmp', '=', $carnet)
                    ->first(
                    array(
                        'id',
                        'nombre_tecnico'
                    )
            );

            $sql = "SELECT 	
                    g.id_atc, g.id,gd.codactu,q.apocope AS quiebre,
                    e.nombre AS empresa,IFNULL(gm.fecha_agenda,'') fecha_agenda,
                    IFNULL(t.nombre_tecnico,'') AS tecnico,es.nombre AS estado,
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
                    '0'
                ) AS existe,
                IFNULL(
                    ta.paso,
                    IF(g.n_evento=1,'1','0')
                ) AS transmision,
                IFNULL(
                    (SELECT estado
                    FROM webpsi_officetrack.paso_tres pt
                    WHERE pt.task_id=ta.id
                    LIMIT 1),
                    ''
                ) cierre_estado,e.id AS empresa_id,
                IFNULL(t.id,'') AS tecnico_id,gm.coordinado,
                IFNULL(gm.celula_id,'') AS celula_id,es.id AS estado_id,
                q.id AS quiebre_id,q.quiebre_grupo_id,a.id AS actividad_id,
                g.nombre_cliente_critico,g.celular_cliente_critico,
                g.telefono_cliente_critico, gd.zonal_id,
                gd.tipo_averia tipoactu,
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
                IFNULL(gd.wu_nagendas,'0') wu_nagendas,
                IFNULL(gd.wu_nmovimientos,'0') wu_nmovimientos,
                gd.wu_fecha_ult_agenda,
                gd.total_llamadas_tecnicas,
                gd.total_llamadas_seguimiento,
                gd.llamadastec15dias,gd.llamadastec30dias,
                gd.lejano,gd.distrito,gd.eecc_zona,
                gd.zona_movistar_uno,
                IFNULL(gd.paquete,'') AS paquete,
                gd.data_multiproducto,gd.averia_m1,
                gd.fecha_data_fuente,gd.telefono_codclientecms,
                gd.rango_dias,gd.sms1,gd.sms2,gd.area2,gd.microzona,
                gd.tipo_actuacion,IFNULL(gm.horario_id,'') AS horario_id, 
                IFNULL(h.horario,'') hora_agenda,IFNULL(gm.dia_id,'') AS dia_id,
                CONCAT( 
                    IF( IFNULL(gm.fecha_agenda,'')='',
                        '',CONCAT(gm.fecha_agenda,' / ')
                    ),
                    IFNULL(h.horario,'')
                ) AS fh_agenda, gd.x, gd.y 
                FROM gestiones g
                INNER JOIN gestiones_detalles gd 
                    ON g.id=gd.gestion_id
                INNER JOIN gestiones_movimientos gm 
                    ON (g.id=gm.gestion_id AND 
                        gm.id IN (  SELECT MAX(gm2.id)
                                    FROM gestiones_movimientos gm2
                                    WHERE gm2.gestion_id=g.id
                                 )
                       )
                INNER JOIN actividades a ON a.id=g.actividad_id
                INNER JOIN quiebres q ON q.id=gd.quiebre_id
                INNER JOIN empresas e ON e.id=gm.empresa_id
                INNER JOIN estados es ON es.id=gm.estado_id
                LEFT JOIN tecnicos t ON t.id=gm.tecnico_id
                LEFT JOIN horarios h ON h.id=gm.horario_id
                LEFT JOIN webpsi_officetrack.tareas ta 
                    ON (ta.task_id=g.id AND 
                        ta.id IN (  SELECT MAX(ta2.id)
                                    FROM webpsi_officetrack.tareas ta2
                                    WHERE ta2.task_id=g.id
                                 )
                       )
                /*LEFT JOIN clientes cl ON cl.codigo=gd.inscripcion*/
                WHERE 
                    es.id=2 
                    AND t.id=$tecnico->id ";
                    //AND gm.fecha_agenda='$fec_agenda'";

            if ($gestion_id != '') {
                $sql .= " AND g.id = $gestion_id ";
            }
            if (empty($filtro_fec)) {//si esta vacio
                $fec_agenda = date("Y-m-d");
                $sql .=" AND gm.fecha_agenda='$fec_agenda'";
            } else {
                $sql .=" AND gm.fecha_agenda".
                        $filtro_fec["condicion"]."'".$filtro_fec["valor"]."'";
            }

            $result = DB::select($sql);

            return $result;
        } catch (Exception $exc) {
            return "<h2>Error: No se encontraron datos</h2>";
        }
    }

}
