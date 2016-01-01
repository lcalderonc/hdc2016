<?php 
class Zonal extends Base
{

    public $table = 'zonales';
    public static $where =['id', 'nombre', 'abreviatura', 'estado'];
    public static $selec =['id', 'nombre', 'abreviatura', 'estado'];
    /**
     * Usuario relationship
     */
    public function usuarios()
    {
        return $this->belongsToMany('Usuario');
    }
    public static function getZonal()
    {
        $z  =   DB::table('zonales')
                  ->select(
                    'nombre',
                    DB::raw(
                      'CONCAT(abreviatura,"|",id) as id'
                    )
                  )
                  ->where( 'estado', '=', '1')
                  ->where(
                      function($query){
                          if( Input::get('usuario') ){
                            $query->whereRaw( ' id IN (SELECT zonal_id
                                                      FROM usuario_zonal
                                                      WHERE usuario_id="'.Auth::user()->id.'"
                                                      AND estado=1
                                                      )
                                              ');
                          }
                      }
                  )
                  ->get();
        return $z;
    }
    public static function getZonalM()
    {
        $z  =   DB::table('zonales')
                  ->select(
                    'nombre',
                    'id'
                  )
                  ->where( 'estado', '=', '1')
                  ->where(
                      function($query){
                          if( Input::get('usuario') ){
                            $query->whereRaw( ' id IN (SELECT zonal_id
                                                      FROM usuario_zonal
                                                      WHERE usuario_id="'.Auth::user()->id.'"
                                                      AND estado=1
                                                      )
                                              ');
                          }
                      }
                  )
                  ->get();
        return $z;
    }
    public static function getZonalUsuario( $usuarioId)
    {
        $z  =   DB::table('zonales as z')
                  ->join('usuario_zonal as uz','z.id','=','uz.zonal_id')
                  /*->leftJoin(
                    'usuario_zonal as uz', function($join) use ($usuarioId)
                        {
                        $join->on('z.id', '=', 'uz.zonal_id')
                        ->on('uz.usuario_id', '=', DB::raw($usuarioId));
                        }
                    )*/
                  ->select(
                    'z.nombre',
                    'z.id',
                    'uz.estado',
                    'uz.pertenece'
                  )
                  ->where( 'z.estado', '=', '1')
                  ->where( 'uz.estado', '=', '1')
                  ->where( 'uz.usuario_id', '=', $usuarioId)
                  ->get();
        return $z;
    }
}

