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

    /**
     * 
     * @param array|Exception $exc
     */
    public function saveError($exc)
    {
        if (is_array($exc)) {
            $error["code"] = $exc["code"] ? $exc["code"] : '';
            $error["file"] = $exc["file"] ? $exc["file"] : '';
            $error["line"] = $exc["line"] ? $exc["line"] : '';
            $error["message"] = $exc["message"] ? $exc["message"] : '';
            $error["trace"] = $exc["trace"] ? $exc["trace"] : '';
            $error = $exc;
        } else {
            $error["code"] = $exc->getCode();
            $error["file"] = $exc->getFile();
            $error["line"] = $exc->getLine();
            $error["message"] = $exc->getMessage();
            $error["trace"] = $exc->getTraceAsString();
        }
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

    /**
     * 
     * @param Exception $error
     * @param string $code
     */
    public function handlerError($error, $code = '')
    {
        if (empty($code))
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

    public function postBuscar()
    {
        if (Request::ajax()) {

            $fechaIni = "";
            $fechaFin = "";

            if (Input::get('fecha_agenda')) {
                $fechaAgenda = explode(" - ", Input::get('fecha_agenda'));
                $fechaIni = $fechaAgenda[0]; //Fecha inicio
                $fechaFin = $fechaAgenda[1]; //Fecha final
            }

            $count = 'SELECT COUNT(*) AS total
                      FROM errores
                      WHERE date 
                      BETWEEN "' . $fechaIni . '" AND "' . $fechaFin . '"';

            $consulta = 'SELECT e.id, e.code, 
                        u.usuario as nombre, e.line,
                        SUBSTRING_INDEX(e.file, "/", -2) AS file,
                        e.message, e.trace, e.date, e.estado, e.comentario 
                        FROM errores e  
                        INNER JOIN usuarios u 
                        ON e.usuario_id = u.id 
                        WHERE e.date 
                        BETWEEN "' . $fechaIni . '" AND "' . $fechaFin . '"
                        ORDER BY e.id asc 
                        LIMIT ' . Input::get('start')
                    . ',' . Input::get('length') . '';

            $rcount = DB::select($count);
            $datos = DB::select($consulta);

            //Asignado los números de Paginacion al Arreglo $wsenvios
            $errores["draw"] = Input::get('draw');
            $errores["recordsTotal"] = $rcount[0]->total;
            $errores["recordsFiltered"] = $rcount[0]->total;

            //Asignado la data al Arreglo $wsenvios
            $errores["data"] = $datos;

            return Response::json($errores);
        }
    }

    /**
     * 
     * @return integer
     */
    public function postCambiarestado()
    {
        $estado = Input::get('estado');
        $id = Input::get('id');
        $comentario = trim(Input::get('comentario'));
        if (Request::ajax() && $estado != '' && $id != '') {
            return DB::table('errores')
                            ->where('id', $id)
                            ->update(
                                array(
                                    'estado' => $estado
                                    , 'comentario' => $comentario
                                )
                            );
        }
    }

    public function postDetalle()
    {
        if (Request::ajax()) {
            $detalle = DB::table('errores')
                    ->select('message')
                    ->where('id', '=', Input::get('id'))
                    ->get();

            return Response::json(array('data' => $detalle));
        }
    }

}
