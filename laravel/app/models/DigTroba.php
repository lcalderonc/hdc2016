<?php 
class DigTroba extends Base
{

    protected $table = 'dig_trobas';
    //public $timestamps = false;
    public static $where =[
                        'id', 'troba_id', 'can_clientes',
                        'fecha_inicio','fecha_fin', 'digitalizacion',
                        'est_seguim','fini_seguim', 'ffin_seguim',
                        'contrata', 'contrata_zona', 'obs'
                        ];
    public static $selec =[
                        'id', 'troba_id', 'can_clientes',
                        'fecha_inicio','fecha_fin', 'digitalizacion',
                        'est_seguim','fini_seguim', 'ffin_seguim',
                        'contrata', 'contrata_zona', 'obs'
                        ];
    public static function getTrobas()
    {
        $area=DB::table('dig_trobas as dt')
                //->join('zonales as z', 'dt.zonal_id', '=', 'z.id')
                ->leftJoin('empresas as e', 'dt.empresa_id', '=', 'e.id')
                ->leftJoin('empresas as e1', 'dt.contrata_zona', '=', 'e1.id')
                ->leftJoin('geo_trobapunto as g', 'dt.troba_id', '=', 'g.id')
                ->select(
                    'dt.id',
                    'g.nodo',
                    'g.troba',
                    'g.zonal',
                    'dt.empresa_id',
                    'dt.contrata_zona as contrata_zona_id',
                    DB::raw("IFNULL(dt.can_clientes,'0') as can_clientes"),
                    DB::raw(
                        "IF(
                            dt.fecha_inicio='0000-00-00',
                            '',
                            dt.fecha_inicio
                        ) as fecha_inicio"
                    ),
                    DB::raw(
                        "IF(
                            dt.fecha_planificacion='0000-00-00',
                            '',
                            dt.fecha_planificacion
                        ) as fecha_planificacion"
                    ),
                    DB::raw(
                        "IF(
                            dt.fecha_fin='0000-00-00',
                            '',
                            dt.fecha_fin
                        ) as fecha_fin"
                    ),
                    DB::raw("IFNULL(dt.digitalizacion,'') as digitalizacion"),
                    DB::raw("IFNULL(dt.est_seguim,'I') as est_seguim"),
                    'dt.fini_seguim',
                    'dt.ffin_seguim',
                    DB::raw("IFNULL(dt.obs,'') as obs"),
                    DB::raw("IFNULL(e.nombre,'') as contrata"),
                    DB::raw("IFNULL(e1.nombre,'') as contrata_zona")
                )
                //->where( 'dt.est_seguim','=', 'A' )
                //->where( 'z.estado','=', '1' )
                ->get();
                
        return $area;
    }
    public static function getZonal()
    {
        $query=DB::table('geo_trobapunto as g')
        ->select('g.zonal')
        ->groupby('g.zonal')
        ->get();
        return $query;
    }
    public static function getNodo($zonalId = '')
    {

        $query=DB::table('geo_trobapunto as g')
        ->select('g.nodo')
        ->where('zonal',$zonalId)
        ->groupby('g.nodo')
        ->get();
        return $query;
    }
    /**
     * utilizado en mantenimiento de trobas digitalizadas, registro manual
     */

    public static function getTroba($nodoId = '')
    {
        $query=DB::table('geo_trobapunto as g')
        ->select(
            'g.troba',
            'troba as id',
            'troba as nombre',
            DB::raw("IFNULL(coord_x,'') as coord_x"),
            DB::raw("IFNULL(coord_y,'') as coord_y"),
            DB::raw("'sin direccion' as direccion")
            )
        ->where('nodo',$nodoId)
        ->groupby('g.troba')
        ->get();
        return $query;
    }
    /**
     * registro manual
     */
    public static function getAmp($troba = '', $nodo = '')
    {
        $query=DB::table('geo_amplificador')
        ->select(
            'amplificador as id',
            'amplificador as nombre',
            DB::raw("IFNULL(coord_x,'') as coord_x"),
            DB::raw("IFNULL(coord_y,'') as coord_y"),
            DB::raw("'sin direccion' as direccion")
            )
        ->where('troba',$troba)
        ->where('nodo',$nodo)
        ->get();
        return $query;
    }
    /**
     * registro manual
     */
    public static function getTap($amp = '', $troba = '', $nodo = '')
    {
        $query=DB::table('geo_tap')
        ->select(
            'tap as id',
            'tap as nombre',
            DB::raw("IF(LENGTH(tap)=1,CONCAT('0',tap),tap) as orden"),
            DB::raw("IFNULL(coord_x,'') as coord_x"),
            DB::raw("IFNULL(coord_y,'') as coord_y"),
            DB::raw("IFNULL(REPLACE(direccion,'\"',''),'') as direccion")
            )
        ->where('amplificador',$amp)
        ->where('troba',$troba)
        ->where('nodo',$nodo)
        ->orderBy('orden')
        ->get();
        return $query;
    }
    /**
     * registro manual
     */
    public static function getCable($mdf = '')
    {
        $query=DB::table('geo_terminald')
        ->select(
            'cable as id',
            'cable as nombre',
            DB::raw("IFNULL(coord_x,'') as coord_x"),
            DB::raw("IFNULL(coord_y,'') as coord_y"),
            DB::raw("IFNULL(REPLACE(direccion,'\"',''),'') as direccion")
            )
        ->where('mdf',$mdf)
        ->groupby('cable')
        ->get();
        return $query;
    }
    /**
     * registro manual
     */
    public static function getArmario($mdf = '')
    {
        $query=DB::table('geo_terminalf')
        ->select(
            'armario as id',
            'armario as nombre',
            DB::raw("IFNULL(coord_x,'') as coord_x"),
            DB::raw("IFNULL(coord_y,'') as coord_y"),
            DB::raw("IFNULL(REPLACE(direccion,'\"',''),'') as direccion")
            )
        ->where('mdf',$mdf)
        ->groupby('armario')
        ->get();
        return $query;
    }
    /**
     * registro manual
     */
    public static function getTerminalCable($cable = '', $mdf = '')
    {
        $query=DB::table('geo_terminald')
        ->select(
            'terminald as id',
            'terminald as nombre',
            DB::raw("IFNULL(coord_x,'') as coord_x"),
            DB::raw("IFNULL(coord_y,'') as coord_y"),
            DB::raw("IFNULL(REPLACE(direccion,'\"',''),'') as direccion")
            )
        ->where('cable',$cable)
        ->where('mdf',$mdf)
        ->get();
        return $query;
    }
    /**
     * registro manual
     */
    public static function getTerminalArmario($armario = '', $mdf = '')
    {
        $query=DB::table('geo_terminalf')
        ->select(
            'terminalf as id',
            'terminalf as nombre',
            DB::raw("IFNULL(coord_x,'') as coord_x"),
            DB::raw("IFNULL(coord_y,'') as coord_y"),
            DB::raw("IFNULL(REPLACE(direccion,'\"',''),'') as direccion")
            )
        ->where('armario',$armario)
        ->where('mdf',$mdf)
        ->get();
        return $query;
    }    
}
