<script type="text/javascript">
var opts = {
      lines: 13, // The number of lines to draw
      length: 25, // The length of each line
      width: 10, // The line thickness
      radius: 41, // The radius of the inner circle
      corners: 1, // Corner roundness (0..1)
      rotate: 21, // The rotation offset
      direction: 1, // 1: clockwise, -1: counterclockwise
      color: '#000', // #rgb or #rrggbb or array of colors
      speed: 1, // Rounds per second
      trail: 60, // Afterglow percentage
      shadow: false, // Whether to render a shadow
      hwaccel: false, // Whether to use hardware acceleration
      className: 'spinner', // The CSS class to assign to the spinner
      zIndex: 2e9, // The z-index (defaults to 2000000000)
      top: 'auto', // Top position relative to parent in px
      left: 'auto' // Left position relative to parent in px
    };
var target = document.getElementById('foo');
var esCritico = false;
var rm_telefono, 
    rm_inscripcion, 
    rm_codcliente,
    rm_nombre,
    rm_apaterno, 
    rm_amaterno,
    rm_segmento,
    rm_zonal,
    rm_mdf,
    rm_averia;
$(document).ready(function() {
    $('#myTab a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
    });
    $("#btn_historico").click(buscarCliente);
    $("#btn_limpiar").click(limpiaForm);
});
buscarCliente=function(){
    var fono = $("#telefonoCliente").val();
    var codATIS = $("#codigoClienteATIS").val();
    var codServCMS = $("#codigoServicioCMS").val();
    var codCliCMS = $("#codigoClienteCMS").val();
    var parametro='';

    if (fono.length>0 && codATIS.length===0 && codServCMS.length===0 && codCliCMS.length===0){
        parametro={telefonoCliente:fono};
    }
    else if (fono.length===0 && codATIS.length>0 && codServCMS.length===0 && codCliCMS.length===0){
        parametro={codigoClienteATIS:codATIS};
    }
    else if (fono.length===0 && codATIS.length===0 && codServCMS.length>0 && codCliCMS.length===0){
        parametro={codigoServicioCMS:codServCMS};
    }
    else if (fono.length===0 && codATIS.length===0 && codServCMS.length===0 && codCliCMS.length>0){
        parametro={codigoClienteCMS:codCliCMS};
    }
    else {
        alert('debe ingresar un valor de busqueda ');
        return false;
    }
    Historico.BuscaCliente(parametro);

};
limpiaForm=function(){

    $("#tabs-averias").html("");
    $("#tabs-llamadas").html("");
    $("#tabs-provision").html("");
    $("#tabs-criticos").html("");
    //Limpiamos Formulario
    $("#telefonoCliente").val("");
    $("#codigoClienteATIS").val("");
    $("#codigoServicioCMS").val("");
    $("#codigoClienteCMS").val("");
    //Listo para otra busqueda
    $("#telefonoCliente").focus();
    $("#mensaje").html("&nbsp;");
    $("#inscripcion").html("&nbsp;");
    $("#telefono").html("&nbsp;");
    $("#codcli").html("&nbsp;");
    $("#codclicms").html("&nbsp;");
    $("#codsercms").html("&nbsp;");
    $("#paquete").html("&nbsp;");
    $("#segmentos").html("&nbsp;");
    $("#direcc").html("&nbsp;");
    $("#nombre").html("&nbsp;");
    $("#paterno").html("&nbsp;");
    $("#materno").html("&nbsp;");
    $("#modalidad").html("&nbsp;");
    $("#velocidad").html("&nbsp;");
    $("#tasa").html("&nbsp;");
    $("#tecnologia").html("&nbsp;");

    $('#t_resultado_averias').dataTable().fnDestroy();
    $('#tb_resultado_averias').html('');
    $("#t_resultado_averias").dataTable();

    $('#t_resultado_averias2').dataTable().fnDestroy();
    $('#tb_resultado_averias2').html('');
    $("#t_resultado_averias2").dataTable();

    $('#t_resultado_provision').dataTable().fnDestroy();
    $('#tb_resultado_provision').html('');
    $("#t_resultado_provision").dataTable();

    $('#t_resultado_llamadas').dataTable().fnDestroy();
    $('#tb_resultado_llamadas').html('');
    $("#t_resultado_llamadas").dataTable();

    $('#t_resultado_criticos').dataTable().fnDestroy();
    $('#tb_resultado_criticos').html('');
    $("#t_resultado_criticos").dataTable();
};
mostrarUnicoCliente=function(telefono,codcliatis,codsercms,codclicms){
    $("#telefonoCliente").val(telefono);
    $("#codigoClienteATIS").val('');
    $("#codigoServicioCMS").val('');
    $("#codigoClienteCMS").val('');
    $('#dialog-servicios').modal('hide');
    $("#btn_historico").trigger( "click" );
};
datosClienteHTML=function(obj){
    var cantidad = obj.length;

    if(cantidad==1){
        $("#inscripcion").html(obj[0].inscripcio);
        $("#telefono").html(obj[0].telefono);
        $("#codcli").html(obj[0].codclie);
        $("#codclicms").html(obj[0].codclicms);
        $("#codsercms").html(obj[0].codservcms);
        if(obj[0].tipopaq!==''){
            $("#paquete").html(obj[0].tipopaq);
        } else {
            $("#paquete").html("MONO");
        }
        $("#nombre").html(obj[0].nombre);
        $("#paterno").html(obj[0].appater);
        $("#materno").html(obj[0].apmater);
        $("#direcc").html(obj[0].tipocalle+" "+obj[0].nomcalle+" "+obj[0].numcalle);
        $("#segmentos").html(obj[0].segest+" - "+obj[0].desseg);
        $("#modalidad").html(obj[0].modalidad);
        $("#velocidad").html(obj[0].veloc);
        $("#tasa").html(obj[0].tasa);
        $("#tecnologia").html(obj[0].tecnologia);

        //RESUELVE SI MOSTRAR AGENDAR O NO
        // c1 telefono
        // c2 inscripcion
        if(obj[0].validacion!==''){
            $("#critico").html("Cliente Cr&iacute;tico");
            $("#critico").css("display","block");
            $("#nocritico").html("");
            $("#nocritico").css("display","none");
            esCritico = true;
        } else {
            $("#nocritico").html("Cliente no cr&iacute;tico");
            $("#nocritico").css("display","block");
            $("#critico").html("");
            $("#critico").css("display","none");
            esCritico = false;
        }
        if(obj[0].posibleCritico=="1"){
            $("#critico2").html("Posible Cr&iacute;tico");
            $("#critico2").css("display","block");
        } else {
            $("#critico2").html("");
            $("#critico2").css("display","none");
        }
        //Obtenemos datos de averías
        //listarAverias(obj[0].telefono,obj[0].codservcms,obj[0].codclicms);
        Historico.listarAverias(obj[0].telefono,obj[0].codservcms,obj[0].codclicms);
        //Obtenemos datos de Provisión
        if (obj[0].telefono!=0)
            Historico.listarProvision(obj[0].telefono);
        //Obtenemos datos de llamadas
        Historico.listarLlamadas(obj[0].telefono);
        Historico.listarCriticos(obj[0].telefono);
        
    } else { //varios servicios contratados
        listado="";
        for (var i = 0; i < cantidad; i ++ ) {
            listado+="<tr>";
            listado+="<td><a href=\"#\" onclick=\"mostrarUnicoCliente('"+obj[i].telefono+"','"+obj[i].codclie+"','"+obj[i].codservcms+"','"+obj[i].codclicms+"')\">"+obj[i].telefono+"</a></td>";
            listado+="<td>"+obj[i].codservcms+"</td>";
            listado+="<td>"+obj[i].tipocalle+" "+obj[i].nomcalle+" "+obj[i].numcalle+"</td>";
            listado+="<td>"+obj[i].codclie+"</td>";
            listado+="<td>"+obj[i].codclicms+"</td>";
            listado+="</tr>";
        }
        $('#dialog-servicios').modal('show');
        $("#tb_consulta_servicios").html('');
        $('#t_consulta_servicios').dataTable().fnDestroy();
        $("#tb_consulta_servicios").html(listado);
        $("#t_consulta_servicios").dataTable();
    }
};
listarAveriasHTML=function(response){
    var html = '';
    var etiqueta='';
    var accion='';
    if(response.rst==1){
        $.each(response.arrTba,function(index,data){
            html+='<tr>';
            html+='<td>TBA</td>';
            html+='<td><a href="#" onclick="verDetalle("'+data.averia+'");">data.averia</a></td>';
            html+='<td>'+datafecreg+'</td>';
            accion = "Agendar Visita";
            etiqueta='';
            if (data.id_atc){
                etiqueta+='<br>'+data.id_atc;
                etiqueta+='<br>Agenda:<br> '+data.fecha_agenda+'<br>'+data.horario;
                accion = "Reagendar Visita";
            }
            html+='<td>Pendiente'+etiqueta+'</td>';
            etiqueta='';
            if (esCritico===false && data.id_atc===null) {
                etiqueta+="<a href=\"" + data.averia + "\" class=\"rmanual\" title=\"rutina-bas-lima\" >";
                etiqueta+="Registro manual</a>";
            } else {
                if (data.n_evento==0) {//no tiene registro en gestiones
                    etiqueta+='<a class="btn bg-navy btn-sm" data-toggle="modal" data-target="#bandejaModal" data-codactu="'+data.averia+'"><i class="fa fa-desktop fa-lg"></i> </a>';
                }
            }
            html+='<td>'+etiqueta+'</td>';
            html+='</tr>';
        });
        $.each(response.arrAdslPen,function(index,data){
            accion = "Agendar Visita";
            if (data.id_atc){
                accion = "Reagendar Visita";
            }
            html+='<tr>';
            html+='<td>ADSL</td>';
            html+='<td><a href="#" onclick="verDetalle(\'averia\',\'adsl-pen\', \''+data.averia+'\');">'+data.averia+'</a></td>';
            html+='<td>'+data.fecha_registro+'</td>';
            html+='<td>Pendiente</td>';
            etiqueta='';
            if (esCritico===false) {
                etiqueta+="<a href=\"" + data.averia + "\" class=\"rmanual\" title=\"rutina-adsl-pais\">";
                etiqueta+='Registro manual</a>';
            } else{
                if (data.n_evento==0) {
                    etiqueta+='<a class="btn bg-navy btn-sm" data-toggle="modal" data-target="#bandejaModal" data-codactu="'+data.averia+'"><i class="fa fa-desktop fa-lg"></i> </a>';
                }
            }
            html+='<td>'+etiqueta+'</td>';
            html+='</tr>';
        });
        $.each(response.arrCatvPen,function(index,data){
            accion = "Agendar Visita";
            if (data.id_atc){
                accion = "Reagendar Visita";
            }
            html+='<tr>';
            html+='<td>CATV</td>';
            html+='<td><a href="#" onclick="verDetalle(\'averia\',\'adsl-pen\', \''+data.averia+'\');">'+data.averia+'</a></td>';
            html+='<td>'+data.fecha_registro+'</td>';
            html+='<td>Pendiente</td>';
            etiqueta='';
            if (esCritico===false) {
                etiqueta+="<a href=\"" + data.averia + "\" class=\"rmanual\" title=\"rutina-catv-pais\">";
                etiqueta+='Registro manual</a>';
            } else{
                if (data.n_evento==0) {//cargar boton para agendar
                    etiqueta+='<a class="btn bg-navy btn-sm" data-toggle="modal" data-target="#bandejaModal" data-codactu="'+data.averia+'"><i class="fa fa-desktop fa-lg"></i> </a>';
                }
            }
            html+='<td>'+etiqueta+'</td>';
            html+='</tr>';
        });
        $("#tb_resultado_averias").html('');
        $('#t_resultado_averias').dataTable().fnDestroy();
        $("#tb_resultado_averias").html(html);
        $("#t_resultado_averias").dataTable();
        html='';
        etiqueta='';
        $.each(response.datos,function(index,data){
            var tipo='';
            if (data.tipo=='ADSL')
                tipo='adsl-liq';
            else if (data.tipo=='CATV')
                tipo='catv-liq';
            else if (data.tipo=='TBA')
                tipo='tba-liq';

            html+='<tr>';
            html+='<td>'+data.tipo+'</td>';
            etiqueta+='<a href="#" onclick="verDetalle(\'averia\',\''+tipo+'\', \''+data.averia+'\');">'+data.averia+'</';

            if (esCritico===true) {
                etiqueta="<img src='img/dialog_warning.png' alt='critico' title='critico' >";
            }
            html+='<td>'+etiqueta+'</td>';
            html+='<td>'+data.fecha_registro+'</td>';
            html+='<td>'+data.estado+'</td>';
            html+='<td>'+data.fecha_liquidacion+'</td>';
            html+='</tr>';
        });
        $("#tb_resultado_averias2").html('');
        $('#t_resultado_averias2').dataTable().fnDestroy();
        $("#tb_resultado_averias2").html(html);
        $("#t_resultado_averias2").dataTable();
    }
    //Agregamos el evento click al boton de agendar averia, cuadno se termine de cargar las arevias

    //Registro manual
    $(".rmanual").click(function (event){
        event.preventDefault();
        rm_averia = $(this).attr("href");
        var tipo = $(this).attr("title");
        var url = "admin.historico.registromanual";
        newWin=window.open(url,'mywindow');
        newWin.onload=function(){
            newWin.document.getElementById("averia").value=rm_averia;
            // newWin.document.getElementById("ifrm_title").innerHTML=passedURL;
        };
    });
};
listarProvisionHTML=function(datos){
    var html='';
    var etiqueta = '';
    $.each(datos,function(index,data){
        var estadoGestel = data.estado_gestel.trim();
        var estadoCms = data.estado_cms.trim();

        var accion = "Agendar Visita";
        if(data.id_atc){
            var deb = 1;
            var agenda = "<br>" + data.id_atc;
            agenda += "<br>Agenda:<br> " + data.fecha_agenda + "<br>"+ data.horario;
            accion = "Reagendar Visita";
        }
        html+='<tr>';
        html+='<td>'+data.peticion+'</td>';
        html+='<td>'+data.id_cliente+'</td>';
        html+='<td>'+data.numdoc+'</td>';
        html+='<td>'+data.ps+'</td>';
        html+='<td>'+data.paquete+'</td>';
        html+='<td>'+data.fecreg+'</td>';
        html+='<td>';
        if ( estadoGestel == 'PENDIENTE' ) {
            html+= "<a href=\"" + (data.peticion).trim() + "\" class=\"rmanual\" title=\"\" >estadoGestel</a>";
        }
        if ( estadoCms == 'PENDIENTE' ) {
            html+= "<a href=\"" + (data.peticion).trim() + "\" class=\"rmanual\" title=\"\" >estadoCms</a>";
        }
        html+='</td>';
        html+='<td>'+data.estado_cms+'</td>';
        html+='<td>agenda</td>';
        html+='<td cod="'+data.codigo_req+'">';
        if(data.n_evento == 0 ) {
            html+='<a class="btn bg-navy btn-sm" data-toggle="modal" data-target="#bandejaModal" data-codactu="'+data.codigo_req+'"><i class="fa fa-desktop fa-lg"></i> </a>';
        }
        html+='</td>';
        html+='</tr>';
    });
    $('#tb_resultado_provision').html('');
    $('#t_resultado_provision').dataTable().fnDestroy();
    $('#tb_resultado_provision').html(html);
    $("#t_resultado_provision").dataTable();
};
listarLlamadassHTML=function(datos){
    var html='';
    $.each(datos,function(index,data){
        html+='<tr>';
        html+='<td>'+data.fecha_llamada1+'</td>';
        html+='<td>'+data.servicio+'</td>';
        html+='<td>'+data.ges_producto+'</td>';
        html+='<td>'+data.ges_servicio+'</td>';
        html+='<td>'+data.ges_accion+'</td>';
        html+='<td>'+data.observacion+'</td>';
        html+='</tr>';
    });
    $('#tb_resultado_llamadas').html('');
    $('#t_resultado_llamadas').dataTable().fnDestroy();
    $('#tb_resultado_llamadas').html(html);
    $("#t_resultado_llamadas").dataTable();
};
listarCriticosHTML=function(datos){
    var html='';
    $.each(datos,function(index,data){
        html+='<tr>';
        html+='<td>'+data.tipo_actuacion+'</td></td>';
        html+='<td>'+data.averia+'</a></td>';
        html+='<td>'+data.fecha_subida2+'</td>';
        html+='<td>'+data.quiebre+'</td>';
        html+='<td>'+data.esta_pendiente+'</td>';
        html+='</tr>';
    });
    $('#tb_resultado_criticos').html('');
    $('#t_resultado_criticos').dataTable().fnDestroy();
    $('#tb_resultado_criticos').html(html);
    $("#t_resultado_criticos").dataTable();
};
verDetalle=function(tipo, negocio, actuacion){
    Historico.verDetalle(tipo, negocio, actuacion);
};
verDetalleHTML=function(datos){
    var html='';
    var titulo='';
    $.each(datos,function(index,data){

        if (data.tipo=='CATV-LIQ'){
            titulo='AVERIAS CATV LIQUIDADAS';
            html+='<tr>';
                html+='<td>Tipo Requerimiento</td><td>'+data.codigotiporeq+'</td>';
                html+='<td>Motivo Requerimiento</td><td>'+data.codigomotivoreq+'</td>';
                html+='<td>Codigo Cliente</td><td>'+data.cod_cliente+'</td>';
            html+='</tr>';
            html+='<tr>';
                html+='<td>Categoria</td><td>'+data.categoria_cliente+'</td>';
                html+='<td>Codigo Servicio</td><td>'+data.cod_servicio+'</td>';
                html+='<td>Oficina Administrativa</td><td>'+data.oficinaadministrativa+'</td>';
            html+='</tr>';
            html+='<tr>';
                html+='<td>Departamento</td><td>'+data.departamento+'</td>';
                html+='<td>Provincia</td><td>'+data.provincia+'</td>';
                html+='<td>Direccion</td><td>'+data.direccion+'</td>';
            html+='</tr>';
            html+='<tr>';
                html+='<td>Fecha Registro</td><td>'+data.fecharegistro+'</td>';
                html+='<td>Fecha Asignacion</td><td>'+data.fechaasignacion+'</td>';
                html+='<td>Contrata</td><td>'+data.contrata+'</td>  ';
            html+='</tr>';
            html+='<tr>';
                html+='<td>Tecnico</td><td>'+data.tecnico+'</td>';
                html+='<td>Fecha Liquidacion</td><td>'+data.fecha_liquidacion+'</td>';
                html+='<td>Codigo Liquidacion</td><td>'+data.codigodeliquidacion+' / '+data.detalle_liquidacion+'</td>';
            html+='</tr>';
        }
        else if (data.tipo=='TBA-LIQ'){
            titulo='AVERIAS TBA LIMA LIQUIDADAS';
            html+='<tr class="filatr">';
                html+='<td>Averia</td><td>'+data.averia+'</td>';
                html+='<td>Telefono</td><td>'+data.telefono+'</td>';
                html+='<td>Inscripcion</td><td>'+data.inscripcion+'</td>';
            html+='</tr>';
            html+='<tr>';
                html+='<td>Observacion 102</td><td>'+data.observacion_102+'</td>';
                html+='<td>Observacion #2</td><td>'+data.otra_observacion+'</td>';
                html+='<td>Numero Comprobacion</td><td>'+data.numero_comprobacion+'</td>';
            html+='</tr>';
            html+='<tr>';
                html+='<td>MDF</td><td>'+data.mdf+'</td>';
                html+='<td>Area</td><td>'+data.area_sig+'</td>';
                html+='<td>FFTT</td><td>'+data.armario+'." ".'+data.cable+' '+data.bloque+' '+data.terminal+'</td>';
            html+='</tr>';
            html+='<tr>';
                html+='<td>Fecha Registro</td><td>'+data.fecha_registro+'</td>';
                html+='<td>Fecha Comprobacion</td><td>'+data.fecha_de_comprobacion+'</td>';
                html+='<td>Numero Comprobacion</td><td>'+data.numero_comprobacion+'</td>';
            html+='</tr>';
            html+='<tr>';
                html+='<td>Tecnico</td><td>'+data.tecnico_liquidacion+'</td>';
                html+='<td>Fecha Liquidacion</td><td>'+data.fecha_de_liquidacion+'</td>';
                html+='<td>Codigo Liquidacion</td><td>'+data.liquidacion_+' / '+data.detalle+'</td>';
            html+='</tr>';
        }
        else if (data.tipo=='ADSL-LIQ'){
            titulo='AVERIAS ADSL LIMA LIQUIDADAS';
            html+='<tr>';
                html+='<td>Averia</td><td>'+data.averia+'</td>';
                html+='<td>Telefono</td><td>'+data.telefono+'</td>';
                html+='<td>Inscripcion</td><td>'+data.inscripcion+'</td>';
            html+='</tr>';
            html+='<tr>';
                html+='<td>Nombre Contacto</td><td>'+data.nombre_contacto+'</td>';
                html+='<td>Zonal</td><td>'+data.zonal+'</td>';
                html+='<td>Franqueo</td><td>'+data.franqueo+'</td>';
            html+='</tr>';
            html+='<tr>';
                html+='<td>MDF</td><td>'+data.mdf+'</td>';
                html+='<td>Area</td><td>'+data.estado_liq+'</td>';
                html+='<td>FFTT</td><td>'+data.cable+' '+data.sector+' '+data.nro_caja+'." ".'+data.par_distribuidor+'</td>';
            html+='</tr>';
            html+='<tr>';
                html+='<td>Fecha Registro</td><td>'+data.fecha_registro+'</td>';
                html+='<td>Fecha Despacho</td><td>'+data.fecha_despacho+'</td>';
                html+='<td>Fecha Prog Tecnico</td><td>'+data.fecha_pro_tec+'</td>';
            html+='</tr>';
            html+='<tr>';
                html+='<td>Tecnico</td><td>'+data.tecnico_rep_4+'</td>';
                html+='<td>Fecha Liquidacion</td><td>'+data.fecha_liquidacion+'</td>';
                html+='<td>Codigo Liquidacion</td><td>'+data.codigo_liquidacion+' / '+data.observacion_liquidacion+'</td>';
            html+='</tr>';
        }
        else if (data.tipo=='ADSL-PEN'){
            titulo='AVERIA ADSL PENDIENTE';
            html+='<tr>';
                html+='<td>Averia</td><td>'+data.averia+'</td>';
                html+='<td>Telefono</td><td>'+data.telefono+'</td>';
                html+='<td>Inscripcion</td><td>'+data.inscripcion+'</td>';
            html+='</tr>';
            html+='<tr>';
                html+='<td>MDF</td><td>'+data.mdf+'</td>';
                html+='<td>Zonal</td><td>'+data.zonal+'</td>';
                html+='<td>FFTT</td><td>'+data.cable+' '+data.sector+' '+data.nro_caja+' '+data.borne+'</td>';
            html+='</tr>';
            html+='<tr>';
                html+='<td>Fecha Registro</td><td>'+data.fecha_registro+'</td>';
                html+='<td>Fecha Despacho</td><td>'+data.fecha_des+'</td>';
                html+='<td>Fecha Instalacion</td><td>'+data.fec_inst+'</td> ';
            html+='</tr>';
            html+='<tr>';
                html+='<td>Indicador Vip</td><td>'+data.indicador_vip+'</td>';
                html+='<td>Area</td><td>'+data.area+'</td>';
                html+='<td>Direccion Instalacion</td><td>'+data.direccion_instalacion+'</td>';
            html+='</tr>';
        } else {
            html='';
        }
        return;//salir a la primera
    });
    $("#tb_resultado_averias_detalle").html('');
    $("#tb_resultado_averias_detalle").html(html);
    $("#titulo_detalle").html(titulo);
};
</script>