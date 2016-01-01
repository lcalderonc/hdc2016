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
                                             $respuestaEstadoWs=1)
    {
            DB::table('envios_ofsc')->insert(
                array(
                 'accion'             => $accion,
                 'enviado'            => $dataReq,
                 'respuesta'          => $contenidoResp,
                 'estadoRespuestaWs' => $respuestaEstadoWs,
                 'usuario_created_at' => Auth::user()->id
                )
            );

    }//end registrarAccionWebservice()


}//end class
