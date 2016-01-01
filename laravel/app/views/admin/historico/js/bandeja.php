<script type="text/javascript">
var temporalBandeja=0;
var table;
$(document).ready(function() {
    $("#form_Personalizado").attr("onkeyup","return enterGlobal(event,'btn_personalizado')");
    $("#form_General").attr("onkeyup","return enterGlobal(event,'btn_general')");
	$("[data-toggle='offcanvas']").click();
    $("#slct_tipo").change(ValidaTipo);
    $("#btn_personalizado").click(personalizado);
    $("#btn_general").click(general);
    $('#fecha_agenda').daterangepicker(
        {
            format: 'YYYY-MM-DD'
        }
    );
    $('#fecha_consolidacion').daterangepicker({ 
            singleDatePicker: true ,
            format: 'YYYY-MM-DD'
        }
    );
    /*$('#movimientoModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // captura al boton
        var modal = $(this); //captura el modal
        variables={ codactu:button.data('codactu') };
        Bandeja.CargarGestionMovimiento(variables,HTMLCargarGestionMovimiento);
    });

    $('#movimientoModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
    });

    $('#observacionModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // captura al boton
        var modal = $(this); //captura el modal
        variables={ codactu:button.data('codactu'),first:'1' };
        Bandeja.CargarGestionMovimiento(variables,HTMLCargarGestionMovimientoFirst);
    });

    $('#observacionModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
      $("#txt_observacion_o_modal").val("");
      $('#form_observacion input[type="hidden"]').remove();
    });*/

    // Con BD
    //  controlador,slct,tipo,usuarioV,afectado,afectados,slct_id
    var ids = []; //para seleccionar un id
    var data = {usuario: 1};
    slctGlobal.listarSlct('actividad','slct_actividad','multiple');
    slctGlobal.listarSlct('actividadtipo','slct_actividad_tipo','multiple');
    slctGlobal.listarSlct('estado','slct_estado','multiple');
    slctGlobal.listarSlct('quiebre','slct_quiebre','multiple',ids,data);
    slctGlobal.listarSlct('empresa','slct_empresa','multiple',ids,data,0,'#slct_celula,#slct_tecnico','E');
    slctGlobal.listarSlct('celula','slct_celula','multiple',ids,0,1,'#slct_tecnico','C');
    slctGlobal.listarSlct('tecnico','slct_tecnico','multiple',ids,0,1);
    slctGlobal.listarSlct('zonal','slct_zonal','multiple',ids,data);
    slctGlobal.listarSlct('troba','slct_troba','multiple',ids,data);

    data = {bandeja: 1};
    slctGlobal.listarSlct('nodo','slct_nodo','multiple',ids,data);
    slctGlobal.listarSlct('mdf','slct_mdf','multiple',ids,data);

    // Solo ingresar los ids, el primer registro es sin #.
    slctGlobalHtml('slct_tipo,#slct_legado,#slct_coordinado','simple');
    slctGlobalHtml('slct_transmision,#slct_cierre_estado','multiple');
    $(".expImage").hide();
});

descargarReporteImagen=function(){
    $("#form_General").append("<input type='hidden' name='imagen' id='imagen' value='1'>");
    $("#form_General").submit();
    $("#form_General #imagen").remove();
}

validarImagenExp=function(val){
    var datos=$("#slct_transmision").val();
    $(".expImage").hide();
    if( datos!=null && datos.length>0 ){
        if( datos.join("|").split("-").length>1 ){
            //$(".expImage").show();
        }
    }
}

descargarReporte=function(){
   $("#form_General").append("<input type='hidden' name='totalPSI' id='totalPSI' value='1'>");
    $("#form_General").submit();
    $("#form_General #totalPSI").remove();
}

personalizado=function(){
    if( $("#slct_tipo").val()=='' ){
        alert("Seleccione Tipo Filtro");
        $("#slct_tipo").focus();
    }
	else if( $("#txt_buscar").val()=='' ){
        alert("Ingrese datos");
        $("#txt_buscar").focus();
    }
    else{
        $("#txt_buscar").val($.trim($("#txt_buscar").val()));
		Bandeja.CargarBandeja("P",HTMLCargarBandeja);
	}
}

