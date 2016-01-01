<?php

class Toa extends \Eloquent
{
    //protected $table = "actividades";
    public static function ValidaPermiso()
    {
        $codactu=Input::get('codactu');

        $sql="  SELECT c.nombre,cv.valores,cv.campo,cv.relacion,
                        cv.relacion_temporal,cv.campo_temporal
                FROM config c 
                INNER JOIN config_valores cv 
                    ON cv.config_id=c.id AND cv.estado=1
                WHERE c.nombre='toa'
                AND c.estado=1";
        $datos=DB::select($sql);

        $where=''; // Inicializando filtros
        $relacion=''; // Inicializando relacion
        $wheret='';
        $relaciont='';

        $usuarios=explode('|'.Auth::user()->id.'|','|743|469|');
            
        if( count($usuarios)>1 ){
            return 1;
        }

        if ( count($datos)>0 ) {

            foreach ($datos as $key => $value) {
                if(trim($value->relacion)!=''){
                    if(strpos($relacion,$value->relacion)===FALSE){
                        $relacion.=" ".$value->relacion." ";
                    }
                }
                if(trim($value->relacion_temporal)!=''){
                    if(strpos($relaciont,$value->relacion_temporal)===FALSE){
                        $relaciont.=" ".$value->relacion_temporal." ";
                    }
                }

                if(strpos($value->valores,"|")!==FALSE){
                    $det=explode("|",$value->valores);
                    $where.=" AND ( ";
                    $wheret.=" AND ( ";
                    for($i=0;$i<count($det);$i++){
                        if(strpos($value->campo,"*")!==FALSE){
                            $detval=explode("*",$det[$i]);
                            $detcam=explode("*",$value->campo);
                            $where.="(";
                            for($j=0;$j<count($detval);$j++){
                                $where.=" ".$detcam[$j]."='".$detval[$j]."' AND";
                            }
                            $where=substr($where,0,-3);
                            $where.=") OR ";
                        }
                        else{
                        $where.=$value->campo."='".$det[$i]."' OR ";
                        }

                        if(strpos($value->campo_temporal,"*")!==FALSE){
                            $detval=explode("*",$det[$i]);
                            $detcam=explode("*",$value->campo_temporal);
                            $wheret.="(";
                            for($j=0;$j<count($detval);$j++){
                                $wheret.=" ".$detcam[$j]."='".$detval[$j]."' AND";
                            }
                            $wheret=substr($wheret,0,-3);
                            $wheret.=") OR ";
                        }
                        else{
                        $wheret.=$value->campo_temporal."='".$det[$i]."' OR ";
                        }
                    }
                    $where=substr($where,0,-3);
                    $wheret=substr($wheret,0,-3);
                    $where.=") ";
                    $wheret.=") ";
                }
                else{
                    if(strpos($value->campo,"*")!==FALSE){
                        $detval=explode("*",$value->valores);
                        $detcam=explode("*",$value->campo);
                        $where.=" AND (";
                        for($i=0;$i<count($detval);$i++){
                            $where.=" ".$detcam[$i]."='".$detval[$i]."' AND";
                        }
                        $where=substr($where,0,-3);
                        $where.=") ";
                    }
                    else{
                    $where.=" AND ".$value->campo."='".$value->valores."' ";
                    }

                    if(strpos($value->campo_temporal,"*")!==FALSE){
                        $detval=explode("*",$value->valores);
                        $detcam=explode("*",$value->campo_temporal);
                        $wheret.=" AND (";
                        for($i=0;$i<count($detval);$i++){
                            $wheret.=" ".$detcam[$i]."='".$detval[$i]."' AND";
                        }
                        $wheret=substr($wheret,0,-3);
                        $wheret.=") ";
                    }
                    else{
                    $wheret.=" AND ".$value->campo_temporal."='".$value->valores."' ";
                    }
                }
            }
            
            $sqlf=" SELECT count(um.codactu) cant
                    FROM ultimos_movimientos um
                    $relacion
                    WHERE um.codactu='$codactu'
                    $where ";
            $datosf= DB::select($sqlf);

            if ( $datosf[0]->cant==0 ){
                $sqlf=" SELECT count(t.averia) cant
                        FROM webpsi_coc.averias_criticos_final t
                        $relaciont
                        WHERE averia='$codactu'
                        $wheret ";
                $sqlf= str_replace("actividadt","1",$sqlf);
                $datosf= DB::select($sqlf);

                if ( $datosf[0]->cant==0 ){
                    $sqlf=" SELECT count(t.codigo_req) cant
                            FROM webpsi_coc.tmp_provision t
                            $relaciont
                            WHERE codigo_req='$codactu'
                            $wheret ";
                    $sqlf= str_replace("actividadt","2",$sqlf);
                    $datosf= DB::select($sqlf);
                }
            }
            //echo $sqlf;
            return $datosf[0]->cant;
        }
        else {
            return 0;
        }
    }
}
