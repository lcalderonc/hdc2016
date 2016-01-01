<?php

class UbicacionController extends BaseController {

    /**
     * The layout that should be used for responses.
     */
    protected $layout = 'layouts.master';
    
    public function __construct()
    {
	$this->beforeFilter('auth'); // bloqueo de acceso
    }
    
    /**
     * Agrega ubicacion
     */
    public function addAddress()
    {
        //$this->layout = View::make('layouts.master');
        $this->layout->content = View::make('admin.newaddress');
    }    
    
    public function postSave()
    {
        if(Request::ajax())
        {                        
            $inputAll = Input::all();
            //Input::clear();
            foreach ($inputAll as $key=>$val) {
            	$key = str_replace(array("slct_", "txt_"), array("", ""), $key);
            	$inputAll[$key] = $val;
            }
            Input::merge($inputAll);
            
            /**
             *
             * Validacion
             *
             */
            $validacion = array(
                'x'=>array('required'),
	        'y'=>array('required'),
	        'nombre'=>array('required'),
	        //'codigo'=>array('unique:ubicaciones,codigo')	        
	    );
	    
	    $messages = array(
    		//'codigo.unique' => 'El c&oacute;digo ya existe.',
    		'x.required' => 'El campo longitud es obligatorio.',
    		'y.required' => 'El campo latitud es obligatorio.',
    		'nombre.required' => 'El campo nombre es obligatorio.'
	    );
	 
	    $validator = Validator::make(Input::all() , $validacion, $messages);
	 
	    if ($validator->fails())
	    {
	        $mensajes = $validator->messages();
	        foreach ($mensajes->all() as $mensaje){
	            return Response::json(array(
                                        'rst'=>2,
                                        'msj'=>$mensaje,
                    )); 
	        }   
	    }
                        
            /**
             *
             * Registro en DB
             *
             */           
            function saveData(){
                static $result = array();
                $codigo = rand(100000, 10000000);
                try {
	                $ubicacion = new Ubicacion;
	                $ubicacion->codigo = $codigo;
	                $ubicacion->usuario_id = Auth::user()->id;
	                $ubicacion->categoria_id = Input::get('categoria');
	                $ubicacion->tipo_acceso_id = Input::get('tipo');
	                $ubicacion->x = Input::get('x');
	                $ubicacion->y = Input::get('y');
	                $ubicacion->nombre = Input::get('nombre');
	                $ubicacion->descripcion = Input::get('descripcion');
	                $ubicacion->etiquetas = Input::get('etiquetas');
	                $ubicacion->contacto = Input::get('contacto');
	                $ubicacion->estado = 1;
	                $ubicacion->vigencia = Input::get('date') . " " . Input::get('time');
	                $ubicacion->usuario_creacion = Auth::user()->id;
	                $ubicacion->save();
	                
	                $result['flag'] = true;
	                $result['msg'] = $ubicacion->id;
	            } catch (Exception $err) {
	                //return $err->getCode() . " / " . $err->getMessage();
	                if ($err->getCode() == 23000 and strpos($err->getMessage(), "Duplicate entry")!==false){
	                    saveData();
	                } else {
	                    $result['flag'] = false;
	                    $result['msg'] = "Ocurrio un problema. (ID: $codigo)";
	                }
	            }
	            
	            return $result;
            }
            
            //$insert_id = $ubicacion->id;
            $insert_id = 0;
            $guardar = saveData();
            
            if ( $guardar['flag'] === true ) {
                $insert_id = $guardar['msg'];
            }

            
            /**
             *
             * Upload
             *
             */
            $return = array();
            if (isset($_FILES['imagen']) and $_FILES['imagen']['size'] > 0)
            {
            
                /**
                 *
                 * Validar existencia de carpeta de usuario
                 *
                 */
                $upload_folder = '/home/wildhost/public_html/ubicame/img/address/' . md5( 'u' . Auth::user()->id );
                
                if ( !is_dir($upload_folder) ) 
                {
                    mkdir($upload_folder);
                }

    	        $nombre_archivo = $_FILES['imagen']['name'];
	        $ext_archivo = end((explode(".", $nombre_archivo)));	
	        $tipo_archivo = $_FILES['imagen']['type'];	
 	        $tamano_archivo = $_FILES['imagen']['size'];	
	        $tmp_archivo = $_FILES['imagen']['tmp_name'];	
	        $archivo_nuevo = $insert_id . "." . $ext_archivo;	
	        $file = $upload_folder . '/' . $archivo_nuevo;
	    
	        if (!move_uploaded_file($tmp_archivo, $file)) {
	            $return = array(
	                'upload' => FALSE, 
	                'msg' => "Ocurrio un error al subir el archivo. No pudo guardarse.", 
	                'error' => $_FILES['archivo']
	            );
	        } else {
	            $return = array(
	                'upload' => TRUE, 
	                'data' => "OK"
	            );
	        }
	        
	        /**
                 *
                 * Update nombre de la imagen
                 *
                 */
                $ubicacion = Ubicacion::find( $insert_id );
	        $ubicacion->imagen = $archivo_nuevo;		 
	        $ubicacion->save();
	        
            }

            return Response::json(array(
                                    'rst'=>1,
                                    'msj'=>'Registro realizado correctamente',
                                    'file' => $return
                    ));	
        }
    }

