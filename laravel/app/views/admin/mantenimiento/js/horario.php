<script type="text/javascript">
$(document).ready(function() {
    Horarios.CargarHorarios();
    //slctGlobal.listarSlct('thorario','slct_thorario','simple',null,null);

    $('#horarioModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // captura al boton
        var titulo = button.data('titulo'); // extrae del atributo data-
        horario_id = button.data('id'); //extrae el id del atributo data
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this); //captura el modal
        modal.find('.modal-title').text(titulo+' Horario');
        $('#form_horario [data-toggle="tooltip"]').css("display","none");
        $("#form_horario input[type='hidden']").remove();
        
        if(titulo=='Nuevo'){
            var datos={estado:1};
            slctGlobal.listarSlct('horario','slct_thorario','simple',null,datos);
            $('#form_horario #slct_thorario').val(1);
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar3();');
            $('#form_horario #txt_hora_inicio').attr('onClick','varmin();');
            $('#form_horario #txt_hora_fin').attr('onClick','varmin();');
            $('#form_horario #slct_estado').show();
            $('#form_horario .n_estado').remove();
        }
        else{
            ids=[]; ids.push(horarioObj[horario_id].idthorario);
            slctGlobal.listarSlct('thorario','slct_thorario','simple',ids,null);
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar3();');
            $('#form_horario #txt_horario').val( horarioObj[horario_id].horario );
            $('#form_horario #txt_hora_inicio').val( horarioObj[horario_id].hora_inicio );
			$('#form_horario #txt_hora_fin').val( horarioObj[horario_id].hora_fin );
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                $('#form_horario .n_estado').remove();
                $("#form_horario").append('<div class="n_estado"></div>');
                $('#form_horario #slct_estado').hide();
                $('#form_horario .n_estado').show();
                var est = horarioObj[horario_id].estado;
                if(est == 1) est ='Activo';
                else est = 'Inactivo';
                $("#form_horario .n_estado").text( est );
            }
            $('#form_horario #slct_estado').val( horarioObj[horario_id].estado );
            $("#form_horario").append("<input type='hidden' value='"+horarioObj[horario_id].id+"' name='id'>");
        }
    })////;

    $('#horarioModal').on('hide.bs.modal', function (event) {
        var modal = $(this);
        modal.find('.modal-body input').val('');
        $('#slct_thorario').multiselect('destroy');
    });



});

activarTabla5=function(){
    $("#t_horarios").dataTable(); // inicializo el datatable    
};

Editar3=function(){
    //if(validaHorarios()){
        Horarios.AgregarEditarHorario(1);
    //}
};

activar3=function(id){
    Horarios.CambiarEstadoHorarios(id,1);
};
desactivar3=function(id){
    Horarios.CambiarEstadoHorarios(id,0);
};

Agregar3=function(){
    //if(validaHorarios()){
        Horarios.AgregarEditarHorario(0);
    //}
};

validaHorarios=function(){
    $('#form_horario [data-toggle="tooltip"]').css("display","none");
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
HTMLCargarHorario=function(datos){
    var html="";
    $('#t_horarios').dataTable().fnDestroy();

    $.each(datos,function(index,data){//UsuarioObj
        estadohtml='<span id="'+data.id+'" onClick="activar3('+data.id+')" class="btn btn-danger">Inactivo</span>';
        if(data.estado==1){
            estadohtml='<span id="'+data.id+'" onClick="desactivar3('+data.id+')" class="btn btn-success">Activo</span>';
        }
        //PRIVILEGIO DESACTIVAR
        if(eliminarG == 0) { 
            estadohtml='<span class="">Inactivo</span>';
            if(data.estado==1){
                estadohtml='<span class="">Activo</span>';
            }
        }   
         html+="<tr>"+
            "<td>"+data.horario+"</td>"+
            "<td>"+data.hora_inicio+"</td>"+
			"<td>"+data.hora_fin+"</td>"+
            "<td>"+data.thorario+"</td>"+
            "<td>"+estadohtml+"</td>";
             //PRIVILEGIO EDITAR
            if(editarG == 1) { 
                html+='<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#horarioModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a></td>';
            } else {
                html+='<td class="editarG"></td>';
            }
            

        html+="</tr>";
    });
    $("#tb_horarios").html(html);
    if(editarG == 0) $('.editarG').hide();  
    activarTabla5();
};

varmin=function() {
                //Datemask dd/mm/yyyy

                $('#daterange-btn').daterangepicker(
                        {
                            ranges: {
                                'Today': [moment(), moment()],
                                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                                'Last 7 Days': [moment().subtract('days', 6), moment()],
                                'Last 30 Days': [moment().subtract('days', 29), moment()],
                                'This Month': [moment().startOf('month'), moment().endOf('month')],
                                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                            },
                            startDate: moment().subtract('days', 29),
                            endDate: moment()
                        },
                function(start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }
                );

                //iCheck for checkbox and radio inputs
                
                $(".timepicker").timepicker({
                    showInputs: false
                });
            };
</script>
