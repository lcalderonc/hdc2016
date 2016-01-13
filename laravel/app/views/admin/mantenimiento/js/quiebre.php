<script type="text/javascript">
$(document).ready(function() {
    Quiebres.CargarQuiebres();
    slctGlobal.listarSlct('quiebregrupo','slct_quiebregrupos','simple',null,null);

    $('#quiebreModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // captura al boton
        var titulo = button.data('titulo'); // extrae del atributo data-
        quiebre_id = button.data('id'); //extrae el id del atributo data
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this); //captura el modal
        modal.find('.modal-title').text(titulo+' Quiebre');
        $('#form_quiebres [data-toggle="tooltip"]').css("display","none");
//        $("#form_quiebres input[type='hidden']").remove();
        
        if(titulo=='Nuevo'){
            
            slctGlobal.listarSlct('actividad','slct_actividad','multiple',null,null);
            slctGlobal.listarSlct('motivo','slct_motivo','multiple',null,null);
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_quiebres #slct_estado').val(1);
            $('#form_quiebres #txt_nombre').focus();
            $('#form_quiebres #slct_estado').show();
            $('#form_quiebres .n_estado').remove();
            $('#form_quiebres #txt_token').val("<?php echo Session::get('s_token');?>");
        }
        else{
            var grupo_quiebre_id=quiebreObj[quiebre_id].grupo_quiebre_id;
            var data={quiebre_id: quiebreObj[quiebre_id].id};
            slctGlobal.listarSlct('actividad','slct_actividad','multiple',null,data);
            slctGlobal.listarSlct('motivo','slct_motivo','multiple',null,data);

            $('#slct_quiebregrupos').multiselect('select', grupo_quiebre_id);
            $('#slct_quiebregrupos').multiselect('rebuild');

            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
            $('#form_quiebres #txt_nombre').val( quiebreObj[quiebre_id].nombre );
            $('#form_quiebres #txt_apocope').val( quiebreObj[quiebre_id].apocope );
            $('#form_quiebres #txt_token').val("<?php echo Session::get('s_token');?>");
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                $('#form_quiebres .n_estado').remove();
                $("#form_quiebres").append('<div class="n_estado"></div>');
                $('#form_quiebres #slct_estado').hide();
                $('#form_quiebres .n_estado').show();
                var est = quiebreObj[quiebre_id].estado;
                if(est == 1) est ='Activo';
                else est = 'Inactivo';
                $("#form_quiebres .n_estado").text( est );
            }
            $('#form_quiebres #slct_estado').val( quiebreObj[quiebre_id].estado );
            $("#form_quiebres").append("<input type='hidden' value='"+quiebreObj[quiebre_id].id+"' name='id'>");
        }
        $("#form_quiebres #slct_estado").trigger("change");
        $( "#form_quiebres #slct_estado" ).change(function() {
            /*if ($( "#form_quiebres #slct_estado" ).val()==1)
                $('#form_quiebres #slct_actividad').multiselect('enable');
            else
                $('#form_quiebres #slct_actividad').multiselect('disable');*/
        });
    });

    $('#quiebreModal').on('hide.bs.modal', function (event) {
        var modal = $(this);
        modal.find('.modal-body input').val('');
        //reconstruye  multiselect
        $('#slct_actividad').multiselect('destroy');
        //$('#slct_quiebregrupos').multiselect('destroy');
        $('#slct_quiebregrupos option:contains(.::Seleccione::.)').attr("selected","selected");
        $('#slct_quiebregrupos').multiselect('rebuild');
        $('#slct_motivo').multiselect('destroy');
    });
   
});

activarTabla=function(){
    $("#t_quiebres").dataTable(); // inicializo el datatable    
};

Editar=function(){
    if(validaQuiebres()){
        Quiebres.AgregarEditarQuiebre(1);
    }
};

activar=function(id){
    Quiebres.CambiarEstadoQuiebres(id,1);
};
desactivar=function(id){
    Quiebres.CambiarEstadoQuiebres(id,0);
};

Agregar=function(){
    if(validaQuiebres()){
        Quiebres.AgregarEditarQuiebre(0);
    }
};

validaQuiebres=function(){
    $('#form_quiebre [data-toggle="tooltip"]').css("display","none");
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
HTMLCargarQuiebre=function(datos){
    var html="";
    $('#t_quiebres').dataTable().fnDestroy();
    //PRIVILEGIO AGREGAR
    if(agregarG == 0) { 
        $('#nuevo').remove();  
    }  

    $.each(datos,function(index,data){//UsuarioObj
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
            "<td>"+data.apocope+"</td>"+
            "<td>"+data.grupo+"</td>"+
            "<td>"+estadohtml+"</td>";
             //PRIVILEGIO EDITAR
            if(editarG == 1) { 
                html+='<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#quiebreModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a></td>';
            } else {
                html+='<td class="editarG"></td>';
            }
        html+="</tr>";
    });
    $("#tb_quiebres").html(html);
    if(editarG == 0) $('.editarG').hide();  
    activarTabla();
};
</script>