    public function postEdit()
    {
        if(Request::ajax())
        {

            $inputAll = Input::all();
            //Input::clear();
            foreach ($inputAll as $key=>$val) {
                $key = str_replace(array("slct_", "txt_"), array("", ""), $key);
                $inputAll[$key] = $val;
            }
            Input::merge($inputAll);

            $valubicacion = DB::table('ubicaciones as u')
                            ->where('u.usuario_id', '=', Auth::user()->id)
                            ->where('u.id','=',Input::get('id') )
                            ->get();

            if( count($valubicacion)>0 ){
                $edit_id = Input::get('id');
            
                $ubicacion = Ubicacion::find( Input::get('id') );
                $ubicacion->usuario_id = Auth::user()->id;
                $ubicacion->categoria_id = Input::get('categoria');
                $ubicacion->tipo_acceso_id = Input::get('tipo');
                $ubicacion->x = Input::get('x');
                $ubicacion->y = Input::get('y');
                $ubicacion->nombre = Input::get('nombre');
                $ubicacion->descripcion = Input::get('descripcion');
                $ubicacion->contacto = Input::get('contacto');
                $ubicacion->etiquetas = Input::get('etiquetas');
                $ubicacion->estado = 1;
                $ubicacion->vigencia = Input::get('date') . " " . Input::get('time');
                $ubicacion->usuario_modificacion = Auth::user()->id;
                $ubicacion->save();
                
                /**
             *
             * Upload
             *
             */
            $return = array();
            if (isset($_FILES['imagen']) and $_FILES['imagen']['size'] > 0)
            {
            
                /**
                 *
                 * Validar existencia de carpeta de usuario
                 *
                 */
                $upload_folder = '/home/wildhost/public_html/ubicame/img/address/' . md5( 'u' . Auth::user()->id );
                
                if ( !is_dir($upload_folder) ) 
                {
                    mkdir($upload_folder);
                }

    	        $nombre_archivo = $_FILES['imagen']['name'];
	        $ext_archivo = end((explode(".", $nombre_archivo)));	
	        $tipo_archivo = $_FILES['imagen']['type'];	
 	        $tamano_archivo = $_FILES['imagen']['size'];	
	        $tmp_archivo = $_FILES['imagen']['tmp_name'];	
	        $archivo_nuevo = $edit_id . "." . $ext_archivo;	
	        $file = $upload_folder . '/' . $archivo_nuevo;
	    
	        if (!move_uploaded_file($tmp_archivo, $file)) {
	            $return = array(
	                'upload' => FALSE, 
	                'msg' => "Ocurrio un error al subir el archivo. No pudo guardarse.", 
	                'error' => $_FILES['archivo']
	            );
	        } else {
	            $return = array(
	                'upload' => TRUE, 
	                'data' => "OK"
	            );
	        }
	        
	        /**
                 *
                 * Update nombre de la imagen
                 *
                 */
                $ubicacion = Ubicacion::find( $edit_id );
	        $ubicacion->imagen = $archivo_nuevo;		 
	        $ubicacion->save();
	        
            }

                return Response::json(array(
                                    'rst'=>1,
                                    'msj'=>'Registro realizado correctamente',
                                    'file' => $return
                    ));	
            }
            else{
                return Response::json(array(
                                        'rst'=>2,
                                        'msj'=>'<b>No intente manipular la información de los ids.</b>  Atte Administrador => Cualquier consulta escribir a <b>ubicame@puedesencontrar.com</b>',
                )); 
            } 
        }
    }

    public function postCargarubicacion()
    {
        if(Request::ajax())
        {
            $ubicacion= DB::table('ubicaciones as u')
                        ->where('u.usuario_id', '=', Auth::user()->id)
                        ->where('u.id','=',Input::get('id') )
                        ->get();
            if(count($ubicacion)>0){
                $data = $ubicacion[0];
                $data->folder = md5( "u" . $data->usuario_id );
                return Response::json(array('rst'=>1,'datos'=>$data));    
            }
            else{
                return Response::json(array('rst'=>2,'datos'=>$ubicacion, 
                                            'msj'=>'<b>No intente manipular la información de los ids.</b>  Atte Administrador => Cualquier consulta escribir a <b>ubicame@puedesencontrar.com</b>'
                ));
            }
            
        }
    }   

