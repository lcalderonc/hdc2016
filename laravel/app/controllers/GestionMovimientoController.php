<?php

class GestionMovimientoController extends BaseController
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

    public function postCargar()
    {
        if ( Request::ajax() ) {
            $cargar = GestionMovimiento::getGestionMovimiento();
            
            return Response::json(
                array(
                    'rst'=>1,
                    'datos'=>$cargar
                )
            );

        }
    }

    public function postCrearobs()
    {
        if ( Request::ajax() ) {
            $movimientosObservaciones = new MovimientoObservacion;
            $movimientosObservaciones['gestion_movimiento_id']=
                            Input::get('gestion_movimiento_id');
            $movimientosObservaciones['observacion_tipo_id']=
                            Input::get('obs_tipo');
            $movimientosObservaciones['observacion']=
                            Input::get('observacion_o');

            $movimientosObservaciones['usuario_created_at']=Auth::user()->id;
            $movimientosObservaciones->save();

            return Response::json(
                array(
                    'rst'=>1,
                    'msj'=>'Registro realizado correctamente',
                    'codactu'=>Input::get('codactu')
                )
            );
        }
    }

    public function postCrear()
    {
        if ( Request::ajax() or Input::get('noajax') ) {
            $gestionId="";
            $cupos = true;
            if ( Input::get('gestion_id') ) {
                $gestionId= Input::get('gestion_id');
                    $ultmov= DB::table('ultimos_movimientos')
                            ->where('gestion_id', '=', $gestionId)
                            ->first();
                    $ultimoMovimiento= UltimoMovimiento::find($ultmov->id);
                    
                if( Input::get('officetrack_envio')=='1' ){
                    $gestiones= Gestion::find(Input::get('gestion_id'));
                    $gestiones["n_evento"]=1;
                    $ultimoMovimiento["n_evento"]=1;
                    $gestiones->save();
                }

                try {
                    $gestionesMovimientos = new GestionMovimiento;
                    $gestionesMovimientos["quiebre_id"]=
                                Input::get('quiebre_id');
                    $gestionesMovimientos["gestion_id"]=
                                Input::get('gestion_id');
                    $gestionesMovimientos["empresa_id"]=
                                Input::get('empresa_id');
                    $gestionesMovimientos["zonal_id"]=
                                Input::get('zonal_id');
                    $gestionesMovimientos["estado_id"]=
                                Input::get('estado');
                    $gestionesMovimientos["motivo_id"]=
                                Input::get('motivo');
                    $gestionesMovimientos["submotivo_id"]=
                                Input::get('submotivo');
                    $gestionesMovimientos["observacion"]=
                                Input::get('observacion2');
                    $gestionesMovimientos["coordinado"]=
                                Input::get('coordinado2');


                    $ultimoMovimiento["quiebre_id"]=
                                Input::get('quiebre_id');
                    $ultimoMovimiento["empresa_m_id"]=
                                Input::get('empresa_id');
                    $ultimoMovimiento["zonal_id"]=
                                Input::get('zonal_id');
                    $ultimoMovimiento["estado_id"]=
                                Input::get('estado');
                    $ultimoMovimiento["motivo_id"]=
                                Input::get('motivo');
                    $ultimoMovimiento["submotivo_id"]=
                                Input::get('submotivo');
                    $ultimoMovimiento["observacion_m"]=
                                Input::get('observacion2');
                    $ultimoMovimiento["coordinado"]=
                                Input::get('coordinado2');

                    if ( Input::get('flag_tecnico') ) {
                    $ultimoMovimiento["flag_tecnico"]=
                                Input::get('flag_tecnico');
                    $gestionesMovimientos["flag_tecnico"]=
                                Input::get('flag_tecnico');
                    }            

                    if ( Input::get('horario_id') && Input::get('horario_id')!='' ) {
                    $gestionesMovimientos["horario_id"]=
                                Input::get('horario_id');
                    $gestionesMovimientos["dia_id"]=
                                Input::get('dia_id');
                    $gestionesMovimientos["fecha_agenda"]=
                                Input::get('fecha_agenda');

                    $ultimoMovimiento["horario_id"]=
                                Input::get('horario_id');
                    $ultimoMovimiento["dia_id"]=
                                Input::get('dia_id');
                    $ultimoMovimiento["fecha_agenda"]=
                                Input::get('fecha_agenda');
                    }

                    if ( Input::get('tecnico') && Input::get('tecnico')!='' ) {
                    $gestionesMovimientos["celula_id"]=
                                Input::get('celula');
                    $gestionesMovimientos["tecnico_id"]=
                                Input::get('tecnico');

                    $ultimoMovimiento["celula_id"]=
                                Input::get('celula');
                    $ultimoMovimiento["tecnico_id"]=
                                Input::get('tecnico');
                    }

                    if ( Input::get('fecha_consolidacion') && Input::get('fecha_consolidacion')!='' ) {
                    $gestionesMovimientos["fecha_consolidacion"]=
                                Input::get('fecha_consolidacion');

                    $ultimoMovimiento["fecha_consolidacion"]=
                                Input::get('fecha_consolidacion');
                    }
                    
                    //Origen del movimiento realizado
                    $gestionesMovimientos["submodulo_id"] = 3;
                    if ( Input::get('submodulo_id') !== null )
                    {
                        $gestionesMovimientos["submodulo_id"] = 
                                Input::get('submodulo_id');
                    }

                     if ( Input::get('usuario_sistema') !== null )
                    {
                        $gestionesMovimientos['usuario_created_at']=697;
                    }
                    $gestionesMovimientos['usuario_created_at']=Auth::user()->id;
                    $gestionesMovimientos->save();

                    if( substr(Input::get('estado_agendamiento'),-2)=='-1' ) {
                        $gestionesDetalles=GestionDetalle::where('gestion_id', '=', $gestionId)
                                           ->update(
                                                array(
                                                    'x' => Input::get('x'),
                                                    'y' => Input::get('y')
                                                )
                                            );

                        $ultimoMovimiento['x']=Input::get('x');
                        $ultimoMovimiento['y']=Input::get('y');
                    }

                    $ultimoMovimiento['usuario_updated_at']=Auth::user()->id;
                    if ( Input::get('usuario_sistema') !== null )
                    {
                        $ultimoMovimiento['usuario_updated_at']=697;
                    }
                    
                    $ultimoMovimiento->save();
                    
                    //Control de cupos
                    $gestionesMovimientos->estado_agendamiento 
                            = Input::get('estado_agendamiento');
                    $cupos = $this->controlarCupos($gestionesMovimientos);

                } catch (Exception $exc) {
                        return  array(
                            'rst'=>2,
                            'msj'=>'Ocurrió una interrupción en el registro del movimiento',
                            'err'=> $exc
                        );
                }

                if ( Input::get('contacto') ) {
                    try {
                        $liquidados = new Liquidado();
                        $liquidados['gestion_id']=
                            Input::get('gestion_id');
                        $liquidados['feedback_liquidado_id']=
                            Input::get('feedback');
                        $liquidados['solucion_comercial_id']=
                            Input::get('solucion');
                        $liquidados['contacto']=
                            Input::get('contacto');
                        $liquidados['pruebas']=
                            Input::get('pruebas');
                        $liquidados['fecha_consolidacion']=
                            Input::get('fecha_consolidacion');
                        $liquidados['penalizable']=
                            Input::get('penalizable_obs');

                        $liquidados['usuario_created_at']=Auth::user()->id;
                        $liquidados->save();
                    } catch (Exception $exc) {
                        return  array(
                            'rst'=>2,
                            'msj'=>'Ocurrió una interrupción en el registro de la liquidación',
                            'err'=> $exc
                        );
                    }
                }
                
            } else {
                $id="";
                try { 
                    $gestiones = new Gestion;
                    $ultimoMovimiento= new UltimoMovimiento;

                    if( Input::get('gestion_id_officetrack') ){
                        $id=Input::get('gestion_id_officetrack');
                        $gestiones['id']=$id;
                        $gestiones["n_evento"]=1;

                        $ultimoMovimiento["n_evento"]=1;
                    }

                    $gestiones["actividad_id"]=
                    Input::get('actividad_id');
                    $gestiones["nombre_cliente_critico"]=
                    Input::get('nombre_cliente_critico');
                    $gestiones["telefono_cliente_critico"]=
                    Input::get('telefono_cliente_critico');
                    $gestiones["celular_cliente_critico"]=
                    Input::get('celular_cliente_critico');

                    $gestiones['usuario_created_at']=Auth::user()->id;

                    $gestiones->save();

                    $ultimoMovimiento["actividad_id"]=
                    Input::get('actividad_id');
                    $ultimoMovimiento["nombre_cliente_critico"]=
                    Input::get('nombre_cliente_critico');
                    $ultimoMovimiento["telefono_cliente_critico"]=
                    Input::get('telefono_cliente_critico');
                    $ultimoMovimiento["celular_cliente_critico"]=
                    Input::get('celular_cliente_critico');

                    $ultimoMovimiento['usuario_created_at']=Auth::user()->id;

                } catch (Exception $exc) {
                    return  array(
                            'rst'=>2,
                            'msj'=>'Ocurrió una interrupción en el registro de la gestion',
                            'err'=> $exc
                        );
                }

                try {
                    $gestionId= $gestiones->id;
                    $id=$gestiones->id;
                    $gestiones["id_atc"]="ATC_".date("Y")."_".$id;
                    $gestiones->save();

                    $ultimoMovimiento["id_atc"]="ATC_".date("Y")."_".$id;
                } catch (Exception $exc) {
                    return  array(
                            'rst'=>2,
                            'msj'=>'Ocurrió una interrupción en el registro del ATC',
                            'err'=> $exc
                        );
                }

                try {

                    $gestionesDetalles = new GestionDetalle;
                    $gestionesDetalles["gestion_id"]=$id;
                    $gestionesDetalles["quiebre_id"]=
                            Input::get('quiebre_id');
                    $gestionesDetalles["empresa_id"]=
                            Input::get('empresa_id');
                    $gestionesDetalles["zonal_id"]=
                            Input::get('zonal_id');
                    $gestionesDetalles["codactu"]=
                            Input::get('codactu');
                    $gestionesDetalles["tipo_averia"]=
                            Input::get('tipo_averia');
                    $gestionesDetalles["horas_averia"]=
                            Input::get('horas_averia');
                    $gestionesDetalles["fecha_registro"]=
                            Input::get('fecha_registro');
                    $gestionesDetalles["ciudad"]=
                            Input::get('ciudad');
                    $gestionesDetalles["inscripcion"]=
                            Input::get('inscripcion');
                    $gestionesDetalles["fono1"]=
                            Input::get('fono1');
                    $gestionesDetalles["telefono"]=
                            Input::get('telefono');
                    $gestionesDetalles["mdf"]=
                            Input::get('mdf');
                    $gestionesDetalles["observacion"]=
                            Input::get('observacion');
                    $gestionesDetalles["segmento"]=
                            Input::get('segmento');
                    $gestionesDetalles["area"]=
                            Input::get('area');
                    $gestionesDetalles["direccion_instalacion"]=
                            Input::get('direccion_instalacion');
                    $gestionesDetalles["codigo_distrito"]=
                            Input::get('codigo_distrito');
                    $gestionesDetalles["nombre_cliente"]=
                            Input::get('nombre_cliente');
                    $gestionesDetalles["orden_trabajo"]=
                            Input::get('orden_trabajo');
                    $gestionesDetalles["veloc_adsl"]=
                            Input::get('veloc_adsl');
                    $gestionesDetalles["clase_servicio_catv"]=
                            Input::get('clase_servicio_catv');
                    $gestionesDetalles["codmotivo_req_catv"]=
                            Input::get('codmotivo_req_catv');
                    $gestionesDetalles["total_averias_cable"]=
                            Input::get('total_averias_cable');
                    $gestionesDetalles["total_averias_cobre"]=
                            Input::get('total_averias_cobre');
                    $gestionesDetalles["total_averias"]=
                            Input::get('total_averias');
                    $gestionesDetalles["fftt"]=
                            Input::get('fftt');
                    $gestionesDetalles["llave"]=
                            Input::get('llave');
                    $gestionesDetalles["dir_terminal"]=
                            Input::get('dir_terminal');
                    $gestionesDetalles["fonos_contacto"]=
                            Input::get('fonos_contacto');
                    $gestionesDetalles["contrata"]=
                            Input::get('contrata');
                    $gestionesDetalles["zonal"]=
                            Input::get('zonal');
                    $gestionesDetalles["wu_nagendas"]=
                            Input::get('wu_nagendas');
                    $gestionesDetalles["wu_nmovimientos"]=
                            Input::get('wu_nmovimientos');
                    $gestionesDetalles["wu_fecha_ult_agenda"]=
                            Input::get('wu_fecha_ult_agenda');
                    $gestionesDetalles["total_llamadas_tecnicas"]=
                            Input::get('total_llamadas_tecnicas');
                    $gestionesDetalles["total_llamadas_seguimiento"]=
                            Input::get('total_llamadas_seguimiento');
                    $gestionesDetalles["llamadastec15dias"]=
                            Input::get('llamadastec15dias');
                    $gestionesDetalles["llamadastec30dias"]=
                            Input::get('llamadastec30dias');
                    $gestionesDetalles["lejano"]=
                            Input::get('lejano');
                    $gestionesDetalles["distrito"]=
                            Input::get('distrito');
                    $gestionesDetalles["eecc_zona"]=
                            Input::get('eecc_zona');
                    $gestionesDetalles["zona_movistar_uno"]=
                            Input::get('zona_movistar_uno');
                    $gestionesDetalles["paquete"]=
                            Input::get('paquete');
                    $gestionesDetalles["data_multiproducto"]=
                            Input::get('data_multiproducto');
                    $gestionesDetalles["averia_m1"]=
                            Input::get('averia_m1');
                    $gestionesDetalles["fecha_data_fuente"]=
                            Input::get('fecha_data_fuente');
                    $gestionesDetalles["telefono_codclientecms"]=
                            Input::get('telefono_codclientecms');
                    $gestionesDetalles["rango_dias"]=
                            Input::get('rango_dias');
                    $gestionesDetalles["sms1"]=
                            Input::get('sms1');
                    $gestionesDetalles["sms2"]=
                            Input::get('sms2');
                    $gestionesDetalles["area2"]=
                            Input::get('area2');
                    $gestionesDetalles["microzona"]=
                            Input::get('microzona');
                    $gestionesDetalles["tipo_actuacion"]=
                            Input::get('tipo_actuacion');
                    $gestionesDetalles["actividad_tipo_id"]=
                            Input::get('actividad_tipo_id');

                    if( Input::get('x') and Input::get('y')/*substr(Input::get('estado_agendamiento'),-2)=='-1'*/ ) {
                        $gestionesDetalles["x"]=
                                Input::get('x');
                        $gestionesDetalles["y"]=
                                Input::get('y');

                        $ultimoMovimiento["x"]=
                                Input::get('x');
                        $ultimoMovimiento["y"]=
                                Input::get('y');
                    }

                    $gestionesDetalles['usuario_created_at']=Auth::user()->id;
                    $gestionesDetalles->save();

                    /**********************************************************/
                    $ffttExplode=explode("|", Input::get('fftt'));
                    $tipoAExplode=explode("-", Input::get('tipo_averia'));
                    if( count($tipoAExplode)==1 ){
                        $tipoAExplode=explode("_", Input::get('tipo_averia'));
                    }

                    $arrayproadsl=array(1,2,4);
                    $arrayprocatv=array(5,6,7,8,9);

                    $arrayaveradsl=array(1,2,3,14,15,16,17,9,18,19,20);
                    $arrayaverbas=array(1,2,3,10,11,12,4,9);
                    $arrayavercatv=array(5,13,6,7,8,9);


                    $sqlttff='INSERT INTO gestiones_fftt (gestion_id,fftt_tipo_id,nombre) VALUES (?,?,?)';
                    if ( in_array('aver', $tipoAExplode, true) AND count($ffttExplode)>1 ){
                        if ( in_array('adsl', $tipoAExplode, true) ){
                            for($i=0; $i<count($arrayaveradsl); $i++){
                                if (isset($ffttExplode[$i])) {
                                    $array=array($id,$arrayaveradsl[$i],trim($ffttExplode[$i]));

                                    if( trim($ffttExplode[$i])!='' ){
                                        DB::insert($sqlttff, $array);
                                    }
                                }
                            }
                        }
                        elseif ( in_array('bas', $tipoAExplode, true) ){
                            for($i=0; $i<count($arrayaverbas); $i++){
                                if (isset($ffttExplode[$i])) {
                                    $array=array($id,$arrayaverbas[$i],trim($ffttExplode[$i]));

                                    if( trim($ffttExplode[$i])!='' ){
                                        DB::insert($sqlttff, $array);
                                    }
                                }
                            }
                        }
                        elseif ( in_array('catv', $tipoAExplode, true) ){
                            for($i=0; $i<count($arrayavercatv); $i++){
                                if (isset($ffttExplode[$i])) {
                                    $array=array($id,$arrayavercatv[$i],trim($ffttExplode[$i]));

                                    if( trim($ffttExplode[$i])!='' ){
                                        DB::insert($sqlttff, $array);
                                    }
                                }
                            }
                        }
                    }
                    elseif ( in_array('prov', $tipoAExplode, true) AND count($ffttExplode)>1 ){
                        if ( in_array('adsl', $tipoAExplode, true) OR in_array('bas', $tipoAExplode, true) ){
                            for($i=0; $i<count($arrayproadsl); $i++){
                                if (isset($ffttExplode[$i])) {
                                    if( $i==1 AND strtoupper(substr(trim($ffttExplode[$i]),0,1))=='A' ){
                                        $array=array($id,$arrayproadsl[$i],trim($ffttExplode[$i]));
                                    }
                                    elseif($i==1 ){
                                        $array=array($id,3,trim($ffttExplode[$i]));
                                    }
                                    else{
                                        $array=array($id,$arrayproadsl[$i],trim($ffttExplode[$i]));
                                    }

                                    if( trim($ffttExplode[$i])!='' ){
                                        DB::insert($sqlttff, $array);
                                    }
                                }
                            }
                        }
                        elseif ( in_array('catv', $tipoAExplode, true) ){
                            for($i=0; $i<count($arrayprocatv); $i++){
                                if (isset($ffttExplode[$i])) {
                                    $array=array($id,$arrayprocatv[$i],trim($ffttExplode[$i]));

                                    if( trim($ffttExplode[$i])!='' ){
                                        DB::insert($sqlttff, $array);
                                    }
                                }
                            }
                        }
                    }
                    /**********************************************************/

                    $ultimoMovimiento["gestion_id"]=$id;
                    $ultimoMovimiento["quiebre_id"]=
                            Input::get('quiebre_id');
                    $ultimoMovimiento["empresa_id"]=
                            Input::get('empresa_id');
                    $ultimoMovimiento["zonal_id"]=
                            Input::get('zonal_id');
                    $ultimoMovimiento["codactu"]=
                            Input::get('codactu');
                    $ultimoMovimiento["tipo_averia"]=
                            Input::get('tipo_averia');
                    $ultimoMovimiento["horas_averia"]=
                            Input::get('horas_averia');
                    $ultimoMovimiento["fecha_registro"]=
                            Input::get('fecha_registro');
                    $ultimoMovimiento["ciudad"]=
                            Input::get('ciudad');
                    $ultimoMovimiento["inscripcion"]=
                            Input::get('inscripcion');
                    $ultimoMovimiento["fono1"]=
                            Input::get('fono1');
                    $ultimoMovimiento["telefono"]=
                            Input::get('telefono');
                    $ultimoMovimiento["mdf"]=
                            Input::get('mdf');
                    $ultimoMovimiento["observacion"]=
                            Input::get('observacion');
                    $ultimoMovimiento["segmento"]=
                            Input::get('segmento');
                    $ultimoMovimiento["area"]=
                            Input::get('area');
                    $ultimoMovimiento["direccion_instalacion"]=
                            Input::get('direccion_instalacion');
                    $ultimoMovimiento["codigo_distrito"]=
                            Input::get('codigo_distrito');
                    $ultimoMovimiento["nombre_cliente"]=
                            Input::get('nombre_cliente');
                    $ultimoMovimiento["orden_trabajo"]=
                            Input::get('orden_trabajo');
                    $ultimoMovimiento["veloc_adsl"]=
                            Input::get('veloc_adsl');
                    $ultimoMovimiento["clase_servicio_catv"]=
                            Input::get('clase_servicio_catv');
                    $ultimoMovimiento["codmotivo_req_catv"]=
                            Input::get('codmotivo_req_catv');
                    $ultimoMovimiento["total_averias_cable"]=
                            Input::get('total_averias_cable');
                    $ultimoMovimiento["total_averias_cobre"]=
                            Input::get('total_averias_cobre');
                    $ultimoMovimiento["total_averias"]=
                            Input::get('total_averias');
                    $ultimoMovimiento["fftt"]=
                            Input::get('fftt');
                    $ultimoMovimiento["llave"]=
                            Input::get('llave');
                    $ultimoMovimiento["dir_terminal"]=
                            Input::get('dir_terminal');
                    $ultimoMovimiento["fonos_contacto"]=
                            Input::get('fonos_contacto');
                    $ultimoMovimiento["contrata"]=
                            Input::get('contrata');
                    $ultimoMovimiento["zonal"]=
                            Input::get('zonal');
                    $ultimoMovimiento["wu_nagendas"]=
                            Input::get('wu_nagendas');
                    $ultimoMovimiento["wu_nmovimientos"]=
                            Input::get('wu_nmovimientos');
                    $ultimoMovimiento["wu_fecha_ult_agenda"]=
                            Input::get('wu_fecha_ult_agenda');
                    $ultimoMovimiento["total_llamadas_tecnicas"]=
                            Input::get('total_llamadas_tecnicas');
                    $ultimoMovimiento["total_llamadas_seguimiento"]=
                            Input::get('total_llamadas_seguimiento');
                    $ultimoMovimiento["llamadastec15dias"]=
                            Input::get('llamadastec15dias');
                    $ultimoMovimiento["llamadastec30dias"]=
                            Input::get('llamadastec30dias');
                    $ultimoMovimiento["lejano"]=
                            Input::get('lejano');
                    $ultimoMovimiento["distrito"]=
                            Input::get('distrito');
                    $ultimoMovimiento["eecc_zona"]=
                            Input::get('eecc_zona');
                    $ultimoMovimiento["zona_movistar_uno"]=
                            Input::get('zona_movistar_uno');
                    $ultimoMovimiento["paquete"]=
                            Input::get('paquete');
                    $ultimoMovimiento["data_multiproducto"]=
                            Input::get('data_multiproducto');
                    $ultimoMovimiento["averia_m1"]=
                            Input::get('averia_m1');
                    $ultimoMovimiento["fecha_data_fuente"]=
                            Input::get('fecha_data_fuente');
                    $ultimoMovimiento["telefono_codclientecms"]=
                            Input::get('telefono_codclientecms');
                    $ultimoMovimiento["rango_dias"]=
                            Input::get('rango_dias');
                    $ultimoMovimiento["sms1"]=
                            Input::get('sms1');
                    $ultimoMovimiento["sms2"]=
                            Input::get('sms2');
                    $ultimoMovimiento["area2"]=
                            Input::get('area2');
                    $ultimoMovimiento["microzona"]=
                            Input::get('microzona');
                    $ultimoMovimiento["tipo_actuacion"]=
                            Input::get('tipo_actuacion');
                    

                } catch (Exception $exc) {
                    return  array(
                            'rst'=>2,
                            'msj'=>'Ocurrió una interrupción en el registro de la gestion detalle',
                            'err'=> $exc
                        );
                }

                try {
                    $gestionesMovimientos = new GestionMovimiento;
                    $gestionesMovimientos["gestion_id"]=$id;

                    $gestionesMovimientos["quiebre_id"]=
                                Input::get('quiebre_id');

                    $gestionesMovimientos["empresa_id"]=
                                Input::get('empresa_id');
                    $gestionesMovimientos["zonal_id"]=
                                Input::get('zonal_id');
                    $gestionesMovimientos["estado_id"]=
                                Input::get('estado');
                    $gestionesMovimientos["motivo_id"]=
                                Input::get('motivo');
                    $gestionesMovimientos["submotivo_id"]=
                                Input::get('submotivo');
                    $gestionesMovimientos["observacion"]=
                                Input::get('observacion2');
                    $gestionesMovimientos["coordinado"]=
                                Input::get('coordinado2');

                    $ultimoMovimiento["empresa_m_id"]=
                                Input::get('empresa_id');
                    $ultimoMovimiento["estado_id"]=
                                Input::get('estado');
                    $ultimoMovimiento["motivo_id"]=
                                Input::get('motivo');
                    $ultimoMovimiento["submotivo_id"]=
                                Input::get('submotivo');
                    $ultimoMovimiento["observacion_m"]=
                                Input::get('observacion2');
                    $ultimoMovimiento["coordinado"]=
                                Input::get('coordinado2');

                    if ( Input::get('flag_tecnico') ) {
                    $gestionesMovimientos["flag_tecnico"]=
                                Input::get('flag_tecnico');
                    $ultimoMovimiento["flag_tecnico"]=
                                Input::get('flag_tecnico');
                    }            

                    if ( Input::get('horario_id') && Input::get('horario_id')!='' ) {
                    $gestionesMovimientos["horario_id"]=
                                Input::get('horario_id');
                    $gestionesMovimientos["dia_id"]=
                                Input::get('dia_id');
                    $gestionesMovimientos["fecha_agenda"]=
                                Input::get('fecha_agenda');

                    $ultimoMovimiento["horario_id"]=
                                Input::get('horario_id');
                    $ultimoMovimiento["dia_id"]=
                                Input::get('dia_id');
                    $ultimoMovimiento["fecha_agenda"]=
                                Input::get('fecha_agenda');
                    }

                    if ( Input::get('tecnico') && Input::get('tecnico')!='' ) {
                    $gestionesMovimientos["celula_id"]=
                                Input::get('celula');
                    $gestionesMovimientos["tecnico_id"]=
                                Input::get('tecnico');

                    $ultimoMovimiento["celula_id"]=
                                Input::get('celula');
                    $ultimoMovimiento["tecnico_id"]=
                                Input::get('tecnico');
                    }

                    if ( Input::get('fecha_consolidacion') && Input::get('fecha_consolidacion')!='' ) {
                    $gestionesMovimientos["fecha_consolidacion"]=
                                Input::get('fecha_consolidacion');

                    $ultimoMovimiento["fecha_consolidacion"]=
                                Input::get('fecha_consolidacion');
                    }
                    
                    //Origen del movimiento realizado
                    $gestionesMovimientos["submodulo_id"] = 3;
                    if ( Input::get('submodulo_id') !== null )
                    {
                        $gestionesMovimientos["submodulo_id"] = 
                                Input::get('submodulo_id');
                    }
                    
                    //OFSC
                    $ultimoMovimiento["actividad_tipo_id"]=
                            Input::get('actividad_tipo_id');

                    $gestionesMovimientos['usuario_created_at']=Auth::user()->id;
                    $gestionesMovimientos->save();

                    $ultimoMovimiento['usuario_updated_at']=Auth::user()->id;
                    $ultimoMovimiento['updated_at']=date("Y-m-d H:i:s");
                    $ultimoMovimiento['usuario_created_at']=Auth::user()->id;
                    $ultimoMovimiento->save();
                    
                    //Control de cupos
                    $gestionesMovimientos->estado_agendamiento 
                            = Input::get('estado_agendamiento');
                    $cupos = $this->controlarCupos($gestionesMovimientos);
                                        
                } catch (Exception $exc) {
                    return  array(
                            'rst'=>2,
                            'msj'=>'Ocurrió una interrupción en el registro del movimiento',
                            'err'=> $exc
                        );
                }

                if ( Input::get('contacto') ) {
                    try {
                        $liquidados = new Liquidado();
                        $liquidados['gestion_id']=$id;

                        $liquidados['feedback_liquidado_id']=
                            Input::get('feedback');
                        $liquidados['solucion_comercial_id']=
                            Input::get('solucion');
                        $liquidados['contacto']=
                            Input::get('contacto');
                        $liquidados['pruebas']=
                            Input::get('pruebas');
                        $liquidados['fecha_consolidacion']=
                            Input::get('fecha_consolidacion');
                        $liquidados['penalizable']=
                            Input::get('penalizable_obs');

                            if ( Input::get('cumplimiento') ) {
                                $liquidados['cumplimiento_agenda']= Input::get('cumplimiento');
                            }

                        $liquidados['usuario_created_at']=Auth::user()->id;
                        $liquidados->save();
                    } catch (Exception $exc) {
                        return  array(
                            'rst'=>2,
                            'msj'=>'Ocurrió una interrupción en el registro de la gestion',
                            'err'=> $exc
                        );
                    }
                }
                
            /*
            print_r($gestiones);
            print_r($gestionesDetalles);
            print_r($gestionesMovimientos);
            */
            }

            if( Input::get('componente') ){
                $validar=DB::table('componente_gestion')
                            ->where('gestion_id','=',$gestionId)
                            ->get();

                if( count($validar)==0 ){
                    $componentes= Input::get('componente');
                    for($i=0; $i<count($componentes); $i++){
                        $comp=new ComponenteGestion;
                        $comp['componente_id']=$componentes[$i];
                        $comp['gestion_id']=$gestionId;
                        $comp['usuario_created_at']=Auth::user()->id;
                        $comp->save();
                    }
                }
            }
            
            if (!$cupos) {
                return  array(
                    'rst'=>2,
                    'msj'=>'No hay cupos disponibles para el horario'
                           . ' seleccionado. Vuelva a cargar los horarios'
                           . ' de agendamiento.',
                    'err' => 'No hay cupo'
                );
            } else {
                return  array(
                    'rst'=>1,
                    'codactu'=>Input::get('codactu'),
                    'gestion_id'=>$gestionId
                );
            }
            
            
        }

    }

    public function postValidaofficetrack()
    {
        $cant= GestionMovimiento::getValidaOfficetrack();
        return $cant;
    }
    
    
    public function controlarCupos($data){
        
        $cupos = 0;
        $contar = 0;
        $orden = 0;
        $cupoBool = true;
        
        if (substr($data->estado_agendamiento, -2)=='-1' ) {
            $result = GestionMovimiento::getControlarCupos($data);
            
            //Cupos disponibles            
            foreach ($result["cupos"] as $val) {
                $cupos = $val->capacidad;
            }
            
            //Conteo y control
            
            foreach ($result["ocupado"] as $val) {
                $contar++;
                if ($data->id == $val->id) {
                    $orden = $contar;
                }
            }
            
            //Si orden > cupos: FALSE
            if ($orden > $cupos) {
                $cupoBool = false;
            }
            
        }
        
        return $cupoBool;
    }
    
    public function postRegistrar()
    {
        try {
            DB::beginTransaction();
            
            $returnArray = array(
                "rst" => 0,
                "msj" => "No se realizaron cambios",
                "act" => null,
                "error" => "",
                "gestion_id" => ""
            );
     
            if ( Request::ajax() or Input::get('noajax') ) {
                $gestionId="";
                $cupos = true;
                if ( Input::get('gestion_id') ) {
                    
                    $returnArray["act"] = "Nuevo movimiento creado";
                    
                    $gestionId= Input::get('gestion_id');
                    
                    $ultmov= DB::table('ultimos_movimientos')
                            ->where('gestion_id', '=', $gestionId)
                            ->first();
                    $ultimoMovimiento= UltimoMovimiento::find($ultmov->id);


                    $gestionesMovimientos = new GestionMovimiento;
                    $gestionesMovimientos["quiebre_id"]=
                                Input::get('quiebre_id');
                    $gestionesMovimientos["gestion_id"]=
                                Input::get('gestion_id');
                    $gestionesMovimientos["empresa_id"]=
                                Input::get('empresa_id');
                    $gestionesMovimientos["zonal_id"]=
                                Input::get('zonal_id');
                    $gestionesMovimientos["estado_id"]=
                                Input::get('estado');
                    $gestionesMovimientos["motivo_id"]=
                                Input::get('motivo');
                    $gestionesMovimientos["submotivo_id"]=
                                Input::get('submotivo');
                    $gestionesMovimientos["observacion"]=
                                Input::get('observacion2');
                    $gestionesMovimientos["coordinado"]=
                                Input::get('coordinado2');


                    $ultimoMovimiento["quiebre_id"]=
                                Input::get('quiebre_id');
                    $ultimoMovimiento["empresa_m_id"]=
                                Input::get('empresa_id');
                    $ultimoMovimiento["zonal_id"]=
                                Input::get('zonal_id');
                    $ultimoMovimiento["estado_id"]=
                                Input::get('estado');
                    $ultimoMovimiento["motivo_id"]=
                                Input::get('motivo');
                    $ultimoMovimiento["submotivo_id"]=
                                Input::get('submotivo');
                    $ultimoMovimiento["observacion_m"]=
                                Input::get('observacion2');
                    $ultimoMovimiento["coordinado"]=
                                Input::get('coordinado2');

                    if ( Input::get('flag_tecnico') ) {
                        $ultimoMovimiento["flag_tecnico"]=
                                    Input::get('flag_tecnico');
                        $gestionesMovimientos["flag_tecnico"]=
                                    Input::get('flag_tecnico');
                    }            

                    if ( Input::get('horario_id') && Input::get('horario_id')!='' ) {
                        $gestionesMovimientos["horario_id"]=
                                    Input::get('horario_id');
                        $gestionesMovimientos["dia_id"]=
                                    Input::get('dia_id');
                        $gestionesMovimientos["fecha_agenda"]=
                                    Input::get('fecha_agenda');

                        $ultimoMovimiento["horario_id"]=
                                    Input::get('horario_id');
                        $ultimoMovimiento["dia_id"]=
                                    Input::get('dia_id');
                        $ultimoMovimiento["fecha_agenda"]=
                                    Input::get('fecha_agenda');
                    }

                    if ( Input::get('tecnico') && Input::get('tecnico')!='' ) {
                        $gestionesMovimientos["celula_id"]=
                                    Input::get('celula');
                        $gestionesMovimientos["tecnico_id"]=
                                    Input::get('tecnico');

                        $ultimoMovimiento["celula_id"]=
                                    Input::get('celula');
                        $ultimoMovimiento["tecnico_id"]=
                                    Input::get('tecnico');
                        }

                        if ( Input::get('fecha_consolidacion') && Input::get('fecha_consolidacion')!='' ) {
                        $gestionesMovimientos["fecha_consolidacion"]=
                                    Input::get('fecha_consolidacion');

                        $ultimoMovimiento["fecha_consolidacion"]=
                                    Input::get('fecha_consolidacion');
                        }

                        //Origen del movimiento realizado
                        $gestionesMovimientos["submodulo_id"] = 3;
                        if ( Input::get('submodulo_id') !== null )
                        {
                            $gestionesMovimientos["submodulo_id"] = 
                                    Input::get('submodulo_id');
                        }

                         if ( Input::get('usuario_sistema') !== null )
                        {
                            $gestionesMovimientos['usuario_created_at']=697;
                        }

                        $gestionesMovimientos['estado_ofsc_id']='0';
                        $gestionesMovimientos['usuario_created_at']=Auth::user()->id;
                        $gestionesMovimientos->save();

                        if( substr(Input::get('estado_agendamiento'),-2)=='-1' ) {
                            $gestionesDetalles=GestionDetalle::where('gestion_id', '=', $gestionId)
                                               ->update(
                                                    array(
                                                        'x' => Input::get('x'),
                                                        'y' => Input::get('y')
                                                    )
                                                );

                            $ultimoMovimiento['x']=Input::get('x');
                            $ultimoMovimiento['y']=Input::get('y');
                        }

                        $ultimoMovimiento['estado_ofsc_id']='0';
                        $ultimoMovimiento['usuario_updated_at']=Auth::user()->id;
                        $ultimoMovimiento->save();

                } else {
                    $id="";
                    
                    $returnArray["act"] = "Nuevo registro creado";
                    
                        $gestiones = new Gestion;
                        $ultimoMovimiento= new UltimoMovimiento;

                        $gestiones["actividad_id"]=
                        Input::get('actividad_id');
                        $gestiones["nombre_cliente_critico"]=
                        Input::get('nombre_cliente_critico');
                        $gestiones["telefono_cliente_critico"]=
                        Input::get('telefono_cliente_critico');
                        $gestiones["celular_cliente_critico"]=
                        Input::get('celular_cliente_critico');
                        $gestiones['usuario_created_at']=Auth::user()->id;

                        $gestiones->save();
                        

                        $ultimoMovimiento["actividad_id"]=
                        Input::get('actividad_id');
                        $ultimoMovimiento["nombre_cliente_critico"]=
                        Input::get('nombre_cliente_critico');
                        $ultimoMovimiento["telefono_cliente_critico"]=
                        Input::get('telefono_cliente_critico');
                        $ultimoMovimiento["celular_cliente_critico"]=
                        Input::get('celular_cliente_critico');

                        $ultimoMovimiento['usuario_created_at']=Auth::user()->id;

                    
                        $gestionId= $gestiones->id;
                        $id=$gestiones->id;
                        $gestiones["id_atc"]="ATC_".date("Y")."_".$id;
                        $gestiones->save();

                        $ultimoMovimiento["id_atc"]="ATC_".date("Y")."_".$id;
                    

                        $gestionesDetalles = new GestionDetalle;
                        $gestionesDetalles["gestion_id"]=$id;
                        $gestionesDetalles["quiebre_id"]=
                                Input::get('quiebre_id');
                        $gestionesDetalles["empresa_id"]=
                                Input::get('empresa_id');
                        $gestionesDetalles["zonal_id"]=
                                Input::get('zonal_id');
                        $gestionesDetalles["codactu"]=
                                Input::get('codactu');
                        $gestionesDetalles["tipo_averia"]=
                                Input::get('tipo_averia');
                        $gestionesDetalles["horas_averia"]=
                                Input::get('horas_averia');
                        $gestionesDetalles["fecha_registro"]=
                                Input::get('fecha_registro');
                        $gestionesDetalles["ciudad"]=
                                Input::get('ciudad');
                        $gestionesDetalles["inscripcion"]=
                                Input::get('inscripcion');
                        $gestionesDetalles["fono1"]=
                                Input::get('fono1');
                        $gestionesDetalles["telefono"]=
                                Input::get('telefono');
                        $gestionesDetalles["mdf"]=
                                Input::get('mdf');
                        $gestionesDetalles["observacion"]=
                                Input::get('observacion');
                        $gestionesDetalles["segmento"]=
                                Input::get('segmento');
                        $gestionesDetalles["area"]=
                                Input::get('area');
                        $gestionesDetalles["direccion_instalacion"]=
                                Input::get('direccion_instalacion');
                        $gestionesDetalles["codigo_distrito"]=
                                Input::get('codigo_distrito');
                        $gestionesDetalles["nombre_cliente"]=
                                Input::get('nombre_cliente');
                        $gestionesDetalles["orden_trabajo"]=
                                Input::get('orden_trabajo');
                        $gestionesDetalles["veloc_adsl"]=
                                Input::get('veloc_adsl');
                        $gestionesDetalles["clase_servicio_catv"]=
                                Input::get('clase_servicio_catv');
                        $gestionesDetalles["codmotivo_req_catv"]=
                                Input::get('codmotivo_req_catv');
                        $gestionesDetalles["total_averias_cable"]=
                                Input::get('total_averias_cable');
                        $gestionesDetalles["total_averias_cobre"]=
                                Input::get('total_averias_cobre');
                        $gestionesDetalles["total_averias"]=
                                Input::get('total_averias');
                        $gestionesDetalles["fftt"]=
                                Input::get('fftt');
                        $gestionesDetalles["llave"]=
                                Input::get('llave');
                        $gestionesDetalles["dir_terminal"]=
                                Input::get('dir_terminal');
                        $gestionesDetalles["fonos_contacto"]=
                                Input::get('fonos_contacto');
                        $gestionesDetalles["contrata"]=
                                Input::get('contrata');
                        $gestionesDetalles["zonal"]=
                                Input::get('zonal');
                        $gestionesDetalles["wu_nagendas"]=
                                Input::get('wu_nagendas');
                        $gestionesDetalles["wu_nmovimientos"]=
                                Input::get('wu_nmovimientos');
                        $gestionesDetalles["wu_fecha_ult_agenda"]=
                                Input::get('wu_fecha_ult_agenda');
                        $gestionesDetalles["total_llamadas_tecnicas"]=
                                Input::get('total_llamadas_tecnicas');
                        $gestionesDetalles["total_llamadas_seguimiento"]=
                                Input::get('total_llamadas_seguimiento');
                        $gestionesDetalles["llamadastec15dias"]=
                                Input::get('llamadastec15dias');
                        $gestionesDetalles["llamadastec30dias"]=
                                Input::get('llamadastec30dias');
                        $gestionesDetalles["lejano"]=
                                Input::get('lejano');
                        $gestionesDetalles["distrito"]=
                                Input::get('distrito');
                        $gestionesDetalles["eecc_zona"]=
                                Input::get('eecc_zona');
                        $gestionesDetalles["zona_movistar_uno"]=
                                Input::get('zona_movistar_uno');
                        $gestionesDetalles["paquete"]=
                                Input::get('paquete');
                        $gestionesDetalles["data_multiproducto"]=
                                Input::get('data_multiproducto');
                        $gestionesDetalles["averia_m1"]=
                                Input::get('averia_m1');
                        $gestionesDetalles["fecha_data_fuente"]=
                                Input::get('fecha_data_fuente');
                        $gestionesDetalles["telefono_codclientecms"]=
                                Input::get('telefono_codclientecms');
                        $gestionesDetalles["rango_dias"]=
                                Input::get('rango_dias');
                        $gestionesDetalles["sms1"]=
                                Input::get('sms1');
                        $gestionesDetalles["sms2"]=
                                Input::get('sms2');
                        $gestionesDetalles["area2"]=
                                Input::get('area2');
                        $gestionesDetalles["microzona"]=
                                Input::get('microzona');
                        $gestionesDetalles["tipo_actuacion"]=
                                Input::get('tipo_actuacion');
                        $gestionesDetalles["actividad_tipo_id"]=
                                Input::get('actividad_tipo_id');

                        if( Input::get('x') and Input::get('y')/*substr(Input::get('estado_agendamiento'),-2)=='-1'*/ ) {
                            $gestionesDetalles["x"]=
                                    Input::get('x');
                            $gestionesDetalles["y"]=
                                    Input::get('y');

                            $ultimoMovimiento["x"]=
                                    Input::get('x');
                            $ultimoMovimiento["y"]=
                                    Input::get('y');
                        }

                        $gestionesDetalles['usuario_created_at']=Auth::user()->id;
                        $gestionesDetalles->save();
                        

                        /**********************************************************/
                        $ffttExplode=explode("|", Input::get('fftt'));
                        $tipoAExplode=explode("-", Input::get('tipo_averia'));
                        if( count($tipoAExplode)==1 ){
                            $tipoAExplode=explode("_", Input::get('tipo_averia'));
                        }

                        $arrayproadsl=array(1,2,4);
                        $arrayprocatv=array(5,6,7,8,9);

                        $arrayaveradsl=array(1,2,3,14,15,16,17,9,18,19,20);
                        $arrayaverbas=array(1,2,3,10,11,12,4,9);
                        $arrayavercatv=array(5,13,6,7,8,9);


                        $sqlttff='INSERT INTO gestiones_fftt (gestion_id,fftt_tipo_id,nombre) VALUES (?,?,?)';
                        if ( in_array('aver', $tipoAExplode, true) AND count($ffttExplode)>1 ){
                            if ( in_array('adsl', $tipoAExplode, true) ){
                                for($i=0; $i<count($arrayaveradsl); $i++){
                                    $array=array($id,$arrayaveradsl[$i],trim($ffttExplode[$i]));

                                    if( trim($ffttExplode[$i])!='' ){
                                        DB::insert($sqlttff, $array);
                                    }
                                }
                            }
                            elseif ( in_array('bas', $tipoAExplode, true) ){
                                for($i=0; $i<count($arrayaverbas); $i++){
                                    $array=array($id,$arrayaverbas[$i],trim($ffttExplode[$i]));

                                    if( trim($ffttExplode[$i])!='' ){
                                        DB::insert($sqlttff, $array);
                                    }
                                }
                            }
                            elseif ( in_array('catv', $tipoAExplode, true) ){
                                for($i=0; $i<count($arrayavercatv); $i++){
                                    $array=array($id,$arrayavercatv[$i],trim($ffttExplode[$i]));

                                    if( trim($ffttExplode[$i])!='' ){
                                        DB::insert($sqlttff, $array);
                                    }
                                }
                            }
                        }
                        elseif ( in_array('prov', $tipoAExplode, true) AND count($ffttExplode)>1 ){
                            if ( in_array('adsl', $tipoAExplode, true) OR in_array('bas', $tipoAExplode, true) ){
                                for($i=0; $i<count($arrayproadsl); $i++){
                                    if( $i==1 AND strtoupper(substr(trim($ffttExplode[$i]),0,1))=='A' ){
                                        $array=array($id,$arrayproadsl[$i],trim($ffttExplode[$i]));
                                    }
                                    elseif($i==1 ){
                                        $array=array($id,3,trim($ffttExplode[$i]));
                                    }
                                    else{
                                        $array=array($id,$arrayproadsl[$i],trim($ffttExplode[$i]));
                                    }

                                    if( trim($ffttExplode[$i])!='' ){
                                        DB::insert($sqlttff, $array);
                                    }
                                }
                            }
                            elseif ( in_array('catv', $tipoAExplode, true) ){
                                for($i=0; $i<count($arrayprocatv); $i++){
                                    $array=array($id,$arrayprocatv[$i],trim($ffttExplode[$i]));

                                    if( trim($ffttExplode[$i])!='' ){
                                        DB::insert($sqlttff, $array);
                                    }
                                }
                            }
                        }
                        /**********************************************************/

                        $ultimoMovimiento["gestion_id"]=$id;
                        $ultimoMovimiento["quiebre_id"]=
                                Input::get('quiebre_id');
                        $ultimoMovimiento["empresa_id"]=
                                Input::get('empresa_id');
                        $ultimoMovimiento["zonal_id"]=
                                Input::get('zonal_id');
                        $ultimoMovimiento["codactu"]=
                                Input::get('codactu');
                        $ultimoMovimiento["tipo_averia"]=
                                Input::get('tipo_averia');
                        $ultimoMovimiento["horas_averia"]=
                                Input::get('horas_averia');
                        $ultimoMovimiento["fecha_registro"]=
                                Input::get('fecha_registro');
                        $ultimoMovimiento["ciudad"]=
                                Input::get('ciudad');
                        $ultimoMovimiento["inscripcion"]=
                                Input::get('inscripcion');
                        $ultimoMovimiento["fono1"]=
                                Input::get('fono1');
                        $ultimoMovimiento["telefono"]=
                                Input::get('telefono');
                        $ultimoMovimiento["mdf"]=
                                Input::get('mdf');
                        $ultimoMovimiento["observacion"]=
                                Input::get('observacion');
                        $ultimoMovimiento["segmento"]=
                                Input::get('segmento');
                        $ultimoMovimiento["area"]=
                                Input::get('area');
                        $ultimoMovimiento["direccion_instalacion"]=
                                Input::get('direccion_instalacion');
                        $ultimoMovimiento["codigo_distrito"]=
                                Input::get('codigo_distrito');
                        $ultimoMovimiento["nombre_cliente"]=
                                Input::get('nombre_cliente');
                        $ultimoMovimiento["orden_trabajo"]=
                                Input::get('orden_trabajo');
                        $ultimoMovimiento["veloc_adsl"]=
                                Input::get('veloc_adsl');
                        $ultimoMovimiento["clase_servicio_catv"]=
                                Input::get('clase_servicio_catv');
                        $ultimoMovimiento["codmotivo_req_catv"]=
                                Input::get('codmotivo_req_catv');
                        $ultimoMovimiento["total_averias_cable"]=
                                Input::get('total_averias_cable');
                        $ultimoMovimiento["total_averias_cobre"]=
                                Input::get('total_averias_cobre');
                        $ultimoMovimiento["total_averias"]=
                                Input::get('total_averias');
                        $ultimoMovimiento["fftt"]=
                                Input::get('fftt');
                        $ultimoMovimiento["llave"]=
                                Input::get('llave');
                        $ultimoMovimiento["dir_terminal"]=
                                Input::get('dir_terminal');
                        $ultimoMovimiento["fonos_contacto"]=
                                Input::get('fonos_contacto');
                        $ultimoMovimiento["contrata"]=
                                Input::get('contrata');
                        $ultimoMovimiento["zonal"]=
                                Input::get('zonal');
                        $ultimoMovimiento["wu_nagendas"]=
                                Input::get('wu_nagendas');
                        $ultimoMovimiento["wu_nmovimientos"]=
                                Input::get('wu_nmovimientos');
                        $ultimoMovimiento["wu_fecha_ult_agenda"]=
                                Input::get('wu_fecha_ult_agenda');
                        $ultimoMovimiento["total_llamadas_tecnicas"]=
                                Input::get('total_llamadas_tecnicas');
                        $ultimoMovimiento["total_llamadas_seguimiento"]=
                                Input::get('total_llamadas_seguimiento');
                        $ultimoMovimiento["llamadastec15dias"]=
                                Input::get('llamadastec15dias');
                        $ultimoMovimiento["llamadastec30dias"]=
                                Input::get('llamadastec30dias');
                        $ultimoMovimiento["lejano"]=
                                Input::get('lejano');
                        $ultimoMovimiento["distrito"]=
                                Input::get('distrito');
                        $ultimoMovimiento["eecc_zona"]=
                                Input::get('eecc_zona');
                        $ultimoMovimiento["zona_movistar_uno"]=
                                Input::get('zona_movistar_uno');
                        $ultimoMovimiento["paquete"]=
                                Input::get('paquete');
                        $ultimoMovimiento["data_multiproducto"]=
                                Input::get('data_multiproducto');
                        $ultimoMovimiento["averia_m1"]=
                                Input::get('averia_m1');
                        $ultimoMovimiento["fecha_data_fuente"]=
                                Input::get('fecha_data_fuente');
                        $ultimoMovimiento["telefono_codclientecms"]=
                                Input::get('telefono_codclientecms');
                        $ultimoMovimiento["rango_dias"]=
                                Input::get('rango_dias');
                        $ultimoMovimiento["sms1"]=
                                Input::get('sms1');
                        $ultimoMovimiento["sms2"]=
                                Input::get('sms2');
                        $ultimoMovimiento["area2"]=
                                Input::get('area2');
                        $ultimoMovimiento["microzona"]=
                                Input::get('microzona');
                        $ultimoMovimiento["tipo_actuacion"]=
                                Input::get('tipo_actuacion');


                    
                        $gestionesMovimientos = new GestionMovimiento;
                        $gestionesMovimientos["gestion_id"]=$id;

                        $gestionesMovimientos["quiebre_id"]=
                                    Input::get('quiebre_id');

                        $gestionesMovimientos["empresa_id"]=
                                    Input::get('empresa_id');
                        $gestionesMovimientos["zonal_id"]=
                                    Input::get('zonal_id');
                        $gestionesMovimientos["estado_id"]=
                                    Input::get('estado');
                        $gestionesMovimientos["motivo_id"]=
                                    Input::get('motivo');
                        $gestionesMovimientos["submotivo_id"]=
                                    Input::get('submotivo');
                        $gestionesMovimientos["observacion"]=
                                    Input::get('observacion2');
                        $gestionesMovimientos["coordinado"]=
                                    Input::get('coordinado2');

                        $ultimoMovimiento["empresa_m_id"]=
                                    Input::get('empresa_id');
                        $ultimoMovimiento["estado_id"]=
                                    Input::get('estado');
                        $ultimoMovimiento["motivo_id"]=
                                    Input::get('motivo');
                        $ultimoMovimiento["submotivo_id"]=
                                    Input::get('submotivo');
                        $ultimoMovimiento["observacion_m"]=
                                    Input::get('observacion2');
                        $ultimoMovimiento["coordinado"]=
                                    Input::get('coordinado2');

                        if ( Input::get('flag_tecnico') ) {
                        $gestionesMovimientos["flag_tecnico"]=
                                    Input::get('flag_tecnico');
                        $ultimoMovimiento["flag_tecnico"]=
                                    Input::get('flag_tecnico');
                        }            

                        if ( Input::get('horario_id') && Input::get('horario_id')!='' ) {
                        $gestionesMovimientos["horario_id"]=
                                    Input::get('horario_id');
                        $gestionesMovimientos["dia_id"]=
                                    Input::get('dia_id');
                        $gestionesMovimientos["fecha_agenda"]=
                                    Input::get('fecha_agenda');

                        $ultimoMovimiento["horario_id"]=
                                    Input::get('horario_id');
                        $ultimoMovimiento["dia_id"]=
                                    Input::get('dia_id');
                        $ultimoMovimiento["fecha_agenda"]=
                                    Input::get('fecha_agenda');
                        }

                        if ( Input::get('tecnico') && Input::get('tecnico')!='' ) {
                        $gestionesMovimientos["celula_id"]=
                                    Input::get('celula');
                        $gestionesMovimientos["tecnico_id"]=
                                    Input::get('tecnico');

                        $ultimoMovimiento["celula_id"]=
                                    Input::get('celula');
                        $ultimoMovimiento["tecnico_id"]=
                                    Input::get('tecnico');
                        }

                        if ( Input::get('fecha_consolidacion') && Input::get('fecha_consolidacion')!='' ) {
                        $gestionesMovimientos["fecha_consolidacion"]=
                                    Input::get('fecha_consolidacion');

                        $ultimoMovimiento["fecha_consolidacion"]=
                                    Input::get('fecha_consolidacion');
                        }

                        //Origen del movimiento realizado
                        $gestionesMovimientos["submodulo_id"] = 3;
                        if ( Input::get('submodulo_id') !== null )
                        {
                            $gestionesMovimientos["submodulo_id"] = 
                                    Input::get('submodulo_id');
                        }

                        //OFSC
                        $ultimoMovimiento["actividad_tipo_id"]=
                                Input::get('actividad_tipo_id');

                        $gestionesMovimientos['estado_ofsc_id']='0';
                        $gestionesMovimientos['usuario_created_at']=Auth::user()->id;
                        $gestionesMovimientos->save();

                        $ultimoMovimiento['estado_ofsc_id']='0';
                        $ultimoMovimiento['usuario_updated_at']=Auth::user()->id;
                        $ultimoMovimiento['updated_at']=date("Y-m-d H:i:s");
                        $ultimoMovimiento['usuario_created_at']=Auth::user()->id;
                        $ultimoMovimiento->save();

                }
                $returnArray["gestion_id"] = $gestionId;
                $returnArray['gestion_movimiento_id']=$gestionesMovimientos->id;
                
                DB::commit();
            
                $returnArray["rst"] = 1;
                $returnArray["msj"] = 'Registro realizado correctamente';
                $returnArray["error"] = "";
            }
            
            return $returnArray;
        } catch (Exception $exc) {
            DB::rollback();
            
            $returnArray["rst"] = 2;
            $returnArray["msj"] = 'Ocurrió un error en el registro';
            $returnArray["error"] = $exc->getMessage();
            
            return $returnArray;
        }

    }

}
