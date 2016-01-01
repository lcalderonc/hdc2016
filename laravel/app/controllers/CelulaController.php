<?php
class CelulaController extends \BaseController
{

    /**
     * Store a newly created resource in storage.
     * POST /celula/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $celulas =  DB::table('celulas AS c')
                        ->join(
                            'empresas as e',
                            'c.empresa_id', '=', 'e.id'
                        )
                        ->Leftjoin(
                            'zonales as z',
                            'c.zonal_id', '=', 'z.id'
                        )
                        ->select(
                            'c.id',
                            'c.nombre',
                            'c.estado',
                            'c.responsable',
                            'c.empresa_id',
                            'c.zonal_id',
                            'z.nombre as zonal',
                            'e.nombre as empresa'
                        )
                        ->get();

            return Response::json(array('rst' => 1, 'datos' => $celulas));
        }
    }

    /**
     * Listar registro de celulas con estado 1
     * POST /celula/listar
     *
     * @return Response
     */
    public function postListar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            if ( Input::get('empresa_id') ) {
                $empresaId=Input::get('empresa_id');
                $quiebreId=Input::get('quiebre_id');
                $celulas =  DB::table('celulas AS c')
                            ->join(
                                'celula_quiebre AS cq',
                                'c.id', '=', 'cq.celula_id'
                            )
                            ->select(
                                'c.id',
                                'c.nombre',
                                DB::raw(
                                    'CONCAT("E",c.empresa_id) as relation'
                                )
                            )
                            ->where('c.estado', '=', '1')
                            ->where('c.empresa_id', '=', $empresaId)
                            ->where('cq.estado', '=', '1')
                            ->where('cq.quiebre_id', '=', $quiebreId)
                            ->where(
                                function($query)
                                {
                                    if ( Input::get('zonal_id') 
                                    ) {
                                        $query->whereRaw(
                                            'c.zonal_id="'.Input::get('zonal_id').'"'
                                        );
                                    } 
                                }
                            )
                            ->orderBy('c.nombre')
                            ->get();
            } elseif (Input::get('tecnico_id')) {

                $tecnicoId = Input::get('tecnico_id');
                $celulas = DB::table('celula_tecnico as ct')
                            ->rightJoin(
                                'celulas as c', function($join) use ($tecnicoId)
                                {
                                $join->on('ct.celula_id', '=', 'c.id')
                                ->on('ct.tecnico_id', '=', DB::raw($tecnicoId));
                                }
                            )
                            ->select('c.id', 'c.nombre', 'ct.estado')
                            ->where('c.estado', '=', 1)
                            ->get();
            } else {
                $celulas =  DB::table('celulas')
                            ->select(
                                'id',
                                'nombre',
                                DB::raw(
                                    'CONCAT("E",empresa_id) as relation'
                                )
                            )
                            ->where('estado', '=', '1')
                            ->orderBy('nombre')
                            ->get();
            }

            return Response::json(array('rst' => 1, 'datos' => $celulas));
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /celula/crear
     *
     * @return Response
     */
    public function postCrear()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
          $regex='regex:/^([a-zA-Z01-9 .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
            $required='required';
            $numeric='numeric';
            $reglas = array(
                'nombre' => $required.'|'.$regex,
                'empresa' => $required.'|'.$numeric,
                'zonal' => $required.'|'.$numeric,
            );

            $mensaje = array(
                'required'  => ':attribute Es requerido',
                'regex'     => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ($validator->fails()) {
                return Response::json(
                    array(
                    'rst' => 2,
                    'msj' => $validator->messages(),
                    )
                );
            }

            $celulas = new Celula();
            $celulas['nombre'] = Input::get('nombre');
            $celulas['estado'] = Input::get('estado');
            $celulas['responsable'] = Input::get('responsable');
            $celulas['empresa_id'] = Input::get('empresa');
            $celulas['zonal_id'] = Input::get('zonal');
            $celulas->save();

            $quiebres = Input::get('quiebres');

            for ($i=0; $i<count($quiebres); $i++) {
                $quiebreId = $quiebres[$i];
                $quiebre = Celula::find($quiebreId);
                $celulas->quiebres()->save($quiebre, array('estado' => 1));
            }

            return Response::json(
                array(
                'rst' => 1,
                'msj' => 'Registro realizado correctamente',
                )
            );
        }
    }

    /**
     * Update the specified resource in storage.
     * POST /celula/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if (Request::ajax()) {
            $celulaId = Input::get('id');
          $regex='regex:/^([a-zA-Z01-9 .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
            $required='required';
            $numeric='numeric';
            $reglas = array(
                'nombre' => $required.'|'.$regex,
                'empresa' => $required.'|'.$numeric,
                'zonal' => $required.'|'.$numeric,
            );
            $mensaje = array(
                'required'  => ':attribute Es requerido',
                'regex'     => ':attribute Solo debe ser Texto',
            );
            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ($validator->fails()) {
                return Response::json(
                    array(
                    'rst' => 2,
                    'msj' => $validator->messages(),
                    )
                );
            }
            $celulas = Celula::find($celulaId);
            $celulas['nombre'] = Input::get('nombre');
            $celulas['estado'] = Input::get('estado');
            $celulas['responsable'] = Input::get('responsable');
            $celulas['empresa_id'] = Input::get('empresa');
            $celulas['zonal_id'] = Input::get('zonal');
            $celulas->save();
            //actualizando a estado 0 segun quiebre seleccionado
            DB::table('celula_quiebre')
                    ->where('celula_id', $celulaId)
                    ->update(array('estado' => 0));
            $quiebres = Input::get('quiebres');
            //si estado de celula esta activo y no selecciono ningun quiebre
            if (Input::get('estado') == 1 and !empty($quiebres)) {

                for ($i=0; $i<count($quiebres); $i++) {
                    $quiebreId = $quiebres[$i];
                    $quiebre = Quiebre::find($quiebreId);
                    //buscando en la tabla
                    $celulaQuiebre = DB::table('celula_quiebre')
                        ->where('quiebre_id', '=', $quiebreId)
                        ->where('celula_id', '=', $celulaId)
                        ->first();
                    if (is_null($celulaQuiebre)) {
                        $celulas->quiebres()->save(
                            $quiebre, array('estado' => 1)
                        );
                    } else {
                        //update a la tabla celula_quiebre
                        DB::table('celula_quiebre')
                            ->where('quiebre_id', '=', $quiebreId)
                            ->where('celula_id', '=', $celulaId)
                            ->update(array('estado' => 1));
                    }
                }
            }
            return Response::json(
                array(
                'rst' => 1,
                'msj' => 'Registro actualizado correctamente',
                )
            );
        }
    }

    /**
     * Cambiar estado del registro de celula, ello implica cambiar el estado de
     * la tabla celula_quiebre.
     * POST /celula/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {
        if (Request::ajax()) {
            $estado = Input::get('estado');
            $celula = Celula::find(Input::get('id'));
            $celula->estado = Input::get('estado');
            $celula->save();

            if ($estado == 0) {
                DB::table('celula_quiebre')
                        ->where('celula_id', Input::get('id'))
                        ->update(array('estado' => 0));
            }
            return Response::json(
                array(
                'rst' => 1,
                'msj' => 'Registro actualizado correctamente',
                )
            );
        }
    }
}
