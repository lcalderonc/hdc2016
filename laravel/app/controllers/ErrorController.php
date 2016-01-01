<?php

use anlutro\L4SmartErrors\L4SmartErrorsServiceProvider;

class ErrorController extends \BaseController
{

    /**
     * Valida sesion activa
     */
    public function __construct() 
    {
        $this->beforeFilter('auth');
    }

    public function saveError($exc) 
    {
        $error["code"] = $exc->getCode();
        $error["file"] = $exc->getFile();
        $error["line"] = $exc->getLine();
        $error["message"] = $exc->getMessage();
        $error["trace"] = $exc->getTraceAsString();
        $error["usuario_id"] = Auth::user()->id;
        $error["date"] = date("Y-m-d H:i:s");
        
        DB::table('errores')->insert(
            array($error)
        );
    }
    
    public function saveCustomError($custom)
    {
        DB::table('errores')->insert(
            array($custom)
        );
    }

    public function handlerError($error, $code = '')
    {

        if(empty($code))
            $error["code"] = $error->getCode();
        else
            $error["code"] = $code;

        $error["file"] = $error->getFile();
        $error["line"] = $error->getLine();
        $error["message"] = $error->getMessage();
        $error["trace"] = $error->getTraceAsString();
        $error["usuario_id"] = Auth::user()->id;
        $error["date"] = date("Y-m-d H:i:s");

        DB::table('errores')->insert(
            array($error)
        );
    }
        
    public function postListar()
    {
        if ( Request::ajax() ) {
            $errores = DB::table('errores as e')
                    ->join('usuarios as u', 'e.usuario_id', '=', 'u.id')
                    ->select(
                        'e.id',
                        'e.code',    
                        'u.usuario as nombre',
                        'e.line',    
                        'e.file',
                        'e.message',
                        'e.trace', 
                        'e.date', 
                        'e.estado'
                    )
                    ->get();
            return Response::json(array('data' => $errores));
        }
    }
    
    
    public function postCambiarestado()
    {
        if ( Request::ajax() ) {
            DB::table('errores')
            ->where('id', Input::get('id'))
            ->update(array('estado' => Input::get('estado')));
        }
    }
    
}
