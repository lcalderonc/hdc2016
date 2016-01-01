<?php

class Tramo extends Eloquent 
{
    
    public $table = "geo_tramos";


    public function getTramos($distrito, $calle, $numero, $izq)
    {
        $params = array();
        
        $dep = substr($distrito, 0, 2);
        $pro = substr($distrito, 2, 2);
        $dis = substr($distrito, 4, 2);
        
        $sql = "SELECT
                    COD_VIA, COD_TRAM, PKY_TRAMO, 
                    NUM_CUADRA, NOM_VIA_TR, 
                    COD_DPTO, COD_PROV, COD_DIST, 
                    NUM_IZQ_IN, NUM_IZQ_FI, NUM_DER_IN, NUM_DER_FI,
                    XA, YA, XB, YB, XC, YC, XD, YD
                FROM 
                    $this->table 
                WHERE 
                    NOM_VIA_TR LIKE CONCAT('%', ?, '%')";
        $params[] = $calle;

        //Limitar por distrito
        if ( $dep != "00" ) 
        {
            $sql .= " AND COD_DPTO=?";
            $params[] = $dep;
        }
        if ( $pro != "00" ) 
        {
            $sql .= " AND COD_PROV=?";
            $params[] = $pro;
        }
        if ( $dis != "00" ) 
        {
            $sql .= " AND COD_DIST=?";
            $params[] = $dis;
        }

        //Numeracion izquierda o derecha
        if ( $numero != "" ) 
        {
            if ($izq) 
            {
                $sql .= " AND ? BETWEEN NUM_IZQ_IN AND NUM_IZQ_FI";
            } else {
                $sql .= " AND ? BETWEEN NUM_DER_IN AND NUM_DER_FI";
            }
            $params[] = $numero;
        }

        //Orden por coordenadas
        $sql .= " ORDER BY XA";
        
        $tramos = DB::select($sql, $params);
        return $tramos;
    }
    

}
