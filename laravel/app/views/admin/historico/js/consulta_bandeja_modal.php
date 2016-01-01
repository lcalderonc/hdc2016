<script type="text/javascript">
var map;
var markers = [];
var cliente_x;
var cliente_y;
var cliente_xy_insert = 0;

$(document).ready(function() {

    $("#bandejaModal").attr("onkeyup","return enterGlobal(event,'btn_gestion_modal')");
    $("#btn_gestion_modal").click(gestionModal);
    $("#btn_obsevacion_modal").click(observacionModal);
    //$("#fecha_consolidacion").inputmask("yyyy/mm/dd", {"placeholder": "yyyy/mm/dd"});

    slctGlobal.listarSlct('solucion','slct_solucion_modal','simple');
    slctGlobal.listarSlct('feedback','slct_feedback_modal','simple');

    slctGlobalHtml('slct_coordinado2_modal,#slct_contacto_modal,#slct_pruebas_modal','simple');

    $('#bandejaModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // captura al boton
        var modal = $(this); //captura el modal

        $('#form_bandeja [data-toggle="tooltip"]').css("display","none");
        variables={ buscar:button.data('codactu'),
                    tipo:'gd.averia'
                  };
        Bandeja.CargarBandeja('M',verificaDataModal,variables);
    });

    $('#bandejaModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
      $("#txt_observacion2_modal,#txt_penalizable_obs_modal,#fecha_consolidacion").val('');

        $("#txt_observacion_o_modal").val("");
        $('#form_observacion input[type="hidden"]').remove();

        $(".L0,.H0,.T0").css("display","none");
        $('#form_bandeja input[type="hidden"]').remove();
        //////////////////////Resetea Checkbox//////////////////////////////////
        $('#form_bandeja input[type="checkbox"]').prop("checked",false);
        $('#form_bandeja .icheckbox_minimal').removeClass("checked");
        $('#form_bandeja .icheckbox_minimal').attr("aria-checked","false");
        ///////////////////////////////////////////////////////////////////////// 
        $("#slct_motivo_modal,#slct_submotivo_modal,#slct_estado_modal,#slct_contacto_modal,#slct_pruebas_modal,#slct_feedback_modal,#slct_solucion_modal").val("");
        $("#slct_motivo_modal,#slct_submotivo_modal,#slct_estado_modal,#slct_contacto_modal,#slct_pruebas_modal,#slct_feedback_modal,#slct_solucion_modal").multiselect('refresh');
    });

    $('#tb_movimiento').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );

    $(".L0,.H0,.T0").css("display","none");
});
/*
envioMensaje=function(pos){
    var celular = $("#txt_celular_modal").val();
    var mensaje = $("#txt_m_sms"+pos+"_modal").val();
    var iduser = '<?=Auth::user()->id;?>';

                    if (celular.length<9)
                    {
                       
                        alert("Numero celular no valido. Debe tener 9 digitos.");
                        return;
                    }

                    
                    if (mensaje.length<4)
                    {
                        alert("Mensaje debe tener minimo 4 letras.");
                        return;
                    }
                    $.ajax({
                        type: "POST",
                        url: "http://psiweb.ddns.net:2230/webpsi/sms_enviar_individual_ajax.php",
                        data: { 
                            enviar_sms: 1,
                            celular: celular,
                            iduser: iduser,
                            mensaje: mensaje
                        },
                        beforeSend : function() {
                            $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                        },
                        success : function(obj) {
                            $(".overlay,.loading-img").remove();
                                $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                                    '<i class="fa fa-check"></i>'+
                                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                                    '<b>Mensaje Enviado</b>'+
                                                '</div>');
                        },
                        error: function(){
                            $(".overlay,.loading-img").remove();
                            $("#msj").html('<div class="alert alert-dismissable alert-warning">'+
                                                    '<i class="fa fa-warning"></i>'+
                                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                                    '<b>Mensaje no enviado.</b>'+
                                                '</div>');
                        }
                    });
    }
*/
function SetXY(x, y) {
    $("#txt_x_modal").val(x);
    $("#txt_y_modal").val(y);
}

