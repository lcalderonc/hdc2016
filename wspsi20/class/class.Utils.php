<?php

/**
*
*/
class Utils
{
    public static function error($query)
    {
        $fo = fopen("error_sql.txt", "w+");
        fwrite($fo, $query);
        fclose($fo);
    }
    public static function curl($ruta,$postData)
    {
        $result='';
        $gestionId='';
        $acceso="\$PSI20\$";
        $clave="\$1st3m@\$";
        //$url ='http://webpsi20/api/'.$ruta;
        $url ='http://10.226.44.222:7020/api/'.$ruta;
        $hashg = hash('sha256', $acceso . $clave . $gestionId);
        $postData["hashg"] = $hashg;
        $postData["gestion_id"] = "$gestionId";
        try {
            $ch = curl_init();
            if (FALSE === $ch)
                throw new Exception('failed to initialize');
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            trigger_error(
                sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(),
                    $e->getMessage()
                ),
                E_USER_ERROR
            );
        }
        return $result;
    }
}
