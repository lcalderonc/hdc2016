<?php 
class Visorgps extends Eloquent
{
    
    public function strOrField($field, $array, $tmp=false)
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
    

    public function getBuscar($filtro)
    {
        $gstBuscar = false;
        $tmpBuscar = false;
        $tmpAver   = false;
        $tmpProv   = false;
        $tmpStr = "";
        $gstStr = "";
        
        $xdef = Config::get("wpsi.geo.default.lng");
        $ydef = Config::get("wpsi.geo.default.lat");
                
        /**
         * Datos por ID
         */

        //Actividad
        if ( isset($filtro["actividad"]) ) {

            foreach ($filtro["actividad"] as $actId) {
                if ($actId == 1) {
                    $tmpAver = true;
                }
                if ($actId == 2) {
                    $tmpProv = true;
                }
            }
            
            $gstStr .= $this->strOrField(
                "g.actividad_id", 
                $filtro["actividad"],
                false
            );
            $gstBuscar = true;
        }
        
        

        //Empresa
        if ( isset($filtro["empresa"]) ) {
            $empresa = DB::table("empresas")
                    ->whereIn("id", $filtro["empresa"])
                    ->get(array("nombre as valor"));

            $tmpStr .= $this->strOrField(
                "gd.eecc_final", 
                $empresa,
                true
            );
            $gstStr .= $this->strOrField(
                "gd.empresa_id", 
                $filtro["empresa"],
                false
            );
            $gstBuscar = true;
        }

        //Quiebre
        if ( isset($filtro["quiebre"]) ) {
            $quiebre = DB::table("quiebres")
                    ->whereIn("id", $filtro["quiebre"])
                    ->get(array("nombre as valor"));

            $tmpStr .= $this->strOrField(
                "gd.quiebre", 
                $quiebre,
                true
            );
            $gstStr .= $this->strOrField(
                "gd.quiebre_id", 
                $filtro["quiebre"],
                false
            );
            $gstBuscar = true;
        }
                
        //Celula
        if ( isset($filtro["celula"]) ) {
            $gstStr .= $this->strOrField(
                "ce.id", 
                $filtro["celula"],
                false
            );
            $gstBuscar = true;
        }
        
        //Estado
        if ( isset($filtro["estado"]) ) {
            $gstBuscar = true;
            if (array_search("-1", $filtro["estado"]) !== false) {
                $tmpBuscar = true;
            }
            
            $gstStr .= $this->strOrField(
                "gm.estado_id", 
                $filtro["estado"],
                false
            );
            
        }

        //CodActu
        $actuStr = "";
        if ( isset($filtro["codactu"]) ) {
                        
            $actuStr = $this->strOrField(
                "codactu", 
                $filtro["codactu"],
                false
            );
            
        }
        
        //Fecha Agenda
        $setFechaAgenda = "";
        if ( isset($filtro["fecha_agenda"]) ) {
            $setFechaAgenda =
            // "AND gm.fecha_agenda='{$filtro["fecha_agenda"]}'";
             '  AND gm.fecha_agenda 
                                BETWEEN "'.$filtro["fecha_agenda"][0].'" 
                                AND "'.$filtro["fecha_agenda"][1].'" ';
        }
        
        $gstStr = substr($gstStr, 4);
        
        $sql = "SELECT
                    * 
                FROM (  
                    ";
        
        if ($gstBuscar) {
            $sql .= "SELECT 
                        g.id, gd.codactu, q.apocope AS quiebre,
                        e.nombre AS empresa, 
                        IFNULL(gm.fecha_agenda,'') AS fecha_agenda,
                        IFNULL(t.nombre_tecnico,'') AS tecnico, 
                        es.nombre estado,
                        a.nombre AS actividad, gd.fecha_registro,
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
                        ) cierre_estado, 
                        ce.nombre celula, ce.id celula_id, 
                        gd.fftt, gd.tipo_averia tipoactu,
                        t.carnet, t.carnet_tmp,
                        gd.nombre_cliente, gd.direccion_instalacion,
                        gd.inscripcion codigo_cliente,
                        g.id_atc, gd.telefono,
                        gd.horas_averia horas_actu,
                        gm.coordinado, h.horario, 
                        q.id quiebre_id, e.id empresa_id,
            cg.grupo, t.id tecnico_id,
                        x, y
                
                    FROM 
                        gestiones g
                        INNER JOIN gestiones_detalles gd ON g.id=gd.gestion_id
                        INNER JOIN gestiones_movimientos gm 
                            ON (g.id=gm.gestion_id AND 
                                gm.id IN (  SELECT MAX(gm2.id)
                                            FROM gestiones_movimientos gm2
                                            WHERE gm2.gestion_id=g.id
                                         )
                               )
                        INNER JOIN actividades a ON a.id=g.actividad_id
                        INNER JOIN quiebres q ON q.id=gd.quiebre_id
                        INNER JOIN empresas e ON e.id=gd.empresa_id
                        INNER JOIN estados es ON es.id=gm.estado_id
                        LEFT JOIN horarios h ON h.id=gm.horario_id
                        LEFT JOIN tecnicos t ON t.id=gm.tecnico_id
                        LEFT JOIN celulas ce ON gm.celula_id=ce.id
                        LEFT JOIN webpsi_officetrack.tareas ta 
                            ON (ta.task_id=g.id AND 
                                ta.id IN (  SELECT MAX(ta2.id)
                                            FROM webpsi_officetrack.tareas ta2
                                            WHERE ta2.task_id=g.id
                                         )
                               )
                        LEFT JOIN celula_grupos cg 
                            ON cg.celula_id=gm.celula_id 
                                AND cg.tecnico_id=gm.tecnico_id
                    WHERE $gstStr $setFechaAgenda";
        }
        
        //if ($gstBuscar and $tmpBuscar) {
            //$sql .= "";
        //}

        
        if ($tmpBuscar and $tmpAver and empty($filtro["fecha_agenda"]) ) {
            
            if ($gstBuscar) {
                $sql .= " UNION ";
            }
            
            $sql .= "
                 
                SELECT 
                    '' AS id, gd.averia AS codactu, gd.quiebre,
                    gd.eecc_final AS empresa, '' AS fecha_agenda,
                    '' AS tecnico, 'Temporal' AS estado,
                    'Averia' AS actividad, gd.fecha_registro,
                    '1' AS existe,
                    '0' AS transmision,
                    '' AS cierre_estado, 
                    '' celula, '' celula_id, 
                    gd.fftt, gd.tipo_averia tipoactu,
                    '' carnet, '' carnet_tmp,
                    gd.nombre_cliente, gd.direccion_instalacion,
                    gd.inscripcion codigo_cliente,
                    '' id_atc, gd.telefono,
                    gd.horas_averia horas_actu,
                    '' coordinado, '' horario, 
                    q.id quiebre_id, e.id empresa_id,
            '' grupo, '' tecnico_id,
                    IF(gd.xcoord IS NULL OR gd.xcoord=0, $xdef, gd.xcoord) x, 
                    IF(gd.ycoord IS NULL OR gd.ycoord=0, $ydef, gd.ycoord) y
                FROM 
                    webpsi_coc.averias_criticos_final gd
                    LEFT JOIN 
                        webpsi_coc.averias_criticos_final_historico acfh
                        ON gd.averia=acfh.averia
                    LEFT JOIN 
                        psi.quiebres q 
                        ON q.apocope=gd.quiebre
                    LEFT JOIN 
                        psi.empresas e 
                        ON e.nombre=gd.eecc_final
                WHERE 
                    gd.averia NOT IN (
                        SELECT 
                            gd2.codactu 
            FROM 
                            psi.gestiones_detalles gd2
                    )
                $tmpStr";
            
        }

        if ($tmpBuscar and $tmpProv and empty($filtro["fecha_agenda"]) ) {
            
            if ($gstBuscar or $tmpAver) {
                $sql .= " UNION ";
            }
            
            $sql .= "
                
                SELECT 
                    '' AS id,gd.codigo_req AS codactu,gd.quiebre,
                    gd.eecc_final AS empresa,'' AS fecha_agenda,
                    '' AS tecnico,'Temporal' AS estado,
                    'Provision' AS actividad, gd.fecha_Reg AS fecha_registro,
                    '1' AS existe,
                    '0' AS transmision,
                    '' AS cierre_estado, 
                    '' celula, '' celula_id, 
                    gd.fftt, gd.origen tipoactu,
                    '' carnet, '' carnet_tmp,
                    gd.nomcliente, gd.direccion,
                    gd.codigo_del_cliente codigo_cliente,
                    '' id_atc, gd.telefono,
                    gd.horas_pedido horas_actu,
                    '' coordinado, '' horario, 
                    q.id quiebre_id, e.id empresa_id,
            '' grupo, '' tecnico_id,
                    IF(gd.xcoord IS NULL OR gd.xcoord=0, $xdef, gd.xcoord) x, 
                    IF(gd.ycoord IS NULL OR gd.ycoord=0, $ydef, gd.ycoord) y
                FROM 
                    webpsi_coc.tmp_provision gd
                    LEFT JOIN 
                        webpsi_coc.tmp_provision_historico tph 
                        ON (gd.codigo_req=tph.codigo_req)               
                    LEFT JOIN 
                        psi.quiebres q 
                        ON q.apocope=gd.quiebre
                    LEFT JOIN 
                        psi.empresas e 
                        ON e.nombre=gd.eecc_final
                WHERE 
                    gd.codigo_req NOT IN (
                        SELECT 
                            gd2.codactu 
                        FROM 
                            psi.gestiones_detalles gd2
                    )
                $tmpStr";
        }
                
                
        $sql .= "   ) q1 WHERE id IS NOT NULL";
        
        if ($actuStr != "") {
            $sql .= $actuStr;
        }

        $result = DB::select($sql);
        //echo $sql;
        return $result;
        //print_r($result) ;

    }
    
    
    public function getCelulaTecnico($celulaId)
    {
       
        $result = DB::table("tecnicos as t")
                    ->join("celula_tecnico as ct", "ct.tecnico_id", "=", "t.id")
                    ->where("ct.celula_id", "=", $celulaId)
                    ->where("t.estado", "=", "1")
                    ->select(
                        "t.nombre_tecnico", "t.carnet", 
                        "t.carnet_tmp", "t.dni", "t.id",
                        "ct.celula_id"
                    )
                    ->get();
        
        return $result;
    }
    
    
    public function getTecLocations($carnet, $date) 
    {        
        $sql = "SELECT
                    '' id, coord_x X, coord_y Y, numero MobileNumber, 
                    carnet EmployeeNum, bateria Battery, 
                    DATE_FORMAT(fecha_hora, '%Y-%m-%d %H:%i:%s') t,
                    DATE_FORMAT(fecha_hora, '%d/%m/%Y %H:%i:%s') tiempo
                FROM
                    webpsi_officetrack.ultimas_coordenadas
                WHERE
                    carnet='$carnet'
                    AND DATE(fecha_hora)='$date'";
        
        $result = DB::select($sql);

        return $result;
    }
    
    
    public function getPath($date, $code, $fromTime, $toTime)
    {
        
        try {
            $sql = "SELECT 
                    LastX X, LastY Y, MobileNumber, 
                    EmployeeNum, LastBattery Battery, 
                    DATE_FORMAT(TIMESTAMP, '%Y-%m-%d %H:%i:%s') t 
                FROM 
                    webpsi_officetrack.locations 
                WHERE 
                    DATE(TIMESTAMP)='$date' AND EmployeeNum='$code' 
                    AND DATE_FORMAT(TIMESTAMP, '%Y-%m-%d %H:%i:%s') 
                        >= '$fromTime'
                    AND DATE_FORMAT(TIMESTAMP, '%Y-%m-%d %H:%i:%s') 
                        <= '$toTime'
                ORDER BY t";
            
            $result["data"] = DB::select($sql);
            $result["estado"] = true;
            return $result;
        } catch (Exception $exc) {
            $result["data"] = array();
            $result["estado"] = false;
            return $result;
        }
            
    }
    
    
    public function getTecnicoGrupo($celula_id, $tecnico_id)
    {
        try {
            $sql = "SELECT 
                        grupo
                    FROM 
                        celula_grupos
                    WHERE 
                        celula_id = $celula_id
                        AND tecnico_id = $tecnico_id";
            
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