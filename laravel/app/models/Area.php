<?php

class Area extends \Base
{
    public $_table = "areas";

    public static $where =['id', 'nombre', 'estado'];
    public static $selec =['id', 'nombre', 'estado'];
    /**
     * Usuario relationship
     */
    public function usuario()
    {
        return $this->hasMany('Usuario');
    }

}
