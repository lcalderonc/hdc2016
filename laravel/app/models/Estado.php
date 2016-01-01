<?php

class Estado extends \Eloquent
{
    public $table = "estados";

    public static function getEstadoAgendamiento()
    {
        $estado=    DB::table('estado_motivo_submotivo')
                    ->select(
                        DB::raw(
                            "CONCAT(req_tecnico,req_horario) AS estfinal"
                        )
                    )
                    ->where('motivo_id', '=', Input::get('motivo_id'))
                    ->where('submotivo_id', '=', Input::get('submotivo_id'))
                    ->where('estado_id', '=', Input::get('estado_id'))
                    ->first();
                    
        return $estado->estfinal;
    }

    public static function getEstados()
    {
        return DB::table('estados')
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

    public static function getAll()
    {
        return DB::table('estados')
                ->get();
    }

    public static function updateEstadoEstado($id,$estado)
    {
        DB::table('estados')
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
