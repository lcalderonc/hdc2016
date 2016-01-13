<script type="text/javascript">
$(document).ready(function() {

    $( "#form_estados" ).submit(function( event ) {
        event.preventDefault();
    });
    
    Estados.CargarEstados(activarTabla3);

    $('#estadoModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var titulo = button.data('titulo'); // extrae del atributo data-
      var estado_id = button.data('id'); //extrae el id del atributo data
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this); //captura el modal
      modal.find('.modal-title').text(titulo+' Estado');
      $('#form_estados [data-toggle="tooltip"]').css("display","none");
//      $("#form_estados input[type='hidden']").remove();

        if(titulo=='Nuevo') {
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar3();');
            $('#form_estados #slct_estado3').val(1); 
            $('#form_estados #txt_nombre3').focus();
            $('#form_estados #slct_estado3').show();
            $('#form_estados .n_estado').remove();
            $('#form_estados #txt_token').val("<?php echo Session::get('s_token');?>");
        }
        else {
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar3();');
            $('#form_estados #txt_nombre3').val( estadosObj[estado_id].nombre );
            $('#form_estados #txt_token').val("<?php echo Session::get('s_token');?>");
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                $('#form_estados .n_estado').remove();
                $("#form_estados").append('<div class="n_estado"></div>');
                $('#form_estados #slct_estado3').hide();
                $('#form_estados .n_estado').show();
                var est = estadosObj[estado_id].estado;
                if(est == 1) est ='Activo';
                else est = 'Inactivo';
                $("#form_estados .n_estado").text( est );
            }
            $('#form_estados #slct_estado3').val( estadosObj[estado_id].estado );
            
            //$('#form_motivos #txt_nombre').val( $('#t_motivos #nombre_'+button.data('id') ).text() );
            //$('#form_motivos #slct_estado').val( $('#t_motivos #estado_'+button.data('id') ).attr("data-estado") );
            $("#form_estados").append("<input type='hidden' value='"+estadosObj[estado_id].id+"' name='id3'>");
        }

    });

    $('#estadoModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
      modal.find('.modal-body input').val(''); // busca un input para copiarle texto
    });
});

activar3=function(id){
    Estados.CambiarEstadoEstados(id,1);
};

desactivar3=function(id){
    Estados.CambiarEstadoEstados(id,0);
};

activarTabla3=function(){
    var html="", estadohtml="";
        //PRIVILEGIO AGREGAR
    if(agregarG == 0) { 
        $('.nuevo').remove();  
    } 

    $.each(estadosObj,function(index,data){
        estadohtml='<span id="'+data.id+'" onClick="activar3('+data.id+')" class="btn btn-danger btn-xs">Inactivo</span>';
        if(data.estado==1){
            estadohtml='<span id="'+data.id+'" onClick="desactivar3('+data.id+')" class="btn btn-success btn-xs">Activo</span>';
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
            html+='<td><a class="btn btn-primary btn-xs" data-toggle="modal" data-target="#estadoModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-xs"></i> </a></td>';
        } else {
            html+='<td class="editarG"></td>';
        }
        html+="</tr>";
    });
    $("#tb_estados").html(html);
    if(editarG == 0) $('.editarG').hide(); 
    $("#t_estados").dataTable();
};

Editar3=function(){
    if(validaEstados3()){
        Estados.AgregarEditarEstados(1);
    }
};

Agregar3=function(){
    if(validaEstados3()){
        Estados.AgregarEditarEstados(0);
    }
};

validaEstados3=function(){
    $('#form_estados [data-toggle="tooltip"]').css("display","none");
    var a=[];
    a[0]=valida3("txt","nombre3","");
    var rpta=true;

    for(i=0;i<a.length;i++){
        if(a[i]===false){
            rpta=false;
            break;
        }
    }
    return rpta;
};

valida3=function(inicial,id,v_default){
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