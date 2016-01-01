<?php

class Critico extends \Eloquent
{
    public static function critico_averia_historico(
        $fechaIni = null,
        $fechaFin =null
        )
    {
        $query = "SELECT *
                    FROM webpsi_coc.averias_criticos_final_historico
                    WHERE fecha_subida BETWEEN ? AND ?
                    ORDER BY 1 ASC ";
        
        $reporte= DB::select(
                    $query,
                    array(
                        $fechaIni,
                         $fechaFin
                    )
                );
        return $reporte;
    }
    public static function critico_provision_historico(
        $fechaIni = null,
        $fechaFin =null
        )
    {
        $query = "SELECT *
                    FROM webpsi_coc.tmp_provision_historico
                    WHERE fecha_subida BETWEEN ? AND ?
                    ORDER BY 1 ASC ";
        
        $reporte= DB::select(
                    $query,
                    array(
                        $fechaIni,
                         $fechaFin
                    )
                );
        return $reporte;
    }
    public static function critico_averia()
    {
        $query = "SELECT *
                  FROM psi.vistaAveriasCriticosFinal";
        
        $reporte = DB::select( $query );

        return $reporte;
    }
    public static function critico_provision()
    {
        //DB::connection()->disableQueryLog();
        $query = "SELECT *
                  FROM psi.vistaProvisionCriticosFinal";

        $reporte= DB::select( $query );
        return $reporte;
    }
    public static function digitalizacion_averia()
    {
        // Averías
        $query = "SELECT p.*, t.fecha_fin
                    FROM schedulle_sistemas.aver_pen_catv_pais p
                    LEFT JOIN psi.geo_trobapunto tp ON p.nodo=tp.nodo
                                                 AND p.plano=tp.troba
                    LEFT JOIN psi.dig_trobas t ON t.troba_id=tp.id
                WHERE t.fecha_fin IS NOT NULL AND t.fecha_fin!='0000-00-00'
                    AND t.est_seguim='A' AND oficina_administrativa='LIM' ";
                $reporte= DB::select( $query );
        return $reporte;
    }
    public static function digitalizacion_provision()
    {
        // Averías
        $query = "SELECT * from psi.vista_PenProvDig";
                $reporte= DB::select( $query );
        return $reporte;
    }
}
