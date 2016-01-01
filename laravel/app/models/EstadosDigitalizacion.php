<?php

class EstadosDigitalizacion extends \Eloquent
{
    //protected $table = "estados_digitalizacion";

    public function __construct()
    {
         $this->table = "estados_digitalizacion";
    }
    public static function getVistaEstadosDigitalizacion($rst)
    {
        $sql = "SELECT * FROM vistaEstadoDigitalizacion WHERE proyecto=?";
        return DB::select($sql, array($rst));
    }
    public static function getListaProyecto()
    {
        $sql = "SELECT DISTINCT proyecto as id, proyecto as nombre
                FROM vistaEstadoDigitalizacion";
        return DB::select($sql);
    }

    public static function getEstadosDigitalizacionCrear(array $data )
    {
        $sql = "INSERT INTO estados_digitalizacion_gestiones
                (observacion, estados_digitalizacion_id, usuario_created_at,
                estados_digitalizacion_motivos_id, estado, created_at)
                VALUES (?,?,?,?,?,now() )";
        return DB::insert($sql, $data);
    }
    public static function getEstadosDigitalizacionGestiones($id)
    {
        $sql = "SELECT g.id, g.observacion,g.created_at as fecha_movimiento,
                m.nombre AS motivo, CONCAT(u.nombre,' ', u.apellido) AS usuario
                FROM estados_digitalizacion_gestiones g 
                JOIN estados_digitalizacion_motivos m 
                ON g.estados_digitalizacion_motivos_id=m.id
                JOIN usuarios u ON g.usuario_created_at=u.id
                WHERE g.estado=1 AND m.estado=1
                AND g.estados_digitalizacion_id=?
                ORDER BY g.created_at DESC ";

        return DB::select($sql, array($id));
    }
    public static function getEstadosDigitalizacionMotivos()
    {
        $sql = "SELECT m.id, m.nombre, m.estado
                FROM estados_digitalizacion_motivos m
                WHERE estado=1";
        return DB::select($sql);
    }
    public static function getEstadosDigitalizacion($proyecto)
    {
        $sql ="SELECT e.cliente_cms, e.servicio_cms, e.id,
                IFNULL(e.nom_ape,'') as nom_ape, IFNULL(e.cond,'') as cond,
                IFNULL(e.orden,'') as orden, IFNULL(m.nombre,'') AS motivo,
                IFNULL(g.observacion,'') as observacion,
                IFNULL(m.id,'') AS ed_motivo_id,
                IFNULL(g.created_at,'') AS fecha_creacion
                FROM estados_digitalizacion e
                LEFT JOIN (   SELECT MAX(id) AS id , estados_digitalizacion_id
                        FROM estados_digitalizacion_gestiones 
                        WHERE estado=1 
                        GROUP BY estados_digitalizacion_id
                ) g2
                ON e.id=g2.estados_digitalizacion_id
                LEFT JOIN estados_digitalizacion_gestiones g ON g.id=g2.id
                LEFT JOIN estados_digitalizacion_motivos m
                ON m.id=g.estados_digitalizacion_motivos_id
                WHERE e.estado=1 AND e.proyecto=?";
        return DB::select($sql, array($proyecto));
    }
    public static function nuevo($row)
    {
        $estado = new EstadosDigitalizacion;
        $estado['proyecto'] = ($row[0]) ? $row[0] : '';
        $estado['cliente_cms'] = ($row[1]) ? $row[1] : '';
        $estado['servicio_cms'] = ($row[2]) ? $row[2] : '';
        $estado['nom_ape'] = ($row[3]) ? $row[3] : '';
        $estado['cond'] = ($row[4]) ? $row[4] : '';
        $estado['tipvia'] = ($row[5]) ? $row[5] : '';
        $estado['via'] = ($row[6]) ? $row[6] : '';
        $estado['num'] = ($row[7]) ? $row[7] : '';
        $estado['piso'] = ($row[8]) ? $row[8] : '';
        $estado['int'] = ($row[9]) ? $row[9] : '';
        $estado['mzn'] = ($row[10]) ? $row[10] : '';
        $estado['lot'] = ($row[11]) ? $row[11] : '';
        $estado['distrito'] = ($row[12]) ? $row[12] : '';
        $estado['urb'] = ($row[13]) ? $row[13] : '';
        $estado['etapa'] = ($row[14]) ? $row[14] : '';
        $estado['sect'] = ($row[15]) ? $row[15] : '';
        $estado['tel1'] = ($row[16]) ? $row[16] : '';
        $estado['tel2'] = ($row[17]) ? $row[17] : '';
        $estado['tel3'] = ($row[18]) ? $row[18] : '';
        $estado['tel1at'] = ($row[19]) ? $row[19] : '';
        $estado['tel2at'] = ($row[20]) ? $row[20] : '';
        $estado['nodo'] = ($row[21]) ? $row[21] : '';
        $estado['troba'] = ($row[22]) ? $row[22] : '';
        $estado['amp'] = ($row[23]) ? $row[23] : '';
        $estado['tap'] = ($row[24]) ? $row[24] : '';
        $estado['claserv'] = ($row[25]) ? $row[25] : '';
        $estado['Telefono'] = ($row[26]) ? $row[26] : '';
        $estado['tipodeco'] = ($row[27]) ? $row[27] : '';
        $estado['horario1'] = ($row[28]) ? $row[28] : '';
        $estado['horario2'] = ($row[29]) ? $row[29] : '';
        $estado['horario3'] = ($row[30]) ? $row[30] : '';
        $estado['usuario_created_at'] =  Auth::user()->id;
        
        $estado->save();
        
    }
}
