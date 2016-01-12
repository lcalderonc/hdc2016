<?php
namespace ClassOT;
class OfficeTrackGetSet
{
    private $_wsdl;
    private $_client;
        
    public function get_wsdl() {
        return $this->_wsdl;
    }

    public function set_wsdl($_wsdl) {
        $this->_wsdl = $_wsdl;
        $this->set_client();
    }

    public function get_client() {
        return $this->_client;
    }

    private function set_client() {
        $this->_client = new \SoapClient($this->_wsdl);
    }

}