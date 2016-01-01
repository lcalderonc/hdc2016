<?php
class CambioDireccion extends Eloquent
{
    public $id;
    public function __construct()
    {
        $this->table = 'cambios_direcciones';
    }
    public function getCambiosDirecciones()
    {

        try {
            $cambios = DB::table('cambios_direcciones as cd')
                        ->join('tecnicos as te', 'te.id', '=', 'cd.usuario_id')
                        ->join(
                            'gestiones_detalles as gd',
                            'gd.gestion_id',
                            '=',
                            'cd.gestion_id'
                        )
                        ->join('quiebres as qu', 'qu.id', '=', 'gd.quiebre_id')
                        ->select(
                            'cd.gestion_id',
                            'te.nombre_tecnico as nombre_usuario',
                            'cd.coord_x',
                            'cd.coord_y',
                            'gd.codactu',
                            'gd.nombre_cliente',
                            'qu.nombre as quiebre',
                            'cd.tipo_usuario',
                            'cd.estado',
                            'cd.direccion',
                            'cd.referencia',
                            'cd.validacion',
                            DB::raw(
                                'IFNULL(cd.observacion,"") as observacion'
                            ),
                            'cd.id',
                            DB::raw(
                                'IFNULL(cd.created_at,"") as fecha_registro'
                            )
                        )
                        ->where('cd.tipo_usuario', 'tec')
                        ->get();

            return array(
                'rst'=>1,
                'datos'=>$cambios
            );

        } catch (Exception $exc) {
            return array(
                'rst'=>2,
                'error'=>$exc
            );
        }

    }

    public function getCambioDetalle()
    {

        try {
            $cambios = DB::table('cambios_direcciones as cd')
                        ->join('tecnicos as te', 'te.id', '=', 'cd.usuario_id')
                        ->join(
                            'gestiones_detalles as gd',
                            'gd.gestion_id',
                            '=',
                            'cd.gestion_id'
                        )
                        ->select(
                            'cd.gestion_id',
                            'te.nombre_tecnico as nombre_usuario',
                            'cd.coord_x',
                            'cd.coord_y',
                            'gd.codactu',
                            'gd.nombre_cliente',
                            'cd.tipo_usuario',
                            'cd.estado',
                            DB::raw(
                                'IFNULL(cd.created_at,"") as fecha_registro'
                            )
                        )
                        ->where('cd.id', $this->id)
                        ->get();

            return array(
                'rst'=>1,
                'datos'=>$cambios
            );

        } catch (Exception $exc) {
            return array(
                'rst'=>2,
                'error'=>$exc
            );
        }

    }

}