<?php
    /**
     * Registro de las accciones del webService.
     * @return Response
     */

class EnvioOfsc extends \Eloquent
{


    public $table = 'envios_ofsc';


    /**
     * @param string $accion        accion
     * @param string $dataReq       data requerida
     * @param string $contenidoResp respuesta WS
     * @param string $respuestaEstadoWs status final de la WS
     */
    public function registrarAccionWebservice($accion, $dataReq, $contenidoResp,
                                             $usuario) 
    {
            DB::table('envios_ofsc')->insert(
                array(
                 'accion'             => $accion,
                 'enviado'            => $dataReq,
                 'respuesta'          => $contenidoResp,
                 'usuario_created_at' => $usuario,
                 'created_at' => DB::raw('NOW()')

                )
            );

    }//end registrarAccionWebservice()


}//end class
