<?php

class SubmoduloUsuario extends \Eloquent
{
    //protected $table = "actividades";

    public function __construct()
    {
         $this->table = "submodulo_usuario";
    }

    public function getPrivilegio($idUsuario, $idSubmodulo) //NO USADO
    {
         return DB::table('submodulo_usuario')
                ->select(
                    'id',
                    'agregar',
                    'editar',
                    'eliminar'
                )
                ->where('usuario_id', $idUsuario)
                ->where('submodulo_id', $idSubmodulo)
                ->where('estado', 1)
                ->get();
    }
}
