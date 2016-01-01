<?php
class Configuracion extends \Eloquent
{
    public $table = "config";

    /**
     * Config_tables relationship
     */
    public function configuracionTabla()
    {
        return $this->hasMany('ConfiguracionTabla');
    }
}