<?php

require_once "lib/nusoap.php";
$url = ( $_SERVER['SERVER_NAME'] == '10.226.44.222' ?
                'http://test/soap_opp/psi.php?wsdl' :
                'http://localhost/webpsi20/wspsi20/psi.php?wsdl');
$client = new nusoap_client($url, true);
$error = $client->getError();

if ($error) {
    echo "<h2>Constructor error</h2>"
    . "<pre>$error</pre>";
}

$result = $client->call("Tarea.select"/* , array("id" => '1') */);
$errors = $client->getError();
if ($client->fault) {
    echo "<h2>Fault</h2>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";
} elseif ($errors) {
    echo "<h2>Error</h2>"
    . "<pre>$errors</pre>";
} else {
    echo "<h2>Main</h2>";
    foreach ($result['item'] as $tarea) {
        echo $tarea['asunto'] . ' , ' .
        $tarea['actividad'] . " , " .
        $tarea['requerimiento'] . " , " .
        $tarea['serie_deco'] . " , " .
        $tarea['serie_tarjeta'] . " , " .
        $tarea['telefono_origen'];
        echo "<br />";
    }
}

// show soap request and response
echo "<h2>Request</h2>";
echo "<pre>" . htmlspecialchars($client->request, ENT_QUOTES) . "</pre>";
echo "<h2>Response</h2>";
echo "<pre>" . htmlspecialchars($client->response, ENT_QUOTES) . "</pre>";
