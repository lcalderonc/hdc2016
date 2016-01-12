<?php

class AreaController extends BaseController
{

    public function __construct(ErrorController $ErrorController)
    {
        $this->error = $ErrorController;
        $this->beforeFilter('csrf_token', ['only' => ['postCrear', 'postEditar']]);
    }
    /**
     * cargar areas, mantenimiento
     * POST /area/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            try{
                $areas = Area::get(Input::all());
                $submodulo = DB::select(
                    'select id from submodulos where path = ?', array('area')
                ); 

            }catch (Exception $error){
                $this->error->handlerError($error);
            }
            return Response::json(array('rst'=>1,'datos'=>$areas));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /area/listar
     *
     * @return Response
     */
    public function postListar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $areas = Area::get(Input::all());
            return Response::json(array('rst'=>1,'datos'=>$areas));
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /area/crear
     *
     * @return Response
     */
    public function postCrear()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
//            if (Input::get('txt_token') != Session::get('s_token')) { 
//                die('token no valido!! Token Input: '.Input::get('txt_token').' Token Session: '.Session::get('s_token'));
//            } else {
                $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
                $required='required';
                $reglas = array(
                    'nombre' => $required.'|'.$regex,
                );

                $mensaje= array(
                    'required'    => ':attribute Es requerido',
                    'regex'        => ':attribute Solo debe ser Texto',
                );

                $validator = Validator::make(Input::all(), $reglas, $mensaje);

                if ( $validator->fails() ) {
                    return Response::json(
                        array(
                        'rst'=>2,
                        'msj'=>$validator->messages(),
                        )
                    );
                }

                $areas = new Area;
                $areas->nombre = Input::get('nombre');
                $areas->estado = Input::get('estado');
                $areas->save();

                return Response::json(
                    array(
                    'rst'=>1,
                    'msj'=>'Registro realizado correctamente',
                    )
                );
//            } 
        }
    }

    /**
     * Update the specified resource in storage.
     * POST /area/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if ( Request::ajax() ) {
//            if (Input::get('txt_token') != Session::get('s_token')) { 
//                die('token no valido!! Token Input: '.Input::get('txt_token').' Token Session: '.Session::get('s_token'));
//            } else {
                $areaId = Input::get('id');
                $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
                $required='required';
                $reglas = array(
                    'nombre' => $required.'|'.$regex.'|unique:areas,nombre,'.$areaId,
                );

                $mensaje= array(
                    'required'    => ':attribute Es requerido',
                    'regex'        => ':attribute Solo debe ser Texto',
                );

                $validator = Validator::make(Input::all(), $reglas, $mensaje);

                if ( $validator->fails() ) {
                    return Response::json(
                        array(
                        'rst'=>2,
                        'msj'=>$validator->messages(),
                        )
                    );
                }

                $areas = Area::find($areaId);
                $areas->nombre = Input::get('nombre');
                $areas->estado = Input::get('estado');
                $areas->save();

                return Response::json(
                    array(
                    'rst'=>1,
                    'msj'=>'Registro actualizado correctamente',
                    )
                );
//            }
        }
    }

    /**
     * Changed the specified resource from storage.
     * POST /area/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {

        if ( Request::ajax() ) {

            $area = Area::find(Input::get('id'));
            $area->estado = Input::get('estado');
            $area->save();
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );    

        }
    }

}
