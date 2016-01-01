<?php
//controller
class PermisoEventosController extends \BaseController
{
    
    public function postCargar()
    {
        //si la peticion es ajax
        if (Request::ajax()) {
            $tipopersona = Input::get('tipo_persona');
            if ($tipopersona==1) {
                $permisoeventos = PermisoEventos::getCargarPersonas();
            } else {
                $permisoeventos = PermisoEventos::getCargarPersonasTecnicos();
            }    
            return Response::json(array('rst'=>1,'datos'=>$permisoeventos));
        }
    }
    
    public function postEditar()
    {
        if ( Request::ajax() ) {
            
            $usurioid= Input::get('id');
            $tipopersona= Input::get('tipo_persona');

            $usuario['usuario_updated_at'] = Auth::user()->id;
            $usuario = Usuario::find(Auth::user()->id);
            $perfilId=$usuario['perfil_id'];

            
            $data2=array(
                    Auth::user()->id,
                    $usurioid,
                    $tipopersona
                    );
            $desactivardata=PermisoEventos::getDesactivarpermisos($data2);
            
            $metodo = Input::get('metodo');            
             for ($i=0; $i<count($metodo); $i++) {
                 
                 $metodoId = $metodo[$i];
                 
                 $data=array(
                    $metodoId,
                    $usurioid,
                    $tipopersona,
                    '2',
                    $usuario['usuario_updated_at'],
                    $usuario['usuario_updated_at']
                    );
                 
                 $datos=PermisoEventos::getAgregarEvento($data);
             }
             
             $consulta = Input::get('consulta');
             
             for ($i=0; $i<count($consulta); $i++) {
                 
                 $consultaId = $consulta[$i];
                 
                 $data=array(
                    $consultaId,
                    $usurioid,
                    $tipopersona,
                    '1',
                     $usuario['usuario_updated_at'],
                     $usuario['usuario_updated_at']
                    );
                 
                 $datos=PermisoEventos::getAgregarEvento($data);
             }
                return Response::json(
                    array('rst'=>1,
                        'msj'=>"Se modifico permisos exitosamente"
                        )
                );
            
  
                

        }
    }
    

}