general=function(){
	Bandeja.CargarBandeja("G",HTMLCargarBandeja);
}

ValidaTipo=function(){
	$("#txt_buscar").val("");
	$("#txt_buscar").focus();
}

HTMLCargarBandeja=function(datos){
var html="";
var gestionado="";
var officetrackbotton="";
     $('#t_bandeja').dataTable().fnDestroy();

	$.each(datos,function(index,data){
        gestionado="";
        if(data.id==''){
            temporalBandeja++;
            data.id="T_"+temporalBandeja;
        }
        else{
            data.id="ID_"+data.id;
            /*gestionado='<a class="btn btn-warning btn-sm" data-toggle="modal" data-target="#movimientoModal" data-codactu="'+data.codactu+'"><i class="fa fa-search-plus fa-lg"></i> </a>'+
                        '<a class="btn bg-navy btn-sm" data-toggle="modal" data-target="#observacionModal" data-codactu="'+data.codactu+'"><i class="fa fa-comments fa-lg"></i> </a>';*/
        }
        fondo="";
        if(data.existe*1!=1){
            fondo="style='background-color: #DA8F66'";
        }

    html+="<tr>"+
        "<td "+fondo+">"+data.id+"</td>"+
        "<td>"+data.codactu+"</td>"+
        "<td>"+data.fecha_registro+"</td>"+
        "<td>"+data.actividad+"</td>"+
        "<td>"+data.quiebre+"</td>"+
        "<td>"+data.empresa+"</td>"+
        "<td>"+data.mdf+"</td>"+
        "<td>"+data.fh_agenda+"</td>"+
        "<td>"+data.tecnico+"</td>"+
        "<td>"+data.estado+"<br><font color='#327CA7'>"+data.cierre_estado+"</font></td>";

        officetrackColor='';officetrackImage='';officetrackbotton='';
        if(data.transmision!=0){
            officetrackColor='btn-default';
            if(data.transmision!=1 && data.transmision.split("-").length>1 && data.transmision.split("-")[1].substr(0,6).toLowerCase()=='inicio' ){
                officetrackColor='btn-success';
            }
            else if(data.transmision!=1 && data.transmision.split("-").length>1 && data.transmision.split("-")[1].substr(0,11).toLowerCase()=='supervision'){
                officetrackColor='btn-warning';
            }
            else if(data.transmision!=1 && data.transmision.split("-").length>1 && data.transmision.split("-")[1].substr(0,6).toLowerCase()=='cierre' ){
                officetrackColor='btn-primary';
            }

            if(officetrackColor!='btn-default'){
                officetrackbotton='data-toggle="modal" data-target="#officetrackModal" data-id="'+data.id.split("_")[1]+'"';
            }
            officetrackImage=' &nbsp; <a class="btn '+officetrackColor+' btn-sm" '+officetrackbotton+'><i class="fa fa-mobile fa-3x"></i> </a>';
        }
    html+=
        '<td><a class="btn bg-navy btn-sm" data-toggle="modal" data-target="#bandejaModal" data-codactu="'+data.codactu+'"><i class="fa fa-desktop fa-lg"></i> </a>'+
            officetrackImage+
        '</td>';
    html+="</tr>";

	});
	$("#tb_bandeja").html(html); 
    activarTabla();
    $('#t_bandeja').attr("style","width:''");
}

activarTabla=function(){
    $("#t_bandeja").dataTable(); // inicializo el datatable    
/*
	$("#t_bandeja").dataTable({
        responsive: true
    });*/
}

validaBandeja=function(){
	$('#form_roles [data-toggle="tooltip"]').css("display","none");
	var a=new Array();
	a[0]=valida("txt","descripcion","");
	var rpta=true;

	for(i=0;i<a.length;i++){
		if(a[i]==false){
			rpta=false;
			break;
		}
	}
	return rpta;
}

valida=function(inicial,id,v_default){
	var texto="Seleccione";
	if(inicial=="txt"){
		texto="Ingrese"
	}

	if( $.trim($("#"+inicial+"_"+id).val())==v_default ){
		$('#error_'+id).attr('data-original-title',texto+' '+id);
		$('#error_'+id).css('display','');
		return false;
	}	
}
</script>
