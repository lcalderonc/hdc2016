<?php
 
class Asunto extends Eloquent {
 
    protected $table = 'asuntos';
    public static function conversion($fromCurrency, $toCurrency)
    {
        //http://www.webservicex.net/CurrencyConvertor.asmx?WSDL
        // creacion de una instancia de cliente
        $client = new \nusoap_client('http://www.webservicex.net/CurrencyConvertor.asmx?WSDL', true);
        // verifica si existe un error en el objeto

        $result = $client->call('ConversionRate', array('FromCurrency'=>$fromCurrency,'ToCurrency'=>$toCurrency));
        // resultado
        return $result;
        /*var_dump($result);

        echo '<h2>Request</h2>';
        echo '<pre>' . htmlspecialchars($client->request) . '</pre>';
        echo '<h2>Respuesta</h2>';
        echo '<pre>' . htmlspecialchars($client->response) . '</pre>';
        // mensage de debug
        echo '<h2>Debug</h2>';
        echo '<pre>' . htmlspecialchars($client->debug_str) . '</pre>';*/
    }
    public static function server()
    {
        $server = new \soap_server;
        //$namespace = "http://localhost:8000/ws";
        $server->configureWSDL('server.hello', 'urn:server.hello', Request::url());
        //$server->configureWSDL('OverService',$namespace);
        $server->wsdl->schemaTargetNamespace = 'urn:server.hello';
        //$server->decode_utf8 = true;
        //$server->soap_defencoding = "UTF-8";
        $server->register('hello',
                array('name' => 'xsd:string'),
                array('return' => 'xsd:string'),
                'urn:server.hello',
                'urn:server.hello#hello',
                'rpc',
                'encoded',
                'Retorna o nome'
        );

        function hello($name)
        {
            return 'Hello ' . $name;
        }

        // respuesta para uso do serviço
        return Response::make($server->service(file_get_contents("php://input")), 200, array('Content-Type' => 'text/xml; charset=ISO-8859-1'));

    }
}
