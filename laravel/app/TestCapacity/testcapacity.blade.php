<?php
namespace TestCapacity; 
use Ofsc\Capacity;

        $capacity = new Capacity();
        $response = $capacity->getQuotaData();
        header("content-type: text/xml");
        print_r($response);
//        $carga_xml = simplexml_load_string($response);
//        print_r($carga_xml);
