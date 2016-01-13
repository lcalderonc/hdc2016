<script type="text/javascript">
$(document).ready(function() {
    EstadoMotivoSubmotivo.CargarEstadoMotivoSubmotivo(activarTabla4);
    slctGlobal.listarSlct('motivo','slct_motivo','simple',null);
    slctGlobal.listarSlct('submotivo','slct_submotivo','simple',null);
    //slctGlobal.listarSlct('estado','t_slct_estado','simple',null);
    slctGlobal.listarSlct('lista/estados','slct_estados','simple',null);

    $('#estadomotivosubmotivoModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var titulo = button.data('titulo'); // extrae del atributo data-
      var estadomotivosubmotivo_id = button.data('id'); //extrae el id del atributo data
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this); //captura el modal
      modal.find('.modal-title').text(titulo+' Matriz PSI');
      $('#form_estadomotivosubmotivos [data-toggle="tooltip"]').css("display","none");
//      $("#form_estadomotivosubmotivos input[type='hidden']").remove();

        if(titulo=='Nuevo') {
           $('#slct_motivo').val('');
           $('#slct_motivo').multiselect('refresh'); 
           $('#slct_submotivo').val('');
           $('#slct_submotivo').multiselect('refresh'); 
           $('#slct_estados').val('');
           $('#slct_estados').multiselect('refresh'); 
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar4();');
            $('#form_estadomotivosubmotivos #slct_estado4').val(1); 
            $('#form_estadomotivosubmotivos #slct_descripcion').val(1); 
            $('#form_estadomotivosubmotivos #slct_estado4').show();
            $('#form_estadomotivosubmotivos .n_estado').remove();
            $('#form_estadomotivosubmotivos #txt_token').val("<?php echo Session::get('s_token');?>");
        }
        else {
            var data = {estadomotivosubmotivo_id: estadomotivosubmotivoObj[estadomotivosubmotivo_id].id};
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar4();');

            var tecnico = estadomotivosubmotivoObj[estadomotivosubmotivo_id].req_tecnico;

            $('#slct_motivo').multiselect('select', estadomotivosubmotivoObj[estadomotivosubmotivo_id].motivo_id);
            $('#slct_submotivo').multiselect('select', estadomotivosubmotivoObj[estadomotivosubmotivo_id].submotivo_id);
            $('#slct_estados').multiselect('select', estadomotivosubmotivoObj[estadomotivosubmotivo_id].estado_id);

            $('#form_estadomotivosubmotivos #slct_descripcion').val( tecnico );
            $('#form_estadomotivosubmotivos #txt_token').val("<?php echo Session::get('s_token');?>");
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                $('#form_estadomotivosubmotivos .n_estado').remove();
                $("#form_estadomotivosubmotivos").append('<div class="n_estado"></div>');
                $('#form_estadomotivosubmotivos #slct_estado4').hide();
                $('#form_estadomotivosubmotivos .n_estado').show();
                var est = estadomotivosubmotivoObj[estadomotivosubmotivo_id].estado;
                if(est == 1) est ='Activo';
                else est = 'Inactivo';
                $("#form_estadomotivosubmotivos .n_estado").text( est );
            }
            $('#form_estadomotivosubmotivos #slct_estado4').val( estadomotivosubmotivoObj[estadomotivosubmotivo_id].estado );
            $("#form_estadomotivosubmotivos").append("<input type='hidden' value='"+estadomotivosubmotivoObj[estadomotivosubmotivo_id].id+"' name='id4'>");
        }

    });

    $('#estadomotivosubmotivoModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
      modal.find('.modal-body input').val(''); // busca un input para copiarle texto

      $('#slct_motivo option:contains(.::Seleccione::.)').attr("selected","selected");
      $('#slct_motivo').multiselect('rebuild');
      $('#slct_submotivo option:contains(.::Seleccione::.)').attr("selected","selected");
      $('#slct_submotivo').multiselect('rebuild');      
      $('#slct_estados option:contains(.::Seleccione::.)').attr("selected","selected");
      $('#slct_estados').multiselect('rebuild');

    });
});

activar4=function(id){
    EstadoMotivoSubmotivo.CambiarEstado(id,1);
};

desactivar4=function(id){
    EstadoMotivoSubmotivo.CambiarEstado(id,0);
};

activarTabla4=function(){
    var html="", estadohtml="";
    //PRIVILEGIO AGREGAR
    if(agregarG == 0) { 
        $('.nuevo').remove();  
    }  
    $.each(estadomotivosubmotivoObj,function(index,data) {
        estadohtml='<span id="'+data.id+'" onClick="activar4('+data.id+')" class="btn btn-danger btn-xs">Inactivo</span>';
        if(data.estado==1) {
            estadohtml='<span id="'+data.id+'" onClick="desactivar4('+data.id+')" class="btn btn-success btn-xs">Activo</span>';
        }
        //comprobacion de que motivo, submotivo y estado esten activos
        if(data.estado_motivo==0) {
            estadohtml='<span id="'+data.id+'" class="btn btn-danger disabled btn-xs">Inactivo</span>';
        }
        if(data.estado_submotivo==0) {
            estadohtml='<span id="'+data.id+'" class="btn btn-danger disabled btn-xs">Inactivo</span>';
        }
        if(data.estado_estado==0) {
            estadohtml='<span id="'+data.id+'" class="btn btn-danger disabled btn-xs">Inactivo</span>';
        }
        //PRIVILEGIO DESACTIVAR
        if(eliminarG == 0) {
            estadohtml='<span class="">Inactivo</span>';
            if(data.estado==1){
                estadohtml='<span class="">Activo</span>';
            }
        }    
        var variable = "";
        switch (data.req_tecnico) {
          case 1:
            variable = "Agendamiento con técnico";
             break;
          case 2:
          variable = "Agendamiento sin técnico";
             break;
          case 3:
            variable = "Asignación automática";
             break;
          case 9:
            variable = "Inhabilitar gestión";
             break;
          case 0:
            variable = "Sin asignación";
             break;
          default:
            //Statements executed when none of the values match the value of the expression
             break;
        }

        html+="<tr>"+
            "<td>"+data.motivo+"</td>"+
            "<td>"+data.submotivo+"</td>"+
            "<td>"+data.estados+"</td>"+
         /*   "<td>"+data.req_tecnico+"</td>"+
            "<td>"+data.req_horario+"</td>"+*/
            "<td>"+variable+"</td>"+
            "<td>"+estadohtml+"</td>";
         //PRIVILEGIO EDITAR
        if(editarG == 1) { 
            html+='<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#estadomotivosubmotivoModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a></td>'; 
        } else {
            html+='<td class="editarG"></td>';
        }
            
        html+="</tr>";
    });
    $("#tb_estadomotivosubmotivos").html(html);
    if(editarG == 0) $('.editarG').hide(); 
    $("#t_estadomotivosubmotivos").dataTable();
};

Editar4=function(){
        EstadoMotivoSubmotivo.AgregarEditarEstadoMotivoSubmotivos(1);
};

Agregar4=function(){
   // if(validaEstados()){
        EstadoMotivoSubmotivo.AgregarEditarEstadoMotivoSubmotivos(0);
  //  }
};

validaEstados=function(){
    $('#form_estadomotivosubmotivos [data-toggle="tooltip"]').css("display","none");
    var a=[];
    a[0]=valida4("txt","nombre4","");
    var rpta=true;

    for(i=0;i<a.length;i++){
        if(a[i]===false){
            rpta=false;
            break;
        }
    }
    return rpta;
};

valida4=function(inicial,id,v_default){
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