<?php
namespace Ofsc;
use Ofsc\Ofsc;
/**
 * API Capacity
 */
class Outbound extends Ofsc
{
    public function __construct()
    {
        $this->_wsdl = \Config::get("ofsc.wsdl.outbound");
        $this->_client = $this->iniciarCliente();

    }
    /**
     * este metodo se envia a TOAdirect para informar del estado de los mensajes
     * 
     */
    public function setMessageStatus($messages=array())
    {
        try {
            $setArray = array(
                "messages" => $messages
            );

            return $this->doAction('set_message_status', $setArray);
        } catch (Exception $exc) {
            return $exc->getMessage();
        }
    }


}
