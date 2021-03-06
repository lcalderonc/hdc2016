<script type="text/javascript">
$(document).ready(function() {  
    $( "#form_empresas" ).submit(function( event ) {
        event.preventDefault();
    });
    Empresas.CargarEmpresas(activarTabla);

    $('#empresaModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var titulo = button.data('titulo'); // extrae del atributo data-
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this); //captura el modal
      modal.find('.modal-title').text(titulo+' Empresa');
      $('#form_empresas [data-toggle="tooltip"]').css("display","none");
//      $("#form_empresas input[type='hidden']").remove();

        if(titulo=='Nuevo'){
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_empresas #slct_estado').val(1); 
            $('#form_empresas #txt_nombre').focus();
            $('#form_empresas #slct_estado').show();
            $('#form_empresas .n_estado').remove();
            $('#form_empresas #txt_token').val("<?php echo Session::get('s_token');?>");
        }
        else{
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
            $('#form_empresas #txt_nombre').val( $('#t_empresas #nombre_'+button.data('id') ).text() );
            $('#form_empresas #slct_es_ec').val( $('#t_empresas #es_ec_'+button.data('id') ).attr("data-es_ec") );
            $('#form_empresas #txt_token').val("<?php echo Session::get('s_token');?>");
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                $('#form_empresas .n_estado').remove();
                $("#form_empresas").append('<div class="n_estado"></div>');
                $('#form_empresas #slct_estado').hide();
                $('#form_empresas .n_estado').show();
                var est = $('#t_empresas #estado_'+button.data('id') ).attr("data-estado");
                if(est == 1) est ='Activo';
                else est = 'Inactivo';
                $("#form_empresas .n_estado").text( est );
            }
            $('#form_empresas #slct_estado').val( $('#t_empresas #estado_'+button.data('id') ).attr("data-estado") );
            $("#form_empresas").append("<input type='hidden' value='"+button.data('id')+"' name='id'>");
        }

    });

    $('#empresaModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
      modal.find('.modal-body input').val(''); // busca un input para copiarle texto
    });
});

activarTabla=function(){
    $("#t_empresas").dataTable(); // inicializo el datatable    
};

Editar=function(){
    if(validaEmpresas()){
        Empresas.AgregarEditarEmpresa(1);
    }
};

activar=function(id){
    Empresas.CambiarEstadoEmpresas(id,1);
};
desactivar=function(id){
    Empresas.CambiarEstadoEmpresas(id,0);
};

Agregar=function(){
    if(validaEmpresas()){
        Empresas.AgregarEditarEmpresa(0);
    }
};

validaEmpresas=function(){
    $('#form_empresas [data-toggle="tooltip"]').css("display","none");
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