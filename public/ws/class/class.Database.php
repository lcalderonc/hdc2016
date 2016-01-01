<?php
class Database
{
    private $_connection;
    private static $_instance; //The single instance
    private $_host = HOSTNAME;
    private $_username = USERNAME;
    private $_password = PASSWORD;
    private $_database = DATABASE_NAME;
    private $_port = PORT;

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
/*
    public function get_data($sql)
    {
        $ret = array('STATUS'=>'ERROR','ERROR'=>'','DATA'=>array());

        $mysqli = $this->getConnection();
        $res = $mysqli->query($sql);

        if($res)
            $ret['STATUS'] = "OK";
        else
            $ret['ERROR'] = mysqli_error();

        while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
            $ret['DATA'][] = $row;
        }
        return $ret;
    }

    public function exec($sql)
    {
        $ret = array('STATUS'=>'ERROR','ERROR'=>'');

        $mysqli = $this->getConnection();
        $res = $mysqli->query($sql);

        if ($res)
            $ret['STATUS'] = "OK";
        else
            $ret['ERROR'] = mysqli_error();

        return $ret;
    }*/

}