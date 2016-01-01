<?php

class Thorario extends \Base
{
    public $table = "horarios_tipo";

    public static $where =['id', 'minutos', 'nombre', 'estado'];
    public static $selec =['id', 'minutos', 'nombre', 'estado'];
    /**
     * Usuario relationship
     */
    public function usuario()
    {
        return $this->hasMany('Usuario');
    }


    public static function Listar()
    {
        $horariostipo=DB::table("horarios_tipo")
                        ->where( 
                            function($query){
                                if ( Input::get('estado') ) {
                                    $query->where('estado','=','1');
                                }
                            }
                        )
                        ->get();

        return $horariostipo;
    }
}
