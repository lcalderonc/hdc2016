<?php
namespace TestCapacity; 
class Simulador
{ 
     function get_capacity($data) {
             $response=array("get_capacity_response" =>(array(
                "activity_duration" => "60",
                "activity_travel_time" => "30",
                "capacity" =>array( 
                    array(
                    "location" => "routing",
                    "date" => "2014-02-04",
                    "quota" => "2000",
                    "available" => "1820"
                    ),
                    array(
                    "location" => "routing",
                    "date" => "2014-02-04",
                    "time_slot" => "12-17",
                    "quota" => "1000",
                    "available" => "910"
                    ),
                    array(
                    "location" => "routing",
                    "date" => "2014-02-04",
                    "time_slot" => "12-17",
                    "work_skill" => "04",
                    "quota" => "100",
                    "available" => "55"
                    ),
                    array(
                    "location" => "routing",
                    "date" => "2014-02-05",
                    "quota" => "1000",
                    "available" => "1910"
                    ),
                    array(
                    "location" => "routing",
                    "date" => "2014-02-05",
                    "time_slot" => "08-12",
                    "quota" => "100",
                    "available" => "955"
                    ),
                    array(
                    "location" => "planning",
                    "date" => "2014-02-04",
                    "quota" => "2100",
                    "available" => "1875"
                    ),
                    array(
                    "location" => "planning",
                    "date" => "2014-02-04",
                    "time_slot" => "12-17",
                    "quota" => "1050",
                    "available" => "915"
                    ),
                    array(
                    "location" => "planning",
                    "date" => "2014-02-04",
                    "time_slot" => "12-17",
                    "work_skill" => "04",
                    "quota" => "150",
                    "available" => "105"
                    ),
                    array(
                    "location" => "planning",
                    "date" => "2014-02-05",
                    "quota" => "2100",
                    "available" => "2100"
                    ),
                    array(
                    "location" => "planning",
                    "date" => "2014-02-05",
                    "time_slot" => "08-12",
                    "quota" => "1000",
                    "available" => "1000"
                    ), 
                ),
                "time_slot_info" =>array( 
                    array(
                    "name" => "12:00 - 17:00",
                    "label" => "12-17",
                    "time_from" => "12:00:00",
                    "time_to" => "17:00:00"
                    ),
                    array(
                    "name" => "08:00 - 12:00",
                    "label" => "08-12",
                    "time_from" => ">08:00:00",
                    "time_to" => "12:00:00"
                    )
                ))));
             
            $xml = \Array2XML::createXML('soap', $response);
            $string=$xml->saveXML(); 
            
            return $string;
    }
    
    function get_quota_data($data) {
             $response=array("ns1_get_quota_data_response" =>(array(
               "bucket" =>array(
                    array(
                    "bucket_id" => "routing",
                    "name" => "planing",
                    "day" =>array( 
                        "date" => "2014-02-04",
                        "quota" => "456",
                        "close_time" => "2014-02-04 13:51:00",
                        "max_available" => "24150",
                        "other_activities"=>"175",
                        "used"=>"225",
                        "used_quota_percent"=>"49.34210526",
                        "count"=>"5",
                        "time_slot"=>array(
                            array(
                            "label" => "08-12",
                            "quota_percent" => "55",
                            "min_quota" => "67",
                            "quota" => "251",
                            "status" => "4",
                            "max_available" => "8400",
                            "other_activities" => "47",
                            "used" => "90",
                            "used_quota_percent" => "35.85657371",
                            "count" => "2",
                            "category" =>array( 
                                array(
                                "label" => "04",
                                "quota_percent" => "6.81818199",
                                "quota" => "9",
                                "stop_booking_at" => "12",
                                "close_time" => "2014-02-04 23:30:00",
                                "max_available" => "5040",
                                "used" => "45",
                                "used_quota_percent" => "500",
                                "count" => "1"                                    
                                ),
                                array(
                                "label" => "6",
                                "quota_percent" => "93.1818161",
                                "quota" => "123",
                                "stop_booking_at" => "2",
                                "max_available" => "5280",
                                "used" => "45",
                                "used_quota_percent" => "36.58536585",
                                "count" => "1",
                                "work_zone"=>array(
                                    "label"=>"98",
                                    "status"=>"1",
                                    "closed_at"=>"2014-02-03 08:14:37",                                    
                                )
                                )
                            ),
                            "total"=>array(
                                "quota" => "132",
                                "max_available" => "10320",
                                "used" => "90",
                                "count" => "2"
                                )
                            ),
                        array(
                            "label" => "12-17",
                            "quota_percent" => "45",
                            "min_quota" => "567",
                            "quota" => "567",
                            "close_time" => "2014-02-04 16:30:00",
                            "max_available" => "10500",
                            "other_activities" => "89",
                            "used" => "135",
                            "used_quota_percent" => "23.80952381",
                            "count" => "3",
                            "category" =>array( 
                                array(
                                "label" => "04",
                                "quota_percent" => "91.76470947",
                                "quota" => "234",
                                "stop_booking_at" => "7",
                                "max_available" => "6300",
                                "used" => "45",
                                "used_quota_percent" => "19.23076923",
                                "count" => "1"                                    
                                ),
                                array(
                                "label" => "6",
                                "quota_percent" => "8.23529434",
                                "quota" => "21",
                                "stop_booking_at" => "4",
                                "max_available" => "6600",
                                "used" => "90",
                                "used_quota_percent" => "428.57142857",
                                "count" => "2"
                                )
                            ),
                            "total"=>array(
                                "quota" => "255",
                                "max_available" => "12900",
                                "used" => "135",
                                "count" => "3"
                                )
                        )
                      ),
                      "total"=>array(
                                "quota" => "818",
                                "max_available" => "18900",
                                "other_activities" => "136",
                                "used" => "225",
                                "count" => "5"
                                )
                        
                    )),
                    array("bucket_id" => "planing", // me quede
                    "name" => "planing 1",
                    "day" =>""
                    )
                )
                 
               )));
             
            $xml = \Array2XML::createXML('soap', $response);
            $string=$xml->saveXML(); 
            
            return $string;
    }
    
    
    }

?>
