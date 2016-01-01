<?php

class Cupo extends \Eloquent
{
    public $table = "capacidad_horario_detalle";

    public static function getCupos($cupoId = '')
    {
        return DB::table('capacidad_horario as c')
                ->join(
                    'capacidad_horario_detalle as cd',
                    'c.id', '=', 'cd.capacidad_horario_id'
                )
                ->join('dias as d', 'cd.dia_id', '=', 'd.id')
                ->join('horarios as h', 'cd.horario_id', '=', 'h.id')
                ->join('horarios_tipo as ht', 'h.horario_tipo_id', '=', 'ht.id')
                ->join('empresas as e', 'c.empresa_id', '=', 'e.id')
                ->join('quiebre_grupos as q', 'c.quiebre_grupo_id', '=', 'q.id')
                ->join('zonales as z', 'c.zonal_id', '=', 'z.id')
                ->select(
                    'cd.capacidad_horario_id',
                    'cd.id',
                    'cd.capacidad',//
                    'cd.dia_id',
                    'd.nombre as dia',
                    'cd.horario_id',
                    'h.horario',
                    'h.hora_inicio',
                    'h.hora_fin',
                    'c.horario_tipo_id',
                    'c.empresa_id',
                    'e.nombre as empresa',
                    'c.quiebre_grupo_id',
                    'q.nombre as quiebre_grupo',
                    'c.zonal_id',
                    DB::raw('CONCAT(z.abreviatura,"|",z.id) as zonal_select'),
                    'z.nombre as zonal',
                    'cd.estado',
                    'ht.minutos'
                )
                ->where('c.estado', 1)
                ->where(
                        function($query) use ($cupoId)
                        {
                            if ( $cupoId!='' ) {
                                $query->where( 'cd.id', '=', $cupoId );
                            }
                        }
                    )
                //->where('cd.estado', 1)
                ->where('e.estado', 1)
                ->where('h.estado', 1)
                ->where('q.estado', 1)
                ->where('z.estado', 1)
                ->get();
    }
    public static function getIdCupo($empresa,$zonal,$quiebregrupos,$horariotipo)
    {
        return DB::table('capacidad_horario')
            ->select('id')
            ->where('empresa_id',$empresa)
            ->where('zonal_id',$zonal)
            ->where('quiebre_grupo_id',$quiebregrupos)
            ->where('horario_tipo_id',$horariotipo)
            ->where('estado',1)
            ->first();
    }
    public static function updateCupo($id,$capacidad,$estado)
    {
        DB::table('capacidad_horario_detalle')
            ->where('id', $id)
            ->update(
                array(
                    'capacidad' => $capacidad,
                    'estado' => $estado,
                    'usuario_updated_at' => Auth::user()->id
                    )
                );
    }
    public static function updateEstadoCupo($id,$estado)
    {
        DB::table('capacidad_horario_detalle')
            ->where('id', $id)
            ->update(
                array(
                    'estado' => $estado,
                    'usuario_updated_at' => Auth::user()->id
                    )
                );
    }
    public static function createCupo($id,$horario,$dia,$capacidad,$estado)
    {
        $cupo = new Cupo();
        $cupo['capacidad_horario_id']=$id;
        $cupo['dia_id']=$dia;
        $cupo['horario_id']=$horario;
        $cupo['capacidad']=$capacidad;
        $cupo['estado']=$estado;
        $cupo['usuario_created_at']=Auth::user()->id;
        $cupo->save();
        return $cupo;
        /*DB::table('capacidad_horario_detalle')->insert(
            array(
                'capacidad_horario_id' => $id, 
                'dia_id' => $dia,
                'horario_id' => $horario,
                'capacidad' => $capacidad,
                'estado' => $estado,
                'usuario_created_at' => Auth::user()->id
            )
        );*/

    }
    /**
     * crear registros en  capacidad_horario
     */
    public static function createCupoHead($empresa,$zonal,$quiebregrupos,$horariotipo)
    {
        $query="INSERT INTO capacidad_horario (empresa_id, zonal_id, quiebre_grupo_id, horario_tipo_id, estado, usuario_created_at)
                VALUES (?,?,?,?,?,?)";
        DB::insert($query, array(
            $empresa, $zonal, $quiebregrupos, $horariotipo, 1 ,Auth::user()->id
            ));
    }
}
