<?php

class Tecnico extends \Eloquent
{
    public $table = "tecnicos";

    /**
     * Celula relationship
     */
    public function celulas()
    {
        return $this->belongsToMany('Celula');
    }
    /**
     * Empresa relationship
     */
    public function empresas()
    {
        return $this->belongsTo('Empresa');
    }
    /**
     * obtener celulas por tecnico, para cargar en mantenimiento de tecnicos 
     */
    public static function getCelulas($tecnicoId)
    {
        $celulas = DB::table('celula_tecnico as ct')
                    ->join(
                        'celulas as c', 
                        'ct.celula_id', '=', 'c.id'
                    )
                    ->select(
                        'c.id',
                        'c.nombre',
                        DB::raw(
                            'IFNULL(ct.officetrack,0) as officetrack'
                        )
                    )
                    ->where('c.estado', '=', 1)
                    ->where('ct.estado', '=', 1)
                    ->where('ct.tecnico_id', '=', $tecnicoId)
                    ->get();
        return $celulas;
    }
    public static function getTecnico($empresaId,$celulaId)
    {
        $query = "SELECT id, nombre_tecnico, ape_paterno, ape_materno, carnet,
                         dni , IFNULL(g.grupos,0) grupos
                  FROM tecnicos t
                  LEFT JOIN (
                            SELECT  cg.tecnico_id, GROUP_CONCAT(cg.id) grupos
                            FROM celula_grupos cg where  celula_id=?
                            GROUP BY cg.tecnico_id
                  ) g ON t.id=g.tecnico_id 
                  WHERE t.carnet <> '' AND t.estado=1 AND empresa_id =?
                        ";
        
        return DB::select($query, array($celulaId,$empresaId));
        
    }

    public static function getEstadoOfficetrack()
    {

        $estado = DB::table('celula_tecnico')
                    ->select('officetrack')
                    ->where('tecnico_id', '=', Input::get('tecnico_id'))
                    ->where('celula_id', '=', Input::get('celula_id'))
                    ->where('estado', '=', '1')
                    ->first();

        return $estado->officetrack;
    }

    public static function getTecnicosOfficetrackAll()
    {
        return DB::table('tecnicos as t')
                ->join('celula_tecnico as ct', 't.id', '=', 'ct.tecnico_id')
                ->select(
                    't.id',
                    't.nombre_tecnico',
                    't.carnet',
                    't.carnet_tmp',
                    'ct.celula_id'
                )
                ->where('t.estado', '1')
                ->where('ct.officetrack', '1')
                ->groupby('t.id')
                ->get();
    }

    public static function asisTecnicos($fecha, $tecnicos)
    {
        $ot = Config::get("wpsi.schema.officetrack");
        $asistencia = DB::table('asistencia_entradas')->get();
        $sql ='';
        foreach ($asistencia as $asis) {
            $entrada = $asis->entrada;
            $id = $asis->id;

            $sql .= " ,(
                    select MAX(fecha_asistencia) 
                    from $ot.asistencia_tecnico at
                    WHERE at.numero_tecnico = t.carnet_tmp
                    and at.id_entrada = " . $id . "
                    and at.fecha_asistencia >= '$fecha' 
                    and at.fecha_asistencia < DATE_ADD('$fecha',INTERVAL 1 DAY)
                    ) ". $entrada;
        }

        $sql = "SELECT 
                    t.carnet_tmp carnet
                    ,c.nombre celula
                    ,CONCAT_WS(' ',t.ape_paterno, t.ape_materno, t.nombres) 
                        nombre
                    $sql,
                    IFNULL(lo.estado,'Inactivo') estado , lo.t
                FROM tecnicos t
                INNER JOIN celula_tecnico ct ON t.id=ct.tecnico_id
                INNER JOIN celulas c ON ct.celula_id=c.id
                LEFT JOIN (
                    SELECT  l.id, l.EmployeeNum carnet,
                        DATE_FORMAT(l.TIMESTAMP, '%Y-%m-%d %H:%i:%s') t,
                            IF(
                                DATE_ADD(
                                    DATE_FORMAT(
                                        l.TIMESTAMP, '%Y-%m-%d %H:%i:%s'
                                    )
                                    ,INTERVAL 1 HOUR
                                ) >= NOW(),
                                'Activo',
                                'Inactivo'
                            ) estado
                    FROM $ot.locations l
                    INNER JOIN (SELECT MAX(id) id 
                                FROM $ot.locations 
                                WHERE DATE(TIMESTAMP)='$fecha' 
                                GROUP BY EmployeeNum
                    ) mx ON l.id = mx.id
                ) lo ON t.carnet_tmp = lo.carnet
                WHERE t.estado=1 AND ct.estado = 1 /*AND ct.officetrack = 1*/
                AND t.id IN ($tecnicos) ";

        $reporte = DB::select($sql);

        return $reporte;
    }

    public static function asisTecnicoSRango(
        $fechaIni,
        $fechaFin,
        $tecnicos
    )
    {
        $ot = Config::get("wpsi.schema.officetrack");
        $sql = "";
        $ini = new DateTime($fechaIni);
        $fin = new DateTime($fechaFin);
        $interval = $ini->diff($fin);
        $cantDias = (int) $interval->format('%R%a');
        for ($i = 0; $i<= $cantDias; $i++) {
            $fechaNew = date(
                'Y-m-d',
                strtotime(
                    '+'.$i.' days',
                    strtotime($fechaIni)
                )
            );

            $sql.=" ,(
                    select GROUP_CONCAT(tipo_entrada,'(',fasis, ')' , '<br>')
                    from (
                        select  MIN(at.fecha_asistencia) fasis,
                                SUBSTRING(ae.entrada,1,1) tipo_entrada,
                                at.numero_tecnico
                        from $ot.asistencia_tecnico at
                        join $ot.asistencia_entradas ae
                                on ae.id = at.id_entrada
                        where  DATE(at.fecha_asistencia)  = '$fechaNew'
                        group by at.numero_tecnico, at.id_entrada
                    ) q
                    where q.numero_tecnico =  t.carnet
                    ) '$fechaNew' ";

        }

        $sql = "SELECT
                    t.carnet
                    ,t.nombre_tecnico
                    $sql
                from tecnicos t
                INNER JOIN celula_tecnico ct ON t.id=ct.tecnico_id
                WHERE t.estado=1 AND ct.estado = 1 AND ct.officetrack = 1
                AND t.id IN ($tecnicos)";

        $reporte = DB::select($sql);
        return $reporte;
    }
}
