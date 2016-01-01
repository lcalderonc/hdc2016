<?php

class TecnicoController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('auth'); // bloqueo de acceso
    }

    /**
     * Store a newly created resource in storage.
     * POST /tecnico/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $quiebres = DB::table('tecnicos as t')
                ->leftJoin(
                    'empresas as e', 
                    function($join)
                    {
                        $join->on(
                            't.empresa_id', '=', 'e.id'
                        );
                    }
                )
                ->select(
                    't.id',
                    't.nombres',
                    't.ape_paterno',
                    't.ape_materno',
                    't.celular',
                    't.ninguno',
                    't.estado',
                    't.empresa_id',
                    'e.nombre as empresa',
                    DB::raw(
                        'ifnull(t.dni,"") as dni,
                        ifnull(t.carnet,"") as carnet,
                        ifnull(t.carnet_tmp,"") as carnet_tmp'
                    )
                )
                ->groupBy('t.id')
                ->orderBy('t.nombres', 'asc')
                ->get();

            return Response::json(array('rst'=>1,'datos'=>$quiebres));
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /tecnico/listar
     *
     * @return Response
     */
    public function postListar()
    {
        if ( Request::ajax() ) {
            if ( Input::get('empresa_id') ) {
            $actividad= 
                DB::table('tecnicos as t')
                ->join(
                    'celula_tecnico as ct', 
                    'ct.tecnico_id', '=', 't.id'
                )
                ->join(
                    'celulas as c', 
                    'c.id', '=', 'ct.celula_id'
                )
                ->select(
                    't.id', 't.nombre_tecnico as nombre',
                    DB::raw(
                        'CONCAT(
                            GROUP_CONCAT( CONCAT("C",ct.celula_id) 
                                SEPARATOR "|,|" 
                            ),"|,|",
                            GROUP_CONCAT( DISTINCT(CONCAT("E",c.empresa_id) )
                                SEPARATOR "|,|"
                            )
                        ) as relation'
                    ),
                    DB::raw(
                        'GROUP_CONCAT(
                            DISTINCT(CONCAT("C",ct.celula_id,
                                            "-",ct.officetrack
                                            )
                                    ) 
                                SEPARATOR "|,|"
                        ) AS evento'
                    )
                )
                ->where('t.estado', '=', '1')
                ->where('ct.estado', '=', '1')                
                ->where('c.empresa_id', '=', Input::get('empresa_id'))
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
                ->groupBy('t.id')
                ->orderBy('t.nombre_tecnico')
                ->get();
            } else {
            $actividad= 
                DB::table('tecnicos as t')
                ->join(
                    'celula_tecnico as ct', 
                    'ct.tecnico_id', '=', 't.id'
                )
                ->join(
                    'celulas as c', 
                    'c.id', '=', 'ct.celula_id'
                )
                ->select(
                    't.id', 't.nombre_tecnico as nombre',
                    DB::raw(
                        'CONCAT(
                            GROUP_CONCAT( CONCAT("C",ct.celula_id) 
                                SEPARATOR "|,|" 
                            ),"|,|",
                            GROUP_CONCAT( DISTINCT(CONCAT("E",c.empresa_id) )
                                SEPARATOR "|,|"
                            )
                        ) as relation'
                    )
                )
                ->where('t.estado', '=', '1')
                ->where('ct.estado', '=', '1')
                ->groupBy('t.id')
                ->orderBy('t.nombre_tecnico')
                ->get();
            }
            return Response::json(array('rst'=>1,'datos'=>$actividad));
        }
    }
    /**
     * obtener celulas por tecnico, para cargar en mantenimiento de tecnicos
     */
    public function postCargarcelulas()
    {
        $tecnicoId = Input::get('tecnico_id');
        $celulas = Tecnico::getCelulas($tecnicoId);
        return Response::json(array('rst'=>1,'datos'=>$celulas));
    }
    /**
     * Listar registro de celula_quiebre con estado 1
     * POST /tecnico/listarcelula
     *
     * @return Response
     */
    public function postListarcelula()
    {
        $tecnicoId = Input::get('tecnico_id');
        //si la peticion es ajax
        if (Request::ajax()) {

            $celulaTecnico = DB::table('celula_tecnico as ct')
                ->rightJoin(
                    'celulas as c', function($join) use ($tecnicoId)
                    {
                    $join->on('ct.celula_id', '=', 'c.id')
                    ->on('ct.tecnico_id', '=', DB::raw($tecnicoId));
                    }
                )
                ->where('c.estado', '=', 1)
            ->get(array('c.id', 'c.nombre', 'ct.estado'));

            return Response::json(array('rst' => 1, 'datos' => $celulaTecnico));
        }
    }

