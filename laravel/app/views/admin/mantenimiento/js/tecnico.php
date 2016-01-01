<script type="text/javascript">
$(document).ready(function() {  
    Tecnicos.CargarTecnicos(activarTabla);

    $("#btn_ninguno").tooltip({
        title : 'Si activa: él tecnico podra recibir varias tareas en un horario',
        placement : 'right'
    });

    $("#btn_ninguno").click(function() {
        if ($(this).hasClass('btn btn-default')) {
            $(this).attr('class','btn btn-success');
            $(this).html('ACTIVO');
            ninguno=1;
        } else {
            $(this).attr('class','btn btn-default');
            $(this).html('INACTIVO');
            ninguno=0;
        }
    });
    $('#tecnicoModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // captura al boton
        var titulo = button.data('titulo'); // extrae del atributo data-
        var tecnico_id = button.data('id');
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this); //captura el modal
        modal.find('.modal-title').text(titulo+' Tecnico');
        $('#form_tecnicos [data-toggle="tooltip"]').css("display","none");
        $("#form_tecnicos input[type='hidden']").remove();

        if(titulo=='Nuevo'){
            $("#slct_celula").val(0);
            $("#slct_celula").multiselect('refresh');
            Tecnicos.CargarCelulas('nuevo',null);
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_tecnicos #slct_estado').val(1); 
            $('#form_tecnicos #txt_ape_paterno').focus();
            $('#form_tecnicos #slct_estado').show();
            $('#form_tecnicos .n_estado').remove();
        }
        else{
            empresa_id=tecnicosObj[tecnico_id].empresa_id;
            $("#slct_empresa").multiselect('select',[empresa_id]);
            $("#slct_empresa").multiselect('rebuild');
            $("#slct_empresa").multiselect('refresh');
            filtroSlct("slct_empresa","simple",'E','#slct_celula');

            $("#slct_celula").val(0);
            $("#slct_celula").multiselect('refresh');
            Tecnicos.CargarCelulas(tecnicosObj[tecnico_id].id);

            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
            $('#form_tecnicos #txt_ape_paterno').val( tecnicosObj[tecnico_id].ape_paterno );
            $('#form_tecnicos #txt_ape_materno').val( tecnicosObj[tecnico_id].ape_materno );
            $('#form_tecnicos #txt_nombres').val( tecnicosObj[tecnico_id].nombres );
            $('#form_tecnicos #txt_dni').val( tecnicosObj[tecnico_id].dni );
            $('#form_tecnicos #txt_carnet').val( tecnicosObj[tecnico_id].carnet );
            $('#form_tecnicos #txt_carnet_tmp').val( tecnicosObj[tecnico_id].carnet_tmp );
            $('#form_tecnicos #txt_celular').val( tecnicosObj[tecnico_id].celular );
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                $('#form_tecnicos .n_estado').remove();
                $("#form_tecnicos label:contains('Estado:')").after('<div class="n_estado"></div>');
                $('#form_tecnicos #slct_estado').hide();
                $('#form_tecnicos .n_estado').show();
                var est = tecnicosObj[tecnico_id].estado;
                if(est == 1) est ='Activo';
                else est = 'Inactivo';
                $("#form_tecnicos .n_estado").text( est );
            }
            $('#form_tecnicos #slct_estado').val( tecnicosObj[tecnico_id].estado );            
            $('#form_tecnicos #chk_ninguno').val( tecnicosObj[tecnico_id].ninguno );
            if (tecnicosObj[tecnico_id].ninguno==1) {
                $('#btn_ninguno').attr('class','btn btn-success');
                $('#btn_ninguno').html('ACTIVO');
                ninguno=1;
            } else {
                $('#btn_ninguno').attr('class','btn btn-default');
                $('#btn_ninguno').html('INACTIVO');
                ninguno=0;
            }
            $("#form_tecnicos").append("<input type='hidden' value='"+tecnicosObj[tecnico_id].id+"' name='id'>");
        }
        $('#slct_empresa').change(function() {
            $( "#slct_celula" ).val(0);
            $( "#slct_celula" ).multiselect('refresh');
            celulas_selec=[];
            $("#t_celulasTecnico").html(''); 
        });
        $( "#form_tecnicos #slct_estado" ).trigger('change');
        $( "#form_tecnicos" ).on('change','#slct_estado', function() {
            if ($( "#form_tecnicos #slct_estado" ).val()==1)
                $('#f_celulas').removeAttr('disabled');
            else
                $('#f_celulas').attr('disabled', 'disabled');
        });

    });

    $('#tecnicoModal').on('hide.bs.modal', function (event) {
        var modal = $(this); //captura el modal
        modal.find('.modal-body input').val(''); // busca un input para copiarle texto
        $("#slct_empresa").multiselect('deselectAll', false);
        $("#slct_empresa").multiselect('rebuild');
        $("#slct_empresa").multiselect('refresh');
        $("#slct_celula").multiselect('deselectAll', false);
        $("#slct_celula").multiselect('rebuild');
        $("#slct_celula").multiselect('refresh');
        $("#t_celulasTecnico").html('');
        celulas_selec=[];
        $('#btn_ninguno').attr('class','btn btn-default');
        $('#btn_ninguno').html('INACTIVO');
        ninguno=0;
    });
});

