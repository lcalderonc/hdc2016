<?php 
namespace Ofsc;

class XmlTestResponse
{
    public static function get_capacity_response(){
        $xml = file_get_contents("./ofsc_xml/get_capacity_response.xml");
        return $xml;
    }
    
    public static function get_quota_data_response(){
        $xml = file_get_contents("./ofsc_xml/get_quota_data_response.xml");
        return $xml;
    }
}