/**
     * Store a newly created resource in storage.
     * POST /tecnico/crear
     *
     * @return Response
     */
    public function postCrear()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
            $required='required';
            $numeric='numeric';

            $reglas = array(
                'ape_paterno' => $required.'|'.$regex,
                'ape_materno' => $required.'|'.$regex,
                'nombres' => $required.'|'.$regex,
                'dni' => $required.'|min:8',
                'carnet' => $required."|unique:tecnicos",
                'empresa' => $required.'|'.$numeric,
                'celulas_selec' => $required,
            );

            $mensaje= array(
                'required'  => ':attribute Es requerido',
                'regex'     => ':attribute Solo debe ser Texto',
                'numeric'   => ':attribute seleccione una opcion',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ($validator->fails()) {
                return Response::json(
                    array(
                    'rst'=>2,
                    'msj'=>$validator->messages(),
                    )
                );
            }
            $apeP = Input::get('ape_paterno');
            $apeM = Input::get('ape_materno');
            $nombres = Input::get('nombres');
            $carne = Input::get('carnet');

            $tecnicos = new Tecnico;
            $tecnicos['ape_paterno']= $apeP;
            $tecnicos['ape_materno']= $apeM;
            $tecnicos['nombres']= $nombres;
            $tecnicos['celular']= Input::get('celular');
            $tecnicos['nombre_tecnico'] = $apeP.' '.$apeM.' '.$nombres;
            $tecnicos['dni']= Input::get('dni');
            $tecnicos['carnet']= $carne;
            if (Input::has('carnet_tmp') && Input::get('carnet_tmp') <>'')
                $carneTmp = Input::get('carnet_tmp');
            else
                $carneTmp = $carne;

            $tecnicos['carnet_tmp'] = $carneTmp;
            $tecnicos['ninguno']= Input::get('ninguno',0);
            $tecnicos['estado']= Input::get('estado');
            $tecnicos['empresa_id']= Input::get('empresa');
            $tecnicos->save();
            $celulas=explode(',', Input::get('celulas_selec'));
            for ($i=0; $i<count($celulas); $i++) {
                $celulaId = $celulas[$i];
                $celula = Celula::find($celulaId);
                $officetrack = Input::get('officetrack'.$celulaId, 0);
                if ($officetrack === 'on')
                    $officetrack = 1;
                
                $tecnicos->celulas()->save(
                    $celula,
                    array(
                        'estado'=>1,
                        'officetrack'=> $officetrack
                        )
                    );
            }
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro realizado correctamente',
                )
            );
        }
    }

    /**
     * actualizar los quiebres y actividades relacionadas
     * POST /tecnico/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if (Request::ajax()) {
            $tecnicoId = Input::get('id');
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú_-]{2,60})$/i';
            $required='required';
            $reglas = array(
                'ape_paterno' => $required.'|'.$regex,
                'ape_materno' => $required.'|'.$regex,
                'nombres' => $required.'|'.$regex,
                'dni'       => 'required|min:8',
                'carnet' => $required.'|unique:tecnicos,carnet,'.$tecnicoId,
                'empresa' => 'required|numeric',
                'celulas_selec' => $required,
            );

            $mensaje= array(
                'required'  => ':attribute Es requerido',
                'regex'     => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ($validator->fails()) {
                return Response::json(
                    array(
                    'rst'=>2,
                    'msj'=>$validator->messages(),
                    )
                );
            }
            $apeP = Input::get('ape_paterno');
            $apeM = Input::get('ape_materno');
            $nombres = Input::get('nombres');
            $carne = Input::get('carnet');
            //editando quiebre
            $tecnicos = Tecnico::find($tecnicoId);
            $tecnicos['ape_paterno']= $apeP;
            $tecnicos['ape_materno']= $apeM;
            $tecnicos['nombres']= $nombres;
            $tecnicos['celular']= Input::get('celular');
            $tecnicos['nombre_tecnico']= $apeP.' '.$apeM.' '.$nombres;
            $tecnicos['dni']= Input::get('dni');
            $tecnicos['carnet']= $carne;
            if (Input::has('carnet_tmp') && Input::get('carnet_tmp') <>'')
                $carneTmp = Input::get('carnet_tmp');
            else
                $carneTmp = $carne;
            
            $tecnicos['carnet_tmp']= $carneTmp;
            $tecnicos['ninguno']= Input::get('ninguno',0);
            $tecnicos['estado']= Input::get('estado');
            $tecnicos['empresa_id']= Input::get('empresa');
            $tecnicos->save();

            $celulas=explode(',', Input::get('celulas_selec'));
            //actulizando a estado 0 segun quiebre seleccionado
            DB::table('celula_tecnico')
                    ->where('tecnico_id', $tecnicoId)
                    ->update(array('estado' => 0, 'officetrack' => 0));

            //si estado de tecnico esta activo y selecciono celulas
            if (Input::get('estado') == 1 and !empty($celulas)) {

                for ($i=0; $i<count($celulas); $i++) {
                    $celulaId = $celulas[$i];
                    $celula = Celula::find($celulaId);
                    //buscando en la tabla
                    $celulaTecnico = DB::table('celula_tecnico')
                        ->where('tecnico_id', '=', $tecnicoId)
                        ->where('celula_id', '=', $celulaId)
                        ->first();
                    //officetrack
                    $officetrack = Input::get('officetrack'.$celulaId, 0);
                    if ($officetrack === 'on'){
                        $officetrack = 1;
                    }
                    if (is_null($celulaTecnico)) {
                        $tecnicos->celulas()->save(
                            $celula, 
                            array(
                                'estado' => 1,
                                'officetrack' => $officetrack
                            )
                        );
                    } else {
                        DB::table('celula_tecnico')
                            ->where('tecnico_id', '=', $tecnicoId)
                            ->where('celula_id', '=', $celulaId)
                            ->update(
                                array(
                                    'estado' => 1,
                                    'officetrack' => $officetrack
                                    )
                            );
                    }
                }

            }
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

    /**
     * Cambiar estado del registro de quiebre, ello implica cambiar el estado de
     * la tabla celula_quiebre, actividad_quiebre.
     * POST /tecnico/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {
        if (Request::ajax()) {
            $tecnico = Tecnico::find(Input::get('id'));
            $tecnico->estado = Input::get('estado');
            $tecnico->save();
            if (Input::get('estado') == 0) {
                DB::table('celula_tecnico')
                        ->where('tecnico_id', Input::get('id'))
                        ->update(array('estado' => 0));
            }
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

    public function postEstadoofficetrack()
    {
        $estado= Tecnico::getEstadoOfficetrack();
        return $estado;
    }

}
