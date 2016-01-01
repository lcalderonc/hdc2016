<script type="text/javascript">
$(document).ready(function() {
    $('#tap_digitalizacion a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
    });
    $('#tap_digitalizacion_modal a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
    });
    //Descargar
    $("#btn_generar").click(function (){
        generar();
    });
    //buscar
    $("#btn_buscar").click(function (){
        buscar();
    });

    $('#archivo').on('change', prepareUpload);
    //combo con disticnt de la vista vistaEstadoDigitalizacion
    slctGlobal.listarSlct('reporte','slct_proyecto','simple');
    slctGlobal.listarSlct('reporte','slct_proyecto2','simple');
    slctGlobal.listarSlct('lista/estadosdigitalizacionmotivo','slct_ed_motivo_id','simple',null);

    $('#estadosDigitalizacionModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // captura al boton
        var id = button.data('id');
        ed_id = EstadosObj[id].id;
        var titulo = button.data('titulo');
        var modal = $(this); //captura el modal
        modal.find('.modal-title').text(titulo+' digitalización');
        $('#form_digitalizacion [data-toggle="tooltip"]').css("display","none");
        $("#form_digitalizacion input[type='hidden']").remove();
        var ed_motivo_id = EstadosObj[id].ed_motivo_id;
        $('#form_digitalizacion #slct_ed_motivo_id').multiselect('select', ed_motivo_id);
        $('#form_digitalizacion #slct_ed_motivo_id').multiselect('refresh');
        $('#form_digitalizacion #txt_observacion').val( EstadosObj[id].observacion );
        $("#form_digitalizacion").append("<input type='hidden' value='"+ed_id+"' name='ed_id'>");
        Estados.CargarGestiones(ed_id);

        
        modal.find('.modal-footer .btn-primary').text('Guardar');
        modal.find('.modal-footer .btn-primary').attr('onClick','Gestionar();');
        $('#form_digitalizacion #txt_observacion').focus();
        

    });

    $('#estadosDigitalizacionModal').on('hide.bs.modal', function (event) {
        var modal = $(this); //captura el modal
        modal.find('.modal-body input').val(''); // busca un input para copiarle texto
        $('#form_digitalizacion #slct_ed_motivo_id').multiselect('deselectAll', false);
        $('#form_digitalizacion #slct_ed_motivo_id').multiselect('rebuild');
        $('#form_digitalizacion #slct_ed_motivo_id').multiselect('refresh');
    });
});
Gestionar=function(){
    Estados.Crear();
};
prepareUpload=function(event){
    files = event.target.files;
    event.stopPropagation();
    event.preventDefault();
    $.each(files, function(key, value)
    {
        var data = new FormData();
        data.append('archivo', value);
        Estados.uploadFile(data);
    });
};
/**
  * generar file excel de estados_digitalizacion
  * 
*/
generar=function(){
    var proyecto = $('#slct_proyecto').val();
    if (isNaN(proyecto) || proyecto==='') {
        Psi.mensaje('danger', 'Seleccione Proyecto', 6000);
    } else {
        $("input[type='hidden']").remove();
        $("#form_estados").append("<input type='hidden' value='"+proyecto+"' name='proyecto' id='proyecto'>");
        //$("#form_critico").append("<input type='hidden' value='' name='provision' id='provision'>");

        $("#form_estados").submit();
        
    }
};
/**
  * buscar
  *
*/
buscar=function(){
    var proyecto = $('#slct_proyecto2').val();
    if (isNaN(proyecto) || proyecto==='') {
        Psi.mensaje('danger', 'Seleccione Proyecto', 6000);
    } else {
        Estados.cargar(proyecto);
    }
};
HTMLCargarEstados=function(datos){
    var html="";
    $('#t_estados').dataTable().fnDestroy();

    $.each(datos,function(index,data){//UsuarioObj
        html+="<tr>"+
            "<td>"+data.cliente_cms+"</td>"+
            "<td>"+data.servicio_cms+"</td>"+
            "<td>"+data.nom_ape+"</td>"+
            "<td>"+data.cond+"</td>"+
            "<td>"+data.orden+"</td>"+
            "<td>"+data.motivo+"</td>"+
            "<td>"+data.fecha_creacion+"</td>"+
            '<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#estadosDigitalizacionModal" data-id="'+index+'" data-titulo="Gestionar"><i class="fa fa-edit fa-lg"></i> </a></td>';
        html+="</tr>";
    });
    $("#tb_estados").html(html);
    //activarTabla();
    $("#t_estados").dataTable();
};
HTMLCargarGestiones=function(datos){
    var html="", i=0;
    $('#t_gestiones').dataTable().fnDestroy();

    $.each(datos,function(index,data){//UsuarioObj
        i++;
        html+="<tr>"+
            "<td>"+i.toString()+"</td>"+
            "<td>"+data.motivo+"</td>"+
            "<td>"+data.fecha_movimiento+"</td>"+
            "<td>"+data.observacion+"</td>"+
            "<td>"+data.usuario+"</td>"+
            "</tr>";
    });
    $("#tb_gestiones").html(html);
    
    $("#t_gestiones").dataTable();
    //activarTabla();
};
validarFile=function(archivo){
    if ('files' in archivo) {
        if (archivo.files.length === 0) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
};
</script>
