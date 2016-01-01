<?php
class Database
{
    private $_connection;
    private static $_instance; //The single instance
    /*
    private $_host = '10.226.44.223';
    private $_username = 'webpsi';
    private $_password = 'webpsi59u';
    private $_database = 'webpsi_officetrack';
    */
    private $_host = '192.168.1.2';
    private $_username = 'jsalcedo';
    private $_password = '123456';
    private $_database = 'psi';
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
    
    public function exec()
    {
        set_time_limit(0);
        echo "Inicio => ".date("H:i:s")."<br>";
        $query = '  SELECT gd.gestion_id,gd.fftt,gd.tipo_averia
                    FROM gestiones_detalles gd
                    WHERE gd.gestion_id NOT IN (
                        SELECT DISTINCT(gestion_id)
                        FROM gestiones_fftt
                    ) ';
        $result = $this->mysqli->query($query);

        if ( count($result)>0 ) {
            foreach ($result as $key => $value) {
                /**********************************************************/
                $id=$value['gestion_id'];
                $ffttExplode=explode("|", $value['fftt']);
                $tipoAExplode=explode("-", $value['tipo_averia']);
                if( count($tipoAExplode)==1 ){
                    $tipoAExplode=explode("_", $value['tipo_averia']);
                }

                $arrayproadsl=array(1,2,4);
                $arrayprocatv=array(5,6,7,8,9);

                $arrayaveradsl=array(1,2,3,14,15,16,17,9,18,19,20);
                $arrayaverbas=array(1,2,3,10,11,12,4,9);
                $arrayavercatv=array(5,13,6,7,8,9);

                $buscar=array("r1","r2","r3");
                $sqlttff="INSERT INTO gestiones_fftt (gestion_id,fftt_tipo_id,nombre) VALUES ('r1','r2','r3')";
                if ( in_array('aver', $tipoAExplode, true) ){
                    if ( in_array('adsl', $tipoAExplode, true) ){
                        for($i=0; $i<count($arrayaveradsl); $i++){
                            $array=array($id,$arrayaveradsl[$i],trim($ffttExplode[$i]));
                            
                            if( trim($ffttExplode[$i])!='' ){
                                $queryFinal=str_replace($buscar,$array,$sqlttff);
                                $this->mysqli->query( $queryFinal );
                                //DB::insert($sqlttff, $array);
                                //echo $queryFinal." \t <br>";
                            }
                        }
                    }
                    elseif ( in_array('bas', $tipoAExplode, true) ){
                        for($i=0; $i<count($arrayaverbas); $i++){
                            $array=array($id,$arrayaverbas[$i],trim($ffttExplode[$i]));
                            
                            if( trim($ffttExplode[$i])!='' ){
                                $queryFinal=str_replace($buscar,$array,$sqlttff);
                                $this->mysqli->query( $queryFinal );
                                //DB::insert($sqlttff, $array);
                                //echo $queryFinal." \t <br>";
                            }
                        }
                    }
                    elseif ( in_array('catv', $tipoAExplode, true) ){
                        for($i=0; $i<count($arrayavercatv); $i++){
                            $array=array($id,$arrayavercatv[$i],trim($ffttExplode[$i]));
                            
                            if( trim($ffttExplode[$i])!='' ){
                                $queryFinal=str_replace($buscar,$array,$sqlttff);
                                $this->mysqli->query( $queryFinal );
                                //DB::insert($sqlttff, $array);
                                //echo $queryFinal." \t <br>";
                            }
                        }
                    }
                }
                elseif ( in_array('prov', $tipoAExplode, true) ){
                    if ( in_array('adsl', $tipoAExplode, true) OR in_array('bas', $tipoAExplode, true) ){
                        for($i=0; $i<count($arrayproadsl); $i++){
                            if( $i==1 AND strtoupper(substr(trim($ffttExplode[$i]),0,1))=='A' ){
                                $array=array($id,$arrayproadsl[$i],trim($ffttExplode[$i]));
                            }
                            elseif($i==1 ){
                                $array=array($id,3,trim($ffttExplode[$i]));
                            }
                            else{
                                $array=array($id,$arrayproadsl[$i],trim($ffttExplode[$i]));
                            }
                            
                            if( trim($ffttExplode[$i])!='' ){
                                $queryFinal=str_replace($buscar,$array,$sqlttff);
                                $this->mysqli->query( $queryFinal );
                                //DB::insert($sqlttff, $array);
                                //echo $queryFinal." \t <br>";
                            }
                        }
                    }
                    elseif ( in_array('catv', $tipoAExplode, true) ){
                        for($i=0; $i<count($arrayprocatv); $i++){
                            $array=array($id,$arrayprocatv[$i],trim($ffttExplode[$i]));
                            
                            if( trim($ffttExplode[$i])!='' ){
                                $queryFinal=str_replace($buscar,$array,$sqlttff);
                                $this->mysqli->query( $queryFinal );
                                //DB::insert($sqlttff, $array);
                                //echo $queryFinal." \t <br>";
                            }
                        }
                    }
                }
                /**********************************************************/
            }
        }
        echo "Finalizo => ".date("H:i:s")."<br>";
        return true;
    }
}

$newProces= new Procesos();
$newProces->exec();
