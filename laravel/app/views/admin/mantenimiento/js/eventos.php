<script type="text/javascript">
    //js
$(document).ready(function() {
    Eventos.CargarEventos();
//    slctGlobal.listarSlct('quiebregrupo','slct_quiebregrupos','simple',null,null);

    $('#eventosModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // captura al boton
        var titulo = button.data('titulo'); // extrae del atributo data-
        eventos_id = button.data('id'); //extrae el id del atributo data
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this); //captura el modal
        modal.find('.modal-title').text(titulo+' Evento');
        $('#form_eventos [data-toggle="tooltip"]').css("display","none");
//        $("#form_eventos input[type='hidden']").remove();
        
        if(titulo=='Nuevo'){
           
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_eventos #slct_estado').val(1);
            $('#form_eventos #txt_nombre').focus();
            
             $('#form_eventos #slct_tipoevento').attr('disabled',false);
             $('#form_eventos #txt_id_sql').attr('disabled',true);  
             $('#form_eventos #txt_id_where').attr('disabled',true);  
             $('#form_eventos #txt_extraer').attr('disabled',true);  
             $('#form_eventos #txt_valor_where').attr('disabled',true); 
             $('#form_eventos #txt_grupo').attr('disabled',true);  
             $('#form_eventos #txt_orden').attr('disabled',true);
             $('#form_eventos #txt_token').val("<?php echo Session::get('s_token');?>");
        }
        else{

            var tipo=""+eventosObj[eventos_id].tipo+"";

            $('#form_eventos #slct_tipoevento').attr('disabled',true);  
            
            if(tipo==2)
            {
             $('#form_eventos #txt_id_sql').attr('disabled',true);  
             $('#form_eventos #txt_id_where').attr('disabled',true);  
             $('#form_eventos #txt_extraer').attr('disabled',true);  
             $('#form_eventos #txt_valor_where').attr('disabled',true); 
             $('#form_eventos #txt_grupo').attr('disabled',true);  
             $('#form_eventos #txt_orden').attr('disabled',true);
            }
            else
            {
             $('#form_eventos #txt_id_sql').attr('disabled',false);  
             $('#form_eventos #txt_id_where').attr('disabled',false);  
             $('#form_eventos #txt_extraer').attr('disabled',false);  
             $('#form_eventos #txt_valor_where').attr('disabled',false);  
             $('#form_eventos #txt_grupo').attr('disabled',false);  
             $('#form_eventos #txt_orden').attr('disabled',false);
            }    
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
            $('#form_eventos #txt_nombre').val( eventosObj[eventos_id].nombre );
            $('#form_eventos #txt_evento').val( eventosObj[eventos_id].evento );
            $('#form_eventos #txt_id_sql').val( eventosObj[eventos_id].id_sql );
            $('#form_eventos #txt_id_where').val( eventosObj[eventos_id].id_where );
            $('#form_eventos #txt_extraer').val( eventosObj[eventos_id].extraer );
            $('#form_eventos #txt_valor_where').val( eventosObj[eventos_id].valor_where );
            $('#form_eventos #txt_grupo').val( eventosObj[eventos_id].grupo );
            $('#form_eventos #txt_orden').val( eventosObj[eventos_id].orden );
            $('#form_eventos #txt_token').val("<?php echo Session::get('s_token');?>");
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                var est = eventosObj[eventos_id].estado;
                if(est == 1) est ='Activo';
                else est = 'Inactivo';
                $("#form_eventos .n_estado").text( est );
            } else {
                $('#form_eventos #slct_estado').val( eventosObj[eventos_id].estado );
            }
            
            $('#form_eventos #slct_tipoevento').val( eventosObj[eventos_id].tipo );
            $("#form_eventos").append("<input type='hidden' value='"+
                    eventosObj[eventos_id].id+"' name='id'>");
            $("#form_eventos").append("<input type='hidden' value='"+
                    eventosObj[eventos_id].tipo+"' name='tipotabla'>");
        }
        
    });
    

    $('#eventosModal').on('hide.bs.modal', function (event) {
        var modal = $(this);
        modal.find('.modal-body input').val('');
        $('#form_eventos #slct_tipoevento').val(2);
    });
    
     $("#form_eventos #slct_tipoevento").trigger("change");
     $( "#form_eventos #slct_tipoevento" ).change(function() {
            if ($("#form_eventos #slct_tipoevento").val()==1) //consulta
            {  
                 $('#form_eventos #txt_id_sql').attr('disabled',false);  
                 $('#form_eventos #txt_id_where').attr('disabled',false);  
                 $('#form_eventos #txt_extraer').attr('disabled',false);  
                 $('#form_eventos #txt_valor_where').attr('disabled',false);  
                 $('#form_eventos #txt_grupo').attr('disabled',false);  
                 $('#form_eventos #txt_orden').attr('disabled',false);
            }
            else
            {   
                 $('#form_eventos #txt_id_sql').attr('disabled',true);  
                 $('#form_eventos #txt_id_where').attr('disabled',true);  
                 $('#form_eventos #txt_extraer').attr('disabled',true);  
                 $('#form_eventos #txt_valor_where').attr('disabled',true); 
                 $('#form_eventos #txt_grupo').attr('disabled',true);  
                 $('#form_eventos #txt_orden').attr('disabled',true);
            }
     });
   
});

Editar=function(){
    //if(validaQuiebres()){
        Eventos.AgregarEditarEventos(1);
    //}
};

Agregar=function(){
    //if(validaQuiebres()){
        Eventos.AgregarEditarEventos(0);
    //}
};

activar=function(id,tipo){
    Eventos.CambiarEstadoEvento(id,tipo,1);
}

desactivar=function(id,tipo){
    Eventos.CambiarEstadoEvento(id,tipo,0);
}

HTMLCargarEventos=function(datos){
    var html="";
    var tipo='';
    //PRIVILEGIO AGREGAR
    if(agregarG == 0) { 
        $('#nuevo').remove();  
    }  
    $('#t_eventos').dataTable().fnDestroy();

    $.each(datos,function(index,data){//UsuarioObj
        estadohtml='<a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#eventosModal" \n\
                    data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a>';
        tipo='Consulta';
        //estado='Inactivo';
        estado='<span id="'+data.id+'" onClick="activar('+data.id+','+data.tipo+')" class="btn btn-danger">Inactivo</span>';
        if(data.estado==1){
            estado='<span id="'+data.id+'" onClick="desactivar('+data.id+','+data.tipo+')" class="btn btn-success">Activo</span>';
        }
        //PRIVILEGIO DESACTIVAR
        if(eliminarG == 0) { 
            $('#form_eventos #slct_estado').replaceWith('<div class="n_estado"></div>')
            estado='<span class="">Inactivo</span>';
            if(data.estado==1){
                estado='<span class="">Activo</span>';
            }
        }   
        if(data.tipo==2){
            
            tipo='Metodo';
        }
         html+="<tr>"+
            "<td>"+data.nombre+"</td>"+
            "<td>"+data.evento+"</td>"+
            "<td>"+estado+"</td>"+
            "<td>"+tipo+"</td>";
            //PRIVILEGIO EDITAR
            if(editarG == 1) { 
                html+='<td>'+estadohtml+'</td>'
            } else {
                html+='<td class="editarG"></td>';
            }

        html+="</tr>";
    });
    $("#tb_eventos").html(html);
    if(editarG == 0) $('.editarG').hide();  
    activarTabla();
};

activarTabla=function(){
    $("#t_eventos").dataTable(); // inicializo el datatable    
};
</script>
