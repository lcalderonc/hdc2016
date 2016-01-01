<?php

class Horario extends \Base
{
    public $table = "horarios";
    public static $where =['id', 'horario', 'hora_inicio', 'hora_fin','estado','horario_tipo_id'];
    public static $selec =['id', 'horario as nombre', 'hora_inicio', 'hora_fin','estado','horario_tipo_id'];

    public function usuario()
    {
        return $this->hasMany('Usuario');
    }
/*
    public static function Listar()
    {
        $horario=DB::table("horarios_tipo")
                        ->where( 
                            function($query){
                                if ( Input::get('estado') ) {
                                    $query->where('estado','=','1');
                                }
                            }
                        )
                        ->get();
        return $horario;
    }*/
}
