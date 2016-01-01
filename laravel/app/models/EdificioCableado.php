<?php 
class EdificioCableado extends Eloquent {

    protected $table = 'edificios_cableados';
    public static function  getEdificios( $x, $y, $cant )
    {
        $sql = "SELECT CONCAT(
                        id,' ',
                        IFNULL(IF(nombre_proyecto='0','',nombre_proyecto),'')
                    ) AS nombre, id,
                        direccion_obra, coord_y, coord_x,
             ROUND( 3959000 * ACOS( 
                                    COS( RADIANS(?) )  *
                                    COS( RADIANS( coord_y ) ) *
                                    COS( 
                                        RADIANS( coord_x ) - RADIANS(?)
                                        ) +
                                    SIN( RADIANS(?) ) *
                                    SIN( RADIANS( coord_y ) )
                                )
                ) AS distance
                FROM psi.edificios_cableados 
                ORDER BY distance
                LIMIT 0 , ?";

                /*HAVING distance < '%s'*/
        return DB::select($sql, array((float)$y,(float)$x,(float)$y, $cant));
    }
}
?>
