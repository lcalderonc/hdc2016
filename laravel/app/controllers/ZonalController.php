<?php
class ZonalController extends \BaseController
{
    /**
     * Recepciona datos de Bandeja Controller
     *
     * @return type
     */
    public function postListar()
    {

        if ( Request::ajax() ) {
            if (Input::has('estado')) {
                $perfilId=Usuario::getPerfil();
                if ($perfilId=='8') //super admin
                    $z=Zonal::get(Input::all());
                elseif ( Input::has('mantenimiento') ) {
                    Input::replace(array('usuario' => Auth::user()->id));
                    $z =Zonal::getZonalM();
                } else {
                    Input::replace(array('usuario' => Auth::user()->id));
                    $z =Zonal::getZonal();
                 }

            } elseif (Input::has('usuario_id')) {
                $usuarioId= Input::get('usuario_id');
                $z=Zonal::getZonalUsuario($usuarioId);
            } else {
                $z =Zonal::getZonal();
            }
            return Response::json(
                array(
                    'rst' => 1,
                    'datos' => $z
                )
            );
        }
    }

}

