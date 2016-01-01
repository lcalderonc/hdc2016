<?php 
class Categoria extends Eloquent {

    protected $table = 'categorias';

    public function ubicacion()
	{
		return $this->belongsTo('Ubicacion');
	}
    
}