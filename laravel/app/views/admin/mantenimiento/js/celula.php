<script type="text/javascript">
$(document).ready(function() {
    Celulas.CargarCelulas();
    var data = {'estado':1};
    slctGlobal.listarSlct('zonal','slct_zonal','simple',null,data);
    slctGlobal.listarSlct('empresa','slct_empresa','simple',null,null);

    $('#celulaModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); 
        var titulo = button.data('titulo'); 
        var celula_id = button.data('id'); 

        var modal = $(this);
        modal.find('.modal-title').text(titulo+' Celula');
        $('#form_celulas [data-toggle="tooltip"]').css("display","none");
//        $("#form_celulas input[type='hidden']").remove();

        if(titulo=='Nuevo') {
            slctGlobal.listarSlct('quiebre','slct_quiebres','multiple',null,null);
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_celulas #slct_estado').val(1);
            $('#form_celulas #txt_nombre').focus();
            $('#form_celulas #txt_token').val("<?php echo Session::get('s_token');?>");
        }
        else {
            var data = {'celula_id':celulaObj[celula_id].id};
            slctGlobal.listarSlct('quiebre','slct_quiebres','multiple',null,data);
            var zonal_id=celulaObj[celula_id].zonal_id;
            empresa_id=celulaObj[celula_id].empresa_id;

            $('#slct_zonal').multiselect('select', zonal_id);
            $('#slct_zonal').multiselect('rebuild');
            $('#slct_empresa').multiselect('select', empresa_id);
            $('#slct_empresa').multiselect('rebuild');
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
            $('#form_celulas #txt_nombre').val( celulaObj[celula_id].nombre );
            $('#form_celulas #txt_responsable').val( celulaObj[celula_id].responsable );
            $('#form_celulas #txt_token').val("<?php echo Session::get('s_token');?>");
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                $('#form_celulas .n_estado').remove();
                $("#form_celulas").append('<div class="n_estado"></div>');
                $('#form_celulas #slct_estado').hide();
                $('#form_celulas .n_estado').show();
                var est = celulaObj[celula_id].estado;
                if(est == 1) est ='Activo';
                else est = 'Inactivo';
                $("#form_celulas .n_estado").text( est );
            }
            $('#form_celulas #slct_estado').val( celulaObj[celula_id].estado );
            $("#form_celulas").append("<input type='hidden' value='"+celulaObj[celula_id].id+"' name='id'>");
        }
        $("#form_celulas #slct_estado").trigger("change");
        $("#form_celulas" ).on('change','#slct_estado', function() {
           /* if ($( "#form_celulas #slct_estado" ).val()==1)
                $('#slct_quiebres').multiselect('enable');
            else
                $('#slct_quiebres').multiselect('disable');*/
        });
    });

    $('#celulaModal').on('hide.bs.modal', function (event) {
        var modal = $(this); //captura el modal
        modal.find('.modal-body input').val(''); // busca un input para copiarle texto
        $('#slct_quiebres').multiselect('destroy');

        $('#slct_zonal option:contains(.::Seleccione::.)').attr("selected","selected");
        $('#slct_zonal').multiselect('rebuild');

        $('#slct_empresa option:contains(.::Seleccione::.)').attr("selected","selected");
        $('#slct_empresa').multiselect('rebuild');
    });
});
activarTabla=function(){
    $("#t_celulas").dataTable(); 
};
Editar=function(){
    if(validaCelulas()){
        Celulas.AgregarEditarCelula(1);
    }
};
activar=function(id){
    Celulas.CambiarEstadoCelulas(id,1);
};
HTMLCargarCelula=function(datos){
    var html="";
    $('#t_celula').dataTable().fnDestroy();

    //PRIVILEGIO AGREGAR
    if(agregarG == 0) { 
        $('#nuevo').remove();  
    }  

    $.each(datos,function(index,data){
        estadohtml='<span id="'+data.id+'" onClick="activar('+data.id+')" class="btn btn-danger">Inactivo</span>';
        if(data.estado==1){
            estadohtml='<span id="'+data.id+'" onClick="desactivar('+data.id+')" class="btn btn-success">Activo</span>';
        }
        //PRIVILEGIO DESACTIVAR
        if(eliminarG == 0) { 
            estadohtml='<span class="">Inactivo</span>';
            if(data.estado==1){
                estadohtml='<span class="">Activo</span>';
            }
        }     

         html+="<tr>"+
            "<td>"+data.nombre+"</td>"+
            "<td>"+data.responsable+"</td>"+
            "<td>"+data.empresa+"</td>"+
            "<td>"+data.zonal+"</td>"+
            "<td>"+estadohtml+"</td>";

         //PRIVILEGIO EDITAR
        if(editarG == 1) { 
            html+='<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#celulaModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a></td>'; 
        } else {
            html+='<td class="editarG"></td>';
        }

        html+="</tr>";
    });
    $("#tb_celulas").html(html);
    if(editarG == 0) $('.editarG').hide();  
    activarTabla();
};
desactivar=function(id){
    Celulas.CambiarEstadoCelulas(id,0);
};
Agregar=function(){
    if(validaCelulas()){
        Celulas.AgregarEditarCelula(0);
    }
};
validaCelulas=function(){
    $('#form_celula [data-toggle="tooltip"]').css("display","none");
    var a=[];
    a[0]=valida("txt","nombre","");
    var rpta=true;

    for(i=0;i<a.length;i++){
        if(a[i]===false){
            rpta=false;
            break;
        }
    }
    return rpta;
};
valida=function(inicial,id,v_default){
    var texto="Seleccione";
    if(inicial=="txt"){
        texto="Ingrese";
    }
    if( $.trim($("#"+inicial+"_"+id).val())==v_default ){
        $('#error_'+id).attr('data-original-title',texto+' '+id);
        $('#error_'+id).css('display','');
        return false;
    }
};
</script>