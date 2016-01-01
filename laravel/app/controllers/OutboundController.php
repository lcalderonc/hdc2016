<?php

class OutboundController extends \BaseController
{
    public function getServer()
    {
        function isTimestampIsoValid($timestamp)
        {
            if (preg_match(
                '/^'.
                '(\d{4})-(\d{2})-(\d{2})T'. // YYYY-MM-DDT ex: 2014-01-01T
                '(\d{2}):(\d{2}):(\d{2})'.  // HH-MM-SS  ex: 17:00:00
                '(Z|((-|\+)\d{2}:\d{2})|((-|\+)\d{2}\d{2}))'.  // Z or +01:00 or -01:00
                '$/', $timestamp, $parts
            ) == true) {
                try {
                    new \DateTime($timestamp);
                    return true;
                }
                catch ( \Exception $e)
                {
                    return false;
                }
            } else {
                return false;
            }
        }
        function validarUsuario($user)
        {
            if ( !isset($user->now) || !isset($user->login) ||
                 !isset($user->company) || !isset($user->auth_string) ) {
                return "is not authorized";
            }
            $toaNow = $user->now;
            //$toaNow = "2011-07-07T09:25:02+00:00";
            $psiNow= date("c");
            $login = $user->login;
            $company = $user->company;
            $authString = $user->auth_string;
            /*validar now*/
            if ( !isTimestampIsoValid($toaNow) ) {
                return "Parameter now is different of ISO 8601 format";
            }
            //diferencia de hora   $psiNow  $toaNow
            $datetimePsi = new DateTime($psiNow);
            //$datetimePsi->add(new DateInterval('PT5H'));
            //considerar la diferencia de hora quellega
            $datetimeToa = new DateTime($toaNow);
            $segPsi = strtotime($datetimePsi->format('Y-m-d H:i:s'));
            $segToa = strtotime($datetimeToa->format('Y-m-d H:i:s'));
            $diferencia = abs($segPsi-$segToa);

            if ( $diferencia > (int) \Config::get("ofsc.outbound.diferenciaSegundos") ) {
                return "the current time on the server and this difference exceeds";
            }
            /*validar company*/
            if ($company!= \Config::get("ofsc.outbound.company")) {
                return "cannot be found in ETAdirect";
            }
            /*validar login acceso*/  // \Config::get("ofsc.outbound.login")
            if ($login!= \Config::get("ofsc.outbound.login") ) {
                return "cannot be found for this company";
            }
            /*validar login autorizacion*/
            $sendMessage =   \Config::get("ofsc.outbound.authorized.send_message");
            //$sendMessage=true;
            if ($sendMessage==false) {
                return "is not authorized to use send_message method";
            }
            /*validar auth_string*/
            $authStringNow = md5($toaNow. md5(\Config::get("ofsc.outbound.pass")));
            //$authStringNow = md5($toaNow. md5('arg3ntinsa'));
            if ($authString != $authStringNow) {
                return "is not authorized".$authStringNow;
            }
            return 1;
        }
        function send_message($request)
        {
            $validacion="is not authorized";
            if ( isset($request->user) ) {
                $validacion = validarUsuario($request->user);
                if ($validacion<>1) {
                    return new SoapFault('SOAP-ENV:Client', $validacion, 'Authentication module');
                }
            } else {
                return new SoapFault('SOAP-ENV:Client', $validacion, 'Authentication module');
            }
            $return = array();
            if ( !isset($request->messages->message) ) {
                return new SoapFault('SOAP-ENV:Client', $validacion, 'Authentication module');
            }
            $message = $request->messages->message;

            //validar si es array u objeto
            //cuando llega un elemento llega como objecto
            if ( is_object($message) ) {
                $message = array($message);
            }
            if ( is_array($message) ) {

                for ($i=0; $i < count($message); $i++) {
                    /*
                    ====obligatorio=====
                    app_host
                    app_port
                    app_url
                    message_id
                    ====opcional========
                    company_id
                    address
                    send_to
                    subject
                    body
                    */
                    //validar json
                    $body = json_decode($message[$i]->body);
                    if ($body !== null) {
                        $apptNumber = $body->appt_number;
                        $name = $body->name;
                        $phone = $body->phone;
                    }
                    $messageId =  $message[$i]->message_id;

                    $data = array(
                        'message_id' => $messageId,
                        'status' => 'sending',//sent failed delivered
                        //'description' => '',//everything is fine</
                        //'data' => '',
                        //'external_id' => '',
                        //'duration' => '',
                        //'sent' => '',
                        //'fault_attempt' => '',
                        //'stop_further_attempts' => '',
                        //'time_delivered_start' => '',
                        //'time_delivered_end' => ''
                    );
                    array_push($return, $data);
                }
                Queue::push('NotificacionController@sendMessage', $message);

                return $return;
            }
        }
        function drop_message($request)
        {
            //se debe abortar el proceso (Pag 14 Outbound)
            $validacion="is not authorized";
            if ( isset($request->user) ) {
                $validacion = validarUsuario($request->user);
                if ($validacion<>1) {
                    return new SoapFault('SOAP-ENV:Client', $validacion, 'Authentication module');
                }
            } else {
                return new SoapFault('SOAP-ENV:Client', $validacion, 'Authentication module');
            }
            $return = array();
            //var_dump($messages); exit();
            if ( !isset($request->messages->message) ) {
                return new SoapFault('SOAP-ENV:Client', $validacion, 'Authentication module');
            }
            $message = $request->messages->message;
            if ( is_object($message) ) {
                $message = array($message);
            }
            if ( is_array($message) ) {

                for ($i=0; $i < count($message); $i++) {
                    $messageId =  $message[$i]->message_id;
                    //borrar los mensajes que se envian
                    try {
                        $respuesta = Mensaje::drop($messageId);
                    } catch (Exception $e) {
                        $respuesta = array(
                            'code'=>"ERROR",
                            'desc'=>""//$e->getMessage()
                        );
                    }
                    $data = array(
                        'message_id' => $messageId,
                        'result' => $respuesta
                    );
                    array_push($return, $data);
                }
                return $return;
            }
        }
        function get_message_status($request)
        {
            //
            $validacion="is not authorized";
            if ( isset($request->user) ) {
                $validacion = validarUsuario($request->user);
                if ($validacion<>1) {
                    return new SoapFault('SOAP-ENV:Client', $validacion, 'Authentication module');
                }
            } else {
                return new SoapFault('SOAP-ENV:Client', $validacion, 'Authentication module');
            }
            $return = array();
            if ( !isset($request->messages->message) ) {
                return new SoapFault('SOAP-ENV:Client', $validacion, 'Authentication module');
            }
            $message = $request->messages->message;
            if ( is_object($message) ) {
                $message = array($message);
            }

            if ( is_array($message) ) {

                for ($i=0; $i < count($message); $i++) {
                    $messageId =  $message[$i]->message_id;
                    //borrar los mensajes que se envian
                    try {
                        $respuesta = Mensaje::getStatus($messageId);
                    } catch (Exception $e) {
                        $respuesta = array(
                            'code'=>"ERROR",
                            'desc'=>""//$e->getMessage()
                        );
                    }
                    $data = array(
                        'message_id' => $messageId,
                        'result' => $respuesta
                    );
                    array_push($return, $data);
                }
                return $return;
            }
        }
        try {
            //$server = new SoapServer(url('/').'/toa_outbound');
            $server = new SoapServer('http://10.226.44.222:7020/toa_outbound');
            $server->addFunction("send_message");
            $server->addFunction("drop_message");
            $server->addFunction("get_message_status");
            $server->handle();
        } catch (Exception $e) {
            $server->fault($e->getCode(), $e->getMessage());
        }
        //return Response::make(null, 200, array('content-type'=>'application/xml'));
    }
}