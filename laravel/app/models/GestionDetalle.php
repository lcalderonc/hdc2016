<?php 
class GestionDetalle extends Eloquent
{

    protected $table = 'gestiones_detalles';
    
    public function gestion()
    {
        return $this->belongsTo('Gestion');
    }
    /**
     * buscar gestiones si son agendadas (antes de hoy),
     * que no sean liquidadas, ni canceladas
     */
    public static function getGestiones($averia)
    {
        $query = "SELECT gm.id, 
                    (select estado_legado
                    from ultimos_movimientos
                    where gestion_id=g.id) AS estadoLegado, 
                    gd.quiebre_id AS quiebreId, gd.empresa_id AS empresaId,
                    gm.estado_id AS estadoId, gm.fecha_agenda AS fechaAgenda,
                    e.nombre AS empresa, q.nombre AS quiebre
                    FROM
                    gestiones g 
                    JOIN gestiones_detalles gd ON g.id=gd.gestion_id
                    JOIN gestiones_movimientos gm  ON (g.id=gm.gestion_id 
                    AND gm.id IN (
                                SELECT MAX(gm2.id)
                                FROM gestiones_movimientos gm2
                                WHERE gm2.gestion_id=g.id
                                )
                    )
                    LEFT JOIN quiebres q ON gd.quiebre_id=q.id
                    LEFT JOIN empresas e ON gd.empresa_id=e.id
                    WHERE gd.codactu=? AND gm.estado = 1";
                    /*AND gm.estado_id not in ('4','6')
                    AND ( gm.fecha_agenda IS NULL
                    OR gm.fecha_agenda < CURDATE() )*/
        return DB::select($query, array( $averia ));
    }
    public static function getGestionesTemporales($averia)
    {
        $query = "SELECT quiebre , eecc_final as empresa, 'averia' as actividad
                    FROM webpsi_coc.averias_criticos_final
                    WHERE averia = ? 
                    UNION 
                    SELECT quiebre, eecc_final as empresa, 'provision' as actividad
                    FROM webpsi_coc.tmp_provision
                    WHERE codigo_req=?";
        return DB::select($query, array($averia,$averia));
    }
    public static function updateGestiones($averia,$gmId, $array, $estado='',$oldQuiebre='',$oldEmpresa='')
    {
        $observacion='';
        $usuariId =Auth::user()->id;
        //actualizar detalle
        DB::beginTransaction();
        $array1=$array;
        $array1['usuario_updated_at'] = $usuariId;
        try {
            DB::table('gestiones_detalles')
                ->where('codactu', $averia)
                ->update($array1);
        } catch (Exception $e) {
            DB::rollback();
            return 0;
        }
        //insertar un nuevo movimiento
        $gestionMovimiento = GestionMovimiento::find($gmId);
        $nuevoMovimiento = new GestionMovimiento;
        $nuevoMovimiento['gestion_id']=$gestionMovimiento['gestion_id'];
        $nuevoMovimiento['zonal_id']=$gestionMovimiento['zonal_id'];
        $nuevoMovimiento['coordinado']=$gestionMovimiento['coordinado'];
        $nuevoMovimiento['estado']=$gestionMovimiento['estado'];
        $nuevoMovimiento['created_at']=date("Y-m-d H:i:s");
        if (!empty($array['quiebre_id'])) {
            $nuevoMovimiento['quiebre_id']=$array['quiebre_id'];
            $observacion.=' Quiebre: "'.$oldQuiebre.'"';
        } else {
            $nuevoMovimiento['quiebre_id']=$gestionMovimiento['quiebre_id'];
        }
        if (!empty($array['empresa_id'])) {
            $nuevoMovimiento['empresa_id']=$array['empresa_id'];
            $observacion.=' Empresa: "'.$oldEmpresa.'"';
        } else {
            $nuevoMovimiento['empresa_id']=$gestionMovimiento['empresa_id'];
        }

        if ($estado=='agendado') {
            $nuevoMovimiento['estado_id']='7';
            $nuevoMovimiento['motivo_id']='2';
            $nuevoMovimiento['submotivo_id']='18';
        } else {
            $nuevoMovimiento['estado_id']=$gestionMovimiento['estado_id'];
            $nuevoMovimiento['motivo_id']=$gestionMovimiento['motivo_id'];
            $nuevoMovimiento['submotivo_id']=$gestionMovimiento['submotivo_id'];
            $nuevoMovimiento['horario_id']=$gestionMovimiento['horario_id'];
            $nuevoMovimiento['dia_id']=$gestionMovimiento['dia_id'];
            $nuevoMovimiento['celula_id']=$gestionMovimiento['celula_id'];
            $nuevoMovimiento['tecnico_id']=$gestionMovimiento['tecnico_id'];
            $nuevoMovimiento['fecha_agenda']=$gestionMovimiento['fecha_agenda'];
            $nuevoMovimiento['tecnicos_asignados']=$gestionMovimiento['tecnicos_asignados'];
            $nuevoMovimiento['fecha_consolidacion']=$gestionMovimiento['fecha_consolidacion'];
            $nuevoMovimiento['flag_tecnico']=$gestionMovimiento['flag_tecnico'];
            $nuevoMovimiento['coordinado']=$gestionMovimiento['coordinado'];
        }
        $nuevoMovimiento['submodulo_id']='20';
        $nuevoMovimiento['observacion']='actualiacion, de '.$observacion;
        $nuevoMovimiento['updated_at']=date("Y-m-d H:i:s");
        $nuevoMovimiento['usuario_created_at']=$usuariId;

        try {
            $nuevoMovimiento->save();
        } catch (Exception $e) {
            DB::rollback();
            return 0;
        }
        //actualizar ultimo movimiento
        
        $ultimo = UltimoMovimiento::Where('codactu',$averia)->get();
        //existe registro
        if (count($ultimo)>0) {
            $ultimoId = $ultimo[0]->id;
            $array3=$array;
            if ($estado=='agendado') {
                //actualzar fecha de agenda y estado
                $array3['estado_id'] = '7';
                $array3['motivo_id'] = '2';
                $array3['submotivo_id'] = '18';
                $array3['usuario_updated_at'] = $usuariId;
                $array3['fecha_agenda'] = '';
                $array3['estado_legado'] = 'PENDIENTE';
                $array3['usuario_created_at'] = Auth::user()->id;
                $array3['observacion'] = 'actualiacion, de Quiebre: "'.$oldQuiebre.'"" y Empresa: "'.$oldEmpresa.'"';
                $array3['updated_at'] = date("Y-m-d H:i:s");
            }
            try {
                DB::table('ultimos_movimientos')
                        ->where('id', $ultimoId)
                        ->update($array3);
            } catch (Exception $e) {
                DB::rollback();
                return 0;
            }
        }
        DB::commit();
        return 1;
    }
    public static function updateGestionesTemporales($actividad, $sql, $averia)
    {
        if ($actividad == "averia") {
            $sql="UPDATE webpsi_coc.averias_criticos_final
                    SET ".$sql.
                    "WHERE averia IN ('$averia')";
        } elseif ($actividad == "provision") {
            $sql="UPDATE webpsi_coc.tmp_provision
                    SET ".$sql.
                    "WHERE codigo_req IN ('$averia')";
        }
        return DB::update($sql);
    }
}
