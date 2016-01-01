<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
/**
 *  Mensaje
 */
class Mensaje extends \Eloquent
{
    public $table="mensajes";
    use SoftDeletingTrait;
    protected $dates = ['deleted_at'];

    public static function crear( $id, $data )
    {
        $body = json_encode($data['body']);
        $message = new Mensaje;
        $message['app_host'] = $data['app_host'];
        $message['app_port'] = $data['app_port'];
        $message['app_url'] = $data['app_url'];
        $message['message_id'] = $data['message_id'];
        $message['company_id'] = $data['company_id'];
        $message['address'] = $data['address'];
        $message['send_to'] = $data['send_to'];
        $message['subject'] = $data['subject'];
        $message['body'] = $body;
        $message['job_id'] = $id;
        try {
            $message->save();
        } catch (Exception $e) {
            
        }
    }
    public static function drop($idMensaje)
    {
        $mensaje = Mensaje::Where('message_id', $idMensaje)->first();

        if (isset($mensaje)) {
            $mensaje->delete();
        } else {
            return  array(
                    'code'=>"NOT FOUND",//OK, NOT found, ERROR
                    'desc'=>''
                    );
        }
        return  array(
                'code'=>"OK",
                'desc'=>''
                );
    }
    public static function getStatus($idMensaje)
    {
        $mensaje = Mensaje::Where('message_id', $idMensaje)->first();
        if (isset($mensaje)) {
            return  array(
                    'code'=>"OK",
                    'desc'=>$mensaje->estado
                    );
        } else {
            return  array(
                    'code'=>"NOT FOUND",//OK, NOT found, ERROR
                    'desc'=>''
                    );
        }
    }
}
