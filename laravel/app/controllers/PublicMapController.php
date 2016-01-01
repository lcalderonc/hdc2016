<?php

class PublicMapController extends BaseController 
{

    protected $_visorgps;

    public function __construct(VisorgpsController $VisorgpsController) {
        $this->_visorgps = $VisorgpsController;
    }

    public function getRutatecnico($carnet, $condicion='hoy') {

        try {
            $publicmap = new Publicmap();

            //Actuaciones gestionadas
            $hoy = date("Y-m-d");
            if ($condicion=='hoy')
                $filtro_fec = array( "condicion"=>"=", "valor"=>$hoy );
            elseif ($condicion=='pasados')
                $filtro_fec = array( "condicion"=>"<", "valor"=>$hoy );
            elseif ($condicion=='futuros')
                $filtro_fec = array( "condicion"=>">", "valor"=>$hoy );
            elseif ($condicion=='todos')
                $filtro_fec = array( "condicion"=>"<>", "valor"=>"0000-00-00" );
            
            $lista["data"] = $publicmap->getRutaTecnico($carnet, '', $filtro_fec);

            $agenda["data"] = array();
            foreach ($lista["data"] as $key => $val) {
                $buscar["data"] = array();
                //Ordenes sin XY
                if (trim($val->x) == '' or trim($val->y) == '') {
                    $buscar["data"][] = $val;
                    $orden = $this->_visorgps->getActuCoord($buscar);
                    $agenda["data"][] = $orden[0];
                } else {
                    //Ordenes con XY
                    $agenda["data"][] = $val;
                }
            }

            if (count($agenda["data"]) > 0) {
                $array = json_decode(json_encode($agenda), true);

                return View::make(
                                'public.ordenrutatecnico', $array
                );
            } else {
                return "<h3>Sin resultados</h3>";
            }
        } catch (Exception $exc) {
            echo "<h2>Error: No se encontraron datos</h2>";
        }
    }

    public function getOrdentecnico($carnet, $gestion_id) {

        try {
            $publicmap = new Publicmap();

            //Actuaciones gestionadas
            $lista["data"] = $publicmap->getRutaTecnico($carnet, $gestion_id);

            $agenda["data"] = array();
            foreach ($lista["data"] as $key => $val) {
                $buscar["data"] = array();
                //Ordenes sin XY
                if (trim($val->x) == '' or trim($val->y) == '') {
                    $buscar["data"][] = $val;
                    $orden = $this->_visorgps->getActuCoord($buscar);
                    $agenda["data"][] = $orden[0];
                } else {
                    //Ordenes con XY
                    $agenda["data"][] = $val;
                }
            }

            if (count($agenda["data"]) > 0) {
                $array = json_decode(json_encode($agenda), true);

                return View::make(
                                'public.ordenrutatecnico', $array
                );
            } else {
                return "<h3>Sin resultados</h3>";
            }
        } catch (Exception $exc) {
            echo "<h2>Error: No se encontraron datos</h2>";
        }
    }

    public function showAddress($codigo) {
        $ubicacion = DB::table('ubicaciones')
                ->where('codigo', '=', $codigo)
                ->first(
                array(
                    'nombre',
                    'descripcion',
                    'contacto',
                    'x',
                    'y',
                    'usuario_id',
                    'imagen'
                )
        );

        //Si no se encuentran resultados
        if (is_null($ubicacion)) {
            return View::make('ubicaciones.noaddress');
        } else {
            return View::make(
                            'ubicaciones.address', array('ubicacion' => $ubicacion)
            );
        }
    }

}
