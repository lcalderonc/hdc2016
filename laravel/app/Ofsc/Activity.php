<?php
namespace Ofsc;
use Ofsc\Ofsc;

/**
 * API Activity
 */
class Activity extends Ofsc
{
    public function __construct()
    {   
        $this->_wsdl = \Config::get("ofsc.wsdl.activity");
        $this->_client = $this->iniciarCliente();
    }

    /**
     * Permite cancelar una actividad en OFSC
     * 
     * @param Int $activityId AppointemntID
     * @return type
     */
    public function cancelActivity($activityId)
    {
        $setArray = array(
            "activity_id" => $activityId,
            "date" => "",
            "time" => "",
        );

        $response = $this->doAction('cancel_activity', $setArray);
        return $response;
    }
    
    /**
     * Permite iniciar una actividad en OFSC
     * 
     * @param Int $activityId AppointemntID
     * @return type
     */
    public function startActivity($activityId)
    {
        $setArray = array(
            "activity_id" => $activityId,
            "date" => "",
            "time" => "",
        );

        $response = $this->doAction('start_activity', $setArray);
        return $response;
    }
    
    /**
     * Permite completar (liquidar) una actividad en OFSC
     * 
     * @param Int $activityId AppointemntID
     * @return type
     */
    public function completeActivity($activityId)
    {
        $setArray = array(
            "activity_id" => $activityId,
            "date" => "",
            "time" => "",
        );

        $response = $this->doAction('complete_activity', $setArray);
        return $response;
    }
    /**
     * Permite obtener una actividad en OFSC
     * 
     * @param Int $activityId AppointemntID
     * @return type
     */
    public function getActivity($activityId)
    {
        $setArray = array(
            "activity_id" => $activityId,
        );

        $response = $this->doAction('get_activity', $setArray);
        return $response;
    }
    public function updateActivity($activityId, $data)
    {
        try {
            $propArray = array();
            if ( isset($data["direccion"]) ) {
                $propArray[] = array(
                    "name" => "address",
                    "value" => $data["direccion"],
                );
            }
            if ( isset($data["x"]) ) {
                $propArray[] = array(
                    "name" => "acoord_x",
                    "value" => $data["x"],
                );
            }
            if ( isset($data["y"]) ) {
                $propArray[] = array(
                    "name" => "acoord_y",
                    "value" => $data["y"],
                );
            }
            
            $setArray = array(
                "activity_id" => $activityId,
                "position_in_route" => "1",
                "properties" => $propArray,
                //"acoord_x" => "",
                //"acoord_y" => "",
                //"cell" => "",
                //"XA_CONTACT_PHONE_NUMBER_2" => "",
                //"XA_CONTACT_PHONE_NUMBER_3" => "",
                //"XA_CONTACT_PHONE_NUMBER_4" => ""
            );
            //print_r($setArray);die();
            $response = $this->doAction('update_activity', $setArray);
            return $response;
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

}
