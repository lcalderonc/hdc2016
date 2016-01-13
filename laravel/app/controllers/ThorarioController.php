<?php
class ThorarioController extends BaseController
{
	//protected $_errorController;

    public function __construct(ErrorController $ErrorController){
        $this->error = $ErrorController;
        $this->beforeFilter('csrf_token', ['only' => ['postCrear', 'postEditar']]);
    }
    /**
     * cargar thorarios, mantenimiento
     * POST /area/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            try{
                $thorarios = Thorario::get(Input::all());
            }catch (Exception $error){
                $this->error->handlerError($error);
            }
            return Response::json(array('rst'=>1,'datos'=>$thorarios));
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
            $thorarios = Thorario::Listar();
            return Response::json(array('rst'=>1,'datos'=>$thorarios));
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
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
			$numeric='numeric';
            $reglas = array(
                /*'nombre' => $required.'|'.$regex,
				'minutos' => $required.'|'.$numeric,*/
            );

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
				'numeric'   => ':attribute valor numerico'
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

            $thorarios = new Thorario;
            $thorarios->minutos = Input::get('minutos');
            $thorarios->nombre = Input::get('nombre');
			$thorarios->estado = Input::get('estado');
            $thorarios->save();

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro realizado correctamente',
                )
            );
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
            $thorariosId= Input::get('id');
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
			$numeric='numeric';
            $reglas = array(
                //'nombre' => $required.'|'.$regex,
            );

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
				'numeric'   => ':attribute valor numerico'
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
            
            $thorarios = Thorario::find($thorariosId);
			$thorarios->minutos = Input::get('minutos');
            $thorarios->nombre = Input::get('nombre');
            $thorarios->estado = Input::get('estado');
            $thorarios->save();

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
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

            $thorarios = Thorario::find(Input::get('id'));
            $thorarios->estado = Input::get('estado');
            $thorarios->save();
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );    

        }
    }

}
