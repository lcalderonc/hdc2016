<script type="text/javascript">
$(document).ready(function() {
    //buscar
    $("#btn_buscar").click(function (){
        buscar();
    });
        //PRIVILEGIO AGREGAR
    if(agregarG == 0 || editarG == 0) { 
        $('.nuevo').remove();  
    }  
    
    $('#permisoeventosModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // captura al boton
    var titulo = button.data('titulo'); // extrae del atributo data-
    Permisoevento_id = button.data('id'); //extrae el id del atributo data
    // If necessary, you could initiate an AJAX request here (and then do 
    // the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use 
    // a data binding library or other methods instead.
    var modal = $(this); //captura el modal
    modal.find('.modal-title').text(titulo+' PermisoEventos');
    $('#form_permisoeventos [data-toggle="tooltip"]').css("display","none");
//    $("#form_permisoeventos input[type='hidden']").remove();
    
    var data ={tipo:'1',tipo_persona:PermisoeventoObj[Permisoevento_id].tipo_persona, usuario_id: PermisoeventoObj[Permisoevento_id].id};
    var data_={tipo:'2',tipo_persona:PermisoeventoObj[Permisoevento_id].tipo_persona, usuario_id: PermisoeventoObj[Permisoevento_id].id};
    slctGlobal.listarSlct('eventos','slct_consulta','multiple',null,data);
    slctGlobal.listarSlct('eventos','slct_metodo','multiple',null,data_);  
    modal.find('.modal-footer .btn-primary').text('Actualizar');
    modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
    $('#form_permisoeventos #txt_token').val("<?php echo Session::get('s_token');?>");
    $('#form_permisoeventos #txt_nombre').val( PermisoeventoObj[Permisoevento_id].nombre +
            ' '+PermisoeventoObj[Permisoevento_id].apellido);
    $("#form_permisoeventos").append("<input type='hidden' value='"+
            PermisoeventoObj[Permisoevento_id].id+"' name='id'>");
    $("#form_permisoeventos").append("<input type='hidden' value='"+
            PermisoeventoObj[Permisoevento_id].tipo_persona+"' name='tipo_persona'>");
    });
    

    $('#permisoeventosModal').on('hide.bs.modal', function (event) {
        var modal = $(this);
        modal.find('.modal-body input').val('');
        $('#slct_consulta').multiselect('destroy');
        $('#slct_metodo').multiselect('destroy');
    });
    
    
});
/**
  * buscar
  *
*/

Editar=function(){
Permisoeventos.EditarPermisoEventos();
}
buscar=function(){
    var tipo_persona = $('#slct_tipo_persona').val();
    if (isNaN(tipo_persona) || tipo_persona==='') {
        Psi.mensaje('danger', 'Seleccione Tipo persona', 6000);
    } else {
        Permisoeventos.Cargar(tipo_persona);
    }
};
HTMLCargarPersona=function(datos){
    var html="";
    var tipo='';
    $('#t_personas').dataTable().fnDestroy();

    $.each(datos,function(index,data){//UsuarioObj
        estadohtml='<a class="btn btn-primary btn-sm" data-toggle="modal" \n\
                    data-target="#permisoeventosModal" data-id="'+index+'" \n\
                    data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a>';
        tipo='Usuario';
        
        if(data.tipo_persona==2){
            tipo='Tecnico';
        }
         html+="<tr>"+
            "<td>"+data.apellido+"</td>"+
            "<td>"+data.nombre+"</td>"+
            "<td>"+data.dni+"</td>"+
            "<td>"+tipo+"</td>"+
            "<td>"+data.detalle+"</td>"+
            '<td>'+estadohtml+'</td>';

        html+="</tr>";
    });
    $("#tb_personas").html(html);
    activarTabla();
};

activarTabla=function(){
    $("#t_personas").dataTable(); // inicializo el datatable    
};
</script>
