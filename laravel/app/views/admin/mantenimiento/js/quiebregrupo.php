<script type="text/javascript">
$(document).ready(function() {  
    $( "#form_quiebregrupos" ).submit(function( event ) {
        event.preventDefault();
    });
    QuiebreGrupos.CargarQuiebreGrupos(activarTabla);

    $('#quiebregrupoModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var titulo = button.data('titulo'); // extrae del atributo data-
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this); //captura el modal
      modal.find('.modal-title').text(titulo+' Grupo de Quiebres');
      $('#form_quiebregrupos [data-toggle="tooltip"]').css("display","none");
      $("#form_quiebregrupos input[type='hidden']").remove();

        if(titulo=='Nuevo'){
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_quiebregrupos #slct_estado').val(1); 
            $('#form_quiebregrupos #txt_nombre').focus();
            $('#form_quiebregrupos #slct_estado').show();
            $('#form_quiebregrupos .n_estado').remove();
        }
        else{
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
            $('#form_quiebregrupos #txt_nombre').val( $('#t_quiebregrupos #nombre_'+button.data('id') ).text() );
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                $('#form_quiebregrupos .n_estado').remove();
                $("#form_quiebregrupos").append('<div class="n_estado"></div>');
                $('#form_quiebregrupos #slct_estado').hide();
                $('#form_quiebregrupos .n_estado').show();
                var est = $('#t_quiebregrupos #estado_'+button.data('id') ).attr("data-estado");
                if(est == 1) est ='Activo';
                else est = 'Inactivo';
                $("#form_quiebregrupos .n_estado").text( est );
            } 
            $('#form_quiebregrupos #slct_estado').val( $('#t_quiebregrupos #estado_'+button.data('id') ).attr("data-estado") );            
            $("#form_quiebregrupos").append("<input type='hidden' value='"+button.data('id')+"' name='id'>");
        }

    });

    $('#quiebregrupoModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
      modal.find('.modal-body input').val(''); // busca un input para copiarle texto
    });
});

activarTabla=function(){
    $("#t_quiebregrupos").dataTable(); // inicializo el datatable    
};

Editar=function(){
    if(validaQuiebreGrupos()){
        QuiebreGrupos.AgregarEditarQuiebreGrupo(1);
    }
};

activar=function(id){
    QuiebreGrupos.CambiarEstadoQuiebreGrupos(id,1);
};
desactivar=function(id){
    QuiebreGrupos.CambiarEstadoQuiebreGrupos(id,0);
};

Agregar=function(){
    if(validaQuiebreGrupos()){
        QuiebreGrupos.AgregarEditarQuiebreGrupo(0);
    }
};

validaQuiebreGrupos=function(){
    $('#form_quiebregrupos [data-toggle="tooltip"]').css("display","none");
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