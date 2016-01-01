<?php

class Tarea extends \Eloquent
{
    public $table = "tareas";

    public static function reporte_cruce_finalizado($fechaIni = null,
        $fechaFin =null)
    {

        $query = "  SELECT * FROM
                        (
                        SELECT  g.id, g.id_atc, e.estado,
                                IFNULL(SUBSTR(t.paso,6),'') AS paso,
                                (   SELECT fecha_recepcion
                                    FROM webpsi_officetrack.tareas
                                    WHERE task_id=t.task_id
                                    AND cod_tecnico= t.cod_tecnico
                                    AND paso='0001-Inicio'
                                    ORDER BY id DESC LIMIT 1
                                ) AS f_inicio,
                                (   SELECT fecha_recepcion
                                    FROM webpsi_officetrack.tareas
                                    WHERE task_id=t.task_id
                                    AND cod_tecnico= t.cod_tecnico
                                    AND paso='0002-Supervision'
                                    ORDER BY id DESC LIMIT 1
                                ) AS f_supervision,
                                (   SELECT fecha_recepcion
                                    FROM webpsi_officetrack.tareas
                                    WHERE task_id=t.task_id
                                    AND cod_tecnico= t.cod_tecnico
                                    AND paso='0003-Cierre'
                                    ORDER BY id DESC LIMIT 1
                                ) AS f_cierre,
                                gd.codactu AS averia,
                                a.nombre AS tipo_actividad,
                                tipo_averia AS tipo_averia,
                                (SELECT fecha_agenda
                                    FROM gestiones_movimientos 
                                    WHERE gestion_id=g.id 
                                    AND estado_id='2'
                                    /*Agendado con Técnico 2*/
                                    ORDER BY id DESC LIMIT 1
                                ) AS fecha_agenda,
                                (SELECT h.horario
                                    FROM gestiones_movimientos m 
                                    INNER JOIN horarios h ON m.horario_id=h.id
                                    WHERE m.gestion_id=g.id 
                                    AND m.estado_id='2' 
                                    /*Agendado con Técnico 2*/
                                    ORDER BY m.id DESC LIMIT 1
                                ) AS hora_agenda,
                                REPLACE(gm.fecha_consolidacion,'0000-00-00','')
                                AS fecha_consilidacion,
                                gm.observacion,gm.tecnico_id,
                                CONCAT(u.apellido,', ',u.nombre) AS usuario
                        FROM gestiones g
                        JOIN gestiones_movimientos gm ON g.id=gm.gestion_id
                        JOIN gestiones_detalles gd ON g.id=gd.gestion_id
                        JOIN usuarios u ON gm.usuario_created_at=u.id
                        JOIN horarios h ON gm.horario_id=h.id
                        JOIN estados e ON gm.estado_id=e.id
                        JOIN actividades a ON g.actividad_id=a.id
                        LEFT JOIN webpsi_officetrack.tareas t ON
                        (g.id=t.task_id AND t.id=
                            (   SELECT id 
                                FROM webpsi_officetrack.tareas
                                WHERE task_id=g.id 
                                ORDER BY id DESC LIMIT 1
                            )
                        )
                        WHERE gm.id=(SELECT id 
                                    FROM gestiones_movimientos
                                    WHERE gestion_id=g.id 
                                    ORDER BY id DESC LIMIT 1
                        ) AND g.n_evento=1
                        AND gm.estado_id!='4'
                    ) AS reporte
                    WHERE reporte.fecha_agenda BETWEEN ? AND ?
                    ORDER BY reporte.fecha_agenda";

        return DB::select(
            $query,
            array(
                        $fechaIni,
                        $fechaFin
                    )
        );

    }
    public static function getTareas(
        $fechaIni ,
         $fechaFin ,
          $empresaId = "" ,
           $celulaId = ""
           )
    {
        $query="SELECT g.id_atc, t.task_id, t.cod_tecnico, t.paso, t.cliente,
                       t.fecha_recepcion , t.fecha_agenda, tec.celula_id,
                       tec.empresa_id, tec.nombre_tecnico, p3.estado,
                       p3.observacion
                FROM webpsi_officetrack.tareas t
                INNER JOIN (SELECT MAX(id) AS id
                            FROM webpsi_officetrack.tareas
                            WHERE fecha_recepcion BETWEEN ? AND ?
                            GROUP BY task_id, cod_tecnico
                            ) AS u ON t.id=u.id
                INNER JOIN (SELECT t.carnet, t.nombre_tecnico, t.empresa_id,
                            GROUP_CONCAT(ct.celula_id) celula_id
                            FROM tecnicos t
                            INNER JOIN celula_tecnico ct ON t.id=ct.tecnico_id
                            WHERE t.estado=1
                            GROUP BY t.carnet
                            ) tec ON t.cod_tecnico=tec.carnet
                INNER JOIN gestiones g ON t.task_id=g.id
                LEFT JOIN webpsi_officetrack.paso_tres p3 ON t.id=p3.task_id
                WHERE t.fecha_recepcion BETWEEN ? AND ? AND tec.empresa_id = ?
                        AND tec.celula_id like '%$celulaId%'
                ORDER BY t.cod_tecnico, t.paso, t.fecha_recepcion DESC,
                      p3.estado";
        
        return DB::select(
            $query,
            array(
                $fechaIni,
                $fechaFin,
                $fechaIni,
                $fechaFin,
                $empresaId
            )
        );
        //return $tareas;
    }
    public static function getAgendasAll($fechaAgen, $estados, $carnets)
    {
        $estados = implode(',', $estados);

        $query = "SELECT
                    g.id, g.id_atc, g.nombre_cliente_critico, gd.observacion,
                    t.nombre_tecnico as tecnico, gm.empresa_id, t.carnet,
                    h.horario, gd.codactu, gd.inscripcion, gd.telefono, gd.mdf,
                    gd.fftt, gd.direccion_instalacion AS direccion,gd.quiebre_id
                    ,gd.lejano, gd.llave, gd.paquete, emp.nombre AS eecc_final,
                    gd.microzona, gd.tipo_averia AS tipoactu, fg.fecha_agenda,
                    gm.estado_id, a.nombre AS tipo_actividad,
                    gm.usuario_created_at AS usuario_id, e.estado, ee.eecc,
                    (SELECT GROUP_CONCAT(c.nombre) 
                     FROM celulas c JOIN celula_tecnico ct ON c.id=ct.celula_id
                     WHERE ct.tecnico_id=t.id
                     ) AS nombre,
                    (SELECT GROUP_CONCAT(c.id) 
                     FROM celulas c JOIN celula_tecnico ct ON c.id=ct.celula_id
                     WHERE ct.tecnico_id=t.id
                     ) AS celula_id,
                    IF(
                        fg.fecha_agenda = DATE(NOW()),
                        1,
                        IF( fg.fecha_agenda <  DATE(NOW()),0,2)
                    ) AS pendiente,
                    IF(
                        fg.fecha_agenda = DATE(NOW()),
                        'hoy',
                        IF(
                            fg.fecha_agenda <  DATE(NOW()),
                            'pasados',
                            'futuros')
                    ) AS programados,
                    CONCAT_WS(' ',u.apellido,u.nombre) supervisor
                    FROM gestiones g
                    INNER JOIN gestiones_movimientos gm ON g.id=gm.gestion_id
                    INNER JOIN gestiones_detalles gd ON g.id=gd.gestion_id
                    INNER JOIN estados e ON gm.estado_id=e.id
                    INNER JOIN tecnicos t ON gm.tecnico_id=t.id
                    INNER JOIN horarios h ON gm.horario_id=h.id
                    INNER JOIN actividades a ON g.actividad_id=a.id
                    INNER JOIN (
                            SELECT gestion_id, MAX(fecha_agenda) AS fecha_agenda
                            FROM gestiones_movimientos 
                            GROUP BY gestion_id
                             ) fg ON g.id = fg.gestion_id
                    INNER JOIN webpsi.tb_eecc ee ON ee.id = gd.empresa_id
                    INNER JOIN empresas emp ON gd.empresa_id=emp.id
                    INNER JOIN usuarios u ON gm.usuario_created_at=u.id
                    WHERE gm.id IN (SELECT MAX(gm2.id)
                                    FROM gestiones_movimientos gm2
                                    WHERE gm2.gestion_id=gm.gestion_id
                                    GROUP BY gm2.gestion_id)
                    AND fg.fecha_agenda >= ?
                    AND gm.estado_id in ('$estados')
                    AND t.carnet in ('$carnets')
                    ORDER BY t.carnet, pendiente";

        return DB::select($query, array($fechaAgen));
        /*try {
        } catch (Exception $e) {
            Log::error($e);
            return "";
        }*/
        //return $tareas;
    }
}
