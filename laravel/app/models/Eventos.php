<?php
//models
class Eventos extends \Eloquent
{
   
    public static function getCargarEventos(){
        $query = "  (select id,nombre, evento, estado,'1' as tipo,id_sql,
            grupo,orden,extraer,id_where,
            valor_where from evento_consulta )
            UNION
            (select id, nombre, metodo,estado,
            '2','','','','','','' from evento_metodo)   ";

        $res = DB::select($query);

        return $res;
    }
   
    public static function getEstadoAgregarEvento(array $data )
    {   //echo $data['nombre'];
        $sql = "INSERT INTO evento_consulta(estado,evento,id_sql,id_where,nombre,valor_where,extraer,grupo,orden,usuario_created_at,created_at)
                VALUES(?,?,?,?,?,?,?,?,?,?,Now())";
        return DB::insert($sql, $data);
   
    }
    public static function getEstadoAgregarMetodo(array $data )
    {   //echo $data['nombre'];
        $sql = "INSERT INTO evento_metodo(estado,metodo,nombre,usuario_created_at,created_at)
                VALUES(?,?,?,?,Now())";
        return DB::insert($sql, $data);
   
    }
     
}
