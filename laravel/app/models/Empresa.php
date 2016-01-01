<?php

class Empresa extends \Eloquent
{
    public $_table = "empresas";

    /**
     * Celula relationship
     */
    public function celula()
    {
        return $this->hasMany('Celula');
    }

    /**
     * Usuario relationship
     */
    public function usuario()
    {
        return $this->hasMany('Usuario');
    }

    /**
     * Usuario relationship
     */
    public function usuarios()
    {
        return $this->belongsToMany('Usuario');
    }

    /**
     * Tecnico relationship
     */
    public function tecnicos()
    {
        return $this->hasMany('Tecnico');
    }
    /**
     * Empresas por suusrio
     */
    public static function getEmpresasUsuario($usuarioId ='')
    {
        try {
            $empresas = DB::table("empresas as e")
                        ->select("e.id, e.nombre")
                        ->join("empresa_usuario as eu","e.id","=","eu.empresa_id")
                        ->where("eu.usuario_id",$usuarioId)
                        ->where("e.estado","1")
                        ->where("eu.estado","1")
                        ->get();
        } catch (Exception $e) {
            Log::error($e);
            return "";
        }

        return $empresas;
    }
}
