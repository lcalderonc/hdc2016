<?php
require_once "config/require.php";

$server = new soap_server();
$server->configureWSDL("psi", "urn:psi");
$server->soap_defencoding = 'UTF-8';

/************
Obteneractu
************/
$server->wsdl->addComplexType(
    'peticion_obtener_actu',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'hashg'   => array('name' => 'hashg','type' => 'xsd:string'),
        'gestionId'    => array('name' => 'gestionId','type' => 'xsd:string')
    )
);
$server->wsdl->addComplexType(
    'respuesta_obtener_actu',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'id'   => array('name' => 'id','type' => 'xsd:string'),
        'gestion_id'   => array('name' => 'gestion_id','type' => 'xsd:string'),
        'quiebre_id'   => array('name' => 'quiebre_id','type' => 'xsd:string'),
        'empresa_id'   => array('name' => 'empresa_id','type' => 'xsd:string'),
        'zonal_id'   => array('name' => 'zonal_id','type' => 'xsd:string'),
        'tipo_averia'   => array('name' => 'tipo_averia','type' => 'xsd:string'),
        'horas_averia'   => array('name' => 'horas_averia','type' => 'xsd:string'),
        'fecha_registro'   => array('name' => 'fecha_registro','type' => 'xsd:string'),
        'ciudad'   => array('name' => 'ciudad','type' => 'xsd:string'),
        'codactu'   => array('name' => 'codactu','type' => 'xsd:string'),
        'inscripcion'   => array('name' => 'inscripcion','type' => 'xsd:string'),
        'fono1'   => array('name' => 'fono1','type' => 'xsd:string'),
        'telefono'   => array('name' => 'telefono','type' => 'xsd:string'),
        'mdf'   => array('name' => 'mdf','type' => 'xsd:string'),
        'observacion'   => array('name' => 'observacion','type' => 'xsd:string'),
        'segmento'   => array('name' => 'segmento','type' => 'xsd:string'),
        'area'   => array('name' => 'area','type' => 'xsd:string'),
        'direccion_instalacion'   => array('name' => 'direccion_instalacion','type' => 'xsd:string'),
        'codigo_distrito'   => array('name' => 'codigo_distrito','type' => 'xsd:string'),
        'nombre_cliente'   => array('name' => 'nombre_cliente','type' => 'xsd:string'),
        'orden_trabajo'   => array('name' => 'orden_trabajo','type' => 'xsd:string'),
        'veloc_adsl'   => array('name' => 'veloc_adsl','type' => 'xsd:string'),
        'clase_servicio_catv'   => array('name' => 'clase_servicio_catv','type' => 'xsd:string'),
        'codmotivo_req_catv'   => array('name' => 'codmotivo_req_catv','type' => 'xsd:string'),
        'total_averias_cable'   => array('name' => 'total_averias_cable','type' => 'xsd:string'),
        'total_averias_cobre'   => array('name' => 'total_averias_cobre','type' => 'xsd:string'),
        'total_averias'   => array('name' => 'total_averias','type' => 'xsd:string'),
        'fftt'   => array('name' => 'fftt','type' => 'xsd:string'),
        'llave'   => array('name' => 'llave','type' => 'xsd:string'),
        'dir_terminal'   => array('name' => 'dir_terminal','type' => 'xsd:string'),
        'fonos_contacto'   => array('name' => 'fonos_contacto','type' => 'xsd:string'),
        'contrata'   => array('name' => 'contrata','type' => 'xsd:string'),
        'zonal'   => array('name' => 'zonal','type' => 'xsd:string'),
        'wu_nagendas'   => array('name' => 'wu_nagendas','type' => 'xsd:string'),
        'wu_nmovimientos'   => array('name' => 'wu_nmovimientos','type' => 'xsd:string'),
        'wu_fecha_ult_agenda'   => array('name' => 'wu_fecha_ult_agenda','type' => 'xsd:string'),
        'total_llamadas_tecnicas'   => array('name' => 'total_llamadas_tecnicas','type' => 'xsd:string'),
        'total_llamadas_seguimiento'   => array('name' => 'total_llamadas_seguimiento','type' => 'xsd:string'),
        'llamadastec15dias'   => array('name' => 'llamadastec15dias','type' => 'xsd:string'),
        'llamadastec30dias'   => array('name' => 'llamadastec30dias','type' => 'xsd:string'),
        'lejano'   => array('name' => 'lejano','type' => 'xsd:string'),
        'distrito'   => array('name' => 'distrito','type' => 'xsd:string'),
        'eecc_zona'   => array('name' => 'eecc_zona','type' => 'xsd:string'),
        'zona_movistar_uno'   => array('name' => 'zona_movistar_uno','type' => 'xsd:string'),
        'paquete'   => array('name' => 'paquete','type' => 'xsd:string'),
        'data_multiproducto'   => array('name' => 'data_multiproducto','type' => 'xsd:string'),
        'averia_m1'   => array('name' => 'averia_m1','type' => 'xsd:string'),
        'fecha_data_fuente'   => array('name' => 'fecha_data_fuente','type' => 'xsd:string'),
        'telefono_codclientecms'   => array('name' => 'telefono_codclientecms','type' => 'xsd:string'),
        'rango_dias'   => array('name' => 'rango_dias','type' => 'xsd:string'),
        'sms1'   => array('name' => 'sms1','type' => 'xsd:string'),
        'sms2'   => array('name' => 'sms2','type' => 'xsd:string'),
        'area2'   => array('name' => 'area2','type' => 'xsd:string'),
        'microzona'   => array('name' => 'microzona','type' => 'xsd:string'),
        'tipo_actuacion'   => array('name' => 'tipo_actuacion','type' => 'xsd:string'),
        'x'   => array('name' => 'x','type' => 'xsd:string'),
        'y'   => array('name' => 'y','type' => 'xsd:string'),
        'created_at'   => array('name' => 'created_at','type' => 'xsd:string'),
        'updated_at'   => array('name' => 'updated_at','type' => 'xsd:string'),
        'usuario_created_at'   => array('name' => 'usuario_created_at','type' => 'xsd:string'),
        'usuario_updated_at'   => array('name' => 'usuario_updated_at','type' => 'xsd:string'),
        'codservcms'   => array('name' => 'codservcms','type' => 'xsd:string'),
        'codclie'   => array('name' => 'codclie','type' => 'xsd:string'),
        'estado_legado'   => array('name' => 'estado_legado','type' => 'xsd:string'),
        'fec_liq_legado'   => array('name' => 'fec_liq_legado','type' => 'xsd:string'),
        'contrata_legado'   => array('name' => 'contrata_legado','type' => 'xsd:string'),
        'edificio_id'   => array('name' => 'edificio_id','type' => 'xsd:string')
    )
);
$server->register(
    'WebService.obtener_actu',
    array('peticion_obtener_actu' => 'tns:peticion_obtener_actu'),
    //array("return" => "xsd:string"),
    array('return' => 'tns:respuesta_obtener_actu'),
    'urn:psi',   //namespace
    'urn:psi#obtener_actu',  //soapaction
    'rpc', // style
    'encoded', // use
    'solicitar informacion de una gestion '
);


