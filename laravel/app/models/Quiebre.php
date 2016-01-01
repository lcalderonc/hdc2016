<?php

class Quiebre extends \Eloquent
{
    public $table = "quiebres";

    /**
     * Celula relationship
     */
    public function celulas()
    {
        return $this->belongsToMany('Celula');
    }
    /**
     * Actividad relationship
     */
    public function actividades()
    {
        return $this->belongsToMany('Actividad');
    }
    /**
     * Actividad relationship
     */
    public function motivos()
    {
        return $this->belongsToMany('Motivo');
    }
    /**
     * QuiebreGrupo relationship
     */
    public function quiebregrupos()
    {
        return $this->belongsTo('QuiebreGrupo');
    }
    public static function getQuiebresAllOfficeTrack(){
        $query = "  SELECT q.id, q.nombre, q.apocope
                    FROM quiebres q
                    INNER JOIN actividad_quiebre aq ON  q.id=aq.quiebre_id
                    INNER JOIN actividades a ON aq.actividad_id=a.id
                    WHERE q.estado=1 AND aq.estado=1
                    AND a.estado=1 
                    GROUP BY q.id
                    ORDER BY q.nombre";

        $res = DB::select($query);

        return $res;
    }
}
