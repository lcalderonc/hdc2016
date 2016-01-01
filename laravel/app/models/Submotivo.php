<?php

class Submotivo extends \Eloquent
{
    //protected $table = "actividades";

    public function __construct()
    {
         $this->table = "submotivos";
    }


    public static function getSubmotivos()
    {
        return DB::table('submotivos')
                ->select(
                    'id',
                    'nombre',
                    'estado',
                    'created_at',
                    'updated_at'
                )
                //->where('estado', 1)
                ->get();
    }

    public static function updateEstadoSubmotivo($id,$estado)
    {
        DB::table('submotivos')
            ->where('id', $id)
            ->update(
                array(
                    'estado' => $estado,
                    'usuario_updated_at' => Auth::user()->id,
                    'updated_at' => date('Y-m-d h:i:s', time())
                    )
            );
    }
}