activarTabla=function(){
    var html="", estadohtml="";
    //PRIVILEGIO AGREGAR
    if(agregarG == 0) { 
        $('#nuevo').remove();  
    }  
    $.each(tecnicosObj,function(index,data){
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
            "<td>"+data.ape_paterno+"</td>"+
            "<td>"+data.ape_materno+"</td>"+
            "<td>"+data.nombres+"</td>"+
            "<td>"+data.dni+"</td>"+
            "<td>"+data.carnet+"</td>"+
            "<td>"+data.carnet_tmp+"</td>"+
            "<td>"+data.empresa+"</td>"+
            "<td>"+estadohtml+"</td>";
         //PRIVILEGIO EDITAR
        if(editarG == 1) { 
            html+='<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#tecnicoModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a></td>';
        } else {
            html+='<td class="editarG"></td>';
        }
            
        html+="</tr>";
    });
    $("#tb_tecnicos").html(html);
    if(editarG == 0) $('.editarG').hide();  
    $("#t_tecnicos").dataTable();
};

Editar=function(){
    if(validaTecnicos()){
        Tecnicos.AgregarEditarTecnico(1);
    }
};

activar=function(id){
    Tecnicos.CambiarEstadoTecnicos(id,1);
};
HTMLListarSlct=function(obj){
    var html="";
    $.each(obj.datos, function(i,celula){
        var check = '', celulaId='';
        if (celula.officetrack==1) {
            check = 'checked';
        }
        celulaId=celula.id;
        html+="<li class='list-group-item'><div class='row'>";
        html+="<div class='col-sm-6' id='celula_"+celulaId+"'><h5>"+celula.nombre+"</h5></div>";
        html+="<div class='col-sm-4'>";
            html+="<div class='checkbox'>";
                html+="<label><input type='checkbox'"+check+" name='officetrack"+celulaId+"' id='officetrack"+celulaId+"'>";
        html+="Officetrack</label></div></div>";
        html+='<div class="col-sm-2">';
        html+='<button type="button" id="'+celulaId+'" Onclick="EliminarCelula(this)" class="btn btn-danger btn-sm" >';
        html+='<i class="fa fa-minus fa-sm"></i> </button></div>';
        html+="</div></li>";
        celulas_selec.push(Number(celulaId));
    });
    $("#t_celulasTecnico").html(html); 
};
desactivar=function(id){
    Tecnicos.CambiarEstadoTecnicos(id,0);
};

Agregar=function(){
    if(validaTecnicos()){
        Tecnicos.AgregarEditarTecnico(0);
    }
};
AgregarCelula=function(){
    //añadir registro "opcion" por usuario
    var empresaId=$('#slct_empresa option:selected').val();
    var celulaId=$('#slct_celula option:selected').val();
    var celula=$('#slct_celula option:selected').text();
    var buscar_celula = $('#celula_'+celulaId).text();
    if (celulaId!=='' && celulaId!==undefined) {
        if (buscar_celula==="") {
            //evaluar si selecciono empresa
            if (empresaId!=='' && empresaId !==undefined) {
                var html='';
                html+="<li class='list-group-item'><div class='row'>";
                html+="<div class='col-sm-6' id='celula_"+celulaId+"'><h5>"+celula+"</h5></div>";
                html+="<div class='col-sm-4'>";
                    html+="<div class='checkbox'>";
                        html+="<label><input type='checkbox' name='officetrack"+celulaId+"' id='officetrack"+celulaId+"'>";
                html+="Officetrack</label></div></div>";
                html+='<div class="col-sm-2">';
                html+='<button type="button" id="'+celulaId+'" Onclick="EliminarCelula(this)" class="btn btn-danger btn-sm" >';
                html+='<i class="fa fa-minus fa-sm"></i> </button></div>';
                html+="</div></li>";
                $("#t_celulasTecnico").append(html);
                celulas_selec.push(Number(celulaId));
            } else {
                alert("Seleccione Empresa");
            }

        } else 
            alert("Ya se agrego este celula");
    } else 
        alert("Seleccione Celula");
};
EliminarCelula=function(obj){
    //console.log(obj);
    var valor= obj.id;
    obj.parentNode.parentNode.parentNode.remove();
    var index = celulas_selec.indexOf(Number(valor));
    celulas_selec.splice( index, 1 );
};
validaTecnicos=function(){
    $('#form_tecnicos [data-toggle="tooltip"]').css("display","none");
    var a=[];
    a[0]=validat("txt","nombres","");
    var rpta=true;

    for(i=0;i<a.length;i++){
        if(a[i]===false){
            rpta=false;
            break;
        }
    }
    return rpta;
};
validat=function(inicial,id,v_default){
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