<?php 
class Ubigeo extends Eloquent
{

    public $table = 'ubigeos';

    public static function getUbigeo()
    {
        $u  =   DB::table('ubigeos')
                  ->select(
                      'coddep', 'codpro', 'coddis', 'ubigeo', 
                      'nombre','nombre as id'
                  )
                  ->where(
                      function($query){
                          if ( Input::get('coddep')!='' ) {
                            $query->where('coddep', '=', Input::get('coddep'));
                          }

                          if ( Input::get('codpro')!='' ) {
                            $query->where('codpro', '=', Input::get('codpro'));
                          }
                      }
                  )
                  ->get();
        return $u;
    }
}
