<script type="text/javascript">
$(document).ready(function() {
    if(agregarG == 0 && editarG == 0) { 
        $('#guardar').remove();  
    } 
    Cupos.CargarCupos(activarTabla);
 
    $('#myTab a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
          //cuadno hace click en lista actualizar los datos , si hace click en tab TABLA reiniar filtros
          $('#t_cupos').dataTable().fnDestroy();
          Cupos.CargarCupos(activarTabla);
    });
    slctGlobal.listarSlct('zonal','t_slct_zonal','simple',null);
    slctGlobal.listarSlct('empresa','t_slct_empresa','simple',null);
    slctGlobal.listarSlct('quiebregrupo','t_slct_quiebregrupos','simple',null);
    slctGlobal.listarSlct('horariotipo','t_slct_horariotipo','simple',null);

    $('#t_slct_zonal').change(function() {
        zonal_id= $(this).val().substr(4,1);
        $("#tb_cupos2").html('');
        activarTabla2();
    });
    $('#t_slct_empresa').change(function() {
        empresa_id=$(this).val();
        $("#tb_cupos2").html('');
        activarTabla2();
    });
    $('#t_slct_quiebregrupos').change(function() {
        quiebre_grupo_id=$(this).val();
        $("#tb_cupos2").html('');
        activarTabla2();
    });
    $('#t_slct_horariotipo').change(function() {
        horario_tipo_id=$(this).val();
        $("#tb_cupos2").html('');
        var data = {horario_tipo_id:horario_tipo_id};
        Cupos.cargarHorarios(data, horarioHTML);
        //activarTabla2();
    });
    //mover cursor
    $('#tb_cupos2').on('keyup','.edit',function (e){
        var tecla = e.keyCode;
        if(e.keyCode == 13){
            $(this).next('.edit').focus();
            e.preventDefault();
        }
        var dia = $(this).data('dia');
        var tabla = $(this).parent().parent();
        var fila = $(this).parent();
        var horario = fila.data('horario_id');
        var newfil, newcel;
        //tecla = e.keyCode || e.which;
        if( tecla==37 || tecla==38 ||tecla==39 ||tecla==40 ){
            //mover cursor
            if (tecla==37) {//izq
                $(this).prev('.edit').focus();

            } else if (tecla==38) {//arriba
                horario-=1;
                newfil = $('tr[data-horario_id='+horario+']');
                newcel= $(newfil).children('td[data-dia='+dia+']');
                $(newcel).focus();
            } else if (tecla==39) {//derecha
                $(this).next('.edit').focus();
            } else if (tecla==40) {//abajo
                horario+=1;
                newfil = $('tr[data-horario_id='+horario+']');
                newcel= $(newfil).children('td[data-dia='+dia+']');
                $(newcel).focus();
            }
            e.preventDefault();
        }
    });
    $('#guardar').click(function() {
        //guardar
        if (validarSlct()) {
            if (zonal_id===undefined ||
                empresa_id===undefined ||
                quiebre_grupo_id===undefined ||
                horario_tipo_id===undefined
                ) {
                return;
            } else {
                //filtrar solo los cupos modificacod y nuevos
                cupos = _.where(cupos, 
                            {guardar: 1
                            });
                Cupos.updateCupos(cupos);
            }
        }
    });
    //columna
    $('.edit-col').focus(function() {
        gl_capacidad_col = $(this).text();
    });
    $('.edit-col').blur(function() {
        var valor = Number($(this).html());
        if (gl_capacidad_col!=valor){//solo cuando hizo cambios
            var dia=$(this).parent().parent().parent().parent().data('dia');
            $('#tb_cupos2').find('td[data-dia='+dia+']').each(function (rowIndex, r) {
                $(r).trigger( "focus" );
                $(r).html(valor);
                $(r).trigger( "blur" );
            });
        }
    });
    /////filas
    $( "#tb_cupos2" ).on('focus','.edit-row', function() {
        gl_capacidad_row = $(this).text();
    });
    $( "#tb_cupos2" ).on('blur','.edit-row', function() {
        var valor = Number($(this).html());
        if (gl_capacidad_row!=valor){//solo cuando hizo cambios
            $(this).parent().find('td').each(function (rowIndex, r) {
                if (rowIndex >1) {
                    $(r).trigger( "focus" );
                    $(r).html(valor);
                    $(r).trigger( "blur" );
                }
            });
        }
    });
    //celda
    $( "#tb_cupos2" ).on('focus','.edit', function() {
        //var global
        $(this).css('background-color','#EFFF85');
        gl_capacidad = $(this).text();
        //validar
    });
    $( "#tb_cupos2" ).on('blur','.edit', function() {
        $(this).css('background-color','');
        //var dia_id = $(this).data('dia');
        var capacidad = $(this).text();
        if (gl_capacidad!=capacidad){//solo cuando hizo cambios
            //var horario_id = $(this).parent().data("horario_id");
            var capacidad_horario_id = $(this).parent().parent().attr("capacidad_horario_id");
            var horario_id = $(this).parent('tr').data('horario_id');
            var dia_id=$(this).data('dia');//lunes->1
            //buscar en el objeto el id para actualizar
            var id=$(this).attr("id");
            var data;
            //actualizar el campo y volver a cargar la data
            if (id==='') {//nuevo
                //var capacidad_horario_id='';
                data = {
                        horario_id:horario_id,
                        dia_id:dia_id,
                        capacidad:capacidad,
                        capacidad_horario_id:capacidad_horario_id,
                        zonal_id:zonal_id,
                        empresa_id:empresa_id,
                        quiebre_grupo_id:quiebre_grupo_id,
                        horario_tipo_id:horario_tipo_id
                        };
                Cupos.Create(this,data);
            } else {
                data = {
                        id:id,
                        horario_id:horario_id,
                        dia_id:dia_id,
                        capacidad:capacidad,
                        capacidad_horario_id:capacidad_horario_id
                    };
                Cupos.Update(id,data);
            }
        }
    });

    $('#cupoModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // captura al boton
        var titulo = button.data('titulo'); // extrae del atributo data-
        var cupo_id = button.data('id'); //extrae el id del atributo data
        var usuario_id="<?php echo Auth::user()->id; ?>";
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this); //captura el modal
        modal.find('.modal-title').text(titulo+' Cupo');
        $('#form_cupos [data-toggle="tooltip"]').css("display","none");
        $("#form_cupos input[type='hidden']").remove();
        
        //var data = {usuario_id: usuario_id};
        if(titulo=='Nuevo') {
            slctGlobal.listarSlct('quiebregrupo','slct_quiebregrupos','simple',null);
            slctGlobal.listarSlct('empresa','slct_empresa','simple',null);
            slctGlobal.listarSlct('zonal','slct_zonal','simple',null);
            slctGlobal.listarSlct('lista/dia','slct_dia','simple',null);
            slctGlobal.listarSlct('lista/horario','slct_horario','simple',null);
            slctGlobal.listarSlct('horariotipo','slct_horariotipo','simple',null);
            
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_cupos #slct_estado').val(1);
            $('#form_cupos #txt_nombre').focus();
            //eliminar los text
            $('.text').remove();
        }
        else {
            var quiebregrupo_id=cuposObj[cupo_id].quiebre_grupo_id;
            var empresa_id=cuposObj[cupo_id].empresa_id;
            var zonal_id=cuposObj[cupo_id].zonal_id;
            var zonal_select=cuposObj[cupo_id].zonal_select;
            var dia_id=cuposObj[cupo_id].dia_id;
            var horario_id=cuposObj[cupo_id].horario_id;
            var horario_tipo_id=cuposObj[cupo_id].horario_tipo_id;
            $('.text').remove();
            $('#form_cupos #slct_quiebregrupos').after("<div class='text'>"+cuposObj[cupo_id].quiebre_grupo+"</div>");
            $('#form_cupos #slct_quiebregrupos').css('display','none');
            //$('#slct_quiebregrupos').insertBefore(cuposObj[cupo_id].quiebre_grupo);
            $('#form_cupos #slct_empresa').after("<div class='text'>"+cuposObj[cupo_id].empresa+"</div>");
            $('#form_cupos #slct_empresa').css('display','none');
            $('#form_cupos #slct_zonal').after("<div class='text'>"+cuposObj[cupo_id].zonal+"</div>");
            $('#form_cupos #slct_zonal').css('display','none');
            $('#form_cupos #slct_dia').after("<div class='text'>"+cuposObj[cupo_id].dia+"</div>");
            $('#form_cupos #slct_dia').css('display','none');
            $('#form_cupos #slct_horario').after("<div class='text'>"+cuposObj[cupo_id].horario+"</div>");
            $('#form_cupos #slct_horario').css('display','none');
            $('#form_cupos #slct_horariotipo').after("<div class='text'>"+cuposObj[cupo_id].minutos+"</div>");
            $('#form_cupos #slct_horariotipo').css('display','none');

            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
            $('#form_cupos #txt_capacidad').val( cuposObj[cupo_id].capacidad );
            $('#form_cupos #slct_estado').val(  cuposObj[cupo_id].estado );
            $("#form_cupos").append("<input type='hidden' value='"+cuposObj[cupo_id].id+"' name='id'>");
        }
        $('#form_cupos #slct_horariotipo').change(function() {
            if ( $('#form_cupos #slct_horariotipo').val() !== '') {
                $("#form_cupos #slct_horario").html('');
                $("#form_cupos #slct_horario").multiselect('destroy');
                //volver a cargar horario
                var data ={horario_tipo_id:$(this).val()};
                slctGlobal.listarSlct('lista/horario','slct_horario','simple',[horario_id], data);
            }
        });
    });

    $('#cupoModal').on('hide.bs.modal', function (event) {
        var modal = $(this); //captura el modal
        modal.find('.modal-body input').val(''); // busca un input para copiarle texto
        $('#form_cupos #slct_quiebregrupos').multiselect('destroy');
        $('#form_cupos #slct_empresa').multiselect('destroy');
        $("#form_cupos #slct_zonal").multiselect('destroy');
        $("#form_cupos #slct_dia").multiselect('destroy');
        $("#form_cupos #slct_horario").multiselect('destroy');
        $("#form_cupos #slct_horariotipo").multiselect('destroy');
    });
});
soloNumeros=function (e){
    var key = window.Event ? e.which : e.keyCode;
    return (key >= 48 && key <= 57);
};
validarSlct=function(){
    if (zonal_id==='' && zonal_id !==null) {
        alert("Seleccione Zonal");
        $("#slct_zonal").focus();
    } else if (empresa_id===''&& empresa_id !==null) {
        alert("Seleccione Empresa");
        $("#slct_empresa").focus();
    }else if (quiebre_grupo_id===''&& quiebre_grupo_id !==null) {
        alert("Seleccione Grupo de quiebre");
        $("#slct_quiebregrupos").focus();
    }else if (horario_tipo_id===''&& horario_tipo_id !==null) {
        alert("Seleccione Tipo horario");
        $("#slct_horariotipo").focus();
    } else {
        return true;
    }
};
activarTabla2=function(){
    if (validarSlct()) {
        if (zonal_id===undefined ||
            empresa_id===undefined ||
            quiebre_grupo_id===undefined ||
            horario_tipo_id===undefined
            ) {
            return;
        }

        if (horariosObj.length>0) {//existen horarios
            horarioHTML(horariosObj);
        }
    }
};
horarioHTML=function(horariosObj){
    //armar los horarios segun la bd
    var row='';
    $.each(horariosObj,function(index,datos){
        var horario_id=datos.id;
        var horario=datos.nombre;
        
        tr = Templates.trHorario({
            horario_id:horario_id ,
            horario : horario ,
            lunes :'0' ,
            l_id:'',
            martes: '0',
            m_id:'',
            miercoles :'0',
            mi_id:'',
            jueves :'0',
            j_id:'',
            viernes :'0',
            v_id:'',
            sabado :'0',
            s_id:'',
            domingo :'0',
            d_id:''
        });
        row+=tr;
        
    });
    //$("#tb_cupos2").attr('capacidad_horario_id',capacidad_horario_id);
    //cupos='';
    $("#tb_cupos2").html('');
    $("#tb_cupos2").html(row);

    if (validarSlct()) {
        if (zonal_id===undefined ||
            empresa_id===undefined ||
            quiebre_grupo_id===undefined ||
            horario_tipo_id===undefined
            ) {
            return;
        }
        cupos = _.where(cuposObj, 
                            {zonal_id: String(zonal_id),
                             empresa_id: String(empresa_id),
                             quiebre_grupo_id: String(quiebre_grupo_id),
                             horario_tipo_id: String(horario_tipo_id)
                            });
        if (horariosObj.length>0) {//existen horarios
            if (cupos!='undefined' && cupos.length>0 ) {
                cuposHorarioHTML(cupos);
            } else {
                cupos = _.where(cuposObj, 
                            {zonal_id: Number(zonal_id),
                             empresa_id: Number(empresa_id),
                             quiebre_grupo_id: Number(quiebre_grupo_id),
                             horario_tipo_id: Number(horario_tipo_id)
                            });
                if (cupos!='undefined' && cupos.length>0 ) {
                    cuposHorarioHTML(cupos);
                }
            }
        }
    }
};
retornarCupos=function(){
    if (validarSlct()) {
        if (zonal_id===undefined ||
            empresa_id===undefined ||
            quiebre_grupo_id===undefined ||
            horario_tipo_id===undefined
            ) {
            return;
        }
        cupos = _.where(cuposObj, 
                        {zonal_id: String(zonal_id),
                         empresa_id: String(empresa_id),
                         quiebre_grupo_id: String(quiebre_grupo_id),
                         horario_tipo_id: String(horario_tipo_id)
                        });
        
    }
};
cuposHorarioHTML=function(cupos){
    var row='';
    capacidad_horario_id=cupos[0].capacidad_horario_id;

    //cambiar los cupos encontrados
    var table=$("#t_cupos2");
    table.find('tr').each(function (rowIndex, r) {
        if (rowIndex>0) {//no incluir primera fila
            //var cols = [];
            $(this).find('td').each(function (colIndex, c) {
                if (colIndex>0) {//no incluir primera columna
                    var horario_id = $(this).parent('tr').data('horario_id');
                    var dia_id=$(this).data('dia');//lunes->1
                    var cupo = _.where(cupos, 
                            {horario_id: String(horario_id),
                             dia_id: String(dia_id)
                            });
                    var capacidad,id;
                    if (cupo.length>0) {
                        capacidad=Number(cupo[0].capacidad);
                        id=Number(cupo[0].id);
                        $(this).text(capacidad);
                        $(this).attr('id',id);
                    } else {
                        cupo = _.where(cupos, 
                            {horario_id: Number(horario_id),
                             dia_id: Number(dia_id)
                            });
                        if (cupo.length>0) {
                            capacidad=Number(cupo[0].capacidad);
                            id=Number(cupo[0].id);
                            $(this).text(capacidad);
                            $(this).attr('id',id);
                        } else {
                            $(this).text('0');
                            $(this).attr('id','');
                        }
                        
                    }
                }
            });
        }
    });
    $("#tb_cupos2").attr('capacidad_horario_id',capacidad_horario_id);
    cupos=[];
};

