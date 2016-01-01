<?php

class Celula extends \Eloquent
{
    public $table = "celulas";

    /**
     * Empresa relationship
     */
    public function empresas()
    {
        return $this->belongsTo('Empresa');
    }

    /**
     * Quiebre relationship
     */
    public function quiebres()
    {
        return $this->belongsToMany('Quiebre');
    }

    /**
     * Tecnico relationship
     */
    public function tecnicos()
    {
        return $this->belongsToMany('Tecnico');
    }
}
