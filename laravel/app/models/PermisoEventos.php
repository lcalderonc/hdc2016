<?php
//models
class PermisoEventos extends \Eloquent
{
   
    public static function getCargarPersonas(){
        $query = "  select usu.id,apellido, nombre, dni, '1' as 'tipo_persona',if(ev.estado=1,'Con Permisos', 'Sin Permisos') as detalle from usuarios usu 
LEFT JOIN eventos ev 
on usu.id=ev.persona_id and ev.tipo_persona=1 and ev.estado=1
where usu.estado=1  GROUP BY usu.id ";

        $res = DB::select($query);

        return $res;
    }
    
    public static function getCargarPersonasTecnicos(){
        $query = "  select tec.id,concat(ape_paterno,' ',ape_materno) as apellido, nombres as nombre, dni, '2' as 'tipo_persona',if(ev.estado=1,'Con Permisos', 'Sin Permisos') as detalle from tecnicos tec 
LEFT JOIN eventos ev 
on tec.id=ev.persona_id and ev.tipo_persona=2 and ev.estado=1
where tec.estado=1 GROUP BY tec.id
  ";

        $res = DB::select($query);

        return $res;
    }
  
     public static function getAgregarEvento(array $data )
    {  
        $sql = "insert into eventos (evento_id,persona_id,tipo_persona,tipo_evento,estado,created_at,usuario_created_at)
                values(?,?,?,?,'1',now(),?)
                ON DUPLICATE KEY UPDATE updated_at=now(),usuario_updated_at=?, estado='1'";
        return DB::insert($sql, $data);
        // echo var_dump($data);
       
    }
    
     public static function getDesactivarpermisos(array $data2)
    {  
        $sql = "UPDATE eventos set estado=0, updated_at=now(), usuario_updated_at=? where persona_id=? and tipo_persona=? ";
        return DB::update($sql, $data2);
       
    }
    
    
}