function initializeMapModal(coord_x, coord_y) {
    
    if ($.trim(coord_x) == '') {
        coord_x = -77.0427934;
    }
    
    if ($.trim(coord_y) == '') {
        coord_y = -12.046374;
    }
    
    $("#txt_x_modal").val(coord_x);
    $("#txt_y_modal").val(coord_y);
    
    var latitud = coord_y;
    var longitud = coord_x;

    var myLatlng = new google.maps.LatLng(coord_y, coord_x);
    var map = new google.maps.Map(document.getElementById('map_canvas'), {
        zoom: 16,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    
    var markerOptions = {
        draggable: true,
        map: map
    }
    var new_marker_position = new google.maps.LatLng(latitud, longitud);
    var marker = new google.maps.Marker(markerOptions);
    marker.setPosition(new_marker_position);
    markers.push(marker);

    google.maps.event.addListener(map, 'click', function(evento) {

        latitud = evento.latLng.lat();
        longitud = evento.latLng.lng();

        SetXY(longitud, latitud);
        for (var i = 0, marker; marker = markers[i]; i++) {
            marker.setMap(null);
        }

        markerOptions = {
            draggable: true,
            map: map
        }
        
        new_marker_position = new google.maps.LatLng(latitud, longitud);
        marker = new google.maps.Marker(markerOptions);
        marker.setPosition(new_marker_position);
        markers.push(marker);

    });
    
    google.maps.event.addListener(marker, 'click', function() {
        var markerLatLng = marker.getPosition();
        SetXY(markerLatLng.lng(), markerLatLng.lat())
    });
    google.maps.event.addListener(marker, 'dragend', function() {
        var markerLatLng = marker.getPosition();
        SetXY(markerLatLng.lng(), markerLatLng.lat())
    });

}

function format ( datos ) {
    // `d` is the original data object for the row
    var r="";
    r= '<table class="table" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
        var dobs=datos.observaciones.split("|");
        for(i=0;i<dobs.length;i++){
    r+=     '<tr>'+
                '<td><label>Observacion '+(i+1)+':</label></td>'+
                '<td>&nbsp;'+dobs[i]+'</td>'+
            '</tr>';
        }
    r+= '</table>';
    return r;
}

observacionModal=function(){
    if( $.trim($("#txt_observacion_o_modal").val())=='' ){
        alert('Ingrese una observacion');
    }
    else{
        Bandeja.guardarObservacion();
    }
}

HTMLCargarGestionMovimientoFirst=function(datos){
    $("#txt_codactu_o_modal").val(datos.codactu);
    $("#txt_actividad_o_modal").val(datos.actividad);
    $("#txt_quiebre_o_modal").val(datos.quiebre);
    $("#form_observacion").append('<input type="hidden" name="txt_gestion_movimiento_id_modal" value="'+datos.id+'">'); // retorna la actuacion gestionada actualizada.
    $("#form_observacion").append('<input type="hidden" name="txt_codactu_modal" value="'+datos.codactu+'">');
}

HTMLCargarGestionMovimientoFirstDetalle=function(obj){
    //Direccion en la observacion
    $("#txt_observacion2_modal").val(
        "Direccion: " + obj[0].direccion_instalacion 
        + "\r\n"
        + "Referencia:    "
        + "Nombre Contacto:    "
        + "Telefono Contacto:    "
        + "\r\n"
        + "Nro.Piso:     "
        + "Color Casa:    "
    );

        $("#txt_d_codactu_modal").val(obj[0].codactu);
        $("#txt_d_tipo_averia_modal").val(obj[0].tipo_averia);
        $("#txt_d_horas_averia_modal").val(obj[0].horas_averia);
        $("#txt_d_fecha_registro_modal").val(obj[0].fecha_registro);
        $("#txt_d_ciudad_modal").val(obj[0].ciudad);
        $("#txt_d_mdf_modal").val(obj[0].mdf);
        $("#txt_d_inscripcion_modal").val(obj[0].inscripcion);
        $("#txt_d_fono1_modal").val(obj[0].celular_cliente_critico);
        $("#txt_d_telefono_modal").val(obj[0].telefono_cliente_critico);
        $("#txt_d_segmento_modal").val(obj[0].segmento);
        $("#txt_d_area_modal").val(obj[0].area);
        $("#txt_d_nombre_cliente_modal").val(obj[0].nombre_cliente);
        $("#txt_d_direccion_instalacion_modal").val(obj[0].direccion_instalacion);
        $("#txt_d_wu_fecha_ult_agenda_modal").val(obj[0].wu_fecha_ult_agenda);
        $("#txt_d_codigo_distrito_modal").val(obj[0].codigo_distrito);
        $("#txt_d_orden_trabajo_modal").val(obj[0].orden_trabajo);
        $("#txt_d_veloc_adsl_modal").val(obj[0].veloc_adsl);
        $("#txt_d_zonal_modal").val(obj[0].zonal);
        $("#txt_d_clase_servicio_catv_modal").val(obj[0].clase_servicio_catv);
        $("#txt_d_codmotivo_req_catv_modal").val(obj[0].codmotivo_req_catv);
        $("#txt_d_llave_modal").val(obj[0].llave);
        $("#txt_d_quiebre_modal").val(obj[0].quiebre);
        $("#txt_d_total_averias_cable_modal").val(obj[0].total_averias_cable);
        $("#txt_d_total_averias_cobre_modal").val(obj[0].total_averias_cobre);
        $("#txt_d_total_averias_modal").val(obj[0].total_averias);
        $("#txt_d_distrito_modal").val(obj[0].distrito);
        $("#txt_d_fonos_contacto_modal").val(obj[0].fonos_contacto);
        $("#txt_d_contrata_modal").val(obj[0].contrata);
        $("#txt_d_lejano_modal").val(obj[0].lejano);
        $("#txt_d_eecc_zona_modal").val(obj[0].eecc_zona);
        $("#txt_d_fftt_modal").val(obj[0].fftt);
        $("#txt_d_dir_terminal_modal").val(obj[0].dir_terminal);
        $("#txt_d_paquete_modal").val(obj[0].paquete);
        $("#txt_d_zona_movistar_uno_modal").val(obj[0].zona_movistar_uno);
        $("#txt_d_data_multiproducto_modal").val(obj[0].data_multiproducto);
        $("#txt_d_averia_m1_modal").val(obj[0].averia_m1);
        $("#txt_d_fecha_data_fuente_modal").val(obj[0].fecha_data_fuente);
        $("#txt_d_telefono_codclientecms_modal").val(obj[0].telefono_codclientecms);
        $("#txt_d_rango_dias_modal").val(obj[0].rango_dias);
        $("#txt_d_area2_modal").val(obj[0].area2);
        $("#txt_d_total_llamadas_tecnicas_modal").val(obj[0].total_llamadas_tecnicas);
        $("#txt_d_total_llamadas_seguimiento_modal").val(obj[0].total_llamadas_seguimiento);
        $("#txt_d_observacion_modal").val(obj[0].observacion);
        $("#txt_d_sms1_modal").val(obj[0].sms1);
        $("#txt_d_sms2_modal").val(obj[0].sms2);

        $("#txt_m_sms1_modal").val(obj[0].sms1);
        $("#txt_m_sms2_modal").val(obj[0].sms2);
}

HTMLCargarGestionMovimiento=function(datos){
    var html="";
     $('#t_movimiento').dataTable().fnDestroy();

     $("#txt_codactu_m_modal").val(datos[0].codactu);
     $("#txt_actividad_m_modal").val(datos[0].actividad);
     $("#txt_quiebre_m_modal").val(datos[0].quiebre);
    var variablestable=[];
    table= $("#t_movimiento").DataTable(
        {
            "data": datos,
            "order": [[ 1, "desc" ]],
            "columnDefs": [
                { "targets": 0,
                    "data": "id_detalle_table",
                },
                { "targets": 1,
                    "data": "fecha_movimiento",
                },
                { "targets": 2,
                    "data": "empresa" },
                { "targets": 3,
                    "data": "nombre_cliente_critico" },
                { "targets": 4,
                    "data": "fh_agenda"
                },
                { "targets": 5,
                    "data": "celula"
                },
                { "targets": 6,
                    "data": "tecnico" },
                { "targets": 7,
                    "data": "estado" },
                { "targets": 8,
                    "data": "usuario" },
                {   "targets": 9,
                    "orderable":      false,
                    "data": function ( row, type, val, meta ) {
                            if( $.trim(row.observaciones)!='' ){
                                variablestable.push(row.id_detalle_table);
                            }
                            return row.observacion;
                    },
                    "defaultContent": ''
                },
            ]
        }
    );
    var tr;
    for(i=0;i<variablestable.length;i++){
        trsimple=$("#tb_movimiento tr td").filter(function() {
            return $(this).text() == variablestable[i];
        }).parent('tr');
        $(trsimple).find("td:eq(8)").css('color','red').addClass('details-control');
    }
}

verificaDataModal=function(obj){
    var datos={estado_id:obj[0].estado_id};
    Bandeja.validaEstado(datos,listarDataModal,obj);
        $(".modal-header>li.logo").each( function(index,val){
        if(obj[0].estado_id==4){
            $(this).css("display","none");
            if(index==1){
                $(this).css("display","");
                $(this).addClass("active");
            }
            else if(index==3){
                $(this).css("display","");
            }
        }
        else{
            if(index==0){
            $(this).addClass("active");
            }
            else{
                if(index!=3 && index!=4){
                    $(this).css("display","none");
                    $(this).css("display","none");
                }
                $(this).removeClass("active");
            }
        }
    });

    $("#tab_1,#tab_2,#tab_3,#tab_4,#tab_5").removeClass("active");
    if(obj[0].estado_id!='4'){
        $("#tab_1").addClass("active");
    }
    else{
        $("#tab_2").addClass("active");
    }

    if(obj[0].estado_id!='-1'){
        if(obj[0].estado_id!=4){
            $(".modal-header>li.logo").css("display","");
        }
        variables={ codactu:obj[0].codactu };
        Bandeja.CargarGestionMovimiento(variables,HTMLCargarGestionMovimiento);
        variables={ codactu:obj[0].codactu,first:'1' };
        Bandeja.CargarGestionMovimiento(variables,HTMLCargarGestionMovimientoFirst);
    }
    HTMLCargarGestionMovimientoFirstDetalle(obj);
}

listarDataModal=function(objnuev,obj){
    var verifica=objnuev[0].valida;
    if( $("#slct_empresa option[value='"+obj[0].empresa_id+"']").attr("disabled") ){
        alert('Ud no cuenta con permiso para la Empresa: '+obj[0].empresa);
        $("#btn_close_modal").click();
    }
    else if( $("#slct_quiebre option[value='"+obj[0].quiebre_id+"']").attr("disabled") ){
        alert('Ud no cuenta con permiso para el Quiebre: '+obj[0].quiebre);
        $("#btn_close_modal").click();
    }
    else if( $.trim(obj[0].quiebre_id)=='' ){
        alert('El quiebre '+ obj[0].quiebre+', no se encuentra disponible; Comuniquese con su superior para que active el quiebre y pueda continuar con su gestión');
        $("#btn_close_modal").click();
    }
    else{
        var data = 0;
        if(verifica=="2-0"){
            data = { requerimiento: '1-0' }; // indica q se inicializa una gestion para un codactu
        }
        else if(verifica=="-1-0"){
            data = { requerimiento: '1-0","1-1","0-1',mas:'1' }; // aqui buscara mas de un registro por el indicador mas
        }
        else if(verifica=="1-0"){
            data = { requerimiento: '0-0","1-1","2-0","0-1',mas:'1' }; // aqui buscara mas de un registro por el indicador mas
        }
        $('#slct_motivo_modal,#slct_submotivo_modal,#slct_estado_modal,#slct_horario_tipo_modal').multiselect('destroy');
        var ids = [];
        slctGlobal.listarSlct('motivo','slct_motivo_modal','simple',ids,data,0,'#slct_submotivo_modal,#slct_estado_modal','M');
        slctGlobal.listarSlct('submotivo','slct_submotivo_modal','simple',ids,data,1,'#slct_estado_modal','S','slct_motivo_modal','M');
        slctGlobal.listarSlct('estado','slct_estado_modal','simple',ids,data,1);

        var data = { empresa_id:obj[0].empresa_id, zonal_id:obj[0].zonal_id, quiebre_grupo_id:obj[0].quiebre_grupo_id };
        slctGlobal.listarSlct('horariotipo','slct_horario_tipo_modal','simple',ids,data);

        $("#txt_codactu_modal").val(obj[0].codactu);
        $("#txt_estado_modal").val(obj[0].estado);
        $("#txt_empresa_modal").val(obj[0].empresa);
        $("#txt_quiebre2_modal").val(obj[0].quiebre);

        var data = { empresa_id: obj[0].empresa_id ,quiebre_id: obj[0].quiebre_id};
        $('#slct_celula_modal,#slct_tecnico_modal,#slct_empresa_modal').multiselect('destroy');
        var tecnico = []; tecnico.push(obj[0].tecnico_id);
        var celula = []; celula.push(obj[0].celula_id);
        slctGlobal.listarSlct('celula','slct_celula_modal','simple',celula,data,0,'#slct_tecnico_modal','C');
        slctGlobal.listarSlct('tecnico','slct_tecnico_modal','simple',tecnico,data,1);

        var data = {usuario: 1};
        var empresa=[]; empresa.push(obj[0].empresa_id);
        slctGlobal.listarSlct('empresa','slct_empresa_modal','simple',empresa,data,0);

        //var data = { quiebre_id: obj[0].quiebre_id, actividad_id: obj[0].actividad_id };
        var data = {fftt:obj[0].fftt,tipoactu:obj[0].tipo_averia, cod_cliente:obj[0].inscripcion}
        Bandeja.extraerXY(data);
        
        $("#slct_cumplimiento_modal").val("");
        $("#span_fecha_agenda").text(obj[0].fecha_agenda+" | "+ obj[0].hora_agenda);

        // Preparando para la transacción
        $("#form_bandeja").append('<input type="hidden" name="txt_codactu_modal" id="txt_codactu_modal" value="'+obj[0].codactu+'">'); // retorna la actuacion gestionada actualizada.
        $("#form_bandeja").append('<input type="hidden" name="txt_horario_id_modal" id="txt_horario_id_modal" value="">');
        $("#form_bandeja").append('<input type="hidden" id="txt_horario_aux_modal" value="'+obj[0].horario_id+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_dia_id_modal" id="txt_dia_id_modal" value="">');
        $("#form_bandeja").append('<input type="hidden" id="txt_dia_aux_modal" value="'+obj[0].dia_id+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_fecha_agenda_modal" id="txt_fecha_agenda_modal" value="">');
        $("#form_bandeja").append('<input type="hidden" id="txt_fecha_agenda_aux_modal" value="'+obj[0].fecha_agenda+'">');

        $("#form_bandeja").append('<input type="hidden" id="txt_hora_agenda_modal" name="txt_hora_agenda_modal" value="">');

        $("#form_bandeja").append('<input type="hidden" name="txt_empresa_id_modal" id="txt_empresa_id_modal" value="'+obj[0].empresa_id+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_zonal_id_modal" id="txt_zonal_id_modal" value="'+obj[0].zonal_id+'">');

        $("#form_bandeja").append('<input type="hidden" name="txt_quiebre_grupo_id_modal" id="txt_quiebre_grupo_id_modal" value="'+obj[0].quiebre_grupo_id+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_transmision_modal" id="txt_transmision_modal" value="'+obj[0].transmision+'">');


        $("#form_bandeja").append('<input type="hidden" name="txt_nombre_cliente_critico_modal" id="txt_nombre_cliente_critico_modal" value="'+obj[0].nombre_cliente_critico+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_celular_cliente_critico_modal" id="txt_celular_cliente_critico_modal" value="'+obj[0].celular_cliente_critico+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_telefono_cliente_critico_modal" id="txt_telefono_cliente_critico_modal" value="'+obj[0].telefono_cliente_critico+'">');


        $("#form_bandeja").append('<input type="hidden" name="txt_tipo_averia_modal" id="txt_tipo_averia_modal" value="'+obj[0].tipo_averia+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_horas_averia_modal" id="txt_horas_averia_modal" value="'+obj[0].horas_averia+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_fecha_registro_modal" id="txt_fecha_registro_modal" value="'+obj[0].fecha_registro+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_ciudad_modal" id="txt_ciudad_modal" value="'+obj[0].ciudad+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_inscripcion_modal" id="txt_inscripcion_modal" value="'+obj[0].inscripcion+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_fono1_modal" id="txt_fono1_modal" value="'+obj[0].celular_cliente_critico+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_telefono_modal" id="txt_telefono_modal" value="'+obj[0].telefono_cliente_critico+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_mdf_modal" id="txt_mdf_modal" value="'+obj[0].mdf+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_observacion_modal" id="txt_observacion_modal" value="'+obj[0].observacion+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_segmento_modal" id="txt_segmento_modal" value="'+obj[0].segmento+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_area_modal" id="txt_area_modal" value="'+obj[0].area+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_direccion_instalacion_modal" id="txt_direccion_instalacion_modal" value="'+obj[0].direccion_instalacion+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_codigo_distrito_modal" id="txt_codigo_distrito_modal" value="'+obj[0].codigo_distrito+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_nombre_cliente_modal" id="txt_nombre_cliente_modal" value="'+obj[0].nombre_cliente+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_orden_trabajo_modal" id="txt_orden_trabajo_modal" value="'+obj[0].orden_trabajo+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_veloc_adsl_modal" id="txt_veloc_adsl_modal" value="'+obj[0].veloc_adsl+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_clase_servicio_catv_modal" id="txt_clase_servicio_catv_modal" value="'+obj[0].clase_servicio_catv+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_codmotivo_req_catv_modal" id="txt_codmotivo_req_catv_modal" value="'+obj[0].codmotivo_req_catv+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_total_averias_cable_modal" id="txt_total_averias_cable_modal" value="'+obj[0].total_averias_cable+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_total_averias_cobre_modal" id="txt_total_averias_cobre_modal" value="'+obj[0].total_averias_cobre+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_total_averias_modal" id="txt_total_averias_modal" value="'+obj[0].total_averias+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_fftt_modal" id="txt_fftt_modal" value="'+obj[0].fftt+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_llave_modal" id="txt_llave_modal" value="'+obj[0].llave+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_dir_terminal_modal" id="txt_dir_terminal_modal" value="'+obj[0].dir_terminal+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_fonos_contacto_modal" id="txt_fonos_contacto_modal" value="'+obj[0].fonos_contacto+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_contrata_modal" id="txt_contrata_modal" value="'+obj[0].contrata+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_zonal_modal" id="txt_zonal_modal" value="'+obj[0].zonal+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_wu_nagendas_modal" id="txt_wu_nagendas_modal" value="'+obj[0].wu_nagendas+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_wu_nmovimientos_modal" id="txt_wu_nmovimientos_modal" value="'+obj[0].wu_nmovimientos+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_wu_fecha_ult_agenda_modal" id="txt_wu_fecha_ult_agenda_modal" value="'+obj[0].wu_fecha_ult_agenda+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_total_llamadas_tecnicas_modal" id="txt_total_llamadas_tecnicas_modal" value="'+obj[0].total_llamadas_tecnicas+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_total_llamadas_seguimiento_modal" id="txt_total_llamadas_seguimiento_modal" value="'+obj[0].total_llamadas_seguimiento+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_llamadastec15dias_modal" id="txt_llamadastec15dias_modal" value="'+obj[0].llamadastec15dias+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_llamadastec30dias_modal" id="txt_llamadastec30dias_modal" value="'+obj[0].llamadastec30dias+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_lejano_modal" id="txt_lejano_modal" value="'+obj[0].lejano+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_distrito_modal" id="txt_distrito_modal" value="'+obj[0].distrito+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_eecc_zona_modal" id="txt_eecc_zona_modal" value="'+obj[0].eecc_zona+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_zona_movistar_uno_modal" id="txt_zona_movistar_uno_modal" value="'+obj[0].zona_movistar_uno+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_paquete_modal" id="txt_paquete_modal" value="'+obj[0].paquete+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_data_multiproducto_modal" id="txt_data_multiproducto_modal" value="'+obj[0].data_multiproducto+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_averia_m1_modal" id="txt_averia_m1_modal" value="'+obj[0].averia_m1+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_fecha_data_fuente_modal" id="txt_fecha_data_fuente_modal" value="'+obj[0].fecha_data_fuente+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_telefono_codclientecms_modal" id="txt_telefono_codclientecms_modal" value="'+obj[0].telefono_codclientecms+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_rango_dias_modal" id="txt_rango_dias_modal" value="'+obj[0].rango_dias+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_sms1_modal" id="txt_sms1_modal" value="'+obj[0].sms1+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_sms2_modal" id="txt_sms2_modal" value="'+obj[0].sms2+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_area2_modal" id="txt_area2_modal" value="'+obj[0].area2+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_microzona_modal" id="txt_microzona_modal" value="'+obj[0].microzona+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_tipo_actuacion_modal" id="txt_tipo_actuacion_modal" value="'+obj[0].tipo_actuacion+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_quiebre_modal" id="txt_quiebre_modal" value="'+obj[0].quiebre+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_actividad_modal" id="txt_actividad_modal" value="'+obj[0].actividad+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_quiebre_id_modal" id="txt_quiebre_id_modal" value="'+obj[0].quiebre_id+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_actividad_id_modal" id="txt_actividad_id_modal" value="'+obj[0].actividad_id+'">');

        $("#txt_direccion_instalacion2_modal").val(obj[0].direccion_instalacion);
        if(verifica!="-1-0"){
            $("#form_bandeja").append('<input type="hidden" name="txt_gestion_id_modal" id="txt_gestion_id_modal" value="'+obj[0].id+'">');
        }

        $('#slct_coordinado2_modal').multiselect('select', [ obj[0].coordinado ] );
        $('#slct_coordinado2_modal').multiselect('refresh');
    }
}

eventoSlctGlobalSimple=function(slct,valores){ // este evento "eventoSlctGlobalSimple" solo se dará cuando un select tenga como atributo data-evento
    if(slct=="slct_estado_modal"){
        var m=""; var s=""; var val=""; var dval=""
        $(".L0,.H0,.T0").css("display","none");
        M="M"+$("#slct_motivo_modal").val();
        S="S"+$("#slct_submotivo_modal").val();
        val=valores.split("|"+M+S+"-");
            if(val.length>1){
                dval=val[1].substr(0,3);
            }

        $("#html").html("");
        $("#slct_horario_tipo_modal").val("");

        var cant=$("#slct_horario_tipo_modal option").length;
        var valor=$("#slct_horario_tipo_modal option").eq( 1 ).val();
        if(cant==2){
            $("#slct_horario_tipo_modal").val(valor);
            Agenda.show( {zona:$("#txt_zonal_id_modal").val(),empresa:$("#txt_empresa_id_modal").val(),tipo:valor,quiebre_grupo:$("#txt_quiebre_grupo_id_modal").val(),fftt:$("#txt_d_fftt_modal").val(),tipoactu:$("#txt_d_tipo_averia_modal").val(),quiebre:$("#txt_quiebre2_modal").val()} );
        }
        $("#slct_horario_tipo_modal").multiselect('refresh');
        
        
        if(dval=='3-0'){ // para mantener a tecnico y horario
            $(".L0").css("display","none");
            $(".T0").css("display","none");
            $(".H0").css("display","none");

            $(".L1").attr("disabled",true);
            $(".T1").removeAttr("disabled");
            $(".H1").attr("disabled",true);

            $("select.L1").multiselect("disable");
            $("select.T1").multiselect("enable");
            $("select.H1").multiselect("disable");

            $("#txt_horario_id_modal").removeAttr('disabled');
            $("#txt_dia_id_modal").removeAttr('disabled');
            $("#txt_horario_id_modal").val($("#txt_horario_aux_modal").val());
            $("#txt_dia_id_modal").val($("#txt_dia_aux_modal").val());
            $("#txt_fecha_agenda_modal").val($("#txt_fecha_agenda_aux_modal").val());

        }
        else if(dval=='2-0'){ // para seleccionar tecnico en liquidados y mantener horario
            $(".L0").css("display","");
            $(".T0").css("display","");
            $(".H0").css("display","none");

            $(".L1").removeAttr("disabled");
            $(".T1").removeAttr("disabled");
            $(".H1").attr("disabled",true);

            $("select.L1").multiselect("enable");
            $("select.T1").multiselect("enable");
            $("select.H1").multiselect("disable");

            $("#txt_horario_id_modal").removeAttr('disabled');
            $("#txt_dia_id_modal").removeAttr('disabled');
            $("#txt_fecha_agenda_modal").removeAttr('disabled');
            $("#txt_horario_id_modal").val($("#txt_horario_aux_modal").val());
            $("#txt_dia_id_modal").val($("#txt_dia_aux_modal").val());
            $("#txt_fecha_agenda_modal").val($("#txt_fecha_agenda_aux_modal").val());

        }
        else if(dval=='1-1'){ // para seleccionar tecnico y horario
            $(".L0").css("display","none");
            $(".T0").css("display","");
            $(".H0").css("display","");

            $(".L1").attr("disabled",true);
            $(".T1").removeAttr("disabled");
            $(".H1").removeAttr("disabled");

            $("select.L1").multiselect("disable");
            $("select.T1").multiselect("enable");
            $("select.H1").multiselect("enable");

            $("#txt_horario_id_modal").removeAttr('disabled');
            $("#txt_dia_id_modal").removeAttr('disabled');
            $("#txt_fecha_agenda_modal").removeAttr('disabled');
            $("#txt_horario_id_modal").val('');
            $("#txt_dia_id_modal").val('');
            $("#txt_fecha_agenda_modal").val('');

        }
        else if(dval=='0-1'){ // para seleccionar solo Horario sin guardar tecnico
            $(".L0").css("display","none");
            $(".T0").css("display","none");
            $(".H0").css("display","");

            $(".L1").attr("disabled",true);
            $(".T1").attr("disabled",true);
            $(".H1").removeAttr("disabled");

            $("select.L1").multiselect("disable");
            $("select.T1").multiselect("disable");
            $("select.H1").multiselect("enable");

            $("#txt_horario_id_modal").removeAttr('disabled');
            $("#txt_dia_id_modal").removeAttr('disabled');
            $("#txt_fecha_agenda_modal").removeAttr('disabled');
            $("#txt_horario_id_modal").val('');
            $("#txt_dia_id_modal").val('');
            $("#txt_fecha_agenda_modal").val('');
        }
        else if(dval=='1-0'){ // para seleccionar solo tecnico sin guardar horario
            $(".L0").css("display","none");
            $(".T0").css("display","");
            $(".H0").css("display","none");

            $(".L1").attr("disabled",true);
            $(".T1").removeAttr("disabled");
            $(".H1").attr("disabled",true);

            $("select.L1").multiselect("disable");
            $("select.T1").multiselect("enable");
            $("select.H1").multiselect("disable");

            $("#txt_horario_id_modal").attr('disabled',true);
            $("#txt_dia_id_modal").attr('disabled',true);
            $("#txt_fecha_agenda_modal").attr('disabled',true);
        }
    }
    else if(slct=="slct_tecnico_modal"){
        var dval=valores.split("|C"+$("#slct_celula_modal").val()+"-")[1].substr(0,1);
        $("#txt_officetrack_modal").val("No es Officetrack");
        if(dval=='1'){
            $("#txt_officetrack_modal").val("Si es Officetrack");
        }
    }
    else if(slct=="slct_horario_tipo_modal"){
        if ( valores!='' ) {
            Agenda.show( {zona:$("#txt_zonal_id_modal").val(),empresa:$("#txt_empresa_id_modal").val(),tipo:valores.split("|").join(""),quiebre_grupo:$("#txt_quiebre_grupo_id_modal").val(),fftt:$("#txt_d_fftt_modal").val(),tipoactu:$("#txt_d_tipo_averia_modal").val(),quiebre:$("#txt_quiebre2_modal").val()} );
        }
    }
    else if(slct=="slct_empresa_modal"){
        if( $("#txt_empresa_id_modal").val() != $("#slct_empresa_modal").val()){
            $("#txt_empresa_id_modal").val( $("#slct_empresa_modal").val() );
            $("#txt_empresa_modal").val( $("#slct_empresa_modal>option[value='"+$("#slct_empresa_modal").val()+"']").text() );

            var data = { empresa_id: $("#slct_empresa_modal").val() ,quiebre_id: $("#txt_quiebre_id_modal").val() };
            $('#slct_celula_modal,#slct_tecnico_modal,#slct_horario_tipo_modal').multiselect('destroy');
            var tecnico = [];
            var celula = [];
            slctGlobal.listarSlct('celula','slct_celula_modal','simple',celula,data,0,'#slct_tecnico_modal','C');
            slctGlobal.listarSlct('tecnico','slct_tecnico_modal','simple',tecnico,data,1);

            var data = { empresa_id: $("#slct_empresa_modal").val(), zonal_id: $("#txt_zonal_id_modal").val(), quiebre_grupo_id: $("#txt_quiebre_grupo_id_modal").val()};
            slctGlobal.listarSlct('horariotipo','slct_horario_tipo_modal','simple',null,data);
            $("#html").html("");
        }
    }
    initializeMapModal(cliente_x, cliente_y);
}

gestionModal=function(){
    if( $("#slct_motivo_modal").val()=='' ){
        alert('Seleccione Motivo');
    } else if( $("#slct_submotivo_modal").val()=='' ){
        alert('Seleccione Sub Motivo');
    } else if( $("#slct_estado_modal").val()=='' ){
        alert('Seleccione Estado');
    } else if( $(".H0").css("display")!="none" && $("#slct_horario_tipo_modal").val()=='' ){
        alert('Seleccione un Tipo Horario');
    } else if( $("#slct_coordinado2_modal").val()=='' ){
        alert('Seleccione Si coordino con cliente');
    } else if( $("#txt_observacion2_modal").val()=='' ){
        alert('Ingrese una observacion');
    } else if( $(".L0").css("display")!="none" && $("#slct_cumplimiento_modal").val()=='' ){
        alert('Seleccione si cumplió o no cumplió Agendamiento');
    } else if( $(".L0").css("display")!="none" && $("#slct_contacto_modal").val()=='' ){
        alert('Seleccione Contacto');
    } else if( $(".L0").css("display")!="none" && $("#slct_pruebas_modal").val()=='' ){
        alert('Seleccione Prueba');
    } else if( $(".L0").css("display")!="none" && $("#fecha_consolidacion").val()=='' ){
        alert('Ingrese fecha consolidacion');
    } else if( $(".L0").css("display")!="none" && $("#slct_feedback_modal").val()=='' ){
        alert('Seleccione Feedback');
    } else if( $(".L0").css("display")!="none" && $("#slct_solucion_modal").val()=='' ){
        alert('Seleccione solucion');
    } else if( $(".T0").css("display")!="none" && $("#slct_celula_modal").val()=='' ){
        alert('Seleccione Celula');
    } else if( $(".T0").css("display")!="none" && $("#slct_tecnico_modal").val()=='' ){
        alert('Seleccione Tecnico');
    } else if( $(".H0").css("display")!="none" && fecha_agenda=='' ){
        alert('Seleccione un Horario');
    } else if( $(".H0").css("display")!="none" && $("#txt_x_modal").val()=='' ){
        alert('Busque y seleccione un punto en el mapa');
    }
    else{
        if ( $(".H0").css("display")!="none" ) {
            $("#txt_horario_id_modal").val(horario_agenda);
            $("#txt_dia_id_modal").val(dia_agenda);
            $("#txt_fecha_agenda_modal").val(fecha_agenda);
            $("#txt_hora_agenda_modal").val(hora_agenda);
        }

        var valores='';
        var dval='0';
        if( ( $(".T0").css("display")!="none" ) || ( $("#slct_celula_modal").val()!='' && $("#slct_tecnico_modal").val()!='' ) ){
            valores=$("#slct_tecnico_modal>option[value='"+$("#slct_tecnico_modal").val()+"']").attr('data-evento');
            dval=valores.split("|C"+$("#slct_celula_modal").val()+"-")[1].substr(0,1);
        }

        valores=$("#slct_estado_modal>option[value='"+$("#slct_estado_modal").val()+"']").attr('data-evento');
        var M=""; var S=""; var val2=""; var dval2="0";
        M="M"+$("#slct_motivo_modal").val();
        S="S"+$("#slct_submotivo_modal").val();
        val2=valores.split("|"+M+S+"-");
            if(val2.length>1){
                dval2=val2[1].substr(0,3);
            }

        $("#form_bandeja").append('<input type="hidden" name="txt_estado_officetrack_modal" id="txt_estado_officetrack_modal" value="'+dval+'">');
        $("#form_bandeja").append('<input type="hidden" name="txt_estado_agendamiento_modal" id="txt_estado_agendamiento_modal" value="'+dval2+'">');

            Bandeja.guardarMovimiento();
    }
}

</script>
