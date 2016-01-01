<?php

class UsuarioController extends BaseController
{
    /**
     * Constructor de la clase
     *
     */
    public function __construct()
    {
        $this->beforeFilter('auth'); // bloqueo de acceso
    }

    public function postValidaacceso() 
    {
        $hash = hash(
            'sha256', Config::get('wpsi.permisos.key')
            .Input::get('agregarG')
            .Input::get('editarG')
            .Input::get('eliminarG')
        );
        if ($hash == Input::get('hash')) {
            return json_encode(array('rst'=> 1));
        } else {
            return json_encode(array('rst'=> 2));
        }
    }
    /**
     * Mostrar los datos del contacto actual
     * POST /usuario/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            //$perfiId = Session::get('perfilId');
            $usuario = Usuario::find(Auth::user()->id);
            $perfilId=$usuario['perfil_id'];
            $usuarios =  DB::table('usuarios AS u')
                        ->join(
                            'empresas as e',
                            'u.empresa_id', '=', 'e.id'
                        )
                        ->join(
                            'perfiles as p',
                            'u.perfil_id', '=', 'p.id'
                        )
                        ->join(
                            'areas as a',
                            'u.area_id', '=', 'a.id'
                        )
                        ->select(
                            'u.id',
                            'u.nombre',
                            'u.apellido',
                            'u.usuario',
                            'u.password',
                            'u.dni',
                            'u.sexo',
                            'u.imagen',
                            'u.email',
                            'u.celular',
                            'u.estado',
                            'u.area_id',
                            'u.perfil_id',
                            'u.empresa_id',
                            'a.nombre as area',
                            'p.nombre as perfil',
                            'e.nombre as empresa'
                        );
            if ($perfilId!=8) {//super user
                $usuarios=$usuarios->where(
                    'usuario_created_at', Auth::user()->id
                );
            }
            $usuarios=$usuarios->get();

            return Response::json(array('rst' => 1, 'datos' => $usuarios));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /usuario/cargarsubmodulos
     *
     * @return Response
     */
    public function postCargarsubmodulos()
    {
////////////////////////
        $usuarioId = Input::get('usuario_id');
        $usuario = new Usuario;
        $submodulos = $usuario->getSubmodulos($usuarioId);
        return Response::json(array('rst'=>1,'datos'=>$submodulos));
    }

        public function postCargarprivilegios()
        {
////////////////////////
        $usuarioId = Input::get('usuario_id');
        $moduloId = Input::get('modulo_id');
        $usuario = new Usuario;
        $submodulos = $usuario->getPrivilegios($usuarioId, $moduloId);
        return Response::json(array('rst'=>1,'datos'=>$submodulos));
        }
    /**
     * Store a newly created resource in storage.
     * POST /usuario/crear
     *
     * @return Response
     */
    public function postCrear()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $numeric='numeric';

            $reglas = array(
                'nombre'    => $required.'|'.$regex,
                'apellido'    => $required.'|'.$regex,
                'usuario'   => $required.'|'.$regex.'|unique:usuarios',
                'password'  => 'required|min:6',
                'dni'       => 'required|min:8|unique:usuarios',
                //'email' => 'required|email',
                //'celular' => $required.'|'.$numeric,
                'perfil' => $required.'|'.$numeric,
                'empresa' => $required.'|'.$numeric,
                'area' => $required.'|'.$numeric,
                'sexo' => $required,
                'zonales_selec' => $required,
            );

