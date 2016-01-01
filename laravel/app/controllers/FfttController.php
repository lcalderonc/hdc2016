<?php

class FfttController extends \BaseController 
{

    /**
     * Explota la cadena de FFTT y retorna valores 
     * de acuerdo al tipo d actuacion
     * 
     * @param type $val
     *              ->fftt
     *              ->tipoactu
     * @return array
     */
    public function getExplodefftt($val) {
        
        $ffttArray = array();
        
        /**
         * Primera condicion: Gestionadas (-catv-)
         * Segunda condicion: Temporales (_catv)
         */
        if (
                strpos($val->tipoactu, "-catv-") !== false or
                strpos($val->tipoactu, "_catv") !== false
        ) {
            
            //Tipo CATV -> fftt obtener xy del tap
            $ffttArray = explode("|", $val->fftt);

            /**
             * las tablas temporales retornan 5 datos
             */
            $eNodo = "";
            $eTroba = "";
            $eAmplificador = "";
            $eTap = "";

            if (isset($ffttArray[0])) {
                $eNodo = $ffttArray[0];
            }
            if (isset($ffttArray[1])) {
                $eTroba = $ffttArray[1];
            }
            if (isset($ffttArray[3])) {
                $eAmplificador = $ffttArray[3];
            }
            if (isset($ffttArray[4])) {
                $eTap = $ffttArray[4];
            }

            if (count($ffttArray) === 5) {
                $eTroba = $ffttArray[1];
                $eAmplificador = $ffttArray[2];
                $eTap = $ffttArray[3];
            }

            if (ctype_digit($eAmplificador)) {
                $eAmplificador = intval($eAmplificador);
            }
            if (ctype_digit($eTap)) {
                $eTap = intval($eTap);
            }
            
            $ffttArray["tipo"]          = "catv";
            $ffttArray["nodo"]          = $eNodo;
            $ffttArray["troba"]         = $eTroba;
            $ffttArray["amplificador"]  = $eAmplificador;
            $ffttArray["tap"]           = $eTap;

        } else {
            //Tipo BASICA / ADSL -> llave obtener xy del terminal
            $ffttArray = explode("|", $val->fftt);

            /**
             * Algunas llaves tienen 4 datos
             * MDF|ARMARIO|CABLE|TERMINAL
             * Las tablas temporal de provision tiene este formato
             */
            $eTer = "";
            if (isset($ffttArray[6])) {
                $eTer = str_pad($ffttArray[6], 3, "0", STR_PAD_LEFT);
            }

            if (count($ffttArray) === 4) {
                $eTer = str_pad($ffttArray[3], 3, "0", STR_PAD_LEFT);
            }
            
            $ffttArray["tipo"]          = "nocatv";            

            //Sin armario red directa, usa cable
            // CRU0||P/23|649||0|065|9 -> Directa
            // MAU2|A003|P/07|385|S/03|25|014|25 -> Flexible
            if (isset($ffttArray[1])) {
                if (trim($ffttArray[1]) === "") {
                    
                    $ffttArray["red"]       = "directa";
                    $ffttArray["mdf"]       = $ffttArray[0];
                    $ffttArray["cable"]     = $ffttArray[2];
                    $ffttArray["terminal"]  = $eTer;
                    
                } else {
                    //Con armario red flexible
                    
                    $ffttArray["red"]       = "flexible";
                    $ffttArray["mdf"]       = $ffttArray[0];
                    $ffttArray["armario"]   = $ffttArray[2];
                    $ffttArray["terminal"]  = $eTer;
                    
                }
            }

        }
        
        return $ffttArray;
    }

}
