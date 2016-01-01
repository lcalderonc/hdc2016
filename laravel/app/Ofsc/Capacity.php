<?php
namespace Ofsc;
use Ofsc\Ofsc;
use TestCapacity\Simulador; 
/**
 * API Capacity
 */
class Capacity extends Ofsc
{
    public function __construct() 
    {
        $this->_wsdl = \Config::get("ofsc.wsdl.capacity");
        $this->_client = $this->iniciarCliente();
       
    }

    public function _getCapacity() 
    {
        //utilizado para devolver los valores de capacidad, 
        //duración, tiempo de viaje y capacidad
        try {
            $setArray = array(
                "date" => "",
                "location" => "",
                "calculate_duration" => "",
                "calculate_travel_time" => "",
                "calculate_work_skill" => "",
                "return_time_slot_info" => "",
                "determine_location_by_work_zone" => "",
                "dont_aggregate_results" => "",
                "min_time_to_end_of_time_slot" => "",
                "default_duration" => "",
                "time_slot" => "",
                "work_skill" => "",
                "activity_field" => array(
                    "name" => "",
                    "value" => ""
                )
            );
//          $requestArray = array_merge($this->getAuthArray(), $setArray);
//          $response = $this->doAction('get_capacity', $setArray);
            
            //temporal -------------------------------
            $capacity = new Simulador();
            $response = $capacity->get_capacity($setArray); 
            //temporal -------------------------------
            
            
            return $response;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    
    public function getCapacity($data=array()) 
    {
        try {
            $setArray = array(
                "date" => $data["fecha"],
                "calculate_duration" => false,
                "calculate_travel_time" => false,
                "calculate_work_skill" => false,
                "return_time_slot_info" => true,
                "determine_location_by_work_zone" => true,
                //"dont_aggregate_results" => false,
                //"min_time_to_end_of_time_slot" => "0",
                //"default_duration" => 60,
                "activity_field" => $data["activity_field"]              
            );
            
            if (isset($data["bucket"]) and $data["bucket"]!="") {
                $setArray["location"] = $data["bucket"];
            }
            if ($data["work_skill"]!="") {
                $setArray["work_skill"] = $data["work_skill"];
            }
            if ($data["time_slot"]!="") {
                $setArray["time_slot"] = $data["time_slot"];
            }
            
            $response = $this->doAction('get_capacity', $setArray);        
            return $response;
        } catch (Exception $exc) {
            return $exc->getMessage();
        }
    }
    
    
    public function getQuotaData() 
    {

        try {            
            $setArray = array(
            "date" => "",
            "reosurce_id" => "",
            "aggregate_results" => "",
            "calculate_totals" => "",
            "time_slot" => "",
            "category" => "",
            "day_quota_field" => "",
            "time_slot_quota_field" => "",
            "category_quota_field" => "",
            "work_zone_quota_field" => ""    
            );
//            $requestArray = array_merge($this->getAuthArray(), $setArray);
//            $response = $this->doAction('get_quota_data', $setArray);
//            
            //temporal -------------------------------
            $capacity = new Simulador();
            $response = $capacity->get_quota_data($setArray); 
            //temporal -------------------------------
            
            return $response;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    
    public function SetQuota() 
    {

        try {            
            $setArray = array(
            "bucket" => array(
                    "bucket_id" => "",
                    "day" => array(
                            "date" => "",
                            "quota_percent" => "",
                            "min_quota" => "",
                            "quota" => "",
                            "status" => "",
                            "time_slot" => array(
                                    "label" => "",
                                    "quota_percent" => "",
                                    "min_quota" => "",
                                    "quota" => "",
                                    "stop_booking_at" => "",
                                    "status" =>"",
                                    "category" => array(
                                        "label" => "",
                                        "quota_percent" => "",
                                        "min_quota" => "",
                                        "quota" => "",
                                        "stop_booking_at" => "",
                                        "status" => "",
                                        "work_zone" => array(
                                                "label" => "",
                                                "status" => ""
                                            )
                                    )
                               )
                        )
                )
            
            );
            $requestArray = array_merge($this->getAuthArray(), $setArray);
            
            $response = $this->doAction('set_quota', $setArray);
            
            return $response;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    
    public function getQuotaCloseTime() 
    {

        try {            
            $setArray = array(
            "bucket_id" => "",
            "day_offset" => "",
            "time_slot" => "",
            "category" => "",
            "work_zone" => ""  
            );
            
            $requestArray = array_merge($this->getAuthArray(), $setArray);
            
            $response = $this->doAction('get_quota_close_time', $setArray);
            
            return $response;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    
    public function setQuotaCloseTime() 
    {

        try {            
            $setArray = array(
            "bucket_id" => "",
            "day_offset" => "",
            "time_slot" => "",
            "category" => "",
            "work_zone" => "",
            "close_time" => ""
            );
            
            $requestArray = array_merge($this->getAuthArray(), $setArray);
            
            $response = $this->doAction('set_quota_close_time', $setArray);
            
            return $response;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

}
