<?php

class Sms {

    /**
     * Envio de mensajes de texto
     * 
     * @param String $numero
     * @param String $mensaje
     * @param Integer $iduser
     * @return type
     */
    public static function enviar($numero, $mensaje, $iduser) {
        $url = Config::get("wpsi.sms.url");
        
        $postData = array(
            "enviar_sms" => 1,
            "celular" => "$numero",
            "iduser" => "$iduser",
            "mensaje" => "$mensaje"
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        //Retorno  
        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result;
    }

}
