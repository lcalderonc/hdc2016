<?php

class Actividad extends \Eloquent
{
    //protected $table = "actividades";

    public function __construct()
    {
         $this->table = "actividades";
    }
    /**
     * Quiebre relationship
     */
    public function quiebres()
    {
        return $this->belongsToMany('Quiebre');
    }
}
