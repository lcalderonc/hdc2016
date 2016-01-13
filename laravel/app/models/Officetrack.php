<?php

class Officetrack extends \Eloquent 
{

    public function getPasouno($gestion_id) {
        $sql = "SELECT 
                    pu.x, pu.y, pu.casa_img1, 
                    pu.casa_img2, pu.casa_img3,
                    IFNULL(observacion,'') observacion
                FROM
                    webpsi_officetrack.tareas t, 
                    webpsi_officetrack.paso_uno pu 
                WHERE
                    t.id=pu.task_id 
                    AND t.task_id = $gestion_id";
        
        $result = DB::select($sql);        
        return $result;
    }

    public function getPasodos($gestion_id) {
        $sql = "SELECT 
                    pu.motivo, pu.observacion, 
                    pu.problema_img1, pu.problema_img2, 
                    pu.modem_img1, pu.modem_img2, 
                    pu.tap_img1, pu.tap_img2, 
                    pu.tv_img1, pu.tv_img2
                FROM
                    webpsi_officetrack.tareas t, 
                    webpsi_officetrack.paso_dos pu 
                WHERE
                    t.id=pu.task_id 
                    AND t.task_id = $gestion_id";
        
        $result = DB::select($sql);        
        return $result;
    }

    public function getPasotres($gestion_id) {
        $sql = "SELECT 
                    pu.estado, pu.observacion, 
                    pu.final_img1, pu.final_img2, 
                    pu.firma_img
                FROM
                    webpsi_officetrack.tareas t, 
                    webpsi_officetrack.paso_tres pu 
                WHERE
                    t.id=pu.task_id 
                    AND t.task_id = $gestion_id";
        
        $result = DB::select($sql);        
        return $result;
    }

    public function registrar( $trama, $response)
    {

        $db = "webpsi_officetrack";
        $table = "wsenvios";
        try {

            //Iniciar transaccion
            DB::beginTransaction();

            //Registrar error
            $fecha = date("Y-m-d H:i:s");
            DB::insert("INSERT INTO $db.$table
                    (
                        fecha, trama, response
                    )
                    VALUES
                    (
                       ?, ?, ?
                    )",array($fecha, $trama, $response));

            DB::commit();
            $result["estado"] = true;
            $result["msg"] = "Trama enviada hacia OT";
            return $result;
        } catch (PDOException $error) {
            //Rollback
            DB::rollBack();
            $this->error->handlerError($error);
            $result["estado"] = false;
            $result["msg"] = $error->getMessage();
            return $result;
        }
    }
}
