<?php 
class EstadoOfsc extends Eloquent
{

   // public $table = 'estados_ofsc';

   // public static $where =['id', 'nombre', 'estado'];
   // public static $selec =['id', 'nombre', 'estado'];

    public static function Listar()
    {
        $r = DB::table('estados_ofsc')
                        ->select(
                            'id', 
                            'nombre' 
                        )
                        ->where(
                            function($query)
                            {
                                if ( Input::has('estado') ) {
                                    $query->where(
                                        'estado', '=', Input::has('estado')
                                    );
                                }
                            }
                        )
                        ->get();
        return $r;
    }

}
