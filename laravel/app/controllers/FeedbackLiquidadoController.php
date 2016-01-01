<?php

class FeedbackLiquidadoController extends BaseController
{

    public function __construct()
    {
        $this->beforeFilter('auth'); // bloqueo de acceso
    }

    public function postListar()
    {
        $feedback = DB::table('feedback_liquidados')
                    ->select('id', 'nombre')
                    ->where('estado', '=', 1)
                    ->orderBy('nombre')
                    ->get();

        return     Response::json(
            array(
                'rst'=>1,
                'datos'=>$feedback
            )
        );
    }

}
