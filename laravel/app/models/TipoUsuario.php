<?php
class TipoUsuario extends Eloquent
{
	protected $table="tipos_usuarios";

	public function usuario()
	{
		return $this->belongsTo('Usuario');
	}
}
?>