            $mensaje = array(
                'required'  => ':attribute Es requerido',
                'regex'     => ':attribute Solo debe ser Texto',
                'numeric'   => ':attribute seleccione una opcion',
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

            $usuarios = new Usuario();
            $usuarios['nombre'] = Input::get('nombre');
            $usuarios['apellido'] = Input::get('apellido');
            $usuarios['usuario'] = Input::get('usuario');
            $usuarios['password'] = Hash::make(Input::get('password'));
            $usuarios['dni'] = Input::get('dni');
            $usuarios['email'] = Input::get('email');
            $usuarios['celular'] = Input::get('celular');
            $usuarios['perfil_id'] = Input::get('perfil');
            $usuarios['sexo'] = Input::get('sexo');
            $usuarios['empresa_id'] = Input::get('empresa');
            $usuarios['area_id'] = Input::get('area');
            $usuarios['estado'] = Input::get('estado');
            $usuarios['usuario_created_at'] = Auth::user()->id;
            $usuarios->save();

            $empresas=Input::get('empresas');
            $modulos = Input::get('modulos_selec');
            //$zonales = Input::get('zonales_selec');
            $zonales=explode(',', Input::get('zonales_selec'));
            $pertenece = Input::get('pertenece');
            $estado = 0;
            for ($i=0; $i<count($zonales); $i++) {
                $zonalId = $zonales[$i];
                $zonal = Zonal::find($zonalId);
                //$pertenece = Input::get('pertenece'.$zonalId, 0);
                if ($pertenece == $zonalId)
                    $estado = 1;
                else
                    $estado = 0;
                $usuarios->zonales()->save(
                    $zonal,
                    array(
                        'estado'=>1,
                        'pertenece'=> $estado
                        )
                );
            }
            $quiebregrupos=Input::get('quiebregrupos');

            $estado = Input::get('estado');
            if ($estado == 0 ) {
                return Response::json(
                    array(
                    'rst'=>1,
                    'msj'=>'Registro actualizado correctamente',
                    )
                );
            }
            if ($modulos) {//si selecciono algun modulo
                $modulos = explode(',', $modulos);
                $submodulos = array();
                $submoduloId = array();
                $privilegio=array();
                $insert=array();
                for ($i=0; $i<count($modulos); $i++) {
                    $moduloId = $modulos[$i];
                    //almacenar las submodulo seleccionadas
                    $submodulos[] = Input::get('submodulos'.$moduloId);
                }
                for ($i=0; $i<count($submodulos); $i++) {
                    for ($j=0; $j <count($submodulos[$i]); $j++) {
                        //buscar la submodulo en ls BD
                        $submoduloId[] = $submodulos[$i][$j];
                    }
                }

                for ($i=0; $i<count($submoduloId); $i++) {
                    $submodulo = Submodulo::find($submoduloId[$i]);
                    /**/
                    if (Input::has('privilegio'.$submoduloId[$i])) {
                        $privilegio[] = Input::get(
                            'privilegio'.$submoduloId[$i]
                        );
                        $permisos = '';
                    
                        for ($k=0; $k<count($privilegio); $k++) {
                             $permisos = '';
                             for ($l=0; $l<count($privilegio[$k]); $l++) {
                                if ($privilegio[$k][$l] == 1) {
                                    //permisos de agregar
                                    $permisos .= 'a';
                                }
                                if ($privilegio[$k][$l] == 2) {
                                     //tiene permisos de agregar y editar
                                    $permisos .= 'b';
                                }
                                if ($privilegio[$k][$l] == 3) {
                                    //solo tiene permisos de eliminar
                                    $permisos .= 'c';
                                }
                             }
                        }
                        switch ($permisos) {
                        case 'abc': 
                            $insert =  array(
                                'estado' => 1, 'agregar' => 1, 
                                'editar' => 1, 'eliminar' => 1, 
                                'usuario_created_at' => Auth::user()->id,
                                'created_at' => date('Y-m-d h:i:s', time()));
                            break;
                        case 'ab':
                            $insert =  array(
                                'estado' => 1, 'agregar' => 1, 
                                'editar' => 1, 'eliminar' => 0, 
                                'usuario_created_at' => Auth::user()->id,
                                'created_at' => date('Y-m-d h:i:s', time()));
                            break;
                        case 'ac':
                            $insert =  array(
                                'estado' => 1, 'agregar' => 1, 
                                'editar' => 0, 'eliminar' => 1, 
                                'usuario_created_at' => Auth::user()->id,
                                'created_at' => date('Y-m-d h:i:s', time()));
                            break;
                        case 'bc':
                            $insert =  array(
                                'estado' => 1, 'agregar' => 0, 
                                'editar' => 1, 'eliminar' => 1, 
                                'usuario_created_at' => Auth::user()->id,
                                'created_at' => date('Y-m-d h:i:s', time()));
                            break;
                        case 'a':
                            $insert =  array(
                                'estado' => 1, 'agregar' => 1, 
                                'editar' => 0, 'eliminar' => 0, 
                                'usuario_created_at' => Auth::user()->id,
                                'created_at' => date('Y-m-d h:i:s', time()));
                            break;
                        case 'b':
                            $insert =  array(
                                'estado' => 1, 'agregar' => 0, 
                                'editar' => 1, 'eliminar' => 0, 
                                'usuario_created_at' => Auth::user()->id,
                                'created_at' => date('Y-m-d h:i:s', time()));
                            break;
                        case 'c':
                            $insert =  array(
                                'estado' => 1, 'agregar' => 0, 
                                'editar' => 0, 'eliminar' => 1, 
                                'usuario_created_at' => Auth::user()->id,
                                'created_at' => date('Y-m-d h:i:s', time()));
                            break;
                        case '':
                            $insert =  array(
                                'estado' => 1, 'agregar' => 0, 
                                'editar' => 0, 'eliminar' => 0, 
                                'usuario_created_at' => Auth::user()->id,
                                'created_at' => date('Y-m-d h:i:s', time()));
                            break;
                        }

                        $usuarios->submodulos()->save(
                            $submodulo, $insert
                        );
                    } else {
                          $usuarios->submodulos()->save(
                              $submodulo, array('estado' => 1)
                          );  
                    }//enf for
                }
            }
            for ($i=0; $i<count($empresas); $i++) {
                $empresaId = $empresas[$i];
                $empresa = Empresa::find($empresaId);
                $usuarios->empresas()->save($empresa, array('estado' => 1));
            }
            for ($i=0; $i<count($quiebregrupos); $i++) {
                $quiebreGrupoId = $quiebregrupos[$i];
                $quiebreGrupo = QuiebreGrupo::find($quiebreGrupoId);
                $usuarios->quiebregrupos()->save(
                    $quiebreGrupo, 
                    array('estado' => 1)
                );
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
     * POST /usuario/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if (Request::ajax()) {

            $usuarioId = Input::get('id');
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $numeric='numeric';

            $reglas = array(
                'nombre'    => $required.'|'.$regex,
                'apellido'    => $required.'|'.$regex,
                'usuario'   =>$regex.'|unique:usuarios,usuario,'.$usuarioId,
                //'password'  => 'required|min:6',
                'dni'       => 'required|min:8|unique:usuarios,dni,'.$usuarioId,
                //'email' => 'required|email',
                //'celular' => $required.'|'.$numeric,
                'perfil' => $required.'|'.$numeric,
                'empresa' => $required.'|'.$numeric,
                'area' => $required.'|'.$numeric,
                'sexo' => $required,
                'zonales_selec' => $required,
            );
            $mensaje = array(
                'required'  => ':attribute Es requerido',
                'regex'     => ':attribute Solo debe ser Texto',
                'numeric'   => ':attribute seleccione una opcion',
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
            $usuarios = Usuario::find($usuarioId);
            $usuarios['nombre'] = Input::get('nombre');
            $usuarios['apellido'] = Input::get('apellido');
            $usuarios['usuario'] = Input::get('usuario');
            if (Input::get('password')<>'')
                $usuarios['password'] = Hash::make(Input::get('password'));
            $usuarios['dni'] = Input::get('dni');
            $usuarios['email'] = Input::get('email');
            $usuarios['celular'] = Input::get('celular');
            $usuarios['perfil_id'] = Input::get('perfil');
            $usuarios['sexo'] = Input::get('sexo');
            $usuarios['empresa_id'] = Input::get('empresa');
            $usuarios['area_id'] = Input::get('area');
            $usuarios['estado'] = Input::get('estado');
            $usuarios['usuario_updated_at'] = Auth::user()->id;
            $usuarios->save();

            $empresas=Input::get('empresas');
            $submodulos=Input::get('submodulos');
            $quiebregrupos=Input::get('quiebregrupos');
            
            //empresas
            DB::table('empresa_usuario')
                    ->where('usuario_id', $usuarioId)
                    ->update(array('estado' => 0));

            //si estado de usuario esta activo y no selecciono nin gun quebre
            if (Input::get('estado') == 1 
                and $empresas<>'null' and $empresas<>'') {
                for ($i=0; $i<count($empresas); $i++) {
                    $empresaId = $empresas[$i];
                    $empresa = Empresa::find($empresaId);
                    //buscando en la tabla
                    $empresaUsuario = array();
                    $empresaUsuario = DB::table('empresa_usuario')
                        ->where('empresa_id', '=', $empresaId)
                        ->where('usuario_id', '=', $usuarioId)
                        ->first();
                    if (is_null($empresaUsuario) 
                        and count($empresaUsuario)==0 ) {
                        $usuarios->empresas()->save(
                            $empresa, array('estado' => 1)
                        );
                    } else {
                        //update a la tabla empresa_usuario
                        DB::table('empresa_usuario')
                            ->where('empresa_id', '=', $empresaId)
                            ->where('usuario_id', '=', $usuarioId)
                            ->update(array('estado' => 1));
                    }
                }
            }
            //zonales
            $pertenece = Input::get('pertenece');
            $zonales=explode(',', Input::get('zonales_selec'));

            //actulizando a estado 0 segun quiebre seleccionado
            DB::table('usuario_zonal')
                    ->where('usuario_id', $usuarioId)
                    ->update(array('estado' => 0, 'pertenece' => 0));
            $estado = 0;
            //si estado de usuario esta activo y selecciono zonales
            if (Input::get('estado') == 1 and !empty($zonales)) {

                for ($i=0; $i<count($zonales); $i++) {
                    $zonalId = $zonales[$i];
                    $zonal = Zonal::find($zonalId);
                    //buscando en la tabla
                    $usuarioZonal = DB::table('usuario_zonal')
                        ->where('usuario_id', '=', $usuarioId)
                        ->where('zonal_id', '=', $zonalId)
                        ->first();
                    //pertenece
                    //$pertenece = Input::get('pertenece'.$zonalId, 0);
                    if ($pertenece == $zonalId)
                        $estado = 1;
                    else
                        $estado = 0;
                    if (is_null($usuarioZonal) and count($usuarioZonal)==0) {
                        $usuarios->zonales()->save(
                            $zonal,
                            array(
                                'estado' => 1,
                                'pertenece' => $estado
                            )
                        );
                    } else {
                        DB::table('usuario_zonal')
                            ->where('usuario_id', '=', $usuarioId)
                            ->where('zonal_id', '=', $zonalId)
                            ->update(
                                array(
                                    'estado' => 1,
                                    'pertenece' => $estado
                                    )
                            );
                    }
                }

            }
            //submodulos
            $modulos = Input::get('modulos_selec');
            DB::table('submodulo_usuario')
                    ->where('usuario_id', $usuarioId)
                    ->update(array('estado' => 0));

            if ($modulos) {//si selecciono algun menu

                $modulos = explode(',', $modulos);
                $submodulos=array();
                $update=array();

                for ($i=0; $i<count($modulos); $i++) {
                    $moduloId = $modulos[$i];
                    //almacenar las opciones seleccionadas
                    $submodulos[] = Input::get('submodulos'.$moduloId);
                }

                for ($i=0; $i<count($submodulos); $i++) {
                    for ($j=0; $j <count($submodulos[$i]); $j++) {
                        //buscar la opcion en ls BD
                        $submoduloId = $submodulos[$i][$j];
                        $permisos = '';
                        if (Input::has('privilegio'.$submoduloId)) {
                            $update[] = Input::get('privilegio'.$submoduloId);
                           
                            for ($k=0; $k<count($update); $k++) {
                                 $permisos = '';
                                 for ($l=0; $l<count($update[$k]); $l++) {
                                    if ($update[$k][$l] == 1) {
                                        //permisos de agregar
                                        $permisos .= 'a';
                                    }
                                    if ($update[$k][$l] == 2) {
                                         //permisos de agregar y editar
                                        $permisos .= 'b';
                                    }
                                    if ($update[$k][$l] == 3) {
                                        //permisos de eliminar
                                        $permisos .= 'c';
                                    }
                                 }
                            }
                        } 
                        
                        $submodulo = Submodulo::find($submoduloId);
                        $usuarioSubmodulo = array();
                        $usuarioSubmodulo = DB::table('submodulo_usuario')
                            ->where('usuario_id', '=', $usuarioId)
                            ->where('submodulo_id', '=', $submoduloId)
                            ->first();
                        //if (is_null($usuarioSubmodulo)) {
                        if (is_null($usuarioSubmodulo) 
                            and count($usuarioSubmodulo)==0 ) {
                            $usuarios->submodulos()->save(
                                $submodulo, array('estado' => 1)
                            );
                        } else {
                            //update a la tabla cargo_opcion
                            DB::table('submodulo_usuario')
                                ->where('usuario_id', '=', $usuarioId)
                                ->where('submodulo_id', '=', $submoduloId)
                                ->update(array('estado' => 1));
                        }
                        if (Input::has('privilegio'.$submoduloId)) {
                            Submodulo::updatePrivilegios(
                                $usuarioId, $submoduloId, $permisos
                            );
                        }
                    }
                }
            }

            //quiebregrupos
            DB::table('quiebre_grupo_usuario')
                    ->where('usuario_id', $usuarioId)
                    ->update(array('estado' => 0));
            //restriccion de quiebres
            DB::table('quiebre_usuario_restringido')
                    ->where('usuario_id', $usuarioId)
                    ->update(array('estado' => 0));
            //si estado de usuario esta activo y no selecciono nin gun quebre
            if (Input::get('estado') == 1 
                and $quiebregrupos<>'null' and $quiebregrupos<>'') {
                for ($i=0; $i<count($quiebregrupos); $i++) {
                    $quiebreGrupoId = $quiebregrupos[$i];
                    //restriccion de quiebres
                    $quiebres=Input::get('quiebres'.$quiebreGrupoId);
                    //buscar registros
                    //
                    for ($k=0; $k<count($quiebres); $k++) {
                        $quiebreId = $quiebres[$k];
                        $row = DB::table('quiebre_usuario_restringido')
                                ->where('usuario_id', $usuarioId)
                                ->where('quiebre_id', $quiebreId)
                                ->first();
                        if (count($row)>0) {
                            DB::table('quiebre_usuario_restringido')
                                ->where('usuario_id', $usuarioId)
                                ->where('quiebre_id', $quiebreId)
                                ->update(array('estado' => 1));
                        } else {
                            DB::table('quiebre_usuario_restringido')
                                ->insert(
                                    array(
                                        'usuario_id' => $usuarioId,
                                        'quiebre_id' => $quiebreId,
                                    )
                                );
                        }
                    }

                    $quiebreGrupo = QuiebreGrupo::find($quiebreGrupoId);
                    //buscando en la tabla
                    $quiebreGrupoUsuario=array();
                    $quiebreGrupoUsuario = DB::table('quiebre_grupo_usuario')
                        ->where('quiebre_grupo_id', '=', $quiebreGrupoId)
                        ->where('usuario_id', '=', $usuarioId)
                        ->first();
                    if (is_null($quiebreGrupoUsuario) 
                        and count($quiebreGrupoUsuario)==0 ) {
                    //if (is_null($quiebreGrupoUsuario)) {
                        $usuarios->quiebregrupos()->save(
                            $quiebreGrupo, array('estado' => 1)
                        );
                    } else {
                        //update a la tabla quiebre_grupo_usuario
                        DB::table('quiebre_grupo_usuario')
                            ->where('quiebre_grupo_id', '=', $quiebreGrupoId)
                            ->where('usuario_id', '=', $usuarioId)
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
     * Cambiar estado del registro de usuario, ello implica cambiar el estado de
     * la tabla empresa_usuario, quiebre_grupo_usuario, submodulo_usuario.
     * POST /usuario/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {
        if (Request::ajax()) {
            $estado = Input::get('estado');
            $usuario = Usuario::find(Input::get('id'));
            $usuario['estado'] = Input::get('estado');
            $usuario['usuario_updated_at'] = Auth::user()->id;
            $usuario->save();

            if ($estado == 0) {
                DB::table('empresa_usuario')
                        ->where('usuario_id', Input::get('id'))
                        ->update(array('estado' => 0));
                DB::table('submodulo_usuario')
                        ->where('usuario_id', Input::get('id'))
                        ->update(array('estado' => 0));
                DB::table('quiebre_grupo_usuario')
                        ->where('usuario_id', Input::get('id'))
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
    /**
     * Mostrar los datos del contacto actual
     * POST /usuario/misdatos
     *
     * @return Response
     */
    public function postMisdatos()
    {
        if (Request::ajax()) {
            $reglas = array(
                'password'      => 'required|min:6',
                'newpassword'   => 'min:6'
            );

            $validator = Validator::make(Input::all(), $reglas);

            if ($validator->fails()) {
                $final='';
                $datosfinal=array();
                $msj=(array) $validator->messages();

                foreach ($msj as $key => $value) {
                    $datos=$msj[$key];
                    foreach ($datos as $keyI => $valueI) {
                        $datosfinal[$keyI]=str_replace(
                            $keyI, trans('greetings.'.$keyI), $valueI
                        );
                    }
                    break;
                }

                return Response::json(
                    array(
                        'rst'=>2,
                        'msj'=>$datosfinal,
                    )
                );
            }

            $userdata= array(
                'usuario' => Auth::user()->usuario,
                'password' => Input::get('password'),
            );

            if ( Auth::attempt($userdata) ) {

                if ( Input::get('newpassword')!='' ) {
                $usuario = Usuario::find(Auth::user()->id);
                $usuario['password'] = Hash::make(Input::get('newpassword'));
                $usuario['usuario_updated_at'] = Auth::user()->id;
                $usuario->save();
                }

                return Response::json(
                    array(
                        'rst'=>1,
                        'msj'=>'Registro actualizado correctamente',
                    )
                );
            } else {
                return Response::json(
                    array(
                        'rst'=>2,
                        'msj'=>array('password'=>'Contraseña incorrecta'),
                    )
                );
            }
        }
    }

    /**
     * cargar contactos
     * POST /usuario/cargarcontactos
     *
     * @return Response
     */
    public function postCargarcontactos()
    {
        if (Request::ajax()) {
            $contactos = DB::select(
                'SELECT c.id,c.estado,
                IF(c.usuario_id = ? ,u2.email,u.email) email
                FROM `contactos` c
                INNER JOIN usuarios u ON u.id=c.usuario_id
                INNER JOIN usuarios u2 ON u2.id=c.usuario_id2
                WHERE c.usuario_id = ?
                OR c.usuario_id2 = ? ',
                array(Auth::user()->id,Auth::user()->id,Auth::user()->id)
            );
            return Response::json(array('rst'=>1,'datos'=>$contactos));
        }
    }

    /**
     * Display the specified resource.
     * POST /usuario/eliminarcontacto
     *
     * @param  int  $id
     * @return Response
     */
    public function postEliminarcontacto()
    {
        if (Request::ajax()) {
            $contacto = Contacto::find(Input::get('id'));
            $contacto->delete();

            return Response::json(
                array(
                    'rst'=>1,
                    'msj'=>'Registro eliminado correctamente',
                )
            );
        }
    }

    /**
     * crear nuevo contacto
     * POST /usuario/crearcontacto
     *
     * @return Response
     */
    public function postCrearcontacto()
    {
        $msj ='No se pudo realizar el envio de Email; Favor de verificar su';
        $msj.= ' email e intente nuevamente.';
        if (Request::ajax()) {
            $reglas = array(
                'email'         => 'required|email|exists:usuarios',
            );

            $validator = Validator::make(Input::all(), $reglas);

            if ($validator->fails()) {
                return Response::json(
                    array(
                        'rst'=>2,
                        'msj'=>$validator->messages(),
                    )
                );
            }

            $usuario = DB::table('usuarios')
                        ->where('email', '=', Input::get('email'))
                        ->first();

            $validaUsuario = DB::select(
                'select * from `contactos`
                where (`usuario_id` = ? and `usuario_id2` = ?)
                or (`usuario_id` = ? and `usuario_id2` = ?)',
                array(
                    Auth::user()->id,$usuario->id,$usuario->id,Auth::user()->id
                )
            );

            $urlValidacion=Hash::make(Input::get('email').date("Y-m-d"));

            if ( count($validaUsuario)==0 ) {
                DB::beginTransaction();
                $contacto = new Contacto;
                $contacto['usuario_id'] = Auth::user()->id;
                $contacto['usuario_id2']= $usuario->id;
                $contacto['url_validacion']=$urlValidacion;
                $contacto['estado'] = 0;
                $contacto->save();

                $parametros=array(
                    'email'    => Input::get('email'),
                    'email2'   => Auth::user()->email,
                    'hash'     => $urlValidacion,
                );

                try{
                    Mail::send(
                        'emailscontacto', $parametros,
                        function($message){
                            $message->to(
                                Input::get('email'),
                                ''
                            )->subject(
                                '.::Ubicame - Confirmación de contacto::.'
                            );
                        }
                    );

                    DB::commit();
                    return Response::json(
                        array(
                            'rst'=>1,
                            'msj'=>'Registro realizado correctamente',
                        )
                    );
                }
                catch(Exception $e){
                    DB::rollback();
                    return Response::json(
                        array(
                            'rst'=>2,
                            'msj'=>array($msj),
                        )
                    );
                    throw $e;
                }
            } else {
                return Response::json(
                    array(
                        'rst'=>2,
                        'msj'=>array(
                            'email'=>'Usuario ya existe como contacto'
                        ),
                    )
                );
            }
        }
    }
}