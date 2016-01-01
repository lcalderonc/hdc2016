<?php
class RegistroManualController extends \BaseController
{
    protected $_errorController;
    /**
     * Valida sesion activa
     */
    public function __construct(ErrorController $ErrorController)
    {
        $this->beforeFilter('auth');
        $this->_errorController = $ErrorController;
    }
    /**
     * Recepciona datos de Bandeja Controller
     * 
     * @return type
     */
    public function postCrear()
    {
        if ( Request::ajax() ) {
            DB::beginTransaction();
            $id="";
            $gestiones = new Gestion;

            $gestiones["actividad_id"]=
            Input::get('tipo_actividad');
            $gestiones["nombre_cliente_critico"]=
            Input::get('cr_nombre');
            $gestiones["telefono_cliente_critico"]=
            Input::get('cr_telefono');
            $gestiones["celular_cliente_critico"]=
            Input::get('cr_celular');

            $gestiones['usuario_created_at']=Auth::user()->id;

            try {
                $gestiones->save();
            } catch (Exception $exc) {
                DB::rollback();
                $this->_errorController->saveError($exc);
                return Response::json(
                    array(
                        'rst'=>2,
                        'datos'=>'Error gestiones'
                    )
                );
            }
            $gestiones["id_atc"]="RTC_".date("Y")."_".$id;
            $id=$gestiones->id;

            $fechaRegistro=date("Y-m-d H:i:s");

            $gestionesDetalles = new GestionDetalle;
            $gestionesDetalles["gestion_id"]=$id;
            $gestionesDetalles["quiebre_id"]=
                    Input::get('quiebre');
            $gestionesDetalles["empresa_id"]=
                    Input::get('empresa_id');
            $gestionesDetalles["zonal_id"]=
                    Input::get('zonal_id');
            $gestionesDetalles["codactu"]=
                    Input::get('averia');
            $gestionesDetalles["tipo_averia"]=
                    Input::get('tipo_averia');
            $gestionesDetalles["horas_averia"]=
                    '0';
            $gestionesDetalles["fecha_registro"]=
                    $fechaRegistro;
            $gestionesDetalles["ciudad"]=
                    '';
            $gestionesDetalles["inscripcion"]=//codclicms
                    Input::get('telefono');
            $gestionesDetalles["fono1"]=
                    Input::get('telefono');
            $gestionesDetalles["telefono"]=
                    Input::get('telefono');
                    $mdf=explode("___", Input::get('mdf'));
            $gestionesDetalles["mdf"]=
                    $mdf[0];
            $gestionesDetalles["observacion"]=
                    Input::get('cr_observacion');
            $gestionesDetalles["segmento"]=
                    Input::get('segmento');

            $gestionesDetalles["area"]=
                    '';
            $gestionesDetalles["direccion_instalacion"]=
                    Input::get('direccion');
            $gestionesDetalles["codigo_distrito"]=
                    '';
            $gestionesDetalles["nombre_cliente"]=
                    Input::get('cr_nombre');
            $gestionesDetalles["orden_trabajo"]=
                    '';
            $gestionesDetalles["veloc_adsl"]=
                    '';
            $gestionesDetalles["clase_servicio_catv"]=
                    '';
            $gestionesDetalles["codmotivo_req_catv"]=
                    '';
            $gestionesDetalles["total_averias_cable"]=
                    '';
            $gestionesDetalles["total_averias_cobre"]=
                    '';
            $gestionesDetalles["total_averias"]=
                    '';
                    $fftt="";
                    $troba=trim(Input::get("troba"));
                    $amplificador=trim(Input::get("amplificador"));
                    $tap=trim(Input::get("tap"));

                    $cable=trim(Input::get("cable"));
                    $terminal=trim(Input::get("terminal"));
                    if ( Input::get('tipo_averia')=='rutina-catv-pais' ) {
                        $fftt=$mdf[0]."|".$troba."|".$amplificador."|".$tap;
                    } else {
                        $fftt=$mdf[0]."|".$cable."|".$terminal;
                    }
            $gestionesDetalles["fftt"]=
                    $fftt;
            $gestionesDetalles["llave"]=
                    '';
            $gestionesDetalles["dir_terminal"]=
                    '';
            $gestionesDetalles["fonos_contacto"]=
                    Input::get('cr_telefono');
            $gestionesDetalles["contrata"]=
                    Input::get('eecc');
            $gestionesDetalles["zonal"]=
                    Input::get('zonal');
            $gestionesDetalles["wu_nagendas"]=
                    '0';
            $gestionesDetalles["wu_nmovimientos"]=
                    '0';
            $gestionesDetalles["wu_fecha_ult_agenda"]=
                    '';
            $gestionesDetalles["total_llamadas_tecnicas"]=
                    '0';
            $gestionesDetalles["total_llamadas_seguimiento"]=
                    '0';
            $gestionesDetalles["llamadastec15dias"]=
                    '0';
            $gestionesDetalles["llamadastec30dias"]=
                    '0';
            $gestionesDetalles["lejano"]=
                    Input::get('lejano');
            $gestionesDetalles["distrito"]=
                    Input::get('distrito');
            $gestionesDetalles["eecc_zona"]=
                    Input::get('eecc');
            $gestionesDetalles["zona_movistar_uno"]=
                    Input::get('movistar_uno');
            $gestionesDetalles["paquete"]=
                    '';
            $gestionesDetalles["data_multiproducto"]=
                    '';
            $gestionesDetalles["averia_m1"]=
                    '';
            $gestionesDetalles["fecha_data_fuente"]=
                    $fechaRegistro;
            $gestionesDetalles["telefono_codclientecms"]=
                    Input::get('telefono');
            $gestionesDetalles["rango_dias"]=
                    '';
            $gestionesDetalles["sms1"]=
                    '';
            $gestionesDetalles["sms2"]=
                    '';
            $gestionesDetalles["area2"]=
                    'EN CAMPO';
            $gestionesDetalles["microzona"]=
                    Input::get('microzona');
                    $tipoActuacion="";
                    if (Input::get('tipo_actividad')==3) {
                        $tipoActuacion="AVERIA";
                    } else {
                        $tipoActuacion="PROVISION";
                    }

            $gestionesDetalles["tipo_actuacion"]=
                    $tipoActuacion;

            $gestionesDetalles["x"]=
                    Input::get('x');
            $gestionesDetalles["y"]=
                    Input::get('y');
            if (Input::has('codservcms'))
                $gestionesDetalles['codservcms']=Input::get('codservcms');
            if (Input::has('codclie'))
                $gestionesDetalles['codclie']=Input::get('codclie');
            if (Input::has('edificio'))
                $gestionesDetalles['edificio_id']=Input::get('edificio');
            
            $gestionesDetalles['usuario_created_at']=Auth::user()->id;
            
            try {
                $gestionesDetalles->save();
            } catch (Exception $exc) {
                DB::rollback();
                $this->_errorController->saveError($exc);
                return Response::json(
                    array(
                        'rst'=>2,
                        'datos'=>'Error gestion detalle'
                    )
                );
            }
            $gestionesMovimientos = new GestionMovimiento;
            //codigo submodulo origen regis
            $gestionesMovimientos['submodulo_id']=5;
            $gestionesMovimientos["gestion_id"]=$id;

            $gestionesMovimientos["empresa_id"]=
                        Input::get('empresa_id');
            $gestionesMovimientos["zonal_id"]=
                        Input::get('zonal_id');
            $gestionesMovimientos["estado_id"]=
                        '7';
            $gestionesMovimientos["motivo_id"]=
                        '2';
            $gestionesMovimientos["submotivo_id"]=
                        '18';
            $gestionesMovimientos["observacion"]=
                        'Registro Manual';
            $gestionesMovimientos["coordinado"]=
                        '0';

            $gestionesMovimientos['usuario_created_at']=Auth::user()->id;
            
            try {
                $gestionesMovimientos->save();
            } catch (Exception $exc) {
                DB::rollback();
                $this->_errorController->saveError($exc);
                return Response::json(
                    array(
                        'rst'=>2,
                        'datos'=>'Error gestion movimiento'
                    )
                );
            }
            $ultimoMovimiento= new UltimoMovimiento;
            $ultimoMovimiento["actividad_id"]=
            Input::get('tipo_actividad');
            $ultimoMovimiento["nombre_cliente_critico"]=
            Input::get('cr_nombre');
            $ultimoMovimiento["telefono_cliente_critico"]=
            Input::get('cr_telefono');
            $ultimoMovimiento["celular_cliente_critico"]=
            Input::get('cr_celular');
            $ultimoMovimiento['usuario_created_at']=Auth::user()->id;
            $ultimoMovimiento["id_atc"]="RTC_".date("Y")."_".$id;

            $ultimoMovimiento["gestion_id"]=$id;
            $ultimoMovimiento["quiebre_id"]=
                    Input::get('quiebre');
            $ultimoMovimiento["empresa_id"]=
                    Input::get('empresa_id');
            $ultimoMovimiento["zonal_id"]=
                    Input::get('zonal_id');
            $ultimoMovimiento["codactu"]=
                    Input::get('averia');
            $ultimoMovimiento["tipo_averia"]=
                    Input::get('tipo_averia');
            $ultimoMovimiento["horas_averia"]=0;

            $ultimoMovimiento["fecha_registro"]=$fechaRegistro;

            $ultimoMovimiento["ciudad"]='';

            $ultimoMovimiento["inscripcion"]=Input::get('telefono');
            $ultimoMovimiento["fono1"]=Input::get('telefono');
            $ultimoMovimiento["telefono"]=Input::get('telefono');
            $ultimoMovimiento["mdf"]=$mdf[0];
            $ultimoMovimiento["observacion"]='Registro Manual';
            $ultimoMovimiento["segmento"]=
                    Input::get('segmento');
            $ultimoMovimiento["area"]='';
            $ultimoMovimiento["direccion_instalacion"]=
                    Input::get('direccion');
            $ultimoMovimiento["codigo_distrito"]='';
            $ultimoMovimiento["nombre_cliente"]=
                    Input::get('cr_nombre');
            $ultimoMovimiento["orden_trabajo"]='';
            $ultimoMovimiento["veloc_adsl"]='';
            $ultimoMovimiento["clase_servicio_catv"]='';
            $ultimoMovimiento["codmotivo_req_catv"]='';
            $ultimoMovimiento["total_averias_cable"]='';
            $ultimoMovimiento["total_averias_cobre"]='';
            $ultimoMovimiento["total_averias"]='';

                    $fftt="";
                    $troba=trim(Input::get("troba"));
                    $amplificador=trim(Input::get("amplificador"));
                    $tap=trim(Input::get("tap"));

                    $cable=trim(Input::get("cable"));
                    $terminal=trim(Input::get("terminal"));
                    if ( Input::get('tipo_averia')=='rutina-catv-pais' ) {
                        $fftt=$mdf[0]."|".$troba."|".$amplificador."|".$tap;
                    } else {
                        $fftt=$mdf[0]."|".$cable."|".$terminal;
                    }
            $ultimoMovimiento["fftt"]=
                    $fftt;
            $ultimoMovimiento["llave"]='';
            $ultimoMovimiento["dir_terminal"]='';
            $ultimoMovimiento["fonos_contacto"]=
                    Input::get('cr_telefono');
            $ultimoMovimiento["contrata"]=
                    Input::get('eecc');
            $ultimoMovimiento["zonal"]=
                    Input::get('zonal');
            $ultimoMovimiento["wu_nagendas"]=0;
            $ultimoMovimiento["wu_nmovimientos"]=0;
            $ultimoMovimiento["wu_fecha_ult_agenda"]='';
            $ultimoMovimiento["total_llamadas_tecnicas"]='0';
            $ultimoMovimiento["total_llamadas_seguimiento"]='0';
            $ultimoMovimiento["llamadastec15dias"]='0';
            $ultimoMovimiento["llamadastec30dias"]='0';
            $ultimoMovimiento["lejano"]=
                    Input::get('lejano');
            $ultimoMovimiento["distrito"]=
                    Input::get('distrito');
            $ultimoMovimiento["eecc_zona"]=
                    Input::get('eecc');
            $ultimoMovimiento["zona_movistar_uno"]=
                    Input::get('movistar_uno');
            $ultimoMovimiento["paquete"]='';
            $ultimoMovimiento["data_multiproducto"]='';
            $ultimoMovimiento["averia_m1"]='';
            $ultimoMovimiento["fecha_data_fuente"]=$fechaRegistro;
            $ultimoMovimiento["telefono_codclientecms"]=
                    Input::get('telefono');
            $ultimoMovimiento["rango_dias"]='';
            $ultimoMovimiento["sms1"]='';
            $ultimoMovimiento["sms2"]='';
            $ultimoMovimiento["area2"]='EN CAMPO';
            $ultimoMovimiento["microzona"]=
                    Input::get('microzona');
                $tipoActuacion="";
                    if (Input::get('tipo_actividad')==3) {
                        $tipoActuacion="AVERIA";
                    } else {
                        $tipoActuacion="PROVISION";
                    }
            $ultimoMovimiento["tipo_actuacion"]=$tipoActuacion;
            /*$ultimoMovimiento["empresa_m_id"]=
                    Input::get('empresa_id');*/
            $ultimoMovimiento["estado_id"]='7';
            $ultimoMovimiento["motivo_id"]='2';
            $ultimoMovimiento["submotivo_id"]='18';
            /*$ultimoMovimiento["observacion_m"]=
                    Input::get('observacion2');*/
            $ultimoMovimiento["coordinado"]='0';
            /*if ( Input::get('flag_tecnico') ) {
            
            $ultimoMovimiento["flag_tecnico"]=
                        Input::get('flag_tecnico');
            }*/

            $ultimoMovimiento['usuario_updated_at']=Auth::user()->id;
            $ultimoMovimiento['updated_at']=date("Y-m-d H:i:s");
            $ultimoMovimiento['usuario_created_at']=Auth::user()->id;
            
            try {
                $ultimoMovimiento->save();
            } catch (Exception $exc) {
                DB::rollback();
                $this->_errorController->saveError($exc);
                return Response::json(
                    array(
                        'rst'=>2,
                        'datos'=>'Error ultimo movimiento'
                    )
                );
            }
            DB::commit();

            return  array(
                        'rst'=>1,
                        'msj'=>"Registro Realizado con Éxito"
                    );
        }

    }
    /**
     * busqueda de cliente
     * 
     * @return type
     */
    public function postBuscacliente()
    {
        //esta busqueda viene de registro manul
        //primero se busca en gestionados y temporales, en caso de hallarlos ahi
        // terminar busqueda y retornar la data encontrada
            //para el caso de encontrar un registro retornar la data
                //la intension de esto es le redirija a la bandeja de gestion
            //para el caso de encontrar mas  de un registro retornar la data
                //el cliente debera legir en con que registro trabajar
        //de lo contrario continuar buscadno en las tablas de pendientes,
        // liquidados y en la maestra, en caso encontrarlos ahi terminar
        // la busqueda y retornar la data encontrada, en todos los lugares
            //por el lado de cliente se armara una tabla con los datos devueltos
            //esto servira para que el cliente elijan con quen datos trabajara
            //
        // para el caso de no encontrar ninregistro retornar mensaje
        //buscar en la tabla maestra
        //recibir los parametros y enviarlos al modelo, ahi ejecutar el query


        $telefono=$codcliatis=$codsercms=$codclicms='';
        if (Input::has('telefonoCliente'))
            $telefono = Input::get('telefonoCliente');
        if (Input::has('codigoClienteATIS'))
            $codcliatis = Input::get('codigoClienteATIS');
        if (Input::has('codigoServicioCMS'))
            $codsercms = Input::get('codigoServicioCMS');
        if (Input::has('codigoClienteCMS'))
            $codclicms = Input::get('codigoClienteCMS');
        //1
        $arrclienteGest=Historico::getGestiones(
            $telefono,
            $codcliatis,
            $codsercms,
            $codclicms
        );
        $rows = count($arrclienteGest);
        if ($rows>0) {
            return Response::json(
                array(
                    'rst'=>$rows,
                    'datos'=>$arrclienteGest,
                    'estado'=>'gestionado'
                )
            );
        }
        //2
        if ($telefono!='' || $codclicms!='') {
            $arrclienteTemp=Historico::getTemporales(
                $telefono,
                $codclicms
            );
            $rows = count($arrclienteTemp);
            if ($rows>0) {
                return Response::json(
                    array(
                        'rst'=>$rows,
                        'datos'=>$arrclienteTemp,
                        'estado'=>'temporal'
                    )
                );
            }
        }
        $estado=$arrcliente=array();
        //3
        $arrclientePen=Historico::getPendientes(
            $telefono,
            $codcliatis,
            $codsercms,
            $codclicms
        );
        $rows = count($arrclientePen);
        if ($rows>0) {
            $estado[]='pendientes';
            $arrcliente['pendientes']=$arrclientePen;
        }
        //4
        $arrclienteLiq=Historico::getLiquidados(
            $telefono,
            $codcliatis,
            $codsercms,
            $codclicms
        );
        $rows = count($arrclienteLiq);
        if ($rows>0) {
            $estado[]='liquidados';
            $arrcliente['liquidados']=$arrclienteLiq;
        }
        //5
        $arrclienteMaestro=Historico::getMaestro(
            $telefono,
            $codcliatis,
            $codsercms,
            $codclicms
        );
        $rows = count($arrclienteMaestro);
        if ($rows>0) {
            $estado[]='maestro';
            $arrcliente['maestro']=$arrclienteMaestro;
        }
        $rows = count($arrcliente);
        if ($rows>0) {
            return Response::json(
                array(
                    'rst'=>1,
                    'datos'=> $arrcliente,
                    'estado'=>$estado
                )
            );
        }
        return Response::json(
            array(
                'rst'=>0,
                'datos'=>'No se encontraron registros',
                'estado'=>'no existe'
            )
        );
/*
        $telefono=$codcliatis=$codsercms=$codclicms='';
        if (Input::has('telefonoCliente'))
            $telefono = Input::get('telefonoCliente');
        if (Input::has('codigoClienteATIS'))
            $codcliatis = Input::get('codigoClienteATIS');
        if (Input::has('codigoServicioCMS'))
            $codsercms = Input::get('codigoServicioCMS');
        if (Input::has('codigoClienteCMS'))
            $codclicms = Input::get('codigoClienteCMS');
        
        //consulto la base de datos
        try {
            $arrcliente=Historico::gesRegistro(
                $telefono,
                $codcliatis,
                $codsercms,
                $codclicms
            );
        } catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            $msj ='Ocurrió una interrupción en la busqueda';
            return  array(
                'rst'=>0,
                'datos'=>$msj
            );
        }
        return Response::json(
            $arrcliente

        );
        */
    }
}
