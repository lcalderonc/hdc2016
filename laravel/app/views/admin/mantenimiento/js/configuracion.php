<script type="text/javascript">
    $(document).ready(function() {
        $( "#form_configuracion" ).submit(function( event ) {
            event.preventDefault();
        });
        Configuracion.CargarConfiguracion();
        var data = {'estado':1};

        $('#configuracionModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var titulo = button.data('titulo');
            var configuracion_id = button.data('id');

            var modal = $(this);
            modal.find('.modal-title').text(titulo+' Configuracion');
            $('#form_configuracion [data-toggle="tooltip"]').css("display","none");
//            $("#form_configuracion input[type='hidden']").remove();

            if(titulo=='Nuevo') {

                modal.find('.modal-footer .btn-primary').text('Guardar');
                modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
                $('#form_configuracion #slct_estado').val(1);
                $('#form_configuracion #txt_nombre').focus();
                $('#form_configuracion #slct_estado').show();
                $('#form_configuracion .n_estado').remove();
                $('#form_configuracion #txt_token').val("<?php echo Session::get('s_token');?>");
            }
            else {
                var data = {'configuracion_id':configuracionObj[configuracion_id].id};

                modal.find('.modal-footer .btn-primary').text('Actualizar');
                modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
                $('#form_configuracion #txt_nombre').val( configuracionObj[configuracion_id].nombre );
                $('#form_configuracion #txt_descripcion').val( configuracionObj[configuracion_id].descripcion );
                $('#form_configuracion #txt_token').val("<?php echo Session::get('s_token');?>");
                //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
                if(eliminarG == 0) {
                    $('#form_configuracion .n_estado').remove();
                    $("#form_configuracion").append('<div class="n_estado"></div>');
                    $('#form_configuracion #slct_estado').hide();
                    $('#form_configuracion .n_estado').show();
                    var est = configuracionObj[configuracion_id].estado;
                    if(est == 1) est ='Activo';
                    else est = 'Inactivo';
                    $("#form_configuracion .n_estado").text( est );
                }
                $('#form_configuracion #slct_estado').val( configuracionObj[configuracion_id].estado );                
                $("#form_configuracion").append("<input type='hidden' value='"+configuracionObj[configuracion_id].id+"' name='id'>");
            }
            $("#form_configuracion #slct_estado").trigger("change");
            $("#form_configuracion" ).on('change','#slct_estado', function() {
                /* if ($( "#form_celulas #slct_estado" ).val()==1)
                     $('#slct_quiebres').multiselect('enable');
                 else
                     $('#slct_quiebres').multiselect('disable');*/
            });
        });
    });

activarTabla=function(){
    $("#t_configuracion").dataTable();
};
Editar=function(){
    if(validaConfiguracion()){
        Configuracion.AgregarEditarConfiguracion(1);
    }
};
activar=function(id){
    Configuracion.CambiarEstadoConfiguracion(id,1);
};
HTMLCargarCelula=function(datos){
    var html="";
    $('#t_configuracion').dataTable().fnDestroy();

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
            "<td>"+data.descripcion+"</td>"+
            "<td>"+estadohtml+"</td>";
         //PRIVILEGIO EDITAR
        if(editarG == 1) { 
            html+='<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#configuracionModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a></td>'; 
        } else {
            html+='<td class="editarG"></td>';
        }

        html+="</tr>";
    });
    $("#tb_configuracion").html(html);
    if(editarG == 0) $('.editarG').hide();  
    activarTabla();
};
desactivar=function(id){
    Configuracion.CambiarEstadoConfiguracion(id,0);
};
Agregar=function(){
    if(validaConfiguracion()){
        Configuracion.AgregarEditarConfiguracion(0);
    }
};
validaConfiguracion=function(){
    $('#form_configuracion [data-toggle="tooltip"]').css("display","none");
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