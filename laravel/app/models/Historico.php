<?php

class Historico extends \Eloquent
{
    /**
     * 
     */
    public static function getGestiones(
        $telefono='',
        $codcliatis='',
        $codsercms='',
        $codclicms=''
    )
    {
        $query=DB::table('gestiones_detalles as gd')
                ->join('gestiones AS g', 'gd.gestion_id', '=', 'g.id')
                ->select('gd.id','tipo_averia','empresa_id',
                        'actividad_id', 'telefono',
                        'direccion_instalacion AS direccion',
                        'nombre_cliente_critico',
                        'telefono_cliente_critico',
                        'celular_cliente_critico',
                        'observacion','segmento',
                        'zonal','zonal_id','mdf',
                        'distrito', 'zona_movistar_uno',
                        'microzona','lejano','x','y',
                        DB::raw("IFNULL(codactu,'') AS codactu"),
                        DB::raw("IFNULL(eecc_zona,'') AS eecc_zona"),
                        DB::raw("IFNULL(quiebre_id,'') AS quiebre_id"),
                        DB::raw("IFNULL(codservcms,'') AS codservcms"),
                        DB::raw("IFNULL(codclie,'') AS codclie"),
                        DB::raw("IFNULL(inscripcion,'') AS codclicms")
                    );
        if ($telefono!='') {
            $query->whereRAW("TRIM(telefono)= '$telefono'");
            $query->havingRaw('gd.id is not null');
        }
        if ($codcliatis!='') {
            $query->whereRAW("TRIM(codclie)= '$codcliatis'");
            $query->havingRaw('gd.id is not null');
        }
        if ($codsercms!='') {
            $query->whereRAW("TRIM(codservcms)= '$codsercms'");
            $query->havingRaw('gd.id is not null');
        }
        if ($codclicms!='') {
            $query->whereRAW("TRIM(inscripcion)= '$codclicms'");
            $query->havingRaw('gd.id is not null');
        }
        $query->whereRAW(" LENGTH(REPLACE(telefono,'1',''))!=0 ");
        $query->whereRAW(" TRIM(REPLACE(telefono,' ','')) REGEXP '^[0-9]+$' ");
        $query->whereRAW(" TRIM(REPLACE(telefono,' ','')) >2 ");
        return $query->get();

    }
    /**
     * 
     */
    public static function getTemporales(
        $telefono='',
        $codclicms=''
    )
    {
        $whereAveria =$whereProvision='';
        if($telefono!='') {
            $whereAveria = " telefono='".$telefono."'";
            $whereProvision = " telefono='".$telefono."'";
        }
        if($codclicms!='') {
            $whereAveria = " inscripcion='".$codclicms."'";
            $whereProvision = " codigo_del_cliente='".$codclicms."'";
        }
        //buscar en averias inscripcion -> codclicms
        $query="SELECT telefono, dir_terminal AS direccion, '' AS codclie,
                inscripcion AS codclicms , '' AS codservcms, averia AS codactu
                FROM webpsi_coc.averias_criticos_final
                WHERE ".$whereAveria;
        $result = DB::select($query,array());
        $rows = count($result);
        if ($rows>0) {//averia
            return $result;
        } else {//provision
            $query="SELECT telefono, dir_terminal AS direccion, '' AS codclie,
                    codigo_del_cliente AS codclicms , '' AS codservcms,
                    codigo_req AS codactu
                    FROM webpsi_coc.tmp_provision
                    WHERE ".$whereProvision;
            $result = DB::select($query,array());
            $rows = count($result);
            return $result;
        }
        if (count($result)>0 && $result!=='') {
            //se debe enviar X Y de geolocalizacion
            $result = Historico::findGeolocalizacion($result, $tipo_averia);
            return $result;
        } else {
            return array();
        }
    }
    /**
     * 
     */
    public static function getPendientes(
        $telefono='',
        $codcliatis='',
        $codsercms='',
        $codclicms=''
    )
    {
        $numeroSql='';
        $tipo_averia='';
        //pendiente
        if ($codclicms!='') {
            //si busca por codigo cliente cms, terminar la busqueda
            return array();
        }
        if ($telefono!='' || $codcliatis!='') {
            //1
            $sql= " SELECT peticion
                    FROM schedulle_sistemas.prov_pen_bas_pais
                    WHERE telefono=? OR cod_cliente=? AND cod_cliente<>''
                    AND cod_cliente<>'0' AND peticion<>0";
            $query = DB::select($sql, array($telefono,$codcliatis));
            if (count($query)>0) {
                $numeroSql='1';
                $peticion=$query[0]->peticion;
            } else {
                //2
                $sql= " SELECT codigo_req as peticion
                        FROM schedulle_sistemas.prov_pen_catv_pais
                        WHERE codigo_del_cliente=? AND codigo_req<>0 ";
                $query = DB::select($sql, array($codcliatis));
                if (count($query)>0 && trim($codcliatis)<>'') {
                    $tipo_averia='CATV';
                    $numeroSql='2';
                    $peticion=$query[0]->peticion;
                } else {
                    //3 AVERIAS
                    $sql= " SELECT numero_osiptel as peticion
                            FROM schedulle_sistemas.aver_pen_adsl_pais
                            WHERE telefono=?";
                    $query = DB::select($sql, array($telefono));
                    if (count($query)>0 && trim($telefono)<>'') {
                        $tipo_averia='ADSL';
                        $numeroSql='3';
                        $peticion=$query[0]->peticion;
                    } else {
                        //4
                        $sql= " SELECT numero_osiptel as peticion
                                FROM schedulle_sistemas.aver_pen_bas_lima
                                WHERE telefono=? ";
                        $query = DB::select($sql, array($telefono));
                        if (count($query)>0 && trim($telefono)<>'') {
                            $numeroSql='4';
                            $peticion=$query[0]->peticion;
                        } else {
                            //5
                            $sql= " SELECT inscripcion as peticion
                                FROM schedulle_sistemas.aver_pen_bas_prov
                                WHERE telefono=? ";
                            $query = DB::select($sql, array($telefono));
                            if (count($query)>0  && trim($telefono)<>'') {
                                $numeroSql='5';
                                $peticion=$query[0]->peticion;
                            } else {
                                //6
                                $sql= " SELECT codigo_req as peticion
                                    FROM schedulle_sistemas.aver_pen_catv_pais
                                    WHERE codigodelcliente=? ";
                                $query = DB::select($sql, array($codcliatis));
                                if (count($query)>0 && trim($codcliatis)<>'') {
                                    $tipo_averia='CATV';
                                    $numeroSql='6';
                                    $peticion=$query[0]->peticion;
                                }
                            }
                        }
                    }
                }
            }
        } elseif ($codsercms!='') {
            //6
            $sql= " SELECT codigo_req as peticion
                    FROM schedulle_sistemas.aver_pen_catv_pais
                    WHERE codigodelservicio=? ";
            $query = DB::select($sql, array($codsercms));
            if (count($query)>0) {
                $tipo_averia='CATV';
                $numeroSql='6';
                $peticion=$query[0]->peticion;
            }
        }
        //verificar si existe una consulta
        if ($numeroSql!='') {
            $queryPen = Historico::findDatos($peticion, $numeroSql);
            //verificar si hay registro
            if (count($queryPen)>0 && $queryPen!='') {
                $query=$queryPen;
                $query = Historico::findGeolocalizacion($query, $tipo_averia);
                return $query;
            }
             return array();
        }
        return array();
    }
    /**
     * 
     */
    public static function getLiquidados(
        $telefono='',
        $codcliatis='',
        $codsercms='',
        $codclicms=''
    )
    {
        $numeroSql='';
        $tipo_averia='';
        //buscar en liquidados si no se encontraron registros
        /*****************/
        if ($telefono!='' || $codcliatis!='') {
            //7
            $sql= " SELECT peticion
                    FROM schedulle_sistemas.prov_liq_bas_pais
                    WHERE telefono=? OR cod_cliente=? AND cod_cliente<>''
                    AND cod_cliente<>'0' AND peticion<>0 ";
            $query = DB::select($sql, array($telefono,$codcliatis));
            if (count($query)>0 && $query!='') {
                $numeroSql='7';
                $peticion=$query[0]->peticion;
            } else {
                //8
                $sql= " SELECT codigo_req as peticion
                        FROM schedulle_sistemas.prov_liq_catv_pais
                        WHERE codigo_del_cliente=? AND codigo_req<>0 ";
                $query = DB::select($sql, array($codcliatis));
                if (count($query)>0 && $query!='') {
                    $tipo_averia='CATV';
                    $numeroSql='8';
                    $peticion=$query[0]->peticion;
                } else {
                    //9 AVERIAS
                    $sql= " SELECT numero_osiptel as peticion
                            FROM schedulle_sistemas.aver_liq_adsl_pais
                            WHERE telefono=? ";
                    $query = DB::select($sql, array($telefono));
                    if (count($query)>0 && $query!='' && trim($telefono)<>'') {
                        $tipo_averia='ADSL';
                        $numeroSql='9';
                        $peticion=$query[0]->peticion;
                    } else {
                        //10
                        $sql= " SELECT numero_osiptel as peticion
                                FROM schedulle_sistemas.aver_liq_bas_lima
                                WHERE telefono=? ";
                        $query = DB::select($sql, array($telefono));
                        if (count($query)>0 && $query!='' && trim($telefono)<>'') {
                            $numeroSql='10';
                            $peticion=$query[0]->peticion;
                        } else {
                            //11
                            $sql= " SELECT inscripcion as peticion
                                FROM schedulle_sistemas.aver_liq_bas_prov_pedidos
                                WHERE telefono=?  ";
                            $query = DB::select($sql, array($telefono));
                            if (count($query)>0 && $query!='' && trim($telefono)<>'') {
                                $numeroSql='11';
                                $peticion=$query[0]->peticion;
                            } else {
                                //12
                                $sql= " SELECT codigoreq as peticion
                                    FROM schedulle_sistemas.aver_liq_catv_pais
                                    WHERE codigodelcliente=?  ";
                                $query = DB::select($sql, array($codcliatis));
                                if (count($query)>0 && $query!='' && trim($codcliatis)<>'') {
                                    $tipo_averia='CATV';
                                    $numeroSql='12';
                                    $peticion=$query[0]->peticion;
                                }
                            }
                        }
                    }
                }
            }
        } elseif ($codsercms!='') {
            //12
            $tipo_averia='CATV';
            $sql= " SELECT codigoreq as peticion
                    FROM schedulle_sistemas.aver_liq_catv_pais
                    WHERE codigodelservicio=?  ";
            $query = DB::select($sql, array($codsercms));
            if (count($query)>0 && $query!='') {
                $numeroSql='12';
                $peticion=$query[0]->peticion;
            }
        }
        //verificar si existe una consulta
        if ($numeroSql!='') {
            $queryLiq = Historico::findDatos($peticion, $numeroSql);
            //verificar si hay registro
            if (count($queryLiq)>0 && $queryLiq!='') {
                $query=$queryLiq;
                //se debe enviar X Y de geolocalizacion
                $query = Historico::findGeolocalizacion($query,$tipo_averia);
                return $query;
            }
            return array();
            
        }
        return array();
    }
    /**
     * 
     */
    public static function getMaestro(
        $telefono='',
        $codcliatis='',
        $codsercms='',
        $codclicms=''
    )
    {
        //temporal, maestro cliente
        $qryTelefono = $qryCodcliatis = $qryCodsercms = $qryCodclicms = '';
        if($telefono!='')
            $where = " l.telefono='".$telefono."' AND ";
        if($codcliatis!='')
            $where = " l.codclie='".$codcliatis."' AND ";
        if($codsercms!='')
            $where = " l.codservcms='".$codsercms."' AND ";
        if($codclicms!='')
            $where = " l.codclicms='".$codclicms."' AND ";

        $sql="SELECT l.tiposerv, l.telefono, l.tipocalle, l.nomcalle,l.numcalle,
            l.codclie, l.codservcms, l.nombre, l.appater, l.apmater, l.zonal,
            l.mdf, l.armario, l.cabprim AS cable, l.cajater AS terminal,
            l.xtroba, l.ytroba, l.xtap, l.ytap, l.xterminal, l.yterminal,
            IFNULL(
                (SELECT direccion
                 FROM geo_tap gt
                 WHERE gt.coord_x=l.xtroba
                 AND gt.coord_y=l.ytroba), 'sin dirección') as dir_troba,
            IFNULL(
                (SELECT direccion
                FROM geo_tap gt
                WHERE gt.coord_x=l.xterminal
                AND gt.coord_y=l.yterminal), 'sin dirección') as dir_term,
            IFNULL(
                (SELECT direccion
                FROM geo_tap gt
                WHERE gt.coord_x=l.xtap
                AND gt.coord_y=l.ytap), 'sin dirección') as dir_tab,
            IFNULL(m.eecc,'') as eecc,
            IFNULL(m.lejano,'') as lejano,
            IFNULL(m.zona_critico,'') as microzona,
            (select id from empresas where nombre like m.eecc) as empresa_id
        FROM webpsi_coc.tb_lineas_servicio_total l
        LEFT JOIN webpsi_fftt.mdfs_eecc_regiones m
            ON l.mdf=m.mdf
        WHERE $where
            TRIM(REPLACE(l.telefono,' ','')) REGEXP '^[0-9]+$' AND
            TRIM(REPLACE(l.telefono,' ','')) >2
            AND LENGTH(REPLACE(l.telefono,'1',''))!=0
        GROUP BY l.telefono,l.codclie,l.codservcms,l.codclicms";
        $query = DB::select($sql);
        //verificar si hay registros
        if (count($query)>0 && $query!=='') {
            //se debe enviar X Y de geolocalizacion
            $query = Historico::findGeolocalizacion($query, '');
            return $query;
        }
        return array();
    }
    /**
     * tmb mostrar las coordenadas segun 
     * si existe cable o armario, mostrar xy de cable
     * si existe terminal
     */
    public static function findGeolocalizacion($query=0, $tipo_averia='')
    {
        if (count($query)>0 && $query!='') {
            $cable=$terminal='';
            $coordX=$coordY='';
            $mdf = $query[0]->mdf;
            if ($tipo_averia=='STB' || $tipo_averia=='ADSL' || $tipo_averia=='') {
                $cable = $query[0]->armario;
                $terminal = $query[0]->terminal;
                if (trim($cable)!='' && trim($mdf)!='' && trim($terminal)!='') {
                    $fftt=DB::table('geo_terminald')
                        ->select(
                            'id',
                            'terminald as nombre',
                            DB::raw("IFNULL(coord_x,'') as coord_x"),
                            DB::raw("IFNULL(coord_y,'') as coord_y"),
                            DB::raw("IFNULL(direccion,'') as direccion")
                        )
                        ->where('cable', $cable)
                        ->where('mdf', $mdf)
                        ->where('terminald', $terminal)
                        ->get();
                    if (count($fftt)>0 && $fftt!='') {
                        $coordX = $fftt[0]->coord_x;
                        $coordY = $fftt[0]->coord_y;
                    }
                } elseif (trim($cable)!='' && trim($mdf)!='') {
                    $fftt=DB::table('geo_terminald')
                        ->select(
                            'id',
                            'terminald as nombre',
                            DB::raw("IFNULL(coord_x,'') as coord_x"),
                            DB::raw("IFNULL(coord_y,'') as coord_y"),
                            DB::raw("IFNULL(direccion,'') as direccion")
                        )
                        ->where('cable', $cable)
                        ->where('mdf', $mdf)
                        ->get();
                    if (count($fftt)>0 && $fftt!='') {
                        $coordX = $fftt[0]->coord_x;
                        $coordY = $fftt[0]->coord_y;
                    }
                } elseif (trim($mdf)!='') {
                    $fftt=DB::table('geo_terminald')
                        ->select(
                            'cable as id',
                            'cable as nombre',
                            DB::raw("IFNULL(coord_x,'') as coord_x"),
                            DB::raw("IFNULL(coord_y,'') as coord_y"),
                            DB::raw("IFNULL(direccion,'') as direccion")
                        )
                        ->where('mdf', $mdf)
                        ->groupby('cable')
                        ->get();
                    if (count($fftt)>0 && $fftt!='') {
                        $coordX = $fftt[0]->coord_x;
                        $coordY = $fftt[0]->coord_y;
                    }
                }
            } elseif ($tipo_averia=='CATV') {
                $troba = $query[0]->troba;
                $amp = $query[0]->amplificador;
                $tap = $query[0]->tap;
                //buscar por troba amplificador y tap
                if (trim($troba)!='' && trim($amp) && trim($tap)!='') {
                    $fftt=DigTroba::getTap($amp,$troba, $mdf);
                    if (count($fftt)>0 && $fftt!='') {
                        $coordX = $fftt[0]->coord_x;
                        $coordY = $fftt[0]->coord_y;
                    }
                } elseif (trim($troba)!='' && trim($amp!='')) {
                    $fftt=DigTroba::getAmp($troba, $mdf);
                    if (count($fftt)>0 && $fftt!='') {
                        $coordX = $fftt[0]->coord_x;
                        $coordY = $fftt[0]->coord_y;
                    }
                } elseif (trim($troba)!='' ) {
                    $fftt=DigTroba::getTroba($mdf);
                    if (count($fftt)>0 && $fftt!='') {
                        $coordX = $fftt[0]->coord_x;
                        $coordY = $fftt[0]->coord_y;
                    }
                }
            }
            
            //añadiendo al query las coordenadas
            if ($coordX!='' && $coordY!='') {
                $query[0]->coordx=$coordX;
                $query[0]->coordy=$coordY;
            }
            //retornas los valores
            return $query;
        }
        return $query;
    }
    /**
     * ejecutar consultas de acuerdo al numero que se le asigno
     */
    public static function findDatos($peticion='', $num=0)
    {
        switch ($num) {
            case '1':
                //prov_pen_bas_pais  ****************************
                $sql = "  SELECT 'prov_pen_bas_pais' AS tabla,
                         s.peticion AS codclie,
                        '4' as actividad_id,
                         IFNULL(s.cod_negocio,'') as cod_negocio,
                         IFNULL(s.dir_instal,'') as dir_instal,
                         IFNULL(s.cod_cliente,'') as cod_cliente,
                         IFNULL(s.telef_contac,'') as telef_contac,
                         IFNULL(s.celular_contac,'') as celular_contac,
                         IFNULL(s.telefono,'') as telefono,
                        IF(s.cod_negocio='01',IFNULL(tba.cliente,''),
                            IF(s.cod_negocio='03',IFNULL(adsl.cliente,''),'')
                        ) AS cliente,
                        IF(s.cod_negocio='01',IFNULL(tba.segmento,''),
                            IF(s.cod_negocio='03',IFNULL(adsl.segmento,''),'')
                        ) AS segmento,
                        IF(s.cod_negocio='01',IFNULL(tba.zonal,''),
                            IF(s.cod_negocio='03',IFNULL(adsl.zonal,''),'')
                        ) AS zonal,
                        IF(s.cod_negocio='01',IFNULL(tba.mdf,''),
                            IF(s.cod_negocio='03',IFNULL(adsl.mdf,''),'')
                        ) AS mdf,
                        IF(s.cod_negocio='01',IFNULL(tba.c7,''),
                            IF(s.cod_negocio='03',IFNULL(adsl.c7,''),'')
                        ) AS armario,
                        IF(s.cod_negocio='01',IFNULL(tba.c8,''),
                            IF(s.cod_negocio='03',IFNULL(adsl.c8,''),'')
                        ) AS terminal,
                        IF(s.cod_negocio='01',IFNULL(m_tba.eecc,''),
                            IF(s.cod_negocio='03',IFNULL(m_adsl.eecc,''),'')
                        ) AS eecc,
                        IF(s.cod_negocio='01',IFNULL(m_tba.lejano,''),
                            IF(s.cod_negocio='03',IFNULL(m_adsl.lejano,''),'')
                        ) AS lejano,
                        IF(s.cod_negocio='01',IFNULL(m_tba.zona_critico,''),
                        IF(s.cod_negocio='03',IFNULL(m_adsl.zona_critico,''),'')
                        ) AS microzona,
                        IF(s.cod_negocio='01',
                            (SELECT id 
                            FROM empresas 
                            WHERE nombre 
                            LIKE m_tba.eecc),
                            IF(s.cod_negocio='03',
                                (SELECT id 
                                FROM empresas 
                                WHERE nombre 
                                LIKE m_adsl.eecc),
                            '') 
                        ) AS empresa_id
                    FROM schedulle_sistemas.prov_pen_bas_pais s 
                    LEFT JOIN schedulle_sistemas.tmp_gaudi_tba tba 
                    ON s.peticion =tba.pedidoatis
                    LEFT JOIN schedulle_sistemas.tmp_gaudi_adsl adsl 
                    ON s.peticion =adsl.pedidoatis
                    LEFT JOIN webpsi_fftt.mdfs_eecc_regiones m_tba
                    ON tba.mdf=m_tba.mdf
                    LEFT JOIN webpsi_fftt.mdfs_eecc_regiones m_adsl
                    ON adsl.mdf=m_adsl.mdf 
                    WHERE s.peticion=? ";
                    $query = DB::select($sql, array($peticion));

                break;
            case '2':
                //prov_pen_catv_pais ***********************
                $sql = "SELECT 'prov_pen_catv_pais' AS tabla,
                         s.codigo_req AS codclie,
                        '4' AS actividad_id,
                        '02' AS cod_negocio,
                        s.direccion_facturacion AS dir_instal,
                        s.codigo_del_cliente AS cod_cliente,
                        s.telefono_contacto AS telef_contac,
                        s.celular_contacto AS celular_contac,
                        s.codigo_del_servicio,
                        CONCAT(
                                s.apellido_paterno, ' ',
                                s.apellido_materno,' ',
                                s.nombres
                        ) AS cliente,
                        '' AS armario, 
                        '' AS terminal, 
                        IFNULL(s.oficina_administrativa,'') AS zonal,
                        IFNULL(s.nodo,'') AS mdf,
                        IFNULL(s.troba,'') AS troba,
                        IFNULL(s.lex,'') AS amplificador,
                        IFNULL(s.tap,'') AS tap,
                        IFNULL(m.eecc,'') AS eecc,
                        IFNULL(m.lejano,'') AS lejano,
                        IFNULL(m.zona_critico,'') AS microzona, 
                        IFNULL(catv.segmento,'') AS segmento, 
                        IFNULL(
                        (SELECT id
                        FROM empresas 
                        WHERE nombre 
                        LIKE m.eecc),'') AS empresa_id
                    FROM schedulle_sistemas.prov_pen_catv_pais s
                    LEFT JOIN schedulle_sistemas.tmp_gaudi_catv catv
                        ON s.codigo_req=catv.codreq
                    LEFT JOIN webpsi_fftt.mdfs_eecc_regiones m
                        ON s.nodo=m.mdf
                    WHERE s.codigo_req = ? ";
                $query = DB::select($sql, array($peticion));
                break;
            case '3':
                //aver_pen_adsl_pais******************************
                $sql = "SELECT 'aver_pen_adsl_pais' AS tabla,
                         s.numero_osiptel AS codclie,
                        '3' AS actividad_id,
                        '03' AS cod_negocio,
                        IFNULL(s.direccion_instalacion,'') AS dir_instal,
                        IFNULL(s.codigo_cliente,'') AS cod_cliente,
                        IFNULL(s.telefono_con,'') AS telef_contac,
                        /*IFNULL(s.celular_contac,'') as celular_contac,*/
                        IFNULL(s.telefono,'') AS telefono,
                        IFNULL(
                            CONCAT(
                                REPLACE(s.ape_paterno,' ',''),' ',
                                REPLACE(s.ape_materno,' ',''),' ',
                                REPLACE(s.nombre,' ','')
                            )
                            ,''
                        ) AS cliente,
                        IFNULL(s.segmento,'') AS segmento,
                        IFNULL(s.zonal,'') AS zonal,
                        IFNULL(s.mdf,'') AS mdf,
                        IFNULL(s.cable,'') AS armario,
                        IFNULL(adsl.c8,'') AS terminal,
                        IFNULL(m.eecc,'') AS eecc,
                        IFNULL(m.lejano,'') AS lejano,
                        IFNULL(m.zona_critico,'') AS microzona,
                        (SELECT id 
                                FROM empresas 
                                WHERE nombre 
                                LIKE m.eecc) AS empresa_id
                        FROM schedulle_sistemas.aver_pen_adsl_pais s 
                        LEFT JOIN schedulle_sistemas.tmp_gaudi_adsl adsl 
                        ON s.inscripcion =adsl.DATA11
                        LEFT JOIN webpsi_fftt.mdfs_eecc_regiones m
                        ON adsl.mdf=m.mdf
                        WHERE s.numero_osiptel=? ";
                $query = DB::select($sql, array($peticion));
                break;
            case '4':
                //aver_pen_bas_lima ***********************************
                $sql = "SELECT 'aver_pen_bas_lima' AS tabla,
                         s.numero_osiptel AS codclie,
                        '3' AS actividad_id,
                         IFNULL(s.codigo_negocio,'') AS cod_negocio,
                         IFNULL(s.direccion_instalacion,'') AS dir_instal,
                         IFNULL(s.telefono,'') AS telefono,
                         IFNULL(
                            CONCAT(
                                REPLACE(s.ape_paterno,' ',''),' ',
                                REPLACE(s.ape_materno,' ',''),' ',
                                REPLACE(s.nombre,' ','')
                            )
                            ,''
                        ) AS cliente,
                        IFNULL(s.segmento,'') AS segmento,
                        'LIM' AS zonal,
                        IFNULL(s.mdf,'') AS mdf,
                        IFNULL(s.cable,'')AS armario,
                        IFNULL(s.terminal,'') AS terminal,
                        '' AS eecc,
                        '' AS lejano,
                        '' AS microzona,
                        '' AS empresa_id
                        FROM schedulle_sistemas.aver_pen_bas_lima s 
                        WHERE s.numero_osiptel=? ";
                $query = DB::select($sql, array($peticion));
                break;
            case '5':
                //aver_pen_bas_prov *********************
                $sql = "SELECT 'aver_pen_bas_prov' AS tabla,
                         s.inscripcion AS codclie,
                        '3' AS actividad_id,
                         IFNULL(s.negocio,'') AS cod_negocio,
                         IFNULL(s.direccioninstalacion,'') AS dir_instal,
                         IFNULL(s.d_contacto,'') AS celular_contac,
                         IFNULL(s.telefono,'') AS telefono,
                        IFNULL(
                            CONCAT(
                                REPLACE(s.ape_paterno,' ',''),' ',
                                REPLACE(s.ape_materno,' ',''),' ',
                                REPLACE(s.nombre,' ','')
                            )
                            ,''
                        ) AS cliente,
                        '' AS armario,
                        '' AS terminal,
                        IFNULL(s.segmento,'') AS segmento,
                        '' AS zonal,
                        IFNULL(s.mdf,'') AS mdf
                        FROM schedulle_sistemas.aver_pen_bas_prov s 
                        WHERE s.inscripcion=? ";
                $query = DB::select($sql, array($peticion));
                break;
            case '6':
                //aver_pen_catv_pais
                $sql = "  SELECT 'aver_pen_catv_pais' AS tabla,
                     s.codigo_req AS codclie,
                    '3' AS actividad_id,
                    '02' AS cod_negocio,
                    CONCAT(
                        s.tipodevia,' ',
                        s.nombredelavia,' ',
                        s.numero
                    ) AS dir_instal,
                    s.codigodelcliente AS cod_cliente,
                    CONCAT(
                        s.apellidopaterno, ' ',
                        s.apellidomaterno,' ',
                        s.nombres
                    ) AS cliente,
                    '' AS armario,
                    '' AS terminal,
                    '' AS segmento,
                    IFNULL(s.oficina_administrativa,'') AS zonal,
                    IFNULL(s.nodo,'') AS mdf,
                    IFNULL(s.troba,'') AS troba,
                    IFNULL(s.lex,'') AS amplificador,
                    IFNULL(s.tap,'') AS tap
                    FROM schedulle_sistemas.aver_pen_catv_pais s
                    LEFT JOIN schedulle_sistemas.tmp_gaudi_catv catv
                    ON s.codigo_req=catv.codreq
                    LEFT JOIN webpsi_fftt.mdfs_eecc_regiones m
                                        ON catv.mdf=m.mdf
                    WHERE s.codigo_req =? ";
                $query = DB::select($sql, array($peticion));
                break;
            case '7':
                //prov_liq_bas_pais ***************************************
                $sql = "SELECT 'prov_liq_bas_pais' AS tabla,
                         s.peticion AS codclie,
                        '4' AS actividad_id,
                         IFNULL(s.cod_negocio,'') AS cod_negocio,
                         IFNULL(s.direccion_instalacion,'') AS dir_instal,
                         IFNULL(s.cod_cliente,'') AS cod_cliente,
                         /*IFNULL(s.telef_contac,'') as telef_contac,*/
                         /*IFNULL(s.celular_contac,'') as celular_contac,*/
                         IFNULL(s.telefono,'') AS telefono,
                        IF(s.cod_negocio='01',IFNULL(tba.cliente,''),
                            IF(s.cod_negocio='03',IFNULL(adsl.cliente,''),'')
                        ) AS cliente,
                        IF(s.cod_negocio='01',IFNULL(tba.segmento,''),
                            IF(s.cod_negocio='03',IFNULL(adsl.segmento,''),'')
                        ) AS segmento,
                        IF(s.cod_negocio='01',IFNULL(tba.zonal,''),
                            IF(s.cod_negocio='03',IFNULL(adsl.zonal,''),'')
                        ) AS zonal,
                        IF(s.cod_negocio='01',IFNULL(tba.mdf,''),
                            IF(s.cod_negocio='03',IFNULL(adsl.mdf,''),'')
                        ) AS mdf,
                        IF(s.cod_negocio='01',IFNULL(tba.c7,''),
                            IF(s.cod_negocio='03',IFNULL(adsl.c7,''),'')
                        ) AS armario,
                        IF(s.cod_negocio='01',IFNULL(tba.c8,''),
                            IF(s.cod_negocio='03',IFNULL(adsl.c8,''),'')
                        ) AS terminal,
                        IF(s.cod_negocio='01',IFNULL(m_tba.eecc,''),
                            IF(s.cod_negocio='03',IFNULL(m_adsl.eecc,''),'')
                        ) AS eecc,
                        IF(s.cod_negocio='01',IFNULL(m_tba.lejano,''),
                            IF(s.cod_negocio='03',IFNULL(m_adsl.lejano,''),'')
                        ) AS lejano,
                        IF(s.cod_negocio='01',IFNULL(m_tba.zona_critico,''),
                        IF(s.cod_negocio='03',IFNULL(m_adsl.zona_critico,''),'')
                        ) AS microzona,
                        IF(s.cod_negocio='01',
                            (SELECT id 
                            FROM empresas 
                            WHERE nombre 
                            LIKE m_tba.eecc),
                            IF(s.cod_negocio='03',
                                (SELECT id 
                                FROM empresas 
                                WHERE nombre 
                                LIKE m_adsl.eecc),
                            '') 
                        ) AS empresa_id
                    FROM schedulle_sistemas.prov_liq_bas_pais s 
                    LEFT JOIN schedulle_sistemas.tmp_gaudi_tba tba 
                    ON s.peticion =tba.pedidoatis
                    LEFT JOIN schedulle_sistemas.tmp_gaudi_adsl adsl 
                    ON s.peticion =adsl.pedidoatis
                    LEFT JOIN webpsi_fftt.mdfs_eecc_regiones m_tba
                    ON tba.mdf=m_tba.mdf
                    LEFT JOIN webpsi_fftt.mdfs_eecc_regiones m_adsl
                    ON adsl.mdf=m_adsl.mdf 
                    WHERE s.peticion=? ";
                $query = DB::select($sql, array($peticion));
                break;
            case '8':
                //prov_liq_catv_pais **********************************
                $sql = "SELECT 'prov_liq_catv_pais' AS tabla,
                         s.codigo_req AS codclie,
                         '4' AS actividad_id,
                        '02' AS cod_negocio,
                        IFNULL(
                            CONCAT(s.tipo_de_via,' ',
                                   s.nombre_de_la_via,' ',
                                   s.numero
                            )
                            ,''
                        ) AS dir_instal,
                        IFNULL(s.codigo_del_cliente,'') AS cod_cliente,
                        IFNULL(s.telefono_de_contacto,'') AS telef_contac,
                        IFNULL(s.celular_de_contacto,'') AS celular_contac,
                        IFNULL(s.codigo_del_servicio,'') AS codigo_del_servicio,
                        CONCAT(
                                s.apellido_paterno, ' ',
                                s.apellido_materno,' ',
                                s.nombres
                        ) AS cliente,
                        '' AS armario,
                        '' AS terminal,
                        IFNULL(s.oficina_administrativa,'') AS zonal,
                        IFNULL(s.nodo,'') AS mdf,
                        IFNULL(s.troba,'') AS troba,
                        IFNULL(s.lex,'') AS amplificador,
                        IFNULL(s.tap,'') AS tap,
                        IFNULL(m.eecc,'') AS eecc,
                        IFNULL(m.lejano,'') AS lejano,
                        IFNULL(m.zona_critico,'') AS microzona, 
                        IFNULL(catv.segmento,'') AS segmento, 
                        IFNULL(
                        (SELECT id
                        FROM empresas 
                        WHERE nombre 
                        LIKE m.eecc),'') AS empresa_id 
                    FROM schedulle_sistemas.prov_liq_catv_pais s
                    LEFT JOIN schedulle_sistemas.tmp_gaudi_catv catv
                        ON s.codigo_req=catv.codreq
                    LEFT JOIN webpsi_fftt.mdfs_eecc_regiones m
                        ON s.nodo=m.mdf
                    WHERE s.codigo_req =? ";
                $query = DB::select($sql, array($peticion));
                break;
            case '9':
                //aver_liq_adsl_pais **********************
                $sql = "SELECT 'aver_liq_adsl_pais' AS tabla,
                         s.numero_osiptel AS codclie,
                        '3' AS actividad_id,
                         '03' AS cod_negocio,
                         IFNULL(s.direccion_instalacion,'') AS dir_instal,
                         IFNULL(s.codigo_cliente,'') AS cod_cliente,
                         IFNULL(s.telefono_con,'') AS telef_contac,
                         IFNULL(s.telefono,'') AS telefono,
                        IFNULL(
                            CONCAT(
                                REPLACE(s.ape_paterno,' ',''),' ',
                                REPLACE(s.ape_materno,' ',''),' ',
                                REPLACE(s.nombre,' ','')
                            )
                            ,''
                        ) AS cliente,
                        IFNULL(s.segmento,'') AS segmento,
                        IFNULL(s.zonal,'') AS zonal,
                        IFNULL(s.mdf,'') AS mdf,
                        IFNULL(s.cable,'') AS armario,
                        IFNULL(adsl.c8,'') AS terminal,
                        IFNULL(m.eecc,'') AS eecc,
                        IFNULL(m.lejano,'') AS lejano,
                        IFNULL(m.zona_critico,'') AS microzona,
                        (SELECT id 
                                FROM empresas 
                                WHERE nombre 
                                LIKE m.eecc) AS empresa_id
                        FROM schedulle_sistemas.aver_liq_adsl_pais s 
                        LEFT JOIN schedulle_sistemas.tmp_gaudi_adsl adsl 
                        ON s.inscripcion =adsl.DATA11
                        LEFT JOIN webpsi_fftt.mdfs_eecc_regiones m
                        ON adsl.mdf=m.mdf 
                        WHERE s.numero_osiptel=? ";
                $query = DB::select($sql, array($peticion));
                break;
            case '10':
                //aver_liq_bas_lima ************
                $sql = "SELECT 'aver_liq_bas_lima' AS tabla,
                         s.numero_osiptel AS codclie,
                        '3' AS actividad_id,
                        IFNULL(s.codigo_negocio,'') AS cod_negocio,
                        IFNULL(s.direccion_instalacion,'') AS dir_instal,
                        IFNULL(s.telefono,'') AS telefono,
                        IFNULL(
                            CONCAT(
                                    REPLACE(s.ape_paterno,' ',''),' ',
                                    REPLACE(s.ape_materno,' ',''),' ',
                                    REPLACE(s.nombre,' ','')
                            )
                            ,''
                        ) AS cliente,
                        IFNULL(s.segmento,'') AS segmento,
                        'LIM' AS zonal,
                        IFNULL(s.mdf,'') AS mdf,
                        IFNULL(s.cable,'')AS armario,
                        IFNULL(s.terminal,'') AS terminal,
                        '' AS eecc,
                        '' AS lejano,
                        '' AS microzona,
                        '' AS empresa_id
                        FROM schedulle_sistemas.aver_liq_bas_lima s 
                    WHERE s.numero_osiptel=? ";
                $query = DB::select($sql, array($peticion));
                break;
            case '11':
                //aver_liq_bas_prov_pedidos  ******************
                $sql = " SELECT 'aver_liq_bas_prov_pedidos' AS tabla,
                         s.inscripcion AS codclie,
                         '3' AS actividad_id,
                         IFNULL(s.negocio,'') AS cod_negocio,
                         IFNULL(s.direccion_instalacion,'') AS dir_instal,
                         IFNULL(s.telefono,'') AS telefono,
                        IFNULL(
                            CONCAT(
                                REPLACE(s.ape_paterno,' ',''),' ',
                                REPLACE(s.ape_materno,' ',''),' ',
                                REPLACE(s.nombre,' ','')
                            )
                            ,''
                        ) AS cliente,
                        '' AS armario,
                        '' AS terminal,
                        IFNULL(s.segmento,'') AS segmento,
                        '' AS zonal,
                        IFNULL(s.mdf,'') AS mdf
                        FROM schedulle_sistemas.aver_liq_bas_prov_pedidos s 
                        WHERE s.inscripcion=? ";
                $query = DB::select($sql, array($peticion));
                break;
            case '12':
                //aver_liq_catv_pais
                $sql = "SELECT 'aver_liq_catv_pais' AS tabla,
                         s.codigoreq AS codclie,
                         '3' AS actividad_id,
                        '02' AS cod_negocio,
                        CONCAT(
                            s.tipodevia,' ',
                            s.nombredelavia,' ',
                            s.numero
                        ) AS dir_instal,
                        s.codigodelcliente AS cod_cliente,
                        CONCAT(
                            s.apellidopaterno, ' ',
                            s.apellidomaterno,' ',
                            s.nombres
                        ) AS cliente,
                        '' AS armario,
                        '' AS terminal,
                        '' AS segmento,
                        IFNULL(s.oficinaadministrativa,'') AS zonal,
                        IFNULL(s.nodo,'') AS mdf,
                        IFNULL(s.troba,'') AS troba,
                        IFNULL(s.lex,'') AS amplificador,
                        IFNULL(s.tap,'') AS tap
                        FROM schedulle_sistemas.aver_liq_catv_pais s
                        LEFT JOIN schedulle_sistemas.tmp_gaudi_catv catv
                        ON s.codigoreq=catv.codreq
                        LEFT JOIN webpsi_fftt.mdfs_eecc_regiones m
                                            ON catv.mdf=m.mdf
                        WHERE s.codigoreq =? ";
                $query = DB::select($sql, array($peticion));
                break;
            default:
                $query='';
                break;
        }
        return $query;
    }
    public static function getCliente(
        $telefono='',
        $codcliatis='',
        $codsercms='',
        $codclicms=''
    )
    {
        $qryTelefono =$qryCodcliatis =$qryCodsercms =$qryCodclicms='';
        if($telefono!='')
            $qryTelefono = " l.telefono='".$telefono."' AND ";
        if($codcliatis!='')
            $qryCodcliatis = " l.codclie='".$codcliatis."' AND ";
        if($codsercms!='')
            $qryCodsercms = " l.codservcms='".$codsercms."' AND ";
        if($codclicms!='')
            $qryCodclicms = " l.codclicms='".$codclicms."' AND ";

        $query="SELECT l.*, '' as posibleCritico, '' as encontrado,
        CONCAT(
            IFNULL(q.telefono,''),
            IFNULL(q.inscripcion,''),
            IFNULL(pr.telefono,''),
            IFNULL(pr.codigo_del_cliente,''),
            IFNULL(gd.id,'')
        ) AS validacion, IFNULL(gd.codactu,'') as codactu
        FROM webpsi_coc.tb_lineas_servicio_total l
        LEFT JOIN webpsi_coc.averias_criticos_final q 
            ON (l.telefono=q.telefono OR l.codclicms=q.inscripcion)
        LEFT JOIN webpsi_coc.tmp_provision pr 
            ON (pr.telefono = l.telefono OR pr.codigo_del_cliente=l.codclicms)
        LEFT JOIN gestiones_detalles gd 
            ON (l.telefono=gd.telefono OR l.codclicms=gd.inscripcion)
        WHERE $qryTelefono $qryCodcliatis $qryCodsercms $qryCodclicms
        l.telefono!=' '
        GROUP BY l.telefono,l.codclie,l.codservcms,l.codclicms";
        return DB::select($query);
    }
    public static function getAveriasTbaPendientes($tipoBusqueda, $valorBuscar) 
    {
        $filtroBusqueda = "";
        if ($tipoBusqueda=='fono') { 
            if (substr($valorBuscar, 0, 1) == "1") 
                $valorBuscar = ltrim($valorBuscar, "1");
            $filtroBusqueda = " AND a.telefono = '$valorBuscar'";
        }
        else
            $filtroBusqueda = "";
            
        
        $query = "SELECT a.telefono, a.averia, a.cliente, a.direccion,
                    cod_distrito, des_distrito, cod_ciudad,
                    DATE_FORMAT(fecreg, '%d-%m-%Y %H:%i:%s') AS fecreg_format,
                    a.mdf, carea, fecreg,
                    CASE 
                        WHEN HOUR(TIMEDIFF(NOW(), fecreg)) < 31 
                                AND DATEDIFF(NOW(), fecreg) > 0 
                                THEN CONCAT('V:', HOUR(fecreg) ) 
                        ELSE '' 
                    END AS marca,
                    (SELECT FECHA_AGENDA_FIN 
                     FROM webpsi.rpt_agendadas_averias 
                     WHERE AVERIA=a.averia) AS fec_agenda,
                    a.zonal, carmario AS armario, ccable AS cable, 
                    cbloque AS bloque, terminal,
                    IF (carmario='', 
                        CONCAT(a.zonal, a.mdf, ccable,LPAD(terminal, 3, '0')),
                        CONCAT(a.zonal, a.mdf, carmario,LPAD(terminal, 3, '0'))
                    ) AS llavexy,
                    (SELECT `x` 
                     FROM webunificada_fftt.fftt_terminales t
                     WHERE llavexy =  t.mtgespktrm  ) AS coordX,
                    (SELECT `y` 
                     FROM webunificada_fftt.fftt_terminales t
                     WHERE llavexy =  t.mtgespktrm  ) AS coordY,
                    csegmento AS segmentoY, gd.gestion_id,
                    gm.fecha_agenda,g.id_atc , g.n_evento

                    FROM schedulle_sistemas.pen_pais_tba a
                    LEFT JOIN gestiones_detalles gd
                        ON a.averia = gd.codactu
                    LEFT JOIN gestiones g ON gd.gestion_id = g.id
                    LEFT JOIN gestiones_movimientos gm ON g.id=gm.gestion_id
                    WHERE  negocio='STB' /*AND g.actividad_id=1*/ AND
                    ( gm.estado_id not in (6) or gm.estado_id is null )
                    ".$filtroBusqueda."
                    ORDER BY fecreg ASC"; 
        return DB::select($query);

    }
    public static function getAveriasAdslPendientes($tipoBusqueda, $valorBuscar)
    {
        $filtroBusqueda = "";
        if ($tipoBusqueda=="fono") { 
            if (substr($valorBuscar, 0, 1) == "1") {   // LIMA
                $valorBuscar = ltrim($valorBuscar, "1");
                $filtroBusqueda = " AND a.telefono = '$valorBuscar'";
            } else if (substr($valorBuscar, 0, 1) != "1") {  // PROVINCIA
                $valorBuscar = substr($valorBuscar, 2);
                $filtroBusqueda = " AND a.telefono = '$valorBuscar'";
            }
        } else if ($tipoBusqueda=='averia') { 
            $filtroBusqueda = " AND a.numero_osiptel = '$valorBuscar'";
        } else
            $filtroBusqueda = "";

        $query = "SELECT a.ciudad, a.numero_osiptel AS averia,a.inscripcion,
                CONCAT(a.zona_telefonica, a.telefono) AS telefono2, 
                a.telefono, a.segmento, a.subsegmento, a.cable,
                a.par_alimentador, a.sector, a.nro_caja, a.par_distribuidor,
                a.borne, a.fecha_instlacion AS fec_inst, a.zonal, a.coddep,
                a.centro_ejecucion, a.mdf, a.adsl, a.par_adsl, a.pos_adsl, 
                a.fecha_registro, a.fecha_des, a.area, a.direccion_instalacion,
                a.codigo_distrito, a.estado_ult AS AREA, 
                CONCAT(a.ape_paterno,' ', a.ape_materno, ' ', a.nombre)
                    AS nombre_cliente,
                a.indicador_vip, a.direccion_instalacion, gd.gestion_id,
                gm.fecha_agenda, g.id_atc, g.n_evento
                FROM schedulle_sistemas.aver_pen_adsl_pais a
                LEFT JOIN gestiones_detalles gd
                    ON a.numero_osiptel = gd.codactu
                LEFT JOIN gestiones g ON gd.gestion_id = g.id
                LEFT JOIN gestiones_movimientos gm ON g.id=gm.gestion_id
                WHERE 1=1/*g.actividad_id=1*/ ".$filtroBusqueda." 
                ORDER BY fecha_registro DESC "; 
        return DB::select($query);
    }
    public static function getAveriasCatvPendientes($tipoBusqueda, $valorBuscar)
    {
        $filtroBusqueda = "";
        if ($tipoBusqueda=='cliente') { 
            $filtroBusqueda = " AND a.codigodelcliente = '$valorBuscar'";
        } else
            $filtroBusqueda = "";

        $query = "SELECT a.codigo_req AS averia, 'CATV' AS negocio,
                a.codigodelcliente AS cod_cliente, a.ot AS ot, 
                CONCAT(a.apellidopaterno,' ', a.apellidomaterno,' ', a.nombres)
                AS cliente, a.direccion_facturacion AS direccion, 
                a.indicador_vip AS csegmento, '' AS clase, a.nodo,
                a.troba,a.tap, a.borne, a.codigotiporeq AS tipo_req,
                 a.codigomotivoreq, a.oficina_administrativa, a.contrata,
                DATE_FORMAT(a.fecharegistro, '%d-%m-%Y %H:%i:%s') 
                AS fecha_registro, '' AS estado, 
                (SELECT FECHA_AGENDA_FIN 
                 FROM webpsi.rpt_agendadas_averias
                 WHERE AVERIA=a.codigo_req) AS fec_agenda,
                gd.gestion_id, gm.fecha_agenda, g.id_atc, g.n_evento
                FROM schedulle_sistemas.aver_pen_catv_pais a
                LEFT JOIN gestiones_detalles gd
                    ON a.codigo_req = gd.codactu
                LEFT JOIN gestiones g ON gd.gestion_id = g.id
                LEFT JOIN gestiones_movimientos gm ON g.id=gm.gestion_id
                WHERE 1=1/*g.actividad_id=1*/ ".$filtroBusqueda." 
                ORDER BY fecharegistro ASC"; 
        return DB::select($query);
    }
    public static function getAveriasTbaLiquidadasLima(
        $tipoBusqueda,
        $valorBuscar
        )
    {
        $filtroBusqueda = "";
        if ($tipoBusqueda=='fono') { 
            if (substr($valorBuscar, 0, 1) == "1") 
                $valorBuscar = ltrim($valorBuscar, "1");
            $filtroBusqueda = " AND telefono = '$valorBuscar'";
        } else if ($tipoBusqueda=='averia') { 
            $filtroBusqueda = " AND numero_osiptel = '$valorBuscar'";
        } else
            $filtroBusqueda = "";
        
        $query = "SELECT numero_osiptel AS averia, inscripcion,
                telefono, mdf, area_sig , cabecera, armario,
                cable, par_cable, bloque, numero_par_bloque,
                terminal, borne, tecnico, fecha_reporte,
                fecha_registro, observacion_102, otra_observacion,
                numero_comprobacion, tecnico_comprobacion,
                fecha_de_comprobacion, liquidacion_, detalle,
                tecnico_liquidacion, fecha_de_liquidacion,
                segmento, subsegmento, direccion_instalacion,
                ape_paterno, ape_materno, nombre, 
                CONCAT(ape_paterno, ' ', ape_materno, ' ', nombre) AS cliente
                FROM schedulle_sistemas.aver_liq_bas_lima_hist
                WHERE DATEDIFF( NOW(), fecha_registro ) < 120
                ".$filtroBusqueda."
                ORDER BY fecha_de_liquidacion DESC"; 
        return DB::select($query);
        
    }
    public static function getAveriasTbaLiquidadasProvincia(
        $tipoBusqueda,
        $valorBuscar
        )
    {
        $filtroBusqueda = "";
        if ($tipoBusqueda=='fono') { 
            if (substr($valorBuscar, 0, 1) == "1") 
                $valorBuscar = ltrim($valorBuscar, "1");
            $filtroBusqueda = " AND telefono = '$valorBuscar'";
        }
        else
            $filtroBusqueda = "";

        $query = "SELECT ciudad, boleta, inscripcion, telefono,
                codigo_cip, fecha_hora_boleta, fecha_hora_franqueo,
                comentario_de_boletin, fecha_de_sistema,
                correlativo AS averia, mdf, segmento, subsegmento,
                direccion_instalacion, codigo_distrito, ape_paterno,
                ape_materno, nombre,
                CONCAT(ape_paterno, ' ', ape_materno, ' ', nombre) AS cliente,
                contrata, codigo_de_liquidacion, codigo_detalle_liq,
                desc_detalle_liq
                FROM schedulle_sistemas.aver_liq_bas_prov_pedidos
                WHERE DATEDIFF( NOW(), fecha_hora_franqueo ) < 120
                 ".$filtroBusqueda." 
                ORDER BY fecha_hora_franqueo DESC ";

        return DB::select($query);
    }
    public static function getAveriasAdslLiquidadas($tipoBusqueda, $valorBuscar)
    {
        $filtroBusqueda = "";
        if ($tipoBusqueda=='fono') { 
            if (substr($valorBuscar, 0, 1) == "1") 
                $valorBuscar = ltrim($valorBuscar, "1");
            $filtroBusqueda = " AND telefono = '$valorBuscar'";
        } else if ($tipoBusqueda=='averia') { 
            $filtroBusqueda = " AND numero_osiptel = '$valorBuscar'";
        } else
            $filtroBusqueda = "";

        $query = "SELECT ciudad, numero_osiptel as averia, inscripcion,
                zona_telefonica, telefono, segmento, subsegmento,
                nombre_con as nombre_contacto, fecha_instalacion,
                estado_liq, tecnico_reg, tecnico_pro, tecnico_rep_4,
                zonal, cod_dep_, centro_ejecucion, mdf, adsl, numero_par_adsl,
                dslam, codigo_liquidacion, franqueo, observacion_liquidacion,
                cable, nro_dsa, par_alimentador, sector, nro_caja,
                par_distribuidor, borne, fecha_registro,
                fecha_liquidacion_ as fecha_liquidacion,
                fecha_des as fecha_despacho, fecha_pro_tec, fecha_pro_con,
                ciudad, direccion_instalacion, codigo_distrito, ape_paterno,
                ape_materno, nombre, 
                concat(ape_paterno, ' ', ape_materno, ' ', nombre) as cliente,
                contrata_ as contrata
                FROM schedulle_sistemas.aver_liq_adsl_pais_hist
                WHERE DATEDIFF( NOW(), fecha_registro ) < 120
                ".$filtroBusqueda."
                ORDER BY fecha_liquidacion_ DESC ";
        return DB::select($query);
    }
    public static function getAveriasCatvLiquidadas($tipoBusqueda, $valorBuscar)
    {
        $filtroBusqueda = "";
        if ($tipoBusqueda=='cliente') { 
            $filtroBusqueda = " AND codigodelcliente = '$valorBuscar'";
        } else if ($tipoBusqueda=='codServicio') { 
            $filtroBusqueda = " AND codigodelservicio = '$valorBuscar'";
        } else if ($tipoBusqueda=='averia') { 
            $filtroBusqueda = " AND codigoreq = '$valorBuscar'";
        } else
            $filtroBusqueda = " ";
        $query = "SELECT codigoreq AS averia, codigotiporeq,
                codigomotivoreq, codigodelcliente AS cod_cliente,
                apellidopaterno, apellidomaterno, nombres,
                CONCAT(apellidopaterno,' ', apellidomaterno,' ', nombres)
                    AS cliente,
                categoria_cliente, codigodelservicio AS cod_servicio,
                clasedeservicio, oficinaadministrativa,
                departamento, provincia, distrito,
                CONCAT(tipodevia, ' ', nombredelavia, ' ', numero, ' ',
                piso, ' ', interior, ' ', manzana, ' ', lote) AS direccion,
                nodo, troba, lex, tap, borne, estado, fecharegistro,
                ot, fechaasignacion, estadoot, contrata, tecnico,
                fecha_liquidacion, codigodeliquidacion, detalle_liquidacion
                FROM schedulle_sistemas.aver_liq_catv_pais_hist
                WHERE DATEDIFF( NOW(), fecharegistro ) < 120
                 ".$filtroBusqueda."
                ORDER BY fecha_liquidacion DESC ";

        return DB::select($query);
    }
    public static function getRegistroAtis($tipoBusqueda, $valorBuscar) 
    { 

        $filtroBusqueda = "";
        if ($tipoBusqueda=='fono') { 
            $filtroBusqueda = " AND re.telefono = '$valorBuscar'";
        } else if ($tipoBusqueda=='codserv') { 
            $filtroBusqueda = " AND re.codigodelservicio = '$valorBuscar'";
        } else if ($tipoBusqueda=='dni') { 
            $filtroBusqueda = " AND re.dni_ruc = '$valorBuscar'";
        } else
            $filtroBusqueda = " ";

        $query = "SELECT re.* , g.id_atc , tmp.codigo_req , g.n_evento
                FROM `webpsi_coc`.`tb_registradastotal_`  re
                LEFT JOIN webpsi_coc.tmp_provision tmp
                ON tmp.codigo_del_cliente = re.inscripcion
                LEFT JOIN gestiones_detalles gd
                ON tmp.codigo_req = gd.codactu
                LEFT JOIN gestiones g ON gd.gestion_id = g.id
                LEFT JOIN gestiones_movimientos gm ON g.id=gm.gestion_id
                WHERE 1=1 /*g.actividad_id=2*/ $filtroBusqueda  AND
                ( gm.estado_id <> '6' or gm.estado_id is null )
                ORDER BY fecreg DESC ";
                
        return DB::select($query);
    }
    public static function getLlamadasCliente($tipoBusqueda, $valorBuscar) 
    { 

        $filtroBusqueda = "";
        if ($tipoBusqueda=='fono') { 
            $filtroBusqueda = " AND telefono = '$valorBuscar'";
        } else if ($tipoBusqueda=='codserv') { 
            $filtroBusqueda = " AND codigodelservicio = '$valorBuscar'";
        } else if ($tipoBusqueda=='dni') { 
            $filtroBusqueda = " AND dni_ruc = '$valorBuscar'";
        } else
            $filtroBusqueda = " ";

        $query = "SELECT servicio, fecha_llamada as fecha_llamada2, 
                DATE_FORMAT(fecha_llamada, '%d-%m-%Y %H:%i:%s')
                    AS fecha_llamada1,
                telefono, codcli, codserv, dni_ruc, ges_producto,
                ges_servicio, ges_accion, observacion, mes, anho
                FROM webpsi_coc.llamadas
                WHERE 1=1 ".$filtroBusqueda." 
                ORDER BY fecha_llamada DESC ";

        return DB::select($query);
    }
    public static function getListadoCriticosCobre($tipoBusqueda, $valorBuscar) 
    { 

        $filtroBusqueda = "";
        if ($tipoBusqueda=='fono') { 
            $filtroBusqueda = " AND telefono = '$valorBuscar'";
        } else if ($tipoBusqueda=='codserv') { 
            $filtroBusqueda = " AND codigodelservicio = '$valorBuscar'";
        } else if ($tipoBusqueda=='dni') { 
            $filtroBusqueda = " AND dni_ruc = '$valorBuscar'";
        } else
            $filtroBusqueda = " ";

        $query = "SELECT tipo_averia, fecha_registro, averia, telefono,
                mdf, tipo_servicio, tipo_actuacion, fecha_subida,
                DATE_FORMAT(fecha_subida, '%d-%m-%Y %H:%i:%s') as fecha_subida2,
                quiebre,
                CASE (  SELECT COUNT(*) 
                        FROM webpsi_coc.averias_criticos_final b 
                        WHERE b.telefono=a.telefono )
                    WHEN 0 THEN 'NO' 
                    ELSE 'SI' 
                END AS esta_pendiente
                FROM webpsi_coc.averias_criticos_final_historico a
                WHERE 1=1 ".$filtroBusqueda."
                ORDER BY fecha_subida DESC";

        return DB::select($query);
    }
    public static function esPosibleCritico($tipoBusqueda, $valorBuscar)
    { 

        $filtroBusqueda = "";
        if ($tipoBusqueda=='fono') { 
            $filtroBusqueda = " AND telefono = '$valorBuscar'";
        } else if ($tipoBusqueda=='codserv') { 
            $filtroBusqueda = " AND codigodelservicio = '$valorBuscar'";
        } else if ($tipoBusqueda=='dni') { 
            $filtroBusqueda = " AND dni_ruc = '$valorBuscar'";
        } else
            $filtroBusqueda = " ";

        $query = "SELECT COUNT(*) as contador 
                FROM webpsi_coc.clientes_criticos_posibles
                WHERE 1=1 ".$filtroBusqueda." ; ";
        return DB::select($query);
    }
}
