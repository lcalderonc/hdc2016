<?php
require_once "lib/nusoap.php";


$client = new nusoap_client("http://test/soap_opp/psi.php?wsdl", true);
$error  = $client->getError();

if ($error) {
    echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
}

$result = $client->call("Tarea.select"/*, array("id" => '1')*/);
echo "<pre>";

if ($client->fault) {
    echo "<h2>Fault</h2><pre>";
    print_r($result);
    echo "</pre>";
} else {
    $error = $client->getError();
    if ($error) {
        echo "<h2>Error</h2><pre>" . $error . "</pre>";
    } else {
        echo "<h2>Main</h2>";

        foreach ($result as $tarea) {
            echo $tarea['asunto'] . ' , ' .
                 $tarea['actividad'] . " , " .
                 $tarea['requerimiento'] . " , " .
                 $tarea['serie_deco']. " , " .
                 $tarea['serie_tarjeta']. " , " .
                 $tarea['telefono_origen'];
            echo "<br /><br />";
        }
    }
}

// show soap request and response
echo "<h2>Request</h2>";
echo "<pre>" . htmlspecialchars($client->request, ENT_QUOTES) . "</pre>";
echo "<h2>Response</h2>";
echo "<pre>" . htmlspecialchars($client->response, ENT_QUOTES) . "</pre>";
