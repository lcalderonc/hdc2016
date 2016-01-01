<?php

/**
*
*/
class Tarea
{
    private $mysqli;
    /**
     * validar el envio de parametros por webservices
     */
    public function __construct()
    {
        $db = Database::getInstance();
        $mysqli = $db->getConnection();
        $this->mysqli=$mysqli;
    }
    public function select($id = NULL)
    {
        $return = array();
        $query = "SELECT * FROM tareas ";
        if ($id != NULL) {
            $query .= " WHERE id = '$id' ";
        }
        $query .= " ORDER BY created_at DESC";
        try {
            $result = $this->mysqli->query($query);
        } catch (Exception $e) {
            Utils::error($query);
            return $e->errorMessage();
        }
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_object()) {
                $data = array(
                    'asunto' => $row->asunto,
                    'actividad' => $row->actividad,
                    'requerimiento' => $row->requerimiento,
                    'serie_deco' => $row->serie_deco,
                    'serie_tarjeta' => $row->serie_tarjeta,
                    'telefono_origen' => $row->telefono_origen
                );
                array_push($return, $data);
            }
        }
        $result->free();
        return $return;
    }
    public function insert( $data )
    {
        $asunto = strtolower(trim($data['asunto']));
        $actividad = strtolower(trim($data['actividad']));
        $requerimiento = strtolower(trim($data['requerimiento']));
        $serieDeco = strtolower(trim($data['serieDeco']));
        $serieTarjeta = strtolower(trim($data['serieTarjeta']));
        $telefonoOrigen = strtolower(trim($data['telefonoOrigen']));
        $clave = ($data['clave']) ? $data['clave'] : '' ;
        $valor = ($data['valor']) ? $data['valor'] : '' ;

        $query = "INSERT INTO tareas (asunto,actividad, requerimiento,
            serie_deco, serie_tarjeta, telefono_origen, estado,created_at,
            usuario_created_at, clave, valor, procesado)
          VALUES ( '$asunto','$actividad','$requerimiento',
            '$serieDeco','$serieTarjeta','$telefonoOrigen',1, now(),666,
            '$clave', '$valor', 0)";

        try {
            $return = $this->mysqli->query($query);
            $id = $this->mysqli->insert_id;
        } catch (Exception $e) {
            Utils::error($query);
            return '0';
        }

        if ($this->mysqli->affected_rows > 0) {
            //INSERT SUCCESS
            //verificar siningreso celular
            $postData["id"] = "$id";
            if ($telefonoOrigen!='' && is_numeric($telefonoOrigen) ) {
                $telefonoOrigen = substr($telefonoOrigen, strlen($telefonoOrigen)-9);
                if ($asunto=='refresh' || $asunto=='activacion') {
                    //catalogo decos
                    $postData["nombreevento"] = "$asunto";
                    $postData["asunto"] = "$asunto";//puede ser refresh o activa
                    $postData["telefonoOrigen"] = "$telefonoOrigen";//de tec
                    $postData["serieDeco"] = "$serieDeco";
                    $postData["serieTarjeta"] = "$serieTarjeta";
                    $postData["requerimiento"] = "$requerimiento";
                    $postData["$actividad"] = "$valor";
                    $result=Utils::curl('eventometodo', $postData);
                    return '1';
                } elseif ($asunto=='consulta') {
                    //tablas de consultas
                    if ( $actividad!='' && $valor!='' ) {
                        //hacer curl a
                        $postData["asunto"] = "$asunto";//puede ser refresh o activa
                        $postData["serieDeco"] = "$serieDeco";
                        $postData["serieTarjeta"] = "$serieTarjeta";
                        $postData["requerimiento"] = "$requerimiento";
                        $postData["nombreevento"] = "$asunto";
                        
                        $actividad = str_replace('codigo de cliente', 'codcli', $actividad);
                        
                        $postData["$actividad"] = "$valor";
                        $postData["telefonoOrigen"] = "$telefonoOrigen";//de tec
                        $result=Utils::curl('consulta', $postData);
                        return '1';
                    } else {
                        //retornar mensaje que no ingreso consulta
                        return '1';
                    }
                }/* elseif ($asunto=='consulta2') {
                    if ( $actividad!='' && $valor!='' ) {
                        $postData["nombreevento"] = "webservice";
                        $postData["$actividad"] = "$valor";
                        $postData["telefonoOrigen"] = "$telefonoOrigen";
                        $result=Utils::curl('eventometodo', $postData);
                        return '1';
                    } else {
                        return '1';
                    }
                }*/

            } else {
                //telefono origen vacio
                return '0';
            }
            return '1';
        } else {
            //INSERT FAILED
            return '0';
        }
    }
}
