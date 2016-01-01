<?php 
class ActividadTipo extends Eloquent
{

    public $table = 'actividades_tipos';

    public static $where =['id', 'nombre', 'estado'];
    public static $selec =['id', 'nombre', 'estado'];

    public static function Listar(){
        $r = DB::table('actividades_tipos')
                        ->select(
                            'id', 
                            'nombre' 
                        )
                        ->where(
                            function($query)
                            {
                                if ( Input::has('estado') ) {
                                    $query->where(
                                        'estado','=',Input::has('estado')
                                    );
                                }
                            }
                        )
                        ->get();
        return $r;
    }

}
