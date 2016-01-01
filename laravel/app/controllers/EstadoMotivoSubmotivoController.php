<?php
class EstadoMotivoSubmotivoController extends \BaseController
{
     public function postCargar()
     {
        //si la peticion es ajax
        if (Request::ajax()) {
            $estadomotivosubmotivos = EstadoMotivoSubmotivo::get();
            return Response::json(
                array(
                'rst' => 1, 'datos' => $estadomotivosubmotivos
                )
            );
        }
     }


    public function postCambiarestado()
    {
        if (Request::ajax()) {
            $estadomotivosubmotivoId = Input::get('id');
            $estado = Input::get('estado');
            Estadomotivosubmotivo::updateEstadomotivosubmotivo(
                $estadomotivosubmotivoId, $estado
            );
            return Response::json(
                array(
                'rst' => 1,
                'msj' => 'Registro actualizado correctamente',
                )
            );
        }
    }

    public function postCrear()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $numeric='numeric';
            $reglas = array(
                'submotivo' => $required.'|'.$numeric,
            );

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
                'numeric'   => ':attribute seleccione'
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json(
                    array(
                    'rst'=>2,
                    'msj'=>$validator->messages(),
                    )
                );
            }

            $descripcion = Input::get('descripcion');
            switch ( $descripcion ) {
              case 1:
                $tecnico = 1; $horario = 1;
                  break;
              case 2:
                $tecnico = 2; $horario = 0;
                  break;
              case 3:
                $tecnico = 3; $horario = 0;
                  break;
              case 9:
                $tecnico = 9; $horario = 0;
                  break;
              case 0:
                $tecnico = 0; $horario = 0;
                  break;
            }

            $estadomotivosubmotivos = new Estadomotivosubmotivo;
            $estadomotivosubmotivos['estado_id'] = Input::get('estados');
            $estadomotivosubmotivos['motivo_id'] = Input::get('motivo');
            $estadomotivosubmotivos['submotivo_id'] = Input::get('submotivo');
            $estadomotivosubmotivos['req_tecnico'] = $tecnico;
            $estadomotivosubmotivos['req_horario'] = $horario;
            $estadomotivosubmotivos['estado'] = Input::get('estado4');
            $estadomotivosubmotivos['usuario_created_at'] = Auth::user()->id;
            $estadomotivosubmotivos->save();

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro realizado correctamente',
                )
            );
        }
    }

    /**
     * Update the specified resource in storage.
     * POST /submotivo/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if ( Request::ajax() ) {
            $estadomotivosubmotivoId = Input::get('id4');
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $numeric='numeric';
            $reglas = array(
                'submotivo' => $required.'|'.$numeric,
            );

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
                'numeric'   => ':attribute seleccione'
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json(
                    array(
                    'rst'=>2,
                    'msj'=>$validator->messages(),
                    )
                );
            }
            $descripcion = Input::get('descripcion');
            switch ( $descripcion ) {
              case 1:
                $tecnico = 1; $horario = 1;
                  break;
              case 2:
                $tecnico = 2; $horario = 0;
                  break;
              case 3:
                $tecnico = 3; $horario = 0;
                  break;
              case 9:
                $tecnico = 9; $horario = 0;
                  break;
              case 0:
                $tecnico = 0; $horario = 0;
                  break;
            }

            $estadomotivosubmotivos = Estadomotivosubmotivo::find(
                $estadomotivosubmotivoId
            );
            $estadomotivosubmotivos['estado_id'] = Input::get('estados');
            $estadomotivosubmotivos['motivo_id'] = Input::get('motivo');
            $estadomotivosubmotivos['submotivo_id'] = Input::get('submotivo');
            $estadomotivosubmotivos['req_tecnico'] = $tecnico;
            $estadomotivosubmotivos['req_horario']= $horario;
            $estadomotivosubmotivos['estado'] = Input::get('estado4');
            $estadomotivosubmotivos['usuario_created_at'] = Auth::user()->id;
            $estadomotivosubmotivos->save();

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

}
