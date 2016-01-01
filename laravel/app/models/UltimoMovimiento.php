<?php 
class UltimoMovimiento extends Eloquent {

    public $table = 'ultimos_movimientos';
    
    /**
     * Actualiza direccion y 
     * mantiene la anterior en campos "previo"
     * 
     * @param String $x Longitud
     * @param String $y Latitud
     * @param String $direccion direccion
     * @param Integer $gestionId ID de gestion
     * @return boolean
     */
    public static function actualizar_direccion($x, $y, $direccion, $gestionId)
    {
        $setStr = "";
        if (trim($x)!='' and trim($y)!='') {
            $setStr .= "x_previo = x, y_previo = y,";
        }
        
        if (trim($direccion)!='') {
            $setStr .= "direccion_previo = direccion_instalacion,";
        }
        
        $setStr = substr($setStr, 0, strlen($setStr)-1);
        
        try {
            $sql = "UPDATE 
                        ultimos_movimientos 
                    SET 
                        $setStr 
                    WHERE 
                        gestion_id = ?";            
            $result["data"] = DB::update($sql, array($gestionId));
            $result["estado"] = true;
            return $result;
        } catch (Exception $exc) {
            $result["data"] = array();
            $result["estado"] = false;
            return $result;
        }
    }

}
