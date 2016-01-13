<?php

use Ofsc\Capacity;
use Ofsc\Inbound;
use Ofsc\Outbound;

class NotificacionController extends \BaseController
{

    public function fire($job, $data)
    {
        // Process the job...
        $job->delete();
    }

    public function sendMessage($job, $data)
    {
        $id = $job->getJobId();
        try {
            for ( $i=0; $i < count($data); $i++ ) {
                Mensaje::crear( $id, $data[$i] );
                $Outbound = new Outbound();
                $request=array('message'=>
                    array(
                        'message_id'=>$data[$i]['message_id'],
                        'status'=>'delivered'
                        )
                    );
                $response=$Outbound->setMessageStatus($request);
                $pos =  strpos($data[$i]['body'], 'completed');
                $pos2 = strpos($data[$i]['body'], 'suspended');
                $pos3 = strpos($data[$i]['body'], 'notdone');
                $pos4 = strpos($data[$i]['body'], 'canceled');

                $activityId='';
                $codactud=explode("appt_number>",$data[$i]['body']);
                $codactu=explode("<",$codactud[1]);
                if( $activityId!='' AND $codactu[0]!='' ){
                    if ($pos !== false) {
                        $this->completarOfsc($activityId,$codactu[0]);
                    }
                    elseif ($pos2 !== false) {
                        $this->suspenderOfsc($activityId,$codactu[0]);
                    }
                    elseif ($pos3 !== false) {
                        $this->norealizadoOfsc($activityId,$codactu[0]);
                    }
                    elseif ($pos4 !== false) {
                        //$this->cancelarOfsc($activityId,$codactu);
                    } 
                }
            }
        } catch (Exception $e) {
            
        }
        $job->delete();
    }

    public function cancelarOfsc($activityId,$codactu)
    {
        $save["aid"]=$activityId;

            $actu = \Gestion::getCargar($codactu);
            $actuObj = $actu["datos"][0];
            $actuArray = (array) $actuObj;

            $actuArray['estado_agendamiento']="3-0";
            $actuArray['gestion_id'] = $actuObj->id;
            $actuArray['estado']=4;
            $actuArray['motivo']=9;
            $actuArray['submotivo']=3;

            Input::replace($actuArray);
            $save = $this->_gestionMovimientoController->postRegistrar();

            $datos=array();
            $datos['aid']=$activityId;
            $datos['envio_ofsc']=3;
            $datos['gestion_id']=$save['gestion_id'];
            $datos['gestion_movimiento_id']=$save['gestion_movimiento_id'];
            $datos['estado_ofsc_id']=5;
            GestionMovimiento::OfscUpdate($datos);
    }

    public function completarOfsc($activityId,$codactu)
    {
        $save["aid"]=$activityId;

            $actu = \Gestion::getCargar($codactu);
            $actuObj = $actu["datos"][0];
            $actuArray = (array) $actuObj;

            //$actuArray['estado_agendamiento']="3-0";
            $actuArray['gestion_id'] = $actuObj->id;
            $actuArray['estado']=6;
            $actuArray['motivo']=3;
            $actuArray['submotivo']=12;

            Input::replace($actuArray);
            $save = $this->_gestionMovimientoController->postRegistrar();

            $datos=array();
            $datos['aid']=$activityId;
            $datos['envio_ofsc']=0;
            $datos['gestion_id']=$save['gestion_id'];
            $datos['gestion_movimiento_id']=$save['gestion_movimiento_id'];
            $datos['estado_ofsc_id']=6;
            GestionMovimiento::OfscUpdate($datos);
    }

    public function suspenderOfsc()
    {
        $save["aid"]=$activityId;

            $actu = \Gestion::getCargar($codactu);
            $actuObj = $actu["datos"][0];
            $actuArray = (array) $actuObj;

            $actuArray['estado_agendamiento']="3-0";
            $actuArray['gestion_id'] = $actuObj->id;
            $actuArray['estado']=7;
            $actuArray['motivo']=2;
            $actuArray['submotivo']=18;

            unset($actuArray["fecha_agenda"]);
            unset($actuArray["horario_id"]);
            unset($actuArray["dia_id"]);
            unset($actuArray["tecnico"]);
            unset($actuArray["tecnico_id"]);
            unset($actuArray["celula_id"]);

            Input::replace($actuArray);
            $save = $this->_gestionMovimientoController->postRegistrar();

            $datos=array();
            $datos['aid']=$activityId;
            $datos['envio_ofsc']=4;
            $datos['gestion_id']=$save['gestion_id'];
            $datos['gestion_movimiento_id']=$save['gestion_movimiento_id'];
            $datos['estado_ofsc_id']=3;
            GestionMovimiento::OfscUpdate($datos);
    }

    public function norealizadoOfsc()
    {
        $save["aid"]=$activityId;

            $actu = \Gestion::getCargar($codactu);
            $actuObj = $actu["datos"][0];
            $actuArray = (array) $actuObj;

            $actuArray['estado_agendamiento']="3-0";
            $actuArray['gestion_id'] = $actuObj->id;
            $actuArray['estado']=5;
            $actuArray['motivo']=4;
            $actuArray['submotivo']=6;

            unset($actuArray["fecha_agenda"]);
            unset($actuArray["horario_id"]);
            unset($actuArray["dia_id"]);
            unset($actuArray["tecnico"]);
            unset($actuArray["tecnico_id"]);
            unset($actuArray["celula_id"]);

            Input::replace($actuArray);
            $save = $this->_gestionMovimientoController->postRegistrar();

            $datos=array();
            $datos['aid']=$activityId;
            $datos['envio_ofsc']=4;
            $datos['gestion_id']=$save['gestion_id'];
            $datos['gestion_movimiento_id']=$save['gestion_movimiento_id'];
            $datos['estado_ofsc_id']=4;
            GestionMovimiento::OfscUpdate($datos);
    }
}
