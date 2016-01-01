<?php
require_once "config/require.php";

$server = new soap_server();
$server->configureWSDL("psi", "urn:psi");
$server->soap_defencoding = 'UTF-8';

/************
seleccionar tareas
************/
$server->wsdl->addComplexType(
    'Tarea',
    'complexType',
    'struct',
    'all',
    '',
    array(
            'asunto' => array('name' => 'asunto', 'type' => 'xsd:string'),
            'actividad' => array('name' => 'actividad', 'type' => 'xsd:string'),
            'requerimiento' => array('name' => 'requerimiento', 'type' => 'xsd:string'),
            'serie_deco' => array('name' => 'serie_deco', 'type' => 'xsd:string'),
            'serie_tarjeta' => array('name' => 'serie_tarjeta', 'type' => 'xsd:string'),
            'telefono_origen' => array('name' => 'telefono_origen', 'type' => 'xsd:string')
    )
);
$server->wsdl->addComplexType(
    'return_select2',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
            array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Tarea[]')
    ),
    'tns:Tarea'
);
$server->register(
    "Tarea.select",
    array("id" => "xsd:string"),
    //array('return' => 'tns:return_select'),
    array('return' => 'tns:return_select2'),
    "urn:psi",
    "urn:psi#select",
    "rpc",
    "literal",
    "select tarea"
);


/************
inser into
************/
$server->wsdl->addComplexType(
    'insert_tarea',
    'complexType',
    'struct',
    'all',
    '',
    array(
        'asunto'   => array('name' => 'asunto','type' => 'xsd:string'),
        'actividad'    => array('name' => 'actividad','type' => 'xsd:string'),
        'requerimiento'    => array('name' => 'requerimiento','type' => 'xsd:string'),
        'serieDeco'    => array('name' => 'serieDeco','type' => 'xsd:string'),
        'serieTarjeta' => array('name' => 'serieTarjeta','type' => 'xsd:string'),
        'telefonoOrigen'  => array('name' => 'telefonoOrigen','type' => 'xsd:string'),
        'clave'  => array('name' => 'clave','type' => 'xsd:string'),
        'valor'  => array('name' => 'valor','type' => 'xsd:string')
    )
);
$server->register(
    'Tarea.insert',
    array('insert_tarea' => 'tns:insert_tarea'),
    array("return" => "xsd:string"),
    'urn:psi',   //namespace
    'urn:psi#insert',  //soapaction
    'rpc', // style
    'literal', // use
    'insert Tarea'
);

@$server->service($HTTP_RAW_POST_DATA);