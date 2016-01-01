<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Usuario extends Base implements UserInterface, RemindableInterface
{

    use UserTrait, RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'usuarios';
    
    public static $where =[
                        'id', 'nombre','apellido','usuario', 'password',
                        'dni','sexo', 'imagen', 'estado'
                        ];
    public static $selec =[
                        'id', 'nombre','apellido','usuario', 'password',
                        'dni','sexo', 'imagen', 'estado'
                        ];
    public static function get(array $data =array())
    {

        //recorrer la consulta
        $personas = parent::get($data);

        foreach ($personas as $key => $value) {
            if ($key=='password') {
                $personas[$key]['password']='';
            }
        }

        return $personas;
    }
    public static function getPerfil()
    {
        $user = Usuario::find( Auth::user()->id );
        return $user['perfil_id'];
    }
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    public $hidden = array('password', 'remember_token');
    /**
     * SubModulo relationship
     */
    public function submodulos()
    {
        return $this->belongsToMany('Submodulo');
    }

    /**
     * Empresa relationship
     */
    public function area()
    {
        return $this->belongsTo('Area');
    }
    /**
     * Empresa relationship
     */
    public function empresa()
    {
        return $this->belongsTo('Empresa');
    }

    /**
     * Usuario relationship
     */
    public function empresas()
    {
        return $this->belongsToMany('Empresa');
    }
    /**
     * Zonal relationship
     */
    public function zonales()
    {
        return $this->belongsToMany('Zonal');
    }
    /**
     * QuiebreGrupo relationship
     */
    public function quiebregrupos()
    {
        return $this->belongsToMany('QuiebreGrupo');
    }
    public function getSubmodulos($usuarioId)
    {
        //subconsulta
        $sql = DB::table('submodulo_usuario as su')
        ->join(
            'submodulos as s', 
            'su.submodulo_id', '=', 's.id'
        )
        ->join(
            'modulos as m', 
            's.modulo_id', '=', 'm.id'
        )
        ->select(
            DB::raw(
                "
            CONCAT(m.id, '-',
            GROUP_CONCAT(s.id)) as info"
            )
        )
        ->whereRaw("su.usuario_id=$usuarioId AND su.estado=1 AND m.estado=1")

        ->groupBy('m.id');
        //consulta
        $submodulos = DB::table(DB::raw("(".$sql->toSql().") as o"))
                ->select(
                    DB::raw("GROUP_CONCAT( info SEPARATOR '|'  ) as DATA ")
                )
               ->get();

        return $submodulos;

    }
    public function getPrivilegios($usuarioId, $idModulo)
    {
    
        return $submodulos = DB::table('submodulo_usuario as su')
        ->join(
            'submodulos as s', 
            'su.submodulo_id', '=', 's.id'
        )
        ->join(
            'modulos as m', 
            's.modulo_id', '=', 'm.id'
        )
        ->select(
             'su.usuario_id',
             'su.submodulo_id',
             's.nombre',
             //'su.consultar',
             'su.agregar',
             'su.editar',
             'su.eliminar'
        )
        ->where('su.usuario_id',$usuarioId)
        ->where('m.id', $idModulo)
        ->where('su.estado',1)
        ->where('m.estado',1)
        ->orderBy('nombre','asc')
        ->get();

    }
}
