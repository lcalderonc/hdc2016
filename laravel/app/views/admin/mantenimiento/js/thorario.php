<script type="text/javascript">
$(document).ready(function() {  
    Thorarios.CargarThorarios(activarTabla4);

    $('#thorarioModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var titulo = button.data('titulo'); // extrae del atributo data-
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this); //captura el modal
      modal.find('.modal-title').text(titulo+' Tipo Horario');
      $('#form_thorarios [data-toggle="tooltip"]').css("display","none");
      $("#form_thorarios input[type='hidden']").remove();

        if(titulo=='Nuevo'){
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_thorarios #slct_estado').val(1); 
			$('#form_thorarios #txt_nombre').focus();
            $('#form_thorarios #txt_minutos');
            $('#form_thorarios #slct_estado').show();
            $('#form_thorarios .n_estado').remove();
        }
        else{
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
            $('#form_thorarios #txt_nombre').val( $('#t_thorarios #nombre_'+button.data('id') ).text() );
			$('#form_thorarios #txt_minutos').val( $('#t_thorarios #minutos_'+button.data('id') ).text() );
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                $('#form_thorarios .n_estado').remove();
                $("#form_thorarios").append('<div class="n_estado"></div>');
                $('#form_thorarios #slct_estado').hide();
                $('#form_thorarios .n_estado').show();
                var est = $('#t_thorarios #estado_'+button.data('id') ).attr("data-estado");
                if(est == 1) est ='Activo';
                else est = 'Inactivo';
                $("#form_thorarios .n_estado").text( est );
            } 
            $('#form_thorarios #slct_estado').val( $('#t_thorarios #estado_'+button.data('id') ).attr("data-estado") );
            $("#form_thorarios").append("<input type='hidden' value='"+button.data('id')+"' name='id'>");
        }

    });

    $('#thorarioModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
      modal.find('.modal-body input').val(''); // busca un input para copiarle texto
    });
});

activarTabla4=function(){
    $("#t_thorarios").dataTable(); // inicializo el datatable
};

Editar=function(){
    if(validaThorarios()){
        Thorarios.AgregarEditarThorario(1);
    }
};

activar=function(id){
    Thorarios.CambiarEstadoThorarios(id,1);
};
desactivar=function(id){
    Thorarios.CambiarEstadoThorarios(id,0);
};

Agregar=function(){
    if(validaThorarios()){
        Thorarios.AgregarEditarThorario(0);
    }
};

validaThorarios=function(){
    $('#form_thorarios [data-toggle="tooltip"]').css("display","none");
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