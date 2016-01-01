<?php

class Submodulo extends \Eloquent
{
    public $table = "submodulos";

    /**
     * Modulo relationship
     */
    public function modulos()
    {
        return $this->belongsTo('Modulo');
    }

    /**
     * Usuarios relationship
     */
    public function usuarios()
    {
        return $this->belongsToMany('Usuarios');
    }

    public static function updatePrivilegios($idUsuario, $idSubmodulo, $permisos)
    {
        $query = DB::table('submodulo_usuario')
            ->where('usuario_id', $idUsuario)
            ->where('submodulo_id', $idSubmodulo);

        switch ($permisos) {
            case 'abc': 
                $query->update(array('agregar' => 1, 'editar' => 1, 'eliminar' => 1, 'usuario_updated_at' => Auth::user()->id,'updated_at' => date('Y-m-d h:i:s', time())));
                break;
            case 'ab':
                $query->update(array('agregar' => 1, 'editar' => 1, 'eliminar' => 0, 'usuario_updated_at' => Auth::user()->id,'updated_at' => date('Y-m-d h:i:s', time())));
                break;
            case 'ac':
                $query->update(array('agregar' => 1, 'editar' => 0, 'eliminar' => 1, 'usuario_updated_at' => Auth::user()->id,'updated_at' => date('Y-m-d h:i:s', time())));
                break;
            case 'bc':
                $query->update(array('agregar' => 0, 'editar' => 1, 'eliminar' => 1, 'usuario_updated_at' => Auth::user()->id,'updated_at' => date('Y-m-d h:i:s', time())));
                break;
            case 'a':
                $query->update(array('agregar' => 1, 'editar' => 0, 'eliminar' => 0, 'usuario_updated_at' => Auth::user()->id,'updated_at' => date('Y-m-d h:i:s', time())));
                break;
            case 'b':
                $query->update(array('agregar' => 0, 'editar' => 1, 'eliminar' => 0, 'usuario_updated_at' => Auth::user()->id,'updated_at' => date('Y-m-d h:i:s', time())));
                break;
            case 'c':
                $query->update(array('agregar' => 0, 'editar' => 0, 'eliminar' => 1, 'usuario_updated_at' => Auth::user()->id,'updated_at' => date('Y-m-d h:i:s', time())));
                break;
            case '':
                $query->update(array('agregar' => 0, 'editar' => 0, 'eliminar' => 0, 'usuario_updated_at' => Auth::user()->id,'updated_at' => date('Y-m-d h:i:s', time())));
                break;

        }


/*
        $query->update(
                    array(
                        'agregar' => $dato,

                        )
                );
                */
    }
}