activarTabla=function(){
    var html="", estadohtml="";
    //PRIVILEGIO AGREGAR Y EDITAR
    if(agregarG == 0 || editarG == 0) { 
        $('#guardar').remove(); 
    }  
    if(agregarG == 0) { 
        $('.nuevo').remove();   
    }  
    $.each(cuposObj,function(index,data){
        estadohtml='<span id="'+data.id+'" onClick="activar('+data.id+')" class="btn btn-danger btn-xs">Inactivo</span>';
        if(data.estado==1){
            estadohtml='<span id="'+data.id+'" onClick="desactivar('+data.id+')" class="btn btn-success btn-xs">Activo</span>';
        }
        html+="<tr>"+
            "<td>"+data.zonal+"</td>"+
            "<td>"+data.empresa+"</td>"+
            "<td>"+data.quiebre_grupo+"</td>"+
            "<td>"+data.dia+"</td>"+
            "<td>"+data.horario+"</td>"+
            "<td>"+data.capacidad+"</td>"+
            "<td>"+estadohtml+"</td>"+
            '<td><a class="btn btn-primary btn-xs" data-toggle="modal" data-target="#cupoModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-xs"></i> </a></td>';
        html+="</tr>";
    });
    $("#tb_cupos").html(html);
    $("#t_cupos").dataTable();
};

Editar=function(){
    if(validaCupos()){
        Cupos.AgregarEditarCupos(1);
    }
};

activar=function(id){
    Cupos.CambiarEstadoCupos(id,1);
};

desactivar=function(id){
    Cupos.CambiarEstadoCupos(id,0);
};

Agregar=function(){
    if(validaCupos()){
        Cupos.AgregarEditarCupos(0);
    }
};

validaCupos=function(){
    $('#form_cupos [data-toggle="tooltip"]').css("display","none");
    var a=[];
    a[0]=valida("txt","capacidad","");
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