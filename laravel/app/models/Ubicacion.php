<?php 
class Ubicacion extends Eloquent {

    protected $table = 'ubicaciones';

    public function categorias()
	{
		return $this->hasMany('Categoria');
	}
    
}