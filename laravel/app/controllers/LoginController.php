<?php

class LoginController extends BaseController
{

    public function postLogin()
    {
        if (Request::ajax()) {

            $userdata= array(
                'usuario' => Input::get('usuario'),
                'password' => Input::get('password'),
            );

            if ( Auth::attempt($userdata, Input::get('remember', 0)) ) {
                //buscar los permisos de este usuario y guardarlos en sesion
                $query ="SELECT m.nombre as modulo, s.nombre as submodulo,
                        su.agregar, su.editar, su.eliminar,
                        CONCAT(m.path,'.', s.path) as path, m.icon
                        FROM modulos m 
                        JOIN submodulos s ON m.id=s.modulo_id
                        JOIN submodulo_usuario su ON s.id=su.submodulo_id
                        WHERE su.estado = 1 AND m.estado = 1 AND s.estado = 1
                        and su.usuario_id = ?
                        ORDER BY m.nombre, s.nombre ";

                $res = DB::select($query, array(Auth::id()));

                $menu = array();
                $accesos = array();
                foreach ($res as $data) {
                    $modulo = $data->modulo;
                    //$accesos[] = $data->path;
                    array_push($accesos, $data->path);
                    if (isset($menu[$modulo])) {
                        $menu[$modulo][] = $data;
                    } else {
                        $menu[$modulo] = array($data);
                    }
                }
                $usuario = Usuario::find(Auth::id());
                
                Session::set('language', 'Español');
                Session::set('language_id', 'es');
                Session::set('menu', $menu);
                Session::set('accesos', $accesos);
                Session::set('perfilId', $usuario['perfil_id']);
                
                Session::set("s_token", md5(uniqid(mt_rand(), true)));

                Lang::setLocale(Session::get('language_id'));

                return Response::json(
                    array(
                    'rst'=>'1',
                    'estado'=>Auth::user()->estado
                    )
                );
            } else {
            $m='<strong>Usuario</strong> y/o la <strong>contraseña</strong>';
                return Response::json(
                    array(
                    'rst'=>'2',
                    'msj'=>'El'. $m .'son incorrectos.',
                    )
                );
            }

        }

    }

    public function postImagen()
    {
        if (isset($_FILES['imagen']) and $_FILES['imagen']['size'] > 0) {

            $uploadFolder = 'img/user/' . md5('u' . Auth::user()->id);

            if ( !is_dir($uploadFolder) ) {
                mkdir($uploadFolder);
            }

            $nombreArchivo = $_FILES['imagen']['name'];
            $extArchivo = end((explode(".", $nombreArchivo)));
            $tmpArchivo = $_FILES['imagen']['tmp_name'];
            $archivoNuevo = "u".Auth::user()->id . "." . $extArchivo;
            $file = $uploadFolder . '/' . $archivoNuevo;

            @unlink($file);

            if (!move_uploaded_file($tmpArchivo, $file)) {
                $m='Ocurrio un error al subir el archivo. No pudo guardarse.';
                return Response::json(
                    array(
                    'upload' => FALSE, 
                    'rst'     => '2',
                    'msj'      => $m,
                    'error'  => $_FILES['archivo'],
                    )
                );
            }

            $usuario = Usuario::find(Auth::user()->id);
            $usuario->imagen = $archivoNuevo;
            $usuario->save();

            return Response::json(
                array(
                    'rst'        => '1',
                    'msj'        => 'Imagen subida correctamente',
                    'imagen'    => $file,
                    'upload'     => TRUE, 
                    'data'         => "OK",
                    )
            );

        }
    }

}