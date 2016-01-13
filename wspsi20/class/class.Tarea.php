<?php

class Tarea
{

    private $_mysqli;

    /**
     * Obtener una instancia de la conexion de la base de datos
     */
    public function __construct()
    {
        $db = Database::getInstance();
        $mysqli = $db->getConnection();
        $this->_mysqli = $mysqli;
    }

    /**
     * Datos de la tabla: tareas<br>
     * 
     * @param string $id
     * @return array
     */
    public function select($id = NULL)
    {
        $return = array();
        $query = "SELECT * FROM tareas ";
        if ($id != NULL) {
            $query .= " WHERE id = '$id' ";
        }
        $query .= " ORDER BY created_at DESC";
        try {
            $result = $this->_mysqli->query($query);
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
                    'serie_deco' => $row->{serie_deco},
                    'serie_tarjeta' => $row->{serie_tarjeta},
                    'telefono_origen' => $row->{telefono_origen}
                );
                array_push($return, $data);
            }
        }
        $result->free();
        return $return;
    }

    /**
     * No se puede identificar el ID usuario, por defecto: 666<br>
     * Asunto: activacion, refresh y consulta
     * @param array $data
     * @return boolean retorna 0 si a ocurrido un error
     */
    public function insert($data)
    {
        $asunto = strtolower(trim($data['asunto']));
        $actividad = strtolower(trim($data['actividad']));
        $requerimiento = strtolower(trim($data['requerimiento']));
        $serieDeco = strtolower(trim($data['serieDeco']));
        $serieTarjeta = strtolower(trim($data['serieTarjeta']));
        $telefonoOrigen = (trim($data['telefonoOrigen']));
        $clave = ($data['clave']) ? $data['clave'] : '';
        $valor = ($data['valor']) ? $data['valor'] : '';
        $fecha = date('Y-m-d H:i:s'); 
        $query = "INSERT INTO tareas (asunto,actividad, requerimiento,
            serie_deco, serie_tarjeta, telefono_origen, 
            estado,created_at, usuario_created_at, 
            clave, valor, procesado)
          VALUES ( '$asunto','$actividad','$requerimiento',
            '$serieDeco','$serieTarjeta','$telefonoOrigen',
                1, '$fecha',666,
            '$clave', '$valor', 0)"; /* now() */

        try {
            $this->_mysqli->query($query);
            $id = $this->_mysqli->insert_id;
        } catch (Exception $e) {
            Utils::error($query);
            return 0;
        }

        if ($this->_mysqli->affected_rows > 0) {
            //verificar siningreso celular
            $postData["id"] = "$id";
            if ($telefonoOrigen != '' && is_numeric($telefonoOrigen)) {
                $telOrig = substr($telefonoOrigen, strlen($telefonoOrigen) - 9);
                if ($asunto == 'refresh' || $asunto == 'activacion') {
                    //catalogo decos
                    $postData["nombreevento"] = "$asunto";
                    $postData["asunto"] = "$asunto"; //opcion: refresh o activa
                    $postData["telefonoOrigen"] = "$telOrig"; //de tec
                    $postData["serieDeco"] = "$serieDeco";
                    $postData["serieTarjeta"] = "$serieTarjeta";
                    $postData["requerimiento"] = "$requerimiento";

                    $postData["$actividad"] = "$valor";
                    $result = Utils::curl('eventometodo', $postData);
//                    return 1;
                } elseif ($asunto == 'consulta' &&
                        $actividad != '' && $valor != '') {
                    //tablas de consultas
                    $postData["nombreevento"] = "$asunto";
                    $postData["asunto"] = "$asunto";
                    $postData["telefonoOrigen"] = "$telOrig"; //de tec
                    $postData["serieDeco"] = "$serieDeco";
                    $postData["serieTarjeta"] = "$serieTarjeta";
                    $postData["requerimiento"] = "$requerimiento";

                    $texto = 'codigo de cliente';
                    $actividad = str_replace($texto, 'codcli', $actividad);
                    $postData["$actividad"] = "$valor";
                    $result = Utils::curl('consulta', $postData);
//                    return 1;
                }/* elseif ($asunto=='consulta2') {
                  if ( $actividad!='' && $valor!='' ) {
                  $postData["nombreevento"] = "webservice";
                  $postData["$actividad"] = "$valor";
                  $postData["telefonoOrigen"] = "$telOrig";
                  $result=Utils::curl('eventometodo', $postData);
                  return '1';
                  } else {
                  return '1';
                  }
                  } */
                //print_r($postData);
                return 1;
            } else {
                // TelefonoOrigen vacio
                return 0;
            }
        } else {
            // Insertar fallida
            return 0;
        }
    }

}
