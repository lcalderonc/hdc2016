<?php

/**
*
*/
class WebService
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
    private function validate($gestionId='', $hashg)
    {
        $acceso="\$PSI20\$";
        $clave="\$1st3m@\$";
        $hash = hash('sha256', $acceso.$clave.$gestionId);
        if ($hash!=$hashg) {
            return "Hash valido";
        } else {
            return "Hash no valido";
        }

    }
     /**
     * metodo para cambiar x, y
     * POST /api/actualizardireccion
     * @param  int  gestion_id
     * @param  int  carnet
     * @param  int  x
     * @param  int  y
     * @param  int  direccion
     * @param  int  referencia
     * @return Response
     */
    public function actualizar_direccion($data)
    {
        $gestionId = $data['gestionId'];
        $carnet = $data['carnet'];
        $x = $data['x'];
        $y = $data['y'];
        $direccion = $data['direccion'];
        $referencia = $data['referencia'];
        $hashg = $data['hashg'];
        if ($this->validate($gestionId, $hashg)) {

            try {
                //Iniciar transaccion
                //$this->mysql->beginTransaction();
                //ID de tecnico
                $sql = "SELECT id FROM tecnicos WHERE carnet_tmp='$carnet'";

                $result = $this->mysqli->query($sql);
                if ($result->num_rows > 0)
                    while ($row = $result->fetch_object())
                        $tecnicoId = $row->id;

                //INSERT en cambios_direcciones
                $sql = "INSERT INTO cambios_direcciones
                       (gestion_id, tipo_usuario, usuario_id,
                       coord_x, coord_y, direccion, referencia)
                        VALUES ('$gestionId', 'tec', '$tecnicoId', '$x', '$y',
                        '$direccion', '$referencia')";

                $this->mysqli->query($sql);
                if ($this->mysqli->insert_id==0)
                    return "error al insertar registro";

                $sql = "UPDATE ultimos_movimientos
                       SET x = '$x', y = '$y'
                       WHERE gestion_id = '$gestionId'";

                $result = $this->mysqli->query($sql);
                if ($this->mysqli->affected_rows!=0)
                    return "error al actualizar registro";

                //UPDATE gestiones_detalles
                $sql = "UPDATE gestiones_detalles
                       SET x = '$x', y = '$y'
                       WHERE gestion_id = '$gestionId' ";

                $result = $this->mysqli->query($sql);
                if ($this->mysqli->affected_rows!=0)
                    return "error al actualizar registro";


            } catch (PDOException $error) {
                return $error->getMessage();
            }

            return "Direccion actualizada";
        } else {
            return "Hash no valido";
        }
    }
    /**
     * devolver la distancia de la tarea al punto x, y
     * @param  int  gestion_id
     * @param  int  actu
     * @param  int  x
     * @param  int  y
     * @return Response
     */
    ////////////////////
    public function distancia_actu($data)
    {
        $gestionId = $data['gestionId'];
        $hashg = $data['hashg'];
        $actu = $data['actu'];
        $x = $data['x'];
        $y = $data['y'];
        if ($this->validate($gestionId, $hashg)) {
            $distancia = 0;

            //query
            if ($gestionId!=='') {
                $sql = "SELECT x, y FROM gestiones_detalles WHERE gestion_id='$gestionId";
                $result = $this->mysqli->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_object()){
                        $gestionX = $row->X;
                        $gestiony = $row->Y;
                    }
                }

            } elseif ($actu!=='') {

                $sql = "SELECT x, y FROM wpsi_coc.tmp_averia WHERE averia='$gestionId";
                $result = $this->mysqli->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_object()){
                        $gestionX = $row->X;
                        $gestiony = $row->Y;
                    }
                    $tabla = 'tmp_averia';
                } else {
                    $sql = "SELECT x, y FROM wpsi_coc.tmp_provision WHERE averia='$gestionId";
                    $result = $this->mysqli->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_object()){
                            $gestionX = $row->X;
                            $gestiony = $row->Y;
                        }
                        $tabla = 'tmp_averia';
                    } else {
                        return 'no se encontro x,y para este codigo';
                    }
                }
            }
            //distancia
            $distancia = sqrt(
                pow(($gestionX - $x), 2) +
                pow(($gestionY - $y), 2)
            );
            //metros
            $distancia=$distancia*100000;
            return $distancia;
        } else {
            return "Hash no valido";
        }
    }
    /**
     * solicitar informacion de una gestion
     * @param  int  gestion_id
     * @return Response
     */
    public function obtener_actu($data)
    {
        $gestionId = $data['gestionId'];
        $hashg = $data['hashg'];

        if ($this->validate($gestionId, $hashg)) {
            $return = array();
            $sql="SELECT *
                  FROM gestiones_detalles
                  WHERE gestion_id ='$gestionId' LIMIT 1";
            try {
                $result = $this->mysqli->query($sql);
            } catch (Exception $e) {
                return $e->errorMessage();
            }
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_object()) {
                    $data = array(
                        'id' => $row->id,
                        'gestion_id' => $row->gestion_id,
                        'quiebre_id' => $row->quiebre_id,
                        'empresa_id' => $row->empresa_id,
                        'zonal_id' => $row->zonal_id,
                        'tipo_averia' => $row->tipo_averia,
                        'horas_averia' => $row->horas_averia,
                        'fecha_registro' => $row->fecha_registro,
                        'ciudad' => $row->ciudad,
                        'codactu' => $row->codactu,
                        'inscripcion' => $row->inscripcion,
                        'fono1' => $row->fono1,
                        'telefono' => $row->telefono,
                        'mdf' => $row->mdf,
                        'observacion' => $row->observacion,
                        'segmento' => $row->segmento,
                        'area' => $row->area,
                        'direccion_instalacion' => $row->direccion_instalacion,
                        'codigo_distrito' => $row->codigo_distrito,
                        'nombre_cliente' => $row->nombre_cliente,
                        'orden_trabajo' => $row->orden_trabajo,
                        'veloc_adsl' => $row->veloc_adsl,
                        'clase_servicio_catv' => $row->clase_servicio_catv,
                        'codmotivo_req_catv' => $row->codmotivo_req_catv,
                        'total_averias_cable' => $row->total_averias_cable,
                        'total_averias_cobre' => $row->total_averias_cobre,
                        'total_averias' => $row->total_averias,
                        'fftt' => $row->fftt,
                        'llave' => $row->llave,
                        'dir_terminal' => $row->dir_terminal,
                        'fonos_contacto' => $row->fonos_contacto,
                        'contrata' => $row->contrata,
                        'zonal' => $row->zonal,
                        'wu_nagendas' => $row->wu_nagendas,
                        'wu_nmovimientos' => $row->wu_nmovimientos,
                        'total_llamadas_tecnicas' => $row->total_llamadas_tecnicas,
                        'total_llamadas_seguimiento' => $row->total_llamadas_seguimiento,
                        'llamadastec15dias' => $row->llamadastec15dias,
                        'llamadastec30dias' => $row->llamadastec30dias,
                        'lejano' => $row->lejano,
                        'distrito' => $row->distrito,
                        'eecc_zona' => $row->eecc_zona,
                        'zona_movistar_uno' => $row->zona_movistar_uno,
                        'paquete' => $row->paquete,
                        'data_multiproducto' => $row->data_multiproducto,
                        'averia_m1' => $row->averia_m1,
                        'fecha_data_fuente' => $row->fecha_data_fuente,
                        'telefono_codclientecms' => $row->telefono_codclientecms,
                        'rango_dias' => $row->rango_dias,
                        'sms1' => $row->sms1,
                        'sms2' => $row->sms2,
                        'area2' => $row->area2,
                        'microzona' => $row->microzona,
                        'tipo_actuacion' => $row->tipo_actuacion,
                        'x' => $row->x,
                        'y' => $row->y,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                        'usuario_created_at' => $row->usuario_created_at,
                        'usuario_updated_at' => $row->usuario_updated_at,
                        'codservcms' => $row->codservcms,
                        'codclie' => $row->codclie,
                        'estado_legado' => $row->estado_legado,
                        'fec_liq_legado' => $row->fec_liq_legado,
                        'contrata_legado' => $row->contrata_legado,
                        'edificio_id' => $row->edificio_id
                    );
                    array_push($return, $data);
                }
            }
            $result->free();
           // return $result;
            return $data;
        } else {
            return "Hash no valido";
        }

    }
    /**
     * obtener la cantidad de visitas en una tarea,
     * retorna 0 si tiene multiplo de 3 ausentes
     * @param  int  gestion_id
     * @return Response
     */
    public function estado_visitas( $data )
    {
        $gestionId = $data['gestionId'];
        $hashg = $data['hashg'];
        if ($this->validate($gestionId, $hashg)) {
            $sql="SELECT MOD(f.cant, 3) final
            FROM(
                SELECT(
                    SELECT COUNT(t2.id)
                    FROM webpsi_officetrack.tareas t2
                    INNER JOIN webpsi_officetrack.paso_tres p3_2
                    ON t2.id=p3_2.task_id
                    WHERE t2.id>=t.id
                    AND t2.task_id=t.task_id
                    AND p3_2.estado=p3.estado
                    GROUP BY t2.task_id
                    HAVING COUNT(DISTINCT(t2.cod_tecnico))=1
                ) AS cant,
                t.task_id, t.cod_tecnico, p3.id, t.id tid
                FROM webpsi_officetrack.paso_tres p3
                INNER JOIN webpsi_officetrack.tareas t ON t.id=p3.task_id
                WHERE p3.estado LIKE '%usente%'
                AND t.task_id='$gestionId'
                ORDER BY p3.id
            ) f
            WHERE f.cant IS NOT NULL
            ORDER BY f.cant DESC
            LIMIT 0,1";
            try {
                $result = $this->mysqli->query($sql);
            } catch (Exception $e) {
                return $e->errorMessage();
            }
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_object()) {
                    $final = $row->final;
                }
            }
            $result->free();
            return $final;
        } else {
            return "Hash no valido";
        }
    }

}
