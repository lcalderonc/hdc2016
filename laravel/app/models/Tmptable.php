<?php
class Tmptable extends Eloquent 
{
    protected $_operador;
    
    /**
     * Retorna todos los datos de una averia temporal
     * 
     * @param type $averia Código de avería
     * @return type
     */
    public function getAveria($averia) 
    {
        if (trim($averia) == '') {
            $this->_operador = '<>';
        } else {
            $this->_operador = '=';
        }        
        
        $tmpdata = DB::table(Config::get("wpsi.db.tmp_averia"))
                ->select(
                    'tipo_averia as tipo_averia',
                    'horas_averia as horas_averia',
                    'fecha_registro as fecha_registro',
                    'ciudad as ciudad',
                    'averia as codactu',
                    'inscripcion as inscripcion',
                    'fono1 as fono1',
                    'telefono as telefono',
                    'mdf as mdf',
                    'observacion_102 as cr_observacion',
                    'segmento as segmento',
                    'area_ as area',
                    'direccion_instalacion as direccion_instalacion',
                    'codigo_distrito as codigo_distrito',
                    'nombre_cliente as nombre_cliente',
                    'orden_trabajo as orden_trabajo',
                    'veloc_adsl as veloc_adsl',
                    'clase_servicio_catv as clase_servicio_catv',
                    'codmotivo_req_catv as codmotivo_req_catv',
                    'total_averias_cable as total_averias_cable',
                    'total_averias_cobre as total_averias_cobre',
                    'total_averias as total_averias',
                    'fftt as fftt',
                    'llave as llave',
                    'dir_terminal as dir_terminal',
                    'fonos_contacto as fonos_contacto',
                    'contrata as contrata',
                    'zonal as zonal',
                    'wu_nagendas as wu_nagendas',
                    'wu_nmovimientos as wu_nmovimientos',
                    'wu_fecha_ult_agenda as wu_fecha_ult_agenda',
                    'total_llamadas_tecnicas as total_llamadas_tecnicas',
                    'total_llamadas_seguimiento as total_llamadas_seguimiento',
                    'llamadastec15dias as llamadastec15dias',
                    'llamadastec30dias as llamadastec30dias',
                    'quiebre as quiebre',
                    'lejano as lejano',
                    'distrito as distrito',
                    'eecc_zona as eecc_zona',
                    'zona_movistar_uno as zona_movistar_uno',
                    'paquete as paquete',
                    'data_multiproducto as data_multiproducto',
                    'averia_m1 as averia_m1',
                    'fecha_data_fuente as fecha_data_fuente',
                    'telefono_codclientecms as telefono_codclientecms',
                    'rango_dias as rango_dias',
                    'sms1 as sms1',
                    'sms2 as sms2',
                    'area2 as area2',
                    'velocidad_caja_recomendada as veloc_caja_recomen',
                    'tipo_servicio as tipo_servicio',
                    'tipo_actuacion as tipo_actuacion',
                    'eecc_final as eecc_final',
                    'microzona as microzona',
                    'xcoord as x',
                    'ycoord as y'
                        )
                ->where('averia', $this->_operador, $averia)
                ->get();
        return $tmpdata;
    }

    /**
     * Retorna todos los datos de una provisión temporal
     * 
     * @param type $requerimiento Codigo de requerimiento
     * @return type
     */
    public function getProvision($requerimiento) 
    {
        if (trim($requerimiento) === '') {
            $this->_operador = '<>';
        } else {
            $this->_operador = '=';
        }
        
        $tmpdata = DB::table(Config::get("wpsi.db.tmp_provision"))
                ->select(
                    'origen as tipo_averia',
                    'horas_pedido as horas_averia',
                    'fecha_Reg as fecha_registro',
                    'ciudad as ciudad',
                    'codigo_req as codactu',
                    'codigo_del_cliente as inscripcion',
                    'fono1 as fono1',
                    'telefono as telefono',
                    'mdf as mdf',
                    'obs_dev as cr_observacion',
                    'codigosegmento as segmento',
                    'estacion as area',
                    'direccion as direccion_instalacion',
                    'distrito as codigo_distrito',
                    'nomcliente as nombre_cliente',
                    'orden as orden_trabajo',
                    'veloc_adsl as veloc_adsl',
                    'servicio as clase_servicio_catv',
                    'tipo_motivo as codmotivo_req_catv',
                    'tot_aver_cab as total_averias_cable',
                    'tot_aver_cob as total_averias_cobre',
                    'tot_averias as total_averias',
                    'fftt as fftt',
                    'llave as llave',
                    'dir_terminal as dir_terminal',
                    'fonos_contacto as fonos_contacto',
                    'contrata as contrata',
                    'zonal as zonal',
                    'wu_nagendas as wu_nagendas',
                    'wu_nmovimient as wu_nmovimientos',
                    'wu_fecha_ult_age as wu_fecha_ult_agenda',
                    'tot_llam_tec as total_llamadas_tecnicas',
                    'tot_llam_seg as total_llamadas_seguimiento',
                    'llamadastec15d as llamadastec15dias',
                    'llamadastec30d as llamadastec30dias',
                    'quiebre as quiebre',
                    'lejano as lejano',
                    'des_distrito as distrito',
                    'eecc_zon as eecc_zona',
                    'zona_movuno as zona_movistar_uno',
                    'paquete as paquete',
                    'data_multip as data_multiproducto',
                    'aver_m1 as averia_m1',
                    'fecha_data_fuente as fecha_data_fuente',
                    'telefono_codclientecms as telefono_codclientecms',
                    'rango_dias as rango_dias',
                    'sms1 as sms1',
                    'sms2 as sms2',
                    'area2 as area2',
                    'veloc_caja_recomen as veloc_caja_recomen',
                    'tipo_servicio as tipo_servicio',
                    'tipo_actuacion as tipo_actuacion',
                    'eecc_final as eecc_final',
                    'microzona as microzona',
                    'xcoord as x',
                    'ycoord as y'
                        )
                ->where('codigo_req', $this->_operador, $requerimiento)
                ->get();
        return $tmpdata;
    }
}