<?php 
namespace Ofsc;
use models\EnvioOfsc; 

/**
* clase padre de configuracion para consumir web service
*/
class Ofsc
{
    protected $_wsdl;
    protected $_client;

    public function iniciarCliente()
    {
        /*try {
            $soapClient = new \SoapClient(
                $this->_wsdl,
                array("trace" => 1, "exception" => 0)
            );
            return $soapClient;
        } catch (\SoapFault $fault) {
            echo $fault->getMessage();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }*/
        
        /*$soapClient = new \SoapClient(
            $this->_wsdl,
            array("trace" => 1, "exception" => 0)
        );*/
        
        $opts = array('http' => array('protocol_version' => '1.0'));
        $context = stream_context_create($opts);
        
        $wsOptArray = \Config::get("ofsc.fwdev");
        $wsOptArray["stream_context"] = $context;

        $soapClient = new \SoapClient(
            $this->_wsdl,
            //"http://www.webservicex.net/CurrencyConvertor.asmx?WSDL",
            $wsOptArray
            //array(
            //    "stream_context" => $context,
            //    "proxy_host" => "10.226.159.191",
            //    "proxy_port" => 8590,
            //    "trace" => 1, 
            //    "exception" => 0
            //)
        );
        return $soapClient;
    }
    
    /**
     * Cadena de autenticación para OFSC API's
     * @return String
     */
    private function getAuthString($now)
    {
        $authString = $now 
                      . md5(\Config::get("ofsc.auth.pass"));
        return md5($authString);
    }
    
    /**
     * Retorna arreglo de autenticación
     * @return array
     */
    protected function getAuthArray()
    {
        $now = date("c");
        $xmlArray = array(
            "user"=>array(
                "now" => $now,
                "login" => (\Config::get("ofsc.auth.login")),
                "company" => (\Config::get("ofsc.auth.company")),
                "auth_string" => $this->getAuthString($now)
            )
        );
        
        return $xmlArray;
    }
    
    /**
     * Retorna la estructura xml para autenticacion
     * @return String
     */
    protected function getAuthXml()
    {
        $now = date("c");
        $xmlArray = array(
            "now" => $now,
            "login" => (\Config::get("ofsc.auth.login")),
            "company" => (\Config::get("ofsc.auth.company")),
            "auth_string" => $this->getAuthString($now)
        );
        
        $xml = new \SimpleXMLElement('<user/>');
        foreach ($xmlArray as $k => $v) {
            $xml->addChild($k, $v);
        }
        return $xml->asXML();
    }
    
    
    protected function doAction($action, $setArray)
    {
        $resultaoArrayReq = implode("|", $setArray);
    
        $response = new \stdClass();       
        
        try {
            $response->error = false;
            $requestArray = array_merge($this->getAuthArray(), $setArray);
            $result = $this->_client->__soapCall(
                $action, array("request"=>$requestArray)
            );
            
            
            //print_r($result);
            $response->data = $result;

            //foreach($response->data as $respuesta) 
            //{
            $resultaoArrayResp = implode("|", $result);

            //}


            //\EnvioOfsc::registrarAccionWebservice($action,
            // $requestArray, $result);
            $envios = new EnvioOfsc();
            $envios->registrarAccionWebservice(
                $action, $resultaoArrayReq,
                $resultaoArrayResp
            );

            //$response->data = simplexml_load_string($xml);
            //$xml = $this->client->__soapCall($action, $requestArray);print_r($xml);
            
            //->Inicio respuesta test
            //$responseFunc = $action . '_response';
            //$response->data = simplexml_load_string(
            //    XmlTestResponse::$responseFunc()
            //);
            //->Fin respuesta test

            return $response;
        } catch (SoapFault $fault) {
            $response->error = true;
            $response->errorCode = $fault->faultcode;
            $response->errorString = $fault->faultstring;

            //Registro de webservice fallido
            $envios = new EnvioOfsc();
            $envios->registrarAccionWebservice(
                $action, 
                $resultaoArrayReq, "", 0
            );           

            return $response;
        } catch (Exception $error) {
            $response->error = true;
            $response->errorString = $error->getMessage();

            //Registro de webservice fallido
            $envios = new EnvioOfsc();
            $envios->registrarAccionWebservice(
                $action, 
                $resultaoArrayReq, "", 0
            );

            return $response;
        }
        
    }
    
    
}