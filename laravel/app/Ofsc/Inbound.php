<?php
namespace Ofsc;
use Ofsc\Ofsc;

/**
 * API Inbound
 */
class Inbound extends Ofsc
{
    public function __construct()
    {
        $this->_wsdl = \Config::get("ofsc.wsdl.inbound");
        $this->_client = $this->iniciarCliente();
    }
    
    public function _createActivity()
    {
        $setArray = array(
            "head" => array (
                "processing_mode" => "appointment_only",
                "upload_type" => "incremental",
                "date" => "2015-11-04",
                "allow_change_date" => "yes",
                "appointment" => array(
                    "keys" => array(
                        "field" => "appt_number"
                    ),
                    "upload_type" => "full"
                ),
                "inventory" => array(
                    "keys" => array(
                        "field" => "invsn"
                    ),
                    "upload_type" => "full"
                ),
                "properties_mode" => "replace"
            ),
            "data" => array(
                "commands" => array(
                    "command" => array(
                        "date" => "2015-11-04",
                        "type" => "update_activity",
                        "external_id" => "BK_PRUEBAS_TOA",
                        "start_time" => "17:00",
                        "end_time" => "18:00",
                        "appointment" => array(
                            "appt_number" => "PruebaM1031",
                            "customer_number" => "1010101",
                            "worktype_label" => "PROV_INS_CATV",
                            "time_slot" => "16PM - 18PM",
                            "time_of_booking" => "2015-11-04 10:00",
                            "duration" => "60",
                            "name" => "John Doe",
                            "phone" => "5577330",
                            "email" => "jdoe@jdoe.com",
                            "cell" => "991133770",
                            "address" => "Av. Demo 123",
                            "city" => "Lima",
                            "state" => "Lima",
                            "zip" => "LIMA 05",
                            "language" => "1",
                            "reminder_time" => "15",
                            "time_zone" => "19",
                            "coordx" => " -77.016399",
                            "coordy" => "-12.108937",
                            "properties" => array(
                                "property" => array(
                                    array(
                                        "label" => "XA_CREATION_DATE",
                                        "value" => "2015-11-04 10:00"
                                    ),
                                    array(
                                        "label" => "XA_SOURCE_SYSTEM",
                                        "value" => "PSI"
                                    ),
                                    array(
                                        "label" => "XA_CUSTOMER_SEGMENT",
                                        "value" => "S"
                                    ),
                                    array(
                                        "label" => "XA_CUSTOMER_TYPE",
                                        "value" => "RESIDENCIAL"
                                    ),
                                    array(
                                        "label" => "XA_CONTACT_NAME",
                                        "value" => "Jane Doe"
                                    ),
                                    array(
                                        "label" => "XA_CONTACT_PHONE_NUMBER_2",
                                        "value" => "998877665"
                                    ),
                                    array(
                                        "label" => "XA_CONTACT_PHONE_NUMBER_3",
                                        "value" => "999999990"
                                    ),
                                    array(
                                        "label" => "XA_CONTACT_PHONE_NUMBER_4",
                                        "value" => "999999991"
                                    ),
                                    array(
                                        "label" => "XA_CITY_CODE",
                                        "value" => "Lima-01"
                                    ),
                                    array(
                                        "label" => "XA_DISTRICT_CODE",
                                        "value" => "SUR"
                                    ),
                                    array(
                                        "label" => "XA_DISTRICT_NAME",
                                        "value" => "Surquillo"
                                    ),
                                    array(
                                        "label" => "XA_ZONE",
                                        "value" => "LIM"
                                    ),
                                    array(
                                        "label" => "XA_QUADRANT",
                                        "value" => "Primer"
                                    ),
                                    array(
                                        "label" => "XA_WORK_ZONE_KEY",
                                        "value" => "MY"
                                    ),
                                    array(
                                        "label" => "XA_RURAL",
                                        "value" => "1"
                                    ),
                                    array(
                                        "label" => "XA_RED_ZONE",
                                        "value" => "1"
                                    ),
                                    array(
                                        "label" => "XA_WORK_TYPE",
                                        "value" => "Instalación CATV"
                                    ),
                                    array(
                                        "label" => "XA_APPOINTMENT_SCHEDULER",
                                        "value" => "CLI"
                                    ),
                                    array(
                                        "label" => "XA_USER",
                                        "value" => "UAT.TECHNICIAN"
                                    ),
                                    array(
                                        "label" => "XA_REQUIREMENT_NUMBER",
                                        "value" => "11002233"
                                    ),
                                    array(
                                        "label" => "XA_NUMBER_SERVICE_ORDER",
                                        "value" => "9876543210"
                                    ),
                                    array(
                                        "label" => "XA_CHANNEL_ORIGIN",
                                        "value" => "sucursal"
                                    ),
                                    array(
                                        "label" => "XA_SALES_POINT_CODE",
                                        "value" => "12345"
                                    ),
                                    array(
                                        "label" => "XA_SALES_POINT_DESCRIPTION",
                                        "value" => "Punto de venta 1"
                                    ),
                                    array(
                                        "label" => "XA_COMMERCIAL_VALIDATION",
                                        "value" => "1"
                                    ),
                                    array(
                                        "label" => "XA_TECHNICAL_VALIDATION",
                                        "value" => "0"
                                    ),
                                    array(
                                        "label" => "XA_WEB_UNIFICADA",
                                        "value" => "<![CDATA[<XA_WEB_UNIFICADA>
					<WebUnificada>
					<NumeroAgendas>2</NumeroAgendas>
					<NumeroMovimientos>4</NumeroMovimientos>
					<FechaUltimaAgenda>2015-11-03 15:00</FechaUltimaAgenda>
					</WebUnificada>
					</XA_WEB_UNIFICADA>]]>"
                                    ),
                                    array(
                                        "label" => "XA_ORDER_AREA",
                                        "value" => "Canal Online"
                                    ),
                                    array(
                                        "label" => "XA_COMMERCIAL_PACKET",
                                        "value" => "Movistar CATV"
                                    ),
                                    array(
                                        "label" => "XA_COMPANY_NAME",
                                        "value" => "COBRA"
                                    ),                                    
                                    array(
                                        "label" => "XA_GRUPO_QUIEBRE",
                                        "value" => "MASIVO"
                                    ),
                                    array(
                                        "label" => "XA_QUIEBRES",
                                        "value" => "DIGITALIZACION"
                                    ),
                                    array(
                                        "label" => "XA_BUSINESS_TYPE",
                                        "value" => "CATV"
                                    ),
                                    array(
                                        "label" => "XA_PRODUCTS_SERVICES",
                                        "value" => "<![CDATA[<XA_PRODUCTS_SERVICES>
					<ProductoServicio>
						<Codigo>4567</Codigo>
						<Descripcion>Producto M1</Descripcion>
					</ProductoServicio>
					<ProductoServicio>
						<Codigo>4568</Codigo>
						<Descripcion>Producto M2</Descripcion>
					</ProductoServicio>
					<ProductoServicio>
						<Codigo>4569</Codigo>
						<Descripcion>Producto M3</Descripcion>
					</ProductoServicio>
					</XA_PRODUCTS_SERVICES>]]>"
                                    ),
                                    array(
                                        "label" => "XA_CURRENT_PRODUCTS_SERVICES",
                                        "value" => "<![CDATA[<XA_CURRENT_PRODUCTS_SERVICES>
					<ActualProducto>
					<Codigo>8907</Codigo>
					<Descripcion>Movistar Uno</Descripcion>
					</ActualProducto>	
					</XA_CURRENT_PRODUCTS_SERVICES>]]>"
                                    ),
                                    array(
                                        "label" => "XA_EQUIPMENT",
                                        "value" => "<![CDATA[<XA_EQUIPMENT> 
					 <Equipo>
					   <IdEquipo>J1</IdEquipo>
					   <TipoEquipo>K1</TipoEquipo> 
					   <Cantidad>L1</Cantidad> 
					    </Equipo> 
					</XA_EQUIPMENT>]]>"
                                    ),
                                    array(
                                        "label" => "XA_NOTE",
                                        "value" => "Lorem ipsum dolor sit amet, "
                                        . "consectetur adipiscing elit, sed do "
                                        . "eiusmod tempor incididunt ut labore "
                                        . "et dolore magna aliqua"
                                    ),
                                    array(
                                        "label" => "XA_TELEPHONE_TECHNOLOGY",
                                        "value" => "VOIP"
                                    ),
                                    array(
                                        "label" => "XA_BROADBAND_TECHNOLOGY",
                                        "value" => "HFC"
                                    ),
                                    array(
                                        "label" => "XA_TV_TECHNOLOGY",
                                        "value" => "CATV DG"
                                    ),
                                    array(
                                        "label" => "XA_ACCESS_TECHNOLOGY",
                                        "value" => "COAXIAL"
                                    ),
                                    array(
                                        "label" => "XA_HFC_ZONE",
                                        "value" => "1"
                                    ),
                                    array(
                                        "label" => "XA_HFC_NODE",
                                        "value" => "L6"
                                    ),
                                    array(
                                        "label" => "XA_HFC_TROBA",
                                        "value" => "TR003"
                                    ),
                                    array(
                                        "label" => "XA_HFC_AMPLIFIER",
                                        "value" => "111"
                                    ),
                                    array(
                                        "label" => "XA_HFC_TAP",
                                        "value" => "77"
                                    ),
                                    array(
                                        "label" => "XA_HFC_BORNE",
                                        "value" => "3"
                                    ),
                                    array(
                                        "label" => "XA_REQUIREMENT_TYPE",
                                        "value" => "111"
                                    ),
                                    array(
                                        "label" => "XA_REQUIREMENT_REASON",
                                        "value" => "111"
                                    ),
                                    array(
                                        "label" => "XA_CATV_SERVICE_CLASS",
                                        "value" => "111"
                                    ),
                                    array(
                                        "label" => "XA_MDF",
                                        "value" => "ANU0"
                                    ),
                                    array(
                                        "label" => "XA_CABLE",
                                        "value" => "N6"
                                    ),
                                    array(
                                        "label" => "XA_CABINET",
                                        "value" => "L6"
                                    ),
                                    array(
                                        "label" => "XA_BOX",
                                        "value" => "C6"
                                    ),
                                    array(
                                        "label" => "XA_TERMINAL_ADDRESS",
                                        "value" => "Jr. Tambo 123"
                                    ),
                                    array(
                                        "label" => "XA_TERMINAL_LINKHTTP",
                                        "value" => "131"
                                    ),
                                    array(
                                        "label" => "XA_ADSLSTB_PREFFIX",
                                        "value" => "CM"
                                    ),
                                    array(
                                        "label" => "XA_ADSLSTB_MOVEMENT",
                                        "value" => "EXCLUSIVO"
                                    ),
                                    array(
                                        "label" => "XA_ADSL_SPEED",
                                        "value" => "1024"
                                    ),
                                    array(
                                        "label" => "XA_ADSLSTB_SERVICE_TYPE",
                                        "value" => "777"
                                    ),
                                    array(
                                        "label" => "XA_PENDING_EXTERNAL_ACTION",
                                        "value" => "RESCHEDULE"
                                    ),
                                    array(
                                        "label" => "XA_SMS_1",
                                        "value" => "Texto SMS"
                                    )
                                )
                            ),
                            "inventories" => array(
                                "inventory" => array(
                                    "upload_type" => "",
                                    "properties" => array(
                                        "property" => array(
                                            array(
                                                "label" => "invsn",
                                                "value" => "SN34634987"
                                            ),
                                            array(
                                                "label" => "invtype",
                                                "value" => "1"
                                            ),
                                            array(
                                                "label" => "quantity",
                                                "value" => "2"
                                            ),
                                            array(
                                                "label" => "invpool",
                                                "value" => "customer"
                                            ),
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
        
        $response = $this->doAction('inbound_interface', $setArray);        
        return $response;
    }
    
    /**
     * Crear actividad en OFSC
     * 
     * @param Array $data datos de la actividad
     * @param Boolean $sla false=agenda, true=sla
     * @return Object
     */
    public function createActivity($data=array(), $sla=false)
    {
        $setArray = array(
            "head" => array (
                "processing_mode" => "appointment_only",
                "upload_type" => "incremental",
                "date" => $data["date"],
                "allow_change_date" => "yes",
                "appointment" => array(
                    "keys" => array(
                        "field" => "appt_number"
                    ),
                    "upload_type" => "full"
                ),
                "inventory" => array(
                    "keys" => array(
                        "field" => "invsn"
                    ),
                    "upload_type" => "full"
                ),
                "properties_mode" => "replace"
            ),
            "data" => array(
                "commands" => array(
                    "command" => array(
                        "date" => $data["date"],
                        "type" => "update_activity",
                        "external_id" => $data["bucket"],
                        "start_time" => $data["start_time"],
                        "end_time" => $data["end_time"],
                        "appointment" => array(
                            "appt_number" => $data["appt_number"],
                            "customer_number" => $data["customer_number"],
                            "worktype_label" => $data["worktype_label"],
                            "time_slot" => $data["time_slot"],
                            "time_of_booking" => $data["time_of_booking"],
                            "duration" => $data["duration"],
                            "name" => $data["name"],
                            "phone" => $data["phone"],
                            "email" => $data["email"],
                            "cell" => $data["cell"],
                            "address" => $data["address"],
                            "city" => $data["city"],
                            "state" => $data["state"],
                            "zip" => $data["zip"],
                            "language" => $data["language"],
                            "reminder_time" => $data["reminder_time"],
                            "time_zone" => $data["time_zone"],
                            "coordx" => $data["coordx"],
                            "coordy" => $data["coordy"],
                            "properties" => array(
                                "property" => array(
                                    array(
                                        "label" => "XA_CREATION_DATE",
                                        "value" => $data["XA_CREATION_DATE"]
                                    ),
                                    array(
                                        "label" => "XA_SOURCE_SYSTEM",
                                        "value" => $data["XA_SOURCE_SYSTEM"]
                                    ),
                                    array(
                                        "label" => "XA_CUSTOMER_SEGMENT",
                                        "value" => $data["XA_CUSTOMER_SEGMENT"]
                                    ),
                                    array(
                                        "label" => "XA_CUSTOMER_TYPE",
                                        "value" => $data["XA_CUSTOMER_TYPE"]
                                    ),
                                    array(
                                        "label" => "XA_CONTACT_NAME",
                                        "value" => $data["XA_CONTACT_NAME"]
                                    ),
                                    array(
                                        "label" => "XA_CONTACT_PHONE_NUMBER_2",
                                        "value" => $data["XA_CONTACT_PHONE_NUMBER_2"]
                                    ),
                                    array(
                                        "label" => "XA_CONTACT_PHONE_NUMBER_3",
                                        "value" => $data["XA_CONTACT_PHONE_NUMBER_3"]
                                    ),
                                    array(
                                        "label" => "XA_CONTACT_PHONE_NUMBER_4",
                                        "value" => $data["XA_CONTACT_PHONE_NUMBER_4"]
                                    ),
                                    array(
                                        "label" => "XA_CITY_CODE",
                                        "value" => $data["XA_CITY_CODE"]
                                    ),
                                    array(
                                        "label" => "XA_DISTRICT_CODE",
                                        "value" => $data["XA_DISTRICT_CODE"]
                                    ),
                                    array(
                                        "label" => "XA_DISTRICT_NAME",
                                        "value" => $data["XA_DISTRICT_NAME"]
                                    ),
                                    array(
                                        "label" => "XA_ZONE",
                                        "value" => $data["XA_ZONE"]
                                    ),
                                    array(
                                        "label" => "XA_QUADRANT",
                                        "value" => $data["XA_QUADRANT"]
                                    ),
                                    array(
                                        "label" => "XA_WORK_ZONE_KEY",
                                        "value" => $data["XA_WORK_ZONE_KEY"]
                                    ),
                                    array(
                                        "label" => "XA_RURAL",
                                        "value" => $data["XA_RURAL"]
                                    ),
                                    array(
                                        "label" => "XA_RED_ZONE",
                                        "value" => $data["XA_RED_ZONE"]
                                    ),
                                    array(
                                        "label" => "XA_WORK_TYPE",
                                        "value" => $data["XA_WORK_TYPE"]
                                    ),
                                    array(
                                        "label" => "XA_APPOINTMENT_SCHEDULER",
                                        "value" => $data["XA_APPOINTMENT_SCHEDULER"]
                                    ),
                                    array(
                                        "label" => "XA_USER",
                                        "value" => $data["XA_USER"]
                                    ),
                                    array(
                                        "label" => "XA_REQUIREMENT_NUMBER",
                                        "value" => $data["XA_REQUIREMENT_NUMBER"]
                                    ),
                                    array(
                                        "label" => "XA_NUMBER_SERVICE_ORDER",
                                        "value" => $data["XA_NUMBER_SERVICE_ORDER"]
                                    ),
                                    array(
                                        "label" => "XA_CHANNEL_ORIGIN",
                                        "value" => $data["XA_CHANNEL_ORIGIN"]
                                    ),
                                    array(
                                        "label" => "XA_SALES_POINT_CODE",
                                        "value" => $data["XA_SALES_POINT_CODE"]
                                    ),
                                    array(
                                        "label" => "XA_SALES_POINT_DESCRIPTION",
                                        "value" => $data["XA_SALES_POINT_DESCRIPTION"]
                                    ),
                                    array(
                                        "label" => "XA_COMMERCIAL_VALIDATION",
                                        "value" => $data["XA_COMMERCIAL_VALIDATION"]
                                    ),
                                    array(
                                        "label" => "XA_TECHNICAL_VALIDATION",
                                        "value" => $data["XA_TECHNICAL_VALIDATION"]
                                    ),
                                    array(
                                        "label" => "XA_WEB_UNIFICADA",
                                        "value" => $data["XA_WEB_UNIFICADA"]
                                    ),
                                    array(
                                        "label" => "XA_ORDER_AREA",
                                        "value" => $data["XA_ORDER_AREA"]
                                    ),
                                    array(
                                        "label" => "XA_COMMERCIAL_PACKET",
                                        "value" => $data["XA_COMMERCIAL_PACKET"]
                                    ),
                                    array(
                                        "label" => "XA_COMPANY_NAME",
                                        "value" => $data["XA_COMPANY_NAME"]
                                    ),                                    
                                    array(
                                        "label" => "XA_GRUPO_QUIEBRE",
                                        "value" => $data["XA_GRUPO_QUIEBRE"]
                                    ),
                                    array(
                                        "label" => "XA_QUIEBRES",
                                        "value" => $data["XA_QUIEBRES"]
                                    ),
                                    array(
                                        "label" => "XA_BUSINESS_TYPE",
                                        "value" => $data["XA_BUSINESS_TYPE"]
                                    ),
                                    array(
                                        "label" => "XA_PRODUCTS_SERVICES",
                                        "value" => $data["XA_PRODUCTS_SERVICES"]
                                    ),
                                    array(
                                        "label" => "XA_CURRENT_PRODUCTS_SERVICES",
                                        "value" => $data["XA_CURRENT_PRODUCTS_SERVICES"]
                                    ),
                                    array(
                                        "label" => "XA_EQUIPMENT",
                                        "value" => $data["XA_EQUIPMENT"]
                                    ),
                                    array(
                                        "label" => "XA_NOTE",
                                        "value" => $data["XA_NOTE"]
                                    ),
                                    array(
                                        "label" => "XA_TELEPHONE_TECHNOLOGY",
                                        "value" => $data["XA_TELEPHONE_TECHNOLOGY"]
                                    ),
                                    array(
                                        "label" => "XA_BROADBAND_TECHNOLOGY",
                                        "value" => $data["XA_BROADBAND_TECHNOLOGY"]
                                    ),
                                    array(
                                        "label" => "XA_TV_TECHNOLOGY",
                                        "value" => $data["XA_TV_TECHNOLOGY"]
                                    ),
                                    array(
                                        "label" => "XA_ACCESS_TECHNOLOGY",
                                        "value" => $data["XA_ACCESS_TECHNOLOGY"]
                                    ),
                                    array(
                                        "label" => "XA_HFC_ZONE",
                                        "value" => $data["XA_HFC_ZONE"]
                                    ),
                                    array(
                                        "label" => "XA_HFC_NODE",
                                        "value" => $data["XA_HFC_NODE"]
                                    ),
                                    array(
                                        "label" => "XA_HFC_TROBA",
                                        "value" => $data["XA_HFC_TROBA"]
                                    ),
                                    array(
                                        "label" => "XA_HFC_AMPLIFIER",
                                        "value" => $data["XA_HFC_AMPLIFIER"]
                                    ),
                                    array(
                                        "label" => "XA_HFC_TAP",
                                        "value" => $data["XA_HFC_TAP"]
                                    ),
                                    array(
                                        "label" => "XA_HFC_BORNE",
                                        "value" => $data["XA_HFC_BORNE"]
                                    ),
                                    array(
                                        "label" => "XA_REQUIREMENT_TYPE",
                                        "value" => $data["XA_REQUIREMENT_TYPE"]
                                    ),
                                    array(
                                        "label" => "XA_REQUIREMENT_REASON",
                                        "value" => $data["XA_REQUIREMENT_REASON"]
                                    ),
                                    array(
                                        "label" => "XA_CATV_SERVICE_CLASS",
                                        "value" => $data["XA_CATV_SERVICE_CLASS"]
                                    ),
                                    array(
                                        "label" => "XA_MDF",
                                        "value" => $data["XA_MDF"]
                                    ),
                                    array(
                                        "label" => "XA_CABLE",
                                        "value" => $data["XA_CABLE"]
                                    ),
                                    array(
                                        "label" => "XA_CABINET",
                                        "value" => $data["XA_CABINET"]
                                    ),
                                    array(
                                        "label" => "XA_BOX",
                                        "value" => $data["XA_BOX"]
                                    ),
                                    array(
                                        "label" => "XA_TERMINAL_ADDRESS",
                                        "value" => $data["XA_TERMINAL_ADDRESS"]
                                    ),
                                    array(
                                        "label" => "XA_TERMINAL_LINKHTTP",
                                        "value" => $data["XA_TERMINAL_LINKHTTP"]
                                    ),
                                    array(
                                        "label" => "XA_ADSLSTB_PREFFIX",
                                        "value" => $data["XA_ADSLSTB_PREFFIX"]
                                    ),
                                    array(
                                        "label" => "XA_ADSLSTB_MOVEMENT",
                                        "value" => $data["XA_ADSLSTB_MOVEMENT"]
                                    ),
                                    array(
                                        "label" => "XA_ADSL_SPEED",
                                        "value" => $data["XA_ADSL_SPEED"]
                                    ),
                                    array(
                                        "label" => "XA_ADSLSTB_SERVICE_TYPE",
                                        "value" => $data["XA_ADSLSTB_SERVICE_TYPE"]
                                    ),
                                    array(
                                        "label" => "XA_PENDING_EXTERNAL_ACTION",
                                        "value" => $data["XA_PENDING_EXTERNAL_ACTION"]
                                    ),
                                    array(
                                        "label" => "XA_SMS_1",
                                        "value" => $data["XA_SMS_1"]
                                    ),
                                    array(
                                        "label" => "XA_DIAGNOSIS",
                                        "value" => $data["XA_DIAGNOSIS"]
                                    )
                                    ,
                                    array(
                                        "label" => "XA_TOTAL_REPAIRS",
                                        "value" => $data["XA_TOTAL_REPAIRS"]
                                    )
                                )
                            ),
                            "inventories" => array(
                                "inventory" => array(
                                    "upload_type" => "full",
                                    "properties" => array(
                                        "property" => array(
                                            array(
                                                "label" => "invsn",
                                                "value" => "SN34634987"
                                            ),
                                            array(
                                                "label" => "invtype",
                                                "value" => "1"
                                            ),
                                            array(
                                                "label" => "quantity",
                                                "value" => "2"
                                            ),
                                            array(
                                                "label" => "invpool",
                                                "value" => "customer"
                                            ),
                                        )
                                    )
                                )
                            )
                        )
                    )
                )
            )
        );
        
        //SLA
        if ($sla) {
            //Nodo base
            $base = &$setArray["data"]["commands"]["command"];
            
            //Eliminar tags no usados para SLA
            //unset($setArray["head"]["date"]);            
            //unset($base["date"]);
            unset($base["start_time"]);
            unset($base["end_time"]);
            unset($base["appointment"]["time_slot"]);
            
            //Agregar tags para SLA            
            $base["sla_window_start"] = $data["sla_window_start"];
            $base["sla_window_end"] = $data["sla_window_end"];
        }
        
        //Arreglo de propiedadas
        $propArray = &$setArray["data"]["commands"]["command"]
                      ["appointment"]["properties"]["property"];
        
        //Datos averias y/o provision
        if ($data["actividad"] != "averia") {
            //Averias
            foreach ($propArray as $key=>$val) {
                if ($val["label"] == "XA_DIAGNOSIS") {
                    unset($propArray[$key]);
                }
                
                if ($val["label"] == "XA_TOTAL_REPAIRS") {
                    unset($propArray[$key]);
                }
            }
        } else {
            //Provision
            foreach ($propArray as $key=>$val) {
                
            }
        }
        
        $response = $this->doAction('inbound_interface', $setArray);
        return $response;
    }
    
    
    public function updateActivity($data=array(), $sla=false)
    {
        $setArray = array(
            "head" => array (
                "processing_mode" => "appointment_only",
                "upload_type" => "incremental",
                //"date" => $data["date"],
                "allow_change_date" => "yes",
                "appointment" => array(
                    "keys" => array(
                        "field" => "appt_number"
                    ),
                    "upload_type" => "full"
                ),
                "inventory" => array(
                    "keys" => array(
                        "field" => "invsn"
                    ),
                    "upload_type" => "full"
                ),
                "properties_mode" => "replace"
            ),
            "data" => array(
                "commands" => array(
                    "command" => array(
                        //"date" => $data["date"],
                        "type" => "update_activity",
                        //"external_id" => $data["bucket"],
                        //"start_time" => $data["start_time"],
                        //"end_time" => $data["end_time"],
                        "appointment" => array(
                            "appt_number" => $data["appt_number"],
                            "customer_number" => $data["customer_number"],
                            "worktype_label" => $data["worktype_label"],
                            //"time_slot" => $data["time_slot"],
                            //"time_of_booking" => $data["time_of_booking"],
                            //"duration" => $data["duration"],
                            "name" => $data["name"],
                            //"phone" => $data["phone"],
                            //"email" => $data["email"],
                            //"cell" => $data["cell"],
                            //"address" => $data["address"],
                            //"city" => $data["city"],
                            //"state" => $data["state"],
                            //"zip" => $data["zip"],
                            //"language" => $data["language"],
                            //"reminder_time" => $data["reminder_time"],
                            //"time_zone" => $data["time_zone"],
                            //"coordx" => $data["coordx"],
                            //"coordy" => $data["coordy"],
                            "properties" => array(
                                "property" => array(
                                    /*array(
                                        "label" => "XA_CREATION_DATE",
                                        "value" => $data["XA_CREATION_DATE"]
                                    ),*/
                                    array(
                                        "label" => "XA_SOURCE_SYSTEM",
                                        "value" => $data["XA_SOURCE_SYSTEM"]
                                    ),
                                    /*array(
                                        "label" => "XA_CUSTOMER_SEGMENT",
                                        "value" => $data["XA_CUSTOMER_SEGMENT"]
                                    ),
                                    array(
                                        "label" => "XA_CUSTOMER_TYPE",
                                        "value" => $data["XA_CUSTOMER_TYPE"]
                                    ),
                                    array(
                                        "label" => "XA_CONTACT_NAME",
                                        "value" => $data["XA_CONTACT_NAME"]
                                    ),
                                    array(
                                        "label" => "XA_CONTACT_PHONE_NUMBER_2",
                                        "value" => $data["XA_CONTACT_PHONE_NUMBER_2"]
                                    ),
                                    array(
                                        "label" => "XA_CONTACT_PHONE_NUMBER_3",
                                        "value" => $data["XA_CONTACT_PHONE_NUMBER_3"]
                                    ),
                                    array(
                                        "label" => "XA_CONTACT_PHONE_NUMBER_4",
                                        "value" => $data["XA_CONTACT_PHONE_NUMBER_4"]
                                    ),
                                    array(
                                        "label" => "XA_CITY_CODE",
                                        "value" => $data["XA_CITY_CODE"]
                                    ),
                                    array(
                                        "label" => "XA_DISTRICT_CODE",
                                        "value" => $data["XA_DISTRICT_CODE"]
                                    ),
                                    array(
                                        "label" => "XA_DISTRICT_NAME",
                                        "value" => $data["XA_DISTRICT_NAME"]
                                    ),
                                    array(
                                        "label" => "XA_ZONE",
                                        "value" => $data["XA_ZONE"]
                                    ),
                                    array(
                                        "label" => "XA_QUADRANT",
                                        "value" => $data["XA_QUADRANT"]
                                    ),
                                    array(
                                        "label" => "XA_WORK_ZONE_KEY",
                                        "value" => $data["XA_WORK_ZONE_KEY"]
                                    ),
                                    array(
                                        "label" => "XA_RURAL",
                                        "value" => $data["XA_RURAL"]
                                    ),
                                    array(
                                        "label" => "XA_RED_ZONE",
                                        "value" => $data["XA_RED_ZONE"]
                                    ),
                                    array(
                                        "label" => "XA_WORK_TYPE",
                                        "value" => $data["XA_WORK_TYPE"]
                                    ),
                                    array(
                                        "label" => "XA_APPOINTMENT_SCHEDULER",
                                        "value" => $data["XA_APPOINTMENT_SCHEDULER"]
                                    ),
                                    array(
                                        "label" => "XA_USER",
                                        "value" => $data["XA_USER"]
                                    ),
                                    array(
                                        "label" => "XA_REQUIREMENT_NUMBER",
                                        "value" => $data["XA_REQUIREMENT_NUMBER"]
                                    ),
                                    array(
                                        "label" => "XA_NUMBER_SERVICE_ORDER",
                                        "value" => $data["XA_NUMBER_SERVICE_ORDER"]
                                    ),
                                    array(
                                        "label" => "XA_CHANNEL_ORIGIN",
                                        "value" => $data["XA_CHANNEL_ORIGIN"]
                                    ),
                                    array(
                                        "label" => "XA_SALES_POINT_CODE",
                                        "value" => $data["XA_SALES_POINT_CODE"]
                                    ),
                                    array(
                                        "label" => "XA_SALES_POINT_DESCRIPTION",
                                        "value" => $data["XA_SALES_POINT_DESCRIPTION"]
                                    ),
                                    array(
                                        "label" => "XA_COMMERCIAL_VALIDATION",
                                        "value" => $data["XA_COMMERCIAL_VALIDATION"]
                                    ),
                                    array(
                                        "label" => "XA_TECHNICAL_VALIDATION",
                                        "value" => $data["XA_TECHNICAL_VALIDATION"]
                                    ),
                                    array(
                                        "label" => "XA_WEB_UNIFICADA",
                                        "value" => $data["XA_WEB_UNIFICADA"]
                                    ),
                                    array(
                                        "label" => "XA_ORDER_AREA",
                                        "value" => $data["XA_ORDER_AREA"]
                                    ),
                                    array(
                                        "label" => "XA_COMMERCIAL_PACKET",
                                        "value" => $data["XA_COMMERCIAL_PACKET"]
                                    ),
                                    array(
                                        "label" => "XA_COMPANY_NAME",
                                        "value" => $data["XA_COMPANY_NAME"]
                                    ),                                    
                                    array(
                                        "label" => "XA_GRUPO_QUIEBRE",
                                        "value" => $data["XA_GRUPO_QUIEBRE"]
                                    ),
                                    array(
                                        "label" => "XA_QUIEBRES",
                                        "value" => $data["XA_QUIEBRES"]
                                    ),
                                    array(
                                        "label" => "XA_BUSINESS_TYPE",
                                        "value" => $data["XA_BUSINESS_TYPE"]
                                    ),
                                    array(
                                        "label" => "XA_PRODUCTS_SERVICES",
                                        "value" => $data["XA_PRODUCTS_SERVICES"]
                                    ),
                                    array(
                                        "label" => "XA_CURRENT_PRODUCTS_SERVICES",
                                        "value" => $data["XA_CURRENT_PRODUCTS_SERVICES"]
                                    ),
                                    array(
                                        "label" => "XA_EQUIPMENT",
                                        "value" => $data["XA_EQUIPMENT"]
                                    ),
                                    array(
                                        "label" => "XA_NOTE",
                                        "value" => $data["XA_NOTE"]
                                    ),
                                    array(
                                        "label" => "XA_TELEPHONE_TECHNOLOGY",
                                        "value" => $data["XA_TELEPHONE_TECHNOLOGY"]
                                    ),
                                    array(
                                        "label" => "XA_BROADBAND_TECHNOLOGY",
                                        "value" => $data["XA_BROADBAND_TECHNOLOGY"]
                                    ),
                                    array(
                                        "label" => "XA_TV_TECHNOLOGY",
                                        "value" => $data["XA_TV_TECHNOLOGY"]
                                    ),
                                    array(
                                        "label" => "XA_ACCESS_TECHNOLOGY",
                                        "value" => $data["XA_ACCESS_TECHNOLOGY"]
                                    ),
                                    array(
                                        "label" => "XA_HFC_ZONE",
                                        "value" => $data["XA_HFC_ZONE"]
                                    ),
                                    array(
                                        "label" => "XA_HFC_NODE",
                                        "value" => $data["XA_HFC_NODE"]
                                    ),
                                    array(
                                        "label" => "XA_HFC_TROBA",
                                        "value" => $data["XA_HFC_TROBA"]
                                    ),
                                    array(
                                        "label" => "XA_HFC_AMPLIFIER",
                                        "value" => $data["XA_HFC_AMPLIFIER"]
                                    ),
                                    array(
                                        "label" => "XA_HFC_TAP",
                                        "value" => $data["XA_HFC_TAP"]
                                    ),
                                    array(
                                        "label" => "XA_HFC_BORNE",
                                        "value" => $data["XA_HFC_BORNE"]
                                    ),
                                    array(
                                        "label" => "XA_REQUIREMENT_TYPE",
                                        "value" => $data["XA_REQUIREMENT_TYPE"]
                                    ),
                                    array(
                                        "label" => "XA_REQUIREMENT_REASON",
                                        "value" => $data["XA_REQUIREMENT_REASON"]
                                    ),
                                    array(
                                        "label" => "XA_CATV_SERVICE_CLASS",
                                        "value" => $data["XA_CATV_SERVICE_CLASS"]
                                    ),
                                    array(
                                        "label" => "XA_MDF",
                                        "value" => $data["XA_MDF"]
                                    ),
                                    array(
                                        "label" => "XA_CABLE",
                                        "value" => $data["XA_CABLE"]
                                    ),
                                    array(
                                        "label" => "XA_CABINET",
                                        "value" => $data["XA_CABINET"]
                                    ),
                                    array(
                                        "label" => "XA_BOX",
                                        "value" => $data["XA_BOX"]
                                    ),
                                    array(
                                        "label" => "XA_TERMINAL_ADDRESS",
                                        "value" => $data["XA_TERMINAL_ADDRESS"]
                                    ),
                                    array(
                                        "label" => "XA_TERMINAL_LINKHTTP",
                                        "value" => $data["XA_TERMINAL_LINKHTTP"]
                                    ),
                                    array(
                                        "label" => "XA_ADSLSTB_PREFFIX",
                                        "value" => $data["XA_ADSLSTB_PREFFIX"]
                                    ),
                                    array(
                                        "label" => "XA_ADSLSTB_MOVEMENT",
                                        "value" => $data["XA_ADSLSTB_MOVEMENT"]
                                    ),
                                    array(
                                        "label" => "XA_ADSL_SPEED",
                                        "value" => $data["XA_ADSL_SPEED"]
                                    ),
                                    array(
                                        "label" => "XA_ADSLSTB_SERVICE_TYPE",
                                        "value" => $data["XA_ADSLSTB_SERVICE_TYPE"]
                                    ),
                                    array(
                                        "label" => "XA_PENDING_EXTERNAL_ACTION",
                                        "value" => $data["XA_PENDING_EXTERNAL_ACTION"]
                                    ),
                                    array(
                                        "label" => "XA_SMS_1",
                                        "value" => $data["XA_SMS_1"]
                                    ),
                                    array(
                                        "label" => "XA_DIAGNOSIS",
                                        "value" => $data["XA_DIAGNOSIS"]
                                    )
                                    ,
                                    array(
                                        "label" => "XA_TOTAL_REPAIRS",
                                        "value" => $data["XA_TOTAL_REPAIRS"]
                                    )*/
                                )
                            ),
                            /*"inventories" => array(
                                "inventory" => array(
                                    "upload_type" => "full",
                                    "properties" => array(
                                        "property" => array(
                                            array(
                                                "label" => "invsn",
                                                "value" => "SN34634987"
                                            ),
                                            array(
                                                "label" => "invtype",
                                                "value" => "1"
                                            ),
                                            array(
                                                "label" => "quantity",
                                                "value" => "2"
                                            ),
                                            array(
                                                "label" => "invpool",
                                                "value" => "customer"
                                            ),
                                        )
                                    )
                                )
                            )*/
                        )
                    )
                )
            )
        );
        
        //SLA
        if ($sla) {
            //Nodo base
            $base = &$setArray["data"]["commands"]["command"];
            
            //Eliminar tags no usados para SLA
            //unset($setArray["head"]["date"]);            
            //unset($base["date"]);
            unset($base["start_time"]);
            unset($base["end_time"]);
            unset($base["appointment"]["time_slot"]);
            
            //Agregar tags para SLA            
            $base["sla_window_start"] = $data["sla_window_start"];
            $base["sla_window_end"] = $data["sla_window_end"];
        }
        
        //Arreglo de propiedadas
        $propArray = &$setArray["data"]["commands"]["command"]
                      ["appointment"]["properties"]["property"];
        
        //Datos averias y/o provision
        if ($data["actividad"] != "averia") {
            //Averias
            foreach ($propArray as $key=>$val) {
                if ($val["label"] == "XA_DIAGNOSIS") {
                    unset($propArray[$key]);
                }
                
                if ($val["label"] == "XA_TOTAL_REPAIRS") {
                    unset($propArray[$key]);
                }
            }
        } else {
            //Provision
            foreach ($propArray as $key=>$val) {
                
            }
        }
        
        $response = $this->doAction('inbound_interface', $setArray);
        return $response;
    }
    
    
    public function cancelActivity()
    {
        $setArray = array(
            "head" => array (
                "processing_mode" => "appointment_only",
                "upload_type" => "incremental",
                "appointment" => array(
                    "keys" => array(
                        "field" => "appt_number"
                    ),
                    "action_if_completed" => "create"
                ),
                "inventory" => array(
                    "keys" => array(
                        "field" => "invsvn"
                    ),
                    "upload_type" => "full"
                ),
                "properties_mode" => "update"
            ),
            "data" => array(
                "commands" => array(
                    "command" => array(
                        "type" => "cancel_activity",
                        "appointment" => array(
                            "appt_number" => "",
                            "properties" => array(
                                "property" => array(
                                    "label" => "XA_CANCEL_REASON",
                                    "value" => "?"
                                )
                            )
                        )
                    )
                )
            )
        );
        
        
        $requestArray = array_merge($this->getAuthArray(), $setArray);
        /*
        $response = $this->client->call('inbound_interface_request', $setArray);
         * 
         */
        return $requestArray;
    }
    
    
}
