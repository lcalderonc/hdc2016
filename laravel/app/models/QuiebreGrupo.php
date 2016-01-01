<?php

class QuiebreGrupo extends \Eloquent
{
    public $table = "quiebre_grupos";

    /**
     * Usuario relationship
     */
    public function quiebres()
    {
        return $this->hasMany('Quiebre');
    }

    /**
     * Usuario relationship
     */
    public function usuarios()
    {
        return $this->belongsToMany('Usuario');
    }

}
