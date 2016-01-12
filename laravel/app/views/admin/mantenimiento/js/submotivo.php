<script type="text/javascript">
$(document).ready(function() {
    $( "#form_submotivos" ).submit(function( event ) {
        event.preventDefault();
    });
    Submotivos.CargarSubmotivos(activarTabla2);

    $('#submotivoModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var titulo = button.data('titulo'); // extrae del atributo data-
      var submotivo_id = button.data('id'); //extrae el id del atributo data
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this); //captura el modal
      modal.find('.modal-title').text(titulo+' Submotivo');
      $('#form_submotivos [data-toggle="tooltip"]').css("display","none");
//      $("#form_submotivos input[type='hidden']").remove();

        if(titulo=='Nuevo') {
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar2();');
            $('#form_submotivos #slct_estado2').val(1); 
            $('#form_submotivos #txt_nombre2').focus();
            $('#form_submotivos #slct_estado2').show();
            $('#form_submotivos .n_estado').remove();
            $('#form_submotivos #txt_token').val("<?php echo Session::get('s_token');?>");
        }
        else {
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar2();');
            $('#form_submotivos #txt_nombre2').val( submotivosObj[submotivo_id].nombre );
            $('#form_submotivos #txt_token').val("<?php echo Session::get('s_token');?>");
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                $('#form_submotivos .n_estado').remove();
                $("#form_submotivos").append('<div class="n_estado"></div>');
                $('#form_submotivos #slct_estado2').hide();
                $('#form_submotivos .n_estado').show();
                var est = submotivosObj[submotivo_id].estado;
                if(est == 1) est ='Activo';
                else est = 'Inactivo';
                $("#form_submotivos .n_estado").text( est );
            }
            $('#form_submotivos #slct_estado2').val( submotivosObj[submotivo_id].estado );
            //$('#form_motivos #txt_nombre').val( $('#t_motivos #nombre_'+button.data('id') ).text() );
            //$('#form_motivos #slct_estado').val( $('#t_motivos #estado_'+button.data('id') ).attr("data-estado") );
            $("#form_submotivos").append("<input type='hidden' value='"+submotivosObj[submotivo_id].id+"' name='id2'>");
        }

    });

    $('#submotivoModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
      modal.find('.modal-body input').val(''); // busca un input para copiarle texto
    });
});

activar2=function(id){
    Submotivos.CambiarEstadoSubmotivos(id,1);
};

desactivar2=function(id){
    Submotivos.CambiarEstadoSubmotivos(id,0);
};

activarTabla2=function(){
    var html="", estadohtml="";
    //PRIVILEGIO AGREGAR
    if(agregarG == 0) { 
        $('.nuevo').remove();  
    }  
    $.each(submotivosObj,function(index,data){
        estadohtml='<span id="'+data.id+'" onClick="activar2('+data.id+')" class="btn btn-danger btn-xs">Inactivo</span>';
        if(data.estado==1){
            estadohtml='<span id="'+data.id+'" onClick="desactivar2('+data.id+')" class="btn btn-success btn-xs">Activo</span>';
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
            "<td>"+estadohtml+"</td>";
         //PRIVILEGIO EDITAR
        if(editarG == 1) { 
            html+='<td><a class="btn btn-primary btn-xs" data-toggle="modal" data-target="#submotivoModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-xs"></i> </a></td>';
        } else {
            html+='<td class="editarG"></td>';
        } 
        html+="</tr>";
    });
    $("#tb_submotivos").html(html);
    if(editarG == 0) $('.editarG').hide(); 
    $("#t_submotivos").dataTable();
};

Editar2=function(){
    if(validaSubmotivos()){
        Submotivos.AgregarEditarSubmotivos(1);
    }
};

Agregar2=function(){
    if(validaSubmotivos()){
        Submotivos.AgregarEditarSubmotivos(0);
    }
};

validaSubmotivos=function(){
    $('#form_submotivos [data-toggle="tooltip"]').css("display","none");
    var a=[];
    a[0]=valida2("txt","nombre2","");
    var rpta=true;

    for(i=0;i<a.length;i++){
        if(a[i]===false){
            rpta=false;
            break;
        }
    }
    return rpta;
};

valida2=function(inicial,id,v_default){
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