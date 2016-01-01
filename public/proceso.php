<?php
class Database
{
    private $_connection;
    private static $_instance; //The single instance
    private $_host = '10.226.44.223';
    private $_username = 'webpsi';
    private $_password = 'webpsi59u';
    private $_database = 'webpsi_officetrack';
    private $_port = '3306';

    /*
    Get an instance of the Database
    @return Instance
    */
    public static function getInstance()
    {
        // If no instance then make one
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    // Constructor
    private function __construct()
    {
        $this->_connection = new mysqli(
            $this->_host,
            $this->_username,
            $this->_password,
            $this->_database
        );
        // Error handling
        if (mysqli_connect_error()) {
            trigger_error(
                "Failed to conencto to MySQL: " .
                mysql_connect_error(),
                E_USER_ERROR
            );
        }
    }

    // Magic method clone is empty to prevent duplication of connection
    private function __clone()
    {

    }

    // Get mysqli connection
    public function getConnection()
    {
        return $this->_connection;
    }
}
/**
*
*/
class Procesos
{
    public $mysqli;
    function __construct()
    {
        $db = Database::getInstance();
        $mysqli = $db->getConnection();
        $this->mysqli=$mysqli;
    }
    /**
     * convertir long a imagen
     */
    public static function base64_to_jpeg($baseString, $outputFile)
    {
        if (!isset($baseString) || !isset($outputFile)=='') {
            return;
        }
        $ifp = fopen("$outputFile", "w+");
        fwrite($ifp, base64_decode($baseString));
        fclose($ifp);

        return $outputFile;
    }
    public function insertInto($id, $tipoImagen, $name)
    {
        $name=substr($name, 32);

        $query =
            "INSERT INTO webpsi_officetrack.imagenes_tareas
        (tarea_id, imagen_tipo_id, nombre, fecha_creacion)
        values ($id, $tipoImagen, '$name', now() )";

        try {
            $result = $this->mysqli->query($query);

        } catch (Exception $e) {
            return $e->errorMessage();
        }
        return true;
    }
    public function update($tabla, $pasoId, $campo)
    {
        $sql = " UPDATE webpsi_officetrack.$tabla
                        SET $campo='' WHERE id=$pasoId ";
        try {
            $result = $this->mysqli->query($sql);

        } catch (Exception $e) {
            return $e->errorMessage();
        }
        return true;
    }
    public function clear()
    {
        $sqlPasoUno = " UPDATE  webpsi_officetrack.paso_uno 
                SET casa_img1='', casa_img2='', casa_img3=''
		where task_id in (select id
                                from webpsi_officetrack.tareas
                                where date(fecha_recepcion) = subdate(curdate(),interval 1 day)) ";
        $sqlPasoDos = " UPDATE  webpsi_officetrack.paso_dos 
                SET tap_img1='', tap_img2='', tap_img3=''
                , modem_img1='', modem_img2='', modem_img3=''
                , tv_img1='', tv_img2='', tv_img3=''
               , problema_img1='' , problema_img2='', problema_img3=''
		where task_id in (select id
                                from webpsi_officetrack.tareas
                                where date(fecha_recepcion) = subdate(curdate(),interval 1 day))";
        $sqlPasoTres = " UPDATE  webpsi_officetrack.paso_tres 
                SET final_img1='' , final_img2='' , final_img3=''
                , firma_img='', boleta_img=''
		where task_id in (select id
				from webpsi_officetrack.tareas
				where date(fecha_recepcion) = subdate(curdate(),interval 1 day)) ";
        try {
            $result = $this->mysqli->query($sqlPasoUno);
            $result = $this->mysqli->query($sqlPasoDos);
            $result = $this->mysqli->query($sqlPasoTres);

        } catch (Exception $e) {
            return $e->errorMessage();
        }
        return true;

    }
    public function exec()
    {
        set_time_limit(0);

        $query = 'SELECT * FROM webpsi_officetrack.tareas
                  where date(fecha_recepcion) = subdate(curdate(),interval 1 day) ';
        $result = $this->mysqli->query($query);

        $uploadFolder = '/www/virtuales/psi20/htdocs/public/img/officetrack';
        //$uploadFolder = 'C:/server/http/webpsi20/public/img/officetrack';

        if ( !is_dir($uploadFolder) )
            mkdir($uploadFolder);

        foreach ($result as $key => $value) {
            $paso=$value['paso'];
            $paso=explode('-', $paso);
            $paso=$paso[0];
            $id=$value['id'];
            $gestionId=$value['task_id'];
            if ($paso=='0001') {
                $query = "SELECT * FROM webpsi_officetrack.paso_uno
                            WHERE task_id= $id ";

                $resultDos = $this->mysqli->query($query);
                if (count($resultDos)>0) {
                    $folder=$uploadFolder.'/p01';
                    if ( !is_dir($folder) )
                        mkdir($folder);
                    foreach ($resultDos as $k => $val) {
                        $folder = $folder.'/g'.$gestionId;

                        if ( !is_dir($folder) )
                            mkdir($folder);
                        
                        if ($val['casa_img1']) {
                            $string = $val['casa_img1'];
                            $name=$folder.'/i'.$id.'_1.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '8', $name);
                            $this->update('paso_uno', $val['id'], 'casa_img1');
                        }
                        if ($val['casa_img2']) {
                            $string = $val['casa_img2'];
                            $name=$folder.'/i'.$id.'_2.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '8', $name);
                            $this->update('paso_uno', $val['id'], 'casa_img2');
                        }
                        if ($val['casa_img3']) {
                            $string = $val['casa_img3'];
                            $name=$folder.'/i'.$id.'_3.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '8', $name);
                            $this->update('paso_uno', $val['id'], 'casa_img3');
                        }
                    }
                }
            } elseif ($paso=='0002') {
                $query = "SELECT * FROM webpsi_officetrack.paso_dos
                            WHERE task_id=$id ";

                $resultDos = $this->mysqli->query($query);
                if (count($resultDos)>0) {
                    $folder=$uploadFolder.'/p02';
                    if ( !is_dir($folder) )
                        mkdir($folder);
                    foreach ($resultDos as $k => $val) {
                        $folder = $folder.'/g'.$gestionId;

                        if ( !is_dir($folder) )
                            mkdir($folder);

                        if ($val['tap_img1']) {
                            $string = $val['tap_img1'];
                            $name=$folder.'/i'.$id.'_1.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '2', $name);
                            $this->update('paso_dos', $val['id'], 'tap_img1');
                        }
                        if ($val['tap_img2']) {
                            $string = $val['tap_img2'];
                            $name=$folder.'/i'.$id.'_2.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '2', $name);
                            $this->update('paso_dos', $val['id'], 'tap_img2');
                        }
                        if ($val['tap_img3']) {
                            $string = $val['tap_img3'];
                            $name=$folder.'/i'.$id.'_3.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '2', $name);
                            $this->update('paso_dos', $val['id'], 'tap_img3');
                        }
                        if ($val['modem_img1']) {
                            $string = $val['modem_img1'];
                            $name=$folder.'/i'.$id.'_4.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '3', $name);
                            $this->update('paso_dos', $val['id'], 'modem_img1');
                        }
                        if ($val['modem_img2']) {
                            $string = $val['modem_img2'];
                            $name=$folder.'/i'.$id.'_5.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '3', $name);
                            $this->update('paso_dos', $val['id'], 'modem_img2');
                        }
                        if ($val['modem_img3']) {
                            $string = $val['modem_img3'];
                            $name=$folder.'/i'.$id.'_6.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '3', $name);
                            $this->update('paso_dos', $val['id'], 'modem_img3');
                        }
                        if ($val['tv_img1']) {
                            $string = $val['tv_img1'];
                            $name=$folder.'/i'.$id.'_7.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '4', $name);
                            $this->update('paso_dos', $val['id'], 'tv_img1');
                        }
                        if ($val['tv_img2']) {
                            $string = $val['tv_img2'];
                            $name=$folder.'/i'.$id.'_8.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '4', $name);
                            $this->update('paso_dos', $val['id'], 'tv_img2');
                        }
                        if ($val['tv_img3']) {
                            $string = $val['tv_img3'];
                            $name=$folder.'/i'.$id.'_9.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '4', $name);
                            $this->update('paso_dos', $val['id'], 'tv_img3');
                        }
                        if ($val['problema_img1']) {
                            $string = $val['problema_img1'];
                            $name=$folder.'/i'.$id.'_10.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '5', $name);
                           $this->update('paso_dos', $val['id'], 'problema_img1');
                        }
                        if ($val['problema_img2']) {
                            $string = $val['problema_img2'];
                            $name=$folder.'/i'.$id.'_11.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '5', $name);
                           $this->update('paso_dos', $val['id'], 'problema_img2');
                        }
                        if ($val['problema_img3']) {
                            $string = $val['problema_img3'];
                            $name=$folder.'/i'.$id.'_12.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '5', $name);
                           $this->update('paso_dos', $val['id'], 'problema_img3');
                        }
                    }
                }
            } elseif ($paso=='0003') {
                $query = "SELECT * FROM webpsi_officetrack.paso_tres
                            WHERE task_id= $id ";

                $resultDos = $this->mysqli->query($query);
                if (count($resultDos)>0) {
                    $folder=$uploadFolder.'/p03';
                    if ( !is_dir($folder) )
                        mkdir($folder);
                    foreach ($resultDos as $k => $val) {
                        $folder = $folder.'/g'.$gestionId;

                        if ( !is_dir($folder) )
                            mkdir($folder);

                        if ($val['final_img1']) {
                            $string = $val['final_img1'];
                            $name=$folder.'/i'.$id.'_1.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '6', $name);
                            $this->update('paso_tres', $val['id'], 'final_img1');
                        }
                        if ($val['final_img2']) {
                            $string = $val['final_img2'];
                            $name=$folder.'/i'.$id.'_2.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '6', $name);
                            $this->update('paso_tres', $val['id'], 'final_img2');
                        }
                        if ($val['final_img3']) {
                            $string = $val['final_img3'];
                            $name=$folder.'/i'.$id.'_3.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '6', $name);
                            $this->update('paso_tres', $val['id'], 'final_img3');
                        }
                        if ($val['firma_img']) {
                            $string = $val['firma_img'];
                            $name=$folder.'/i'.$id.'_4.jpg';
                            if (file_exists($name)) continue;
                            $this->base64_to_jpeg($string, $name);
                            $this->insertInto($id, '7', $name);
                            $this->update('paso_tres', $val['id'], 'firma_img');
                        }
                    }
                }
            }
            print_r($paso);
        }
        return true;
    }
}

$newProces= new Procesos();
$newProces->exec();
$newProces->clear();
