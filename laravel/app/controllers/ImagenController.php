<?php

class ImagenController extends BaseController
{
    protected $_errorController;
    
    public function __construct(ErrorController $ErrorController)
    {
        $this->_errorController = $ErrorController;
    }
    /**
     * Listar registro de actividades con estado 1
     * POST actividad/listar
     *
     * @return Response
     */
    public function postTareas()
    {
        try {
            $acceso="\$PSI20\$";
            $clave="\$1st3m@\$";
            $imagen=Input::get('img');
            $gestion_id=Input::get('gestion_id');
            $tarea_id=Input::get('tarea_id');
            $pos=Input::get('pos');
            $nimg=Input::get('nimg');
            $hashg=Input::get('hashg');
            $hash=hash('sha256',$acceso.$clave.$gestion_id.$tarea_id.$pos);

            $dirp1= 'img/officetrack/p01/g'.$gestion_id.'/';
            $dirp2= 'img/officetrack/p02/g'.$gestion_id.'/';
            $dirp3= 'img/officetrack/p03/g'.$gestion_id.'/';
            $dirf='';
            if($hash==$hashg){
                if(!is_dir($dirp1)){
                    //mkdir($dirp1, 0777, true);
                    //chmod($dirp1, 0777);
                    mkdir($dirp1);
                }

                if(!is_dir($dirp2)){
                    //mkdir($dirp2, 0777, true);
                    //chmod($dirp2, 0777);
                    mkdir($dirp2);
                }

                if(!is_dir($dirp3)){
                    //mkdir($dirp3, 0777, true);
                    //chmod($dirp3, 0777);
                    mkdir($dirp3);
                }
                
                if($pos==1){
                    $dirf=$dirp1;
                }
                elseif($pos==2){
                    $dirf=$dirp2;
                }
                elseif($pos==3){
                    $dirf=$dirp3;
                }

                //for( $i=0;$i<count($imagen);$i++ ){
                    if($imagen!=''){
                        $this->base64_to_jpeg($imagen, $dirf."i".$tarea_id."_".$nimg.".jpg");
                    }

                if( file_exists($dirf."i".$tarea_id."_".$nimg.".jpg") ){
                    echo "Finalizado";
                }
                else{
                    echo "Error:0003";
                }
                //}
            }
            else{
                echo "Error:0002";
            }
        }
        catch (Exception $exc) {
            $this->_errorController->saveError($exc);
            echo "Error:0001";
        }
    }

    public function postImagen()
    {
        try {
            $paso=Input::get('paso');
            $url=Input::get('url');
            $imagen=Input::get('imagen');
            $id=Input::get('id');

            $pos=strpos($url,"/",4);
            
            if( $paso!='' and $url!='' and $imagen!='' and $id!='' ){
                $dirp= 'img/officetrack/'.substr($url,0,$pos).'/';
                
                $dirf='';
                if(!is_dir($dirp)){
                    mkdir($dirp);
                }

                $this->base64_to_jpeg($imagen, 'img/officetrack/'.$url);

                if( file_exists('img/officetrack/'.$url) ){
                    if( $paso==1 ){
                        DB::update('UPDATE webpsi_officetrack.paso_uno SET casa_img1="",casa_img2="",casa_img3="" WHERE id=?',array($id));
                    }
                    elseif( $paso==2 ){
                        DB::update('UPDATE webpsi_officetrack.paso_dos SET   WHERE id=?',array($id));
                    }
                    elseif( $paso==3 ){
                        DB::update('UPDATE webpsi_officetrack.paso_tres SET final_img1="",final_img2="",final_img3="",boleta_img="",firma_img="" WHERE id=?',array($id));
                    }
                }
            }
        }
        catch (Exception $exc) {
            return json_encode($exc);
        }
    }

    public function base64_to_jpeg($base64_string, $output_file) {
        $ifp = fopen($output_file, "w+");
        fwrite($ifp, base64_decode($base64_string));
        fclose($ifp);

    return $output_file;
    }

}
