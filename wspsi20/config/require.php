<?php

if ( $_SERVER['SERVER_NAME']=='10.226.44.222' ) {
    require_once "global.php";
} else {
    require_once "global_dev.php";
}

require_once(__DIR__."/../lib/nusoap.php");
require_once(__DIR__."/../class/class.Utils.php");
require_once(__DIR__."/../class/class.Database.php");
require_once(__DIR__."/../class/class.Tarea.php");