<?php

class EstadoMotivoSubmotivo extends \Eloquent
{
    public $table = "estado_motivo_submotivo";

    public static function get()
    {
        return DB::table('estado_motivo_submotivo as ems')
                ->join('estados as e', 'ems.estado_id', '=', 'e.id')
                ->join('motivos as m', 'ems.motivo_id', '=', 'm.id')
                ->join('submotivos as s', 'ems.submotivo_id', '=', 's.id')
                ->select(
                    'ems.id',
                    'ems.motivo_id',
                    'ems.submotivo_id',
                    'ems.estado_id',
                    'e.nombre as estados',
                    'e.estado as estado_estado',
                    'm.nombre as motivo',
                    'm.estado as estado_motivo',
                    's.nombre as submotivo',
                    's.estado as estado_submotivo',
                    'ems.req_tecnico',
                    'ems.req_horario',
                    'ems.estado',
                    'ems.created_at',
                    'ems.updated_at'
                )
                ->where('ems.id', '!=', 2)
                ->get();
    }

    public static function updateEstadomotivosubmotivo($id,$estado)
    {
        DB::table('estado_motivo_submotivo')
            ->where('id', $id)
            ->update(
                array(
                    'estado' => $estado,
                    'usuario_updated_at' => Auth::user()->id,
                    'updated_at' => date('Y-m-d h:i:s', time())
                    )
            );
    }



    public static function updateEstadoPorMotivo($idMotivo,$estado)
    {
        $query=DB::table('estado_motivo_submotivo as ems')
             ->leftJoin('estados as e', 'ems.estado_id', '=', 'e.id')
             ->leftJoin('submotivos as s', 'ems.submotivo_id', '=', 's.id')
             ->where('ems.motivo_id', $idMotivo);

        if ($estado == 1) { //si va a activarse, verifica sus dependencias
           $query->where('s.estado', 1)
                 ->where('e.estado', 1)
                 ->update(
                     array(
                     'ems.estado' => $estado,
                     'ems.usuario_updated_at' => Auth::user()->id,
                     'ems.updated_at' => date('Y-m-d h:i:s', time())
                     )
                 );
        } else {
            $query->update(
                array(
                    'ems.estado' => 0,
                    'ems.usuario_updated_at' => Auth::user()->id,
                    'ems.updated_at' => date('Y-m-d h:i:s', time())
                    )
            );
        }
    }
    public static function updateEstadoPorSubmotivo($idSubmotivo,$estado)
    {
        $query=DB::table('estado_motivo_submotivo as ems')
             ->join('motivos as m', 'ems.motivo_id', '=', 'm.id')
             ->join('estados as e', 'ems.estado_id', '=', 'e.id')
             ->join('submotivos as s', 'ems.submotivo_id', '=', 's.id')
            ->where('ems.submotivo_id', $idSubmotivo);

        if ($estado == 1) {
           $query->where('m.estado', 1)
                 ->where('s.estado', 1)
                 ->where('e.estado', 1);
        }
        $query->update(
            array(
                    'ems.estado' => $estado,
                    'ems.usuario_updated_at' => Auth::user()->id,
                    'ems.updated_at' => date('Y-m-d h:i:s', time())
                    )
        );
    }
    public static function updateEstadoPorEstado($idEstado,$estado)
    {
        $query=DB::table('estado_motivo_submotivo as ems')
             ->join('motivos as m', 'ems.motivo_id', '=', 'm.id')
             ->join('estados as e', 'ems.estado_id', '=', 'e.id')
             ->join('submotivos as s', 'ems.submotivo_id', '=', 's.id')
             ->where('ems.estado_id', $idEstado);

        if ($estado == 1) {
           $query->where('m.estado', 1)
                 ->where('s.estado', 1)
                 ->where('e.estado', 1);
        }
        $query->update(
            array(
                    'ems.estado' => $estado,
                    'ems.usuario_updated_at' => Auth::user()->id,
                    'ems.updated_at' => date('Y-m-d h:i:s', time())
                    )
        );
    }
   
}
