<?php

class CatalogoDecos extends \Eloquent
{
    //protected $table = "webpsi_officetrack.catalogo_decos";
    public $timestamps = false;
    public function __construct()
    {
         $this->table = "webpsi_officetrack.catalogo_decos";
    }

    /**
     * metodo que activa o realiza refresh segun el parametro asunto
     * los Input::all vienen directamente del POST api/eventometodo
     */
    public static function registrarAR()
    {
        if (Input::has('asunto') && Input::has('serieDeco') &&
            Input::has('telefonoOrigen') ) {
            $asunto = Input::get('asunto');
            $serieDeco = Input::get('serieDeco');
            $serieTarjeta = Input::get('serieTarjeta', '');
            $telefonoOrigen = Input::get('telefonoOrigen');
            $tipo = Input::get('tipo', '');
            $carnet='';

            if ( $tipo =='2') {
                $tecnico = Tecnico::where('celular', $telefonoOrigen)->first();
                $carnet = $tecnico->carnet_tmp;
            } else {
                $usuario = Usuario::where('celular', $telefonoOrigen)->first();
                $carnet = $usuario->id;
            }
            $gestionId ='0';
            $requerimiento = Input::get('requerimiento', '');
            $registros = array();
            if ( $requerimiento !='') {
                $registros = GestionDetalle::where('codactu', $requerimiento)->first();
                if ( count($registros)>0 ) {
                    $gestionId = $registros->gestion_id;
                }
            }
            if ($asunto=='refresh' || $asunto=='activacion') {
                $catalogoDecos = new CatalogoDecos;
                $catalogoDecos['gestion_id'] = $gestionId;
                $catalogoDecos['carnet'] = $carnet;
                $catalogoDecos['serie'] = $serieDeco;
                $catalogoDecos['tarjeta'] = $serieTarjeta;
                $catalogoDecos['cliente'] = '';
                $catalogoDecos['fecha_registro'] = date('Y-m-d H:i:s');
                $catalogoDecos['accion'] = strtolower($asunto);
                $catalogoDecos['tipo_persona'] = $tipo;
                //$catalogoDecos['fecha_accion'] = 0;
                //$catalogoDecos['resultado'] = 0;
                //$catalogoDecos['activo'] = 0;
                $rst=$catalogoDecos->save();
                $msj = "se envio petición de ".$asunto." de deco ";
            } else {
                $msj = 'no se ha recibido asunto (refresh o activacion)';
            }

        } else {
            $msj = 'no se ha recibido asunto ni serie deco';
        }
        return array(
            'rst'=>'1',
            'datos'=>'',
            'msj'=>$msj,
        );
    }
}
