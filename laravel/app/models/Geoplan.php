<?php

class Geoplan extends Eloquent {

    public function strOrField($field, $array, $tmp = false) 
    {
        $str = "";

        foreach ($array as $val) {
            if ($tmp) {
                $str .= "OR $field='" . $val->valor . "' ";
            } else {
                $str .= "OR $field='" . $val . "' ";
            }
        }

        return " AND ( " . substr($str, 3) . ")";
    }

    /**
     * Retorna los datos del último movimiento de una gestion
     * 
     * @param type $gestion_id ID de gestion
     * @return type
     */
    public function getUltimoMovimiento($gestion_id)
    {
        $movimiento = DB::select(
            'SELECT 
                gm.empresa_id, gm.zonal_id, gm.horario_id, gm.
                dia_id, gm.estado_id, gm.motivo_id, gm.submotivo_id, gm.
                celula_id, gm.tecnico_id, gm.fecha_agenda,
                gd.quiebre_id, g.n_evento transmision
            FROM 
                gestiones_movimientos gm, 
                gestiones_detalles gd,
                gestiones g
            WHERE 
		gm.gestion_id=gd.gestion_id
	    AND g.id=gd.gestion_id
            AND gm.gestion_id = ?
            AND gm.id=(
                SELECT MAX(id) 
                FROM gestiones_movimientos 
                WHERE gestion_id = ?
               )',
            array($gestion_id, $gestion_id)
        );
        return array('rst'=>1,'datos'=>$movimiento);
    }
    
    /**
     * Horarios para Geo Planificacion
     * @param type $quiebre lista de quiebres
     * @param type $zonal ID zonal
     * @param type $empresa ID empresa
     * @return boolean
     */
    public function getPlanHorario($quiebre, $zonal, $empresa)
    {
        try {
            $sql = "SELECT 
                h.id horario_id, qg.id qgrupo_id, q.id quiebre_id, 
                q.apocope quiebre, qg.nombre grupo, h.horario
            FROM
                horarios h, quiebres q, quiebre_grupos qg, 
                capacidad_horario ch, capacidad_horario_detalle chd
            WHERE
                q.quiebre_grupo_id=qg.id
                AND ch.quiebre_grupo_id=qg.id
                AND chd.capacidad_horario_id=ch.id
                AND chd.horario_id=h.id
                AND ch.zonal_id = {$zonal[0]} AND ch.empresa_id = $empresa
                AND ch.estado = 1
                AND chd.estado = 1 
                AND qg.estado = 1
                AND q.estado = 1
                AND h.estado = 1
                AND q.id IN ( $quiebre )
            GROUP BY 3, 1
            ORDER BY 5, 4, h.id";
            
            $result["data"] = DB::select($sql);
            $result["estado"] = true;
            return $result;
        } catch (Exception $exc) {
            $result["data"] = array();
            $result["estado"] = false;
            return $result;
        }
    }

}
