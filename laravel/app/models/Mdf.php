<?php 
class Mdf extends Eloquent
{

    public $table = 'webpsi_fftt.mdfs_eecc_regiones';

    public static function getMdfAll()
    {
        $r  =   DB::table('webpsi_fftt.mdfs_eecc_regiones')
                  ->select(
                      'MDF as id',
                      DB::raw(
                          'CONCAT(MDF,": ",NOMBRE) as nombre'
                      )
                  )
                  ->orderBy('MDF', 'asc')
                  ->get();
        return $r;
    }

    public static function getMdfs($zonal)
    {
        $m  =   DB::table('webpsi_fftt.mdfs_eecc_regiones as m')
                  ->leftJoin('geo_mdfpunto as g', 'm.MDF', '=', 'g.mdf')
                  ->select(
                      'm.MDF AS nombre',
                      DB::raw(
                          'CONCAT(
                            IFNULL(m.MDF,""),"___",
                            IFNULL(
                              replace(
                                m.EECC_CRITICO,"LARI PLAYAS","LARI"
                              ), EECC
                            ) 
                            ,"   ",
                            IFNULL(
                              (
                                SELECT id
                                FROM empresas
                                WHERE nombre IN (
                                  IFNULL(
                                    replace(
                                      m.EECC_CRITICO,"LARI PLAYAS","LARI"
                                    ), EECC
                                  ) 
                                )
                              )
                              , ""
                            )
                            ,"___",
                            IFNULL(m.LEJANO,""),"___",
                            IFNULL(m.ZONA_CRITICO,"")
                        ) AS id'
                      ),
                      DB::raw("IFNULL(g.coord_x,'') AS coord_x"),
                      DB::raw("IFNULL(g.coord_y,'') AS coord_y")
                  )
                  ->where(
                      function($query) use($zonal) {
                          $query->where('m.zonal', '=', $zonal);
                      }
                  )
                  ->orderBy('m.MDF', 'asc')
                  ->get();
        return $m;
    }
    /**
     * cargar los nodos segun zonal
     */
    public static function getMdfCatv($zonal)
    {
        $m  =   DB::table('webpsi_fftt.nodos_eecc_regiones')
                  ->select(
                      'nodo AS nombre',
                      DB::raw(
                          'CONCAT(
                            IFNULL(nodo,""),"___",
                            IFNULL(
                              replace(
                                EECC_CRITICO,"LARI PLAYAS","LARI"
                              ), EECC
                            ) 
                            ,"   ",
                            IFNULL(
                              (
                                SELECT id
                                FROM empresas
                                WHERE nombre IN (
                                  IFNULL(
                                    replace(
                                        EECC_CRITICO,"LARI PLAYAS","LARI"
                                    ), EECC
                                  )
                                )
                              )
                              , ""
                            )
                            ,"___",
                            IFNULL(LEJANO,""),"___",
                            IFNULL(ZONA_CRITICO,"")
                        ) AS id'
                      )
                  )
                  ->where(
                      function($query) use($zonal) {
                          $query->where('zonal', '=', $zonal);
                      }
                  )
                  ->orderBy('nodo', 'asc')
                  ->get();
        return $m;
    }

    public static function getMdfzonal($zonal)
    {
        $r  =   DB::table('geo_mdf')
                  ->select(
                      'MDF as id',
                      DB::raw(
                          'MDF as nombre'
                      )
                  )
                  ->where(
                      function($query) use($zonal) {
                          $query->where('zonal', '=', $zonal);
                      }
                  )
                  ->groupBy('MDF')
                  ->orderBy('MDF', 'asc')
                  ->get();
        return $r;
    }

    public static function getCoordMdf($mdf)
    {
        $r  =   DB::table('geo_mdf')
                  ->select(
                      'MDF as id',
                      DB::raw("coord_y as lat "),
                      DB::raw("coord_x as lng ")
                  )
                  ->whereIn("mdf", $mdf)
                  ->orderBy('mdf', 'asc')
                  ->orderBy('orden', 'asc')
                  ->get();
        return $r;
    }
}
