<?php

namespace Ofsc;
use Ofsc\Ofsc;

/**
* test de prueba para web service cliente
*/
class Test extends Ofsc
{
    function __construct()
    {
        $this->wsdl = \Config::get("ofsc.wsdl.test");
        $this->client = $this->iniciarCliente();
    }

    public function conversion( $fromCurrency, $toCurrency)
    {
        return $this->client->call('ConversionRate', array('FromCurrency'=>$fromCurrency,'ToCurrency'=>$toCurrency));
    }
}

?>