    public function postCargar()
    {
        if(Request::ajax())
        {
            $ubicaciones= DB::table('ubicaciones as u')
                        ->join('categorias as c', 'u.categoria_id', '=', 'c.id')
                        ->join('tipos_accesos as ta', 'u.tipo_acceso_id', '=', 'ta.id')
                        ->select('u.nombre', 'u.id','u.codigo','c.descripcion as categoria','ta.descripcion as tipo_acceso'
                                ,'u.x','u.y','u.descripcion','u.etiquetas','u.estado')
                        ->where('u.usuario_id', '=', Auth::user()->id)
                        ->orderBy('u.id', 'desc')
                        ->get();
            return Response::json(array('rst'=>1,'datos'=>$ubicaciones));
        }
    }

    public function postEditar()
    {
        if(Request::ajax())
        {
            $reglas = array(
                //'codigo'       => 'required|unique:ubicaciones|min:6|regex:([a-zA-Z01-9ñÑÁÉÍÓÚáéíóú])',
            );

            $mensaje= array(
                'required'  => ':attribute Es requerido',
                'unique'    => ':attribute ya existe',
                'min'       => ':attribute Debe tener un mínimo de :min',
                'regex'     => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas,$mensaje);

            if ($validator->fails())
            {
                return Response::json(array(
                        'rst'=>2,
                        'msj'=>$validator->messages(),
                ));         
            }
            
            $ubicaciones = Ubicacion::find(Input::get('id'));
            //$ubicaciones->codigo = Input::get('codigo');
            $ubicaciones->usuario_modificacion=Auth::user()->id;
            $ubicaciones->save();         

                return Response::json(array(
                        'rst'=>1,
                        'msj'=>'Registro actualizado correctamente',
                )); 
        }
    }

    public function postEliminar()
    {
        if(Request::ajax())
        {           
            $ubicaciones = Ubicacion::find(Input::get('id'));
            $ubicaciones->delete();           

                return Response::json(array(
                        'rst'=>1,
                        'msj'=>'Registro eliminado correctamente',
                )); 
        }
    }

    public function postEnviar()
    {
        if(Request::ajax())
        {           
            /*
            $reglas = array(
                'email'         => 'required|email|unique:usuarios',
                'password'      => 'required|min:6',
            );

            $mensaje= array(
                'required'  => ':attribute Es requerido',
                'email'     => ':attribute No es válido',
                'unique'    => ':attribute Existente',
                'min'       => ':attribute Debe ser un minimo de :min'
            );

            $validator = Validator::make(Input::all(), $reglas,$mensaje);

            if ($validator->fails())
            {
                return Response::json(array(
                        'rst'=>2,
                        'msj'=>$validator->messages(),
                ));         
            }
            */

            $parametros=array(
                'email'     => Input::get('email_to'),
                'mensaje'   => Input::get('message'),
                'qr'        => Input::get('dataqr'),
                'url'       => Input::get('urlqr'),
            );

                /*Mail::send('emails', $parametros , 
                    function($message){
                    //$message->from(Auth::user()->email,'Jorge Salcedo');
                    $message->to(Input::get('email_to'),'Prueba')
                            ->cc(Input::get('email_cc'),'Prueba copia')
                            ->subject('.::Mi QR - Ubicame::.');               
                    }
                );*/

            try{
                if( trim(Input::get('email_cc'))!='' ){
                    Mail::send('emailsqr', $parametros , 
                        function($message){
                        $message->to(Input::get('email_to'),'')
                                ->cc(Input::get('email_cc'),'')
                                ->subject('.::Mi QR - Ubicame::.');               
                        }
                    );    
                }
                else{
                    Mail::send('emailsqr', $parametros , 
                        function($message){
                        $message->to(Input::get('email_to'),'')
                                ->subject('.::Mi QR - Ubicame::.');               
                        }
                    );
                }
                

                return Response::json(array(
                    'rst'=>1,
                    'msj'=>'Se realizó con éxito su envio',
                )); 
            }
            catch(Exception $e){
                return Response::json(array(
                    'rst'=>2,
                    'msj'=>array('No se pudo realizar el envio de Email; Favor de verificar su email e intente nuevamente.'),
                ));
                throw $e;
            }
        }
    }
    
    
    public function postDeleteimage(){
    
        if(Request::ajax())
        {
            try{
                $ubicacion = Ubicacion::find( Input::get('uid') );
        
	        //Ruta de la imagen
	        $image = '/home/wildhost/public_html/ubicame/img/address/' . md5( 'u' . Auth::user()->id ) . '/' . $ubicacion->imagen;
	        //Eliminar imagen de la carpeta de usuario
	        unlink($image);
        
        	//Actualizar registro sin imagen
        	$ubicacion->imagen = "";
	        $ubicacion->save();
	        
	        return Response::json(array(
                    'rst'=>1,
                    'msj'=>'Imagen eliminada correctamente.',
                ));
            } catch(Exception $e){
                return Response::json(array(
                    'rst'=>2,
                    'msj'=>array('No se pudo eliminar la imagen. Intente nuevamente.'),
                ));
                throw $e;
            }
        }
    }

}