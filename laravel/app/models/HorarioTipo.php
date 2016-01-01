<?php

class HorarioTipo extends \Base
{
    public $table = "horarios_tipo";
    public static $where = ['id', 'minutos','estado'];
    public static $selec = ['id', 'minutos as nombre','estado'];

}