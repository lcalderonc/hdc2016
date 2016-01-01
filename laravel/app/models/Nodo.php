<?php 
class Nodo extends Eloquent
{

    public $table = 'webpsi_fftt.nodos_eecc_regiones';

    public static function getNodoAll()
    {
        $r  =   DB::table('webpsi_fftt.nodos_eecc_regiones')
          ->select(
              'NODO as id',
              DB::raw(
                  'CONCAT(NODO,": ",NOMBRE,"->",PROVINCIA,"->",DPTO) as nombre'
              )
          )
                  ->orderBy('NODO', 'asc')
                  ->get();
        return $r;
    }

    public static function getNodozonal($zonal)
    {
        $r  =   DB::table('geo_nodopoligono')
                  ->select(
                      'nodo as id',
                      DB::raw(
                          'nodo as nombre'
                      )
                  )
                  ->where(
                      function($query) use($zonal) {
                          $query->where('zonal', '=', $zonal);
                      }
                  )
                  ->groupBy('nodo')
                  ->orderBy('nodo', 'asc')
                  ->get();
        return $r;
    }

    public static function getCoordNodo($nodo)
    {
        $r  =   DB::table('geo_nodopoligono')
                  ->select(
                      'nodo as id',
                      DB::raw("coord_y as lat "),
                      DB::raw("coord_x as lng ")
                  )
                  ->whereIn("nodo", $nodo)
                  ->orderBy('nodo', 'asc')
                  ->orderBy('orden', 'asc')
                  ->get();
        return $r;
    }
}
