<?php

use Ofsc\Capacity;
use Ofsc\Inbound;

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
            }
        } catch (Exception $e) {
            
        }
        $job->delete();
    }
}