/************
estado_visitas
************/
$server->wsdl->addComplexType(
    'peticion_estado_visitas',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'hashg'   => array('name' => 'hashg','type' => 'xsd:string'),
        'gestionId'    => array('name' => 'gestionId','type' => 'xsd:string')
    )
);

$server->register(
    'WebService.estado_visitas',
    array('peticion_estado_visitas' => 'tns:peticion_estado_visitas'),
    array("return" => "xsd:string"),
    
    'urn:psi',   //namespace
    'urn:psi#estado_visitas',  //soapaction
    'rpc', // style
    'encoded', // use
    'obtener la cantidad de visitas en una tarea '
);

/************
actualizar_direccion
************/
$server->wsdl->addComplexType(
    'peticion_actualizar_direccion',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'hashg'   => array('name' => 'hashg','type' => 'xsd:string'),
        'gestionId'    => array('name' => 'gestionId','type' => 'xsd:string'),
        'carnet'    => array('name' => 'carnet','type' => 'xsd:string'),
        'x'    => array('name' => 'x','type' => 'xsd:string'),
        'y'    => array('name' => 'y','type' => 'xsd:string'),
        'direccion'    => array('name' => 'direccion','type' => 'xsd:string'),
        'referencia'    => array('name' => 'referencia','type' => 'xsd:string')
    )
);

$server->register(
    'WebService.actualizar_direccion',
    array('peticion_actualizar_direccion' => 'tns:peticion_actualizar_direccion'),
    array("return" => "xsd:string"),
    'urn:psi',   //namespace
    'urn:psi#actualizar_direccion',  //soapaction
    'rpc', // style
    'encoded', // use
    'metodo para cambiar x, y'
);


/************
distancia_actu
************/
$server->wsdl->addComplexType(
    'peticion_distancia_actu',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'hashg'   => array('name' => 'hashg','type' => 'xsd:string'),
        'gestionId'    => array('name' => 'gestionId','type' => 'xsd:string'),
        'x'    => array('name' => 'x','type' => 'xsd:string'),
        'y'    => array('name' => 'y','type' => 'xsd:string'),
        'actu'    => array('name' => 'direccion','type' => 'xsd:string')
    )
);

$server->register(
    'WebService.distancia_actu',
    array('peticion_distancia_actu' => 'tns:peticion_distancia_actu'),
    array("return" => "xsd:string"),
    'urn:psi',   //namespace
    'urn:psi#distancia_actu',  //soapaction
    'rpc', // style
    'encoded', // use
    'devolver la distancia de la tarea al punto x, y'
);
@$server->service($HTTP_RAW_POST_DATA);
