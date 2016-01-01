<?php

class SubCambioDireccion
{

    public static function cargarCambiosDirecciones()
    {
        try {
            $cambioDireccion = new CambioDireccion();
            $response = $cambioDireccion->getCambiosDirecciones();
        } catch (Exception $exc) {
            $response = array(
                'rst'=>2,
                'error'=>'Ocurrio un error inesperado'
            );
        }
        return json_encode($response);
    }
    public static function ActualizarDirecciones($input)
    {
        $cambioDireccion = CambioDireccion::find($input['id']);
        $cambioDireccion['direccion'] = $input['direccion'];
        $cambioDireccion['coord_y'] = $input['latitud'];
        $cambioDireccion['coord_x'] = $input['longitud'];
        $cambioDireccion['referencia'] = $input['referencia'];
        $cambioDireccion['validacion'] = $input['validacion'];
        $cambioDireccion['usuario_updated_at'] = Auth::user()->id;
        if ($input['validacion']==2) {
            $cambioDireccion['observacion'] = $input['observacion'];
        }
        try {
            $cambioDireccion->save();

        } catch (Exception $exc) {
            $response = array(
                'rst'=>2,
                'error'=>'Ocurrio un error inesperado'
            );
        }
        return Response::json(
            array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
        );
    }
    public static function InsertarDirecciones($input)
    {
        $cambioDireccion = new CambioDireccion;
        $cambioDireccion['direccion'] = $input['direccion'];
        $cambioDireccion['coord_y'] = $input['latitud'];
        $cambioDireccion['coord_x'] = $input['longitud'];
        $cambioDireccion['referencia'] = $input['referencia'];
        $cambioDireccion['validacion'] = $input['validacion'];
        $cambioDireccion['usuario_created_at'] = Auth::user()->id;
        if ($input['validacion']==2) {
            $cambioDireccion['observacion'] = $input['observacion'];
        }
        $cambioDireccion['estado'] = '1';
        try {
            $cambioDireccion->save();
        } catch (Exception $exc) {
            $response = array(
                'rst'=>2,
                'error'=>'Ocurrio un error inesperado'
            );
        }
        return Response::json(
            array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
        );
    }
    public static function ActualizarEstado($id,$estado)
    {
        $cambioDireccion = CambioDireccion::find($id);
        $cambioDireccion->estado = $estado;
        try {
            $cambioDireccion->save();
        } catch (Exception $exc) {
            $response = array(
                'rst'=>2,
                'error'=>'Ocurrio un error inesperado'
            );
        }
        return Response::json(
            array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
        );
    }
}

