<?php

class Motivo extends \Eloquent
{
    //protected $table = "actividades";

    public function __construct()
    {
         $this->table = "motivos";
    }
    /**
     * Quiebre relationship
     */
    public function quiebres()
    {
        return $this->belongsToMany('Quiebre');
    }

    public static function getMotivos()
    {
        return DB::table('motivos')
                ->select(
                    'id',
                    'nombre',
                    'estado',
                    'created_at',
                    'updated_at'
                )
                //->where('m.estado', 1)
                ->get();
    }

    public static function updateEstadoMotivo($id,$estado)
    {
        DB::table('motivos')
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
