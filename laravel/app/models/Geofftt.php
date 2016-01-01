<?php

class Geofftt extends Eloquent {
    
    public function getMdf($mdf)
    {
        $data = DB::table('geo_mdf')
                ->select('coord_x', 'coord_y')
                ->where('mdf', $mdf)
                ->orderBy('orden')
                ->get();
        return $data;
    }
    
    public function getArmario($data)
    {
        $data = DB::table('geo_armariopoligono')
                ->select('coord_x', 'coord_y')
                ->where('mdf', $data['mdf'])
                ->where('armario', $data['armario'])
                ->orderBy('orden')
                ->get();
        return $data;
    }
    
    public function getNodo($nodo)
    {
        $data = DB::table('geo_nodopoligono')
                ->select('coord_x', 'coord_y')
                ->where('nodo', $nodo)
                ->orderBy('orden')
                ->get();
        return $data;
    }
    
    public static function getTroba($data)
    {
        $data = DB::table('geo_troba')
                ->select('coord_x', 'coord_y')
                ->where('nodo', $data['nodo'])
                ->where('troba', $data['troba'])
                ->orderBy('orden')
                ->get();
        return $data;
    }

    public static function getTrobaUsu()
    {
        $data = DB::table('geo_troba as gt')
                ->join(
                    'zonales as z',
                    'z.abreviatura','=','gt.zonal'
                )
                ->join(
                    'usuario_zonal as uz',
                    'uz.zonal_id','=','z.id'
                )
                ->where('uz.usuario_id','=', Auth::user()->id)
                ->select('gt.zonal','gt.nodo','gt.troba','gt.coord_x', 'gt.coord_y',
                    DB::raw('gt.troba as id, gt.troba as nombre ')
                )
                ->groupBy('gt.troba')
                ->orderBy('gt.id')
                ->get();
        return $data;
    }

}
