<script type="text/javascript">
    $(document).ready(function() {
        $( "#form_actividades" ).submit(function( event ) {
            event.preventDefault();
        });
        Actividad.CargarActividades();
        $('#actividadModal').on('show.bs.modal', function (event) {
            $("#form_actividades").trigger('reset');
            var button = $(event.relatedTarget);
            var titulo = button.data('titulo');
            var actividad_id = button.data('id');

            var modal = $(this);
            modal.find('.modal-title').text(titulo+' Actividad');
            $('#form_actividades [data-toggle="tooltip"]').css("display","none");
//            $("#form_actividades input[type='hidden']").remove();

            if(titulo=='Nuevo') {
                $('#form_actividades #txt_token').val("<?php echo Session::get('s_token');?>"); 
                modal.find('.modal-footer .btn-primary').text('Guardar');
                modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
                $('#form_actividades #slct_estado').val(1);
                $('#form_actividades #txt_nombre').focus();
                $('#form_actividades #slct_estado').show();
                $('#form_actividades .n_estado').remove();
            }
            else {
                var data = {'actividad_id':actividadObj[actividad_id].id};

                modal.find('.modal-footer .btn-primary').text('Actualizar');
                modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
                $('#form_actividades #txt_nombre').val( actividadObj[actividad_id].nombre );
                $('#form_actividades #txt_token').val("<?php echo Session::get('s_token');?>"); 
                //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
                if(eliminarG == 0) {
                    $('#form_actividades .n_estado').remove();
                    $("#form_actividades").append('<div class="n_estado"></div>');
                    $('#form_actividades #slct_estado').hide();
                    $('#form_actividades .n_estado').show();
                    var est = actividadObj[actividad_id].estado;
                    if(est == 1) est ='Activo';
                    else est = 'Inactivo';
                    $("#form_actividades .n_estado").text( est );
                }  
                $('#form_actividades #slct_estado').val( actividadObj[actividad_id].estado );              
                $("#form_actividades").append("<input type='hidden' value='"+actividadObj[actividad_id].id+"' name='id'>");
            }
            $("#form_actividades #slct_estado").trigger("change");
        });
        $('#actividadTipoModal').on('show.bs.modal', function (event) {
            $('#slct_actividad').multiselect('destroy');
            $("#form_actividadesTipo").trigger('reset');
            var button = $(event.relatedTarget);
            var titulo = button.data('titulo');
            var actividad_id = button.data('id');

            var modal = $(this);
            modal.find('.modal-title').text(titulo+' Actividad');
            $('#form_actividadesTipo [data-toggle="tooltip"]').css("display","none");
            $("#form_actividadesTipo input[type='hidden']").remove();

            var data = {estado:1};

            if(titulo=='Nuevo') {

                modal.find('.modal-footer .btn-primary').text('Guardar');
                modal.find('.modal-footer .btn-primary').attr('onClick','AgregarTipo();');
                $('#form_actividadesTipo #slct_estado').val(1);
                $('#form_actividadesTipo #txt_nombreTipo').focus();
                $('#form_actividadesTipo #slct_estado').show();
                $('#form_actividadesTipo .n_estado').remove();
                slctGlobal.listarSlct('actividad','slct_actividad','simple',null,data);
            }
            else {

                var tipoActividad = [""+actividadTipoObj[actividad_id].actividad_id+""];

                modal.find('.modal-footer .btn-primary').text('Actualizar');
                modal.find('.modal-footer .btn-primary').attr('onClick','EditarTipo();');
                $('#form_actividadesTipo #txt_nombreTipo').val( actividadTipoObj[actividad_id].nombre );
                $('#form_actividadesTipo #txt_label').val( actividadTipoObj[actividad_id].label );
                $('#form_actividadesTipo #txt_sla').val( actividadTipoObj[actividad_id].sla );
                $('#form_actividadesTipo #txt_duracion').val( actividadTipoObj[actividad_id].duracion );

                //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
                if(eliminarG == 0) {
                    $('#form_actividadesTipo .n_estado').remove();
                    $("#form_actividadesTipo").append('<div class="n_estado"></div>');
                    $('#form_actividadesTipo #slct_estado').hide();
                    $('#form_actividadesTipo .n_estado').show();
                    var est = actividadTipoObj[actividad_id].estado;
                    if(est == 1) est ='Activo';
                    else est = 'Inactivo';
                    $("#form_actividadesTipo .n_estado").text( est );                    
                } 
                $('#form_actividadesTipo #slct_estado').val( actividadTipoObj[actividad_id].estado );    
                $("#form_actividadesTipo").append("<input type='hidden' value='"+actividadTipoObj[actividad_id].id+"' name='id'>");
                slctGlobal.listarSlct('actividad','slct_actividad','simple',tipoActividad,data);
            }
            $("#form_actividadesTipo #slct_estado").trigger("change");
        });
        $('#myTab a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $("[href=#tb_actividadesTipo]").on('show.bs.tab',function(e){
            Actividad.CargarActividadesTipos();
        });
    });
    HTMLCargarActividad=function(datos){
        var html="";
        $('#t_actividades').dataTable().fnDestroy();

        //PRIVILEGIO AGREGAR
        if(agregarG == 0) {
            $('#nuevo').remove();
        }

        $.each(datos,function(index,data){
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
                "<td>"+estadohtml+"</td>";

             //PRIVILEGIO EDITAR
            if(editarG == 1) { 
                html+='<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#actividadModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a></td>'; 
            } else {
                html+='<td class="editarG"></td>';
            }
            html+="</tr>";
        });
        $("#tb_actividad").html(html);
        if(editarG == 0) $('.editarG').hide();  
        activarTabla();
    };
    HTMLCargarActividadTipos=function(datos){
        var html="";
        $('#t_actividadesTipo').dataTable().fnDestroy();
        //PRIVILEGIO AGREGAR
        if(agregarG == 0) { 
            $('#nuevo').remove();  
        }  
        $.each(datos,function(index,data){
            estadohtml='<span id="'+data.id+'" onClick="activarTipo('+data.id+')" class="btn btn-danger">Inactivo</span>';
            if(data.estado==1){
                estadohtml='<span id="'+data.id+'" onClick="desactivarTipo('+data.id+')" class="btn btn-success">Activo</span>';
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
                "<td>"+data.label+"</td>"+
                "<td>"+data.sla+"</td>"+
                "<td>"+data.duracion+"</td>"+
                "<td>"+estadohtml+"</td>";
             //PRIVILEGIO EDITAR
            if(editarG == 1) { 
                html+='<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#actividadTipoModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a></td>';
            } else {
                html+='<td class="editarG"></td>';
            }
                

            html+="</tr>";
        });
        $("#tb_actividadTipo").html(html);
        if(editarG == 0) $('.editarG').hide();  
        activarTablaTipo();
    };
    activarTabla=function(){
        $("#t_actividades").dataTable();
    };
    activarTablaTipo=function(){
        $("#t_actividadesTipo").dataTable();
    };
    validaActividad=function(){
        $('#form_actividades [data-toggle="tooltip"]').css("display","none");
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
    validaActividadTipo=function(){
        $('#form_actividadesTipo [data-toggle="tooltip"]').css("display","none");
        var a=[];
        a[0]=valida("txt","nombreTipo","");
        a[1]=valida("slct","actividad","");
        a[1]=valida("txt","label","");
        a[2]=valida("txt","sla","");
        a[3]=valida("txt","duracion","");
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
    Agregar=function(){
        if(validaActividad()){
            Actividad.AgregarEditarActividad(0);
        }
    };
    Editar=function(){
        if(validaActividad()){
            Actividad.AgregarEditarActividad(1);
        }
    };

    desactivar=function(id){
        Actividad.CambiarEstadoActividad(id,0);
    };

    activar=function(id){
        Actividad.CambiarEstadoActividad(id,1);
    };

    AgregarTipo=function(){
        if(validaActividadTipo()){
            Actividad.AgregarEditarActividadTipo(0);
        }
    };
    EditarTipo=function(){
        if(validaActividadTipo()){
            Actividad.AgregarEditarActividadTipo(1);
        }
    };

    desactivarTipo=function(id){
        Actividad.CambiarEstadoActividadTipo(id,0);
    };

    activarTipo=function(id){
        Actividad.CambiarEstadoActividadTipo(id,1);
    };
</script>