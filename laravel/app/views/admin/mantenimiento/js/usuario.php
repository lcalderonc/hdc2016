<script type="text/javascript">
var submodulos_id=[];
$(document).ready(function() {
    Usuario.CargarUsuarios(activarTabla);
    var data = {estado: 1,mantenimiento:1};
    slctGlobal.listarSlct('zonal','slct_zonal','simple',[],data,0);
    slctGlobal.listarSlct('perfil','slct_perfil','simple',null,null);
    slctGlobal.listarSlct('area','slct_area','simple',null,null);
    slctGlobal.listarSlct('modulo','slct_modulos','simple');

    $("#show_hide_grupoquiebre").click(function (){
        var btn_value = $(this).attr("value");
        if ( btn_value=='-' )
        {
            $("#t_restriccionquiebre").slideUp();
            $(this).attr("value", "+");
        } else {
            $("#t_restriccionquiebre").slideDown();
            $(this).attr("value", "-");
        }
    });
    $("#show_hide_zonales").click(function (){
        var btn_value = $(this).attr("value");
        if ( btn_value=='-' )
        {
            $("#t_zonales").slideUp();
            $(this).attr("value", "+");
        } else {
            $("#t_zonales").slideDown();
            $(this).attr("value", "-");
        }
    });
    $('#usuarioModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var titulo = button.data('titulo');
        usuario_id = button.data('id');//array

        var modal = $(this);
        modal.find('.modal-title').text(titulo+' Usuario');
        $('#form_usuarios [data-toggle="tooltip"]').css("display","none");
        $("#form_usuarios input[type='hidden']").remove();


        var funciones = {change:ChangeGrupoQuiebre, success:successGrupoQuiebre};

        
        $("#slct_zonal").val(0);
        $("#slct_zonal").multiselect('refresh');
        if (titulo=='Nuevo') {
            slctGlobal.listarSlct('quiebregrupo','slct_quiebregrupos','multiple',null,null,0,0,0,0,0,funciones);
            slctGlobal.listarSlct('empresa','slct_empresas','multiple',null,null);
            slctGlobal.listarSlct('empresa','slct_empresa','simple',null,null);
            Usuario.CargarZonales('nuevo',null);
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_usuarios #slct_estado').val(1); 
            $('#form_usuarios #txt_nombre').focus();
            $('#form_usuarios #slct_estado').show();
            $('#form_usuarios .n_estado').remove();
        }
        else {
            var data = {usuario_id: UsuarioObj[usuario_id].id};
            Usuario.CargarSubmodulo(UsuarioObj[usuario_id].id); //no es multiselect
            slctGlobal.listarSlct('quiebregrupo','slct_quiebregrupos','multiple',null,data,0,0,0,0,0,funciones);
            slctGlobal.listarSlct('empresa','slct_empresas','multiple',null,data); //cargarEmpresas

            $('#slct_perfil').multiselect('select', UsuarioObj[usuario_id].perfil_id);
            $('#slct_perfil').multiselect('rebuild');

            slctGlobal.listarSlct('empresa','slct_empresa','simple',[UsuarioObj[usuario_id].empresa_id], data);
            $('#slct_area').multiselect('select', UsuarioObj[usuario_id].area_id);
            $('#slct_area').multiselect('rebuild');

            Usuario.CargarZonales(UsuarioObj[usuario_id].id);
            $('#txt_password').val(''); // Limpia la caja al editar
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                $('#form_usuarios .n_estado').remove();
                $("#form_usuarios label:contains('Estado:')").after('<div class="n_estado"></div>');
                $('#form_usuarios #slct_estado').hide();
                $('#form_usuarios .n_estado').show();
                var est = UsuarioObj[usuario_id].estado;
                if(est == 1) est ='Activo';
                else est = 'Inactivo';
                $("#form_usuarios .n_estado").text( est );
            } 
            $('#form_usuarios #slct_estado').val( UsuarioObj[usuario_id].estado );
            
            $('#form_usuarios #slct_sexo').val( UsuarioObj[usuario_id].sexo );

            if(titulo!='Editar'){
                $('#form_usuarios #txt_nombre').val( '' );
                $('#form_usuarios #txt_apellido').val( '' );
                $('#form_usuarios #txt_usuario').val( '' );
                $('#form_personas #txt_password').val( '' );
                $('#form_usuarios #txt_dni').val( '' );
                $('#form_usuarios #txt_email').val( '' );
                $('#form_usuarios #txt_celular').val( '' );
                modal.find('.modal-footer .btn-primary').text('Guardar');
                modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
                $('#form_usuarios #slct_estado').val(1); 
                $('#form_usuarios #txt_nombre').focus();
            }
            else{
                modal.find('.modal-footer .btn-primary').text('Actualizar');
                modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
                $('#form_usuarios #txt_nombre').val( UsuarioObj[usuario_id].nombre );
                $('#form_usuarios #txt_apellido').val( UsuarioObj[usuario_id].apellido );
                $('#form_usuarios #txt_usuario').val( UsuarioObj[usuario_id].usuario);
                $('#form_personas #txt_password').val( '' );
                $('#form_usuarios #txt_dni').val( UsuarioObj[usuario_id].dni );
                $('#form_usuarios #txt_email').val( UsuarioObj[usuario_id].email );
                $('#form_usuarios #txt_celular').val( UsuarioObj[usuario_id].celular );
                $("#form_usuarios").append("<input type='hidden' value='"+UsuarioObj[usuario_id].id+"' name='id'>");
            }
        }
        $( "#form_usuarios #slct_estado" ).trigger('change');
        $( "#form_usuarios #slct_estado" ).change(function() {
            if ($( "#form_usuarios #slct_estado" ).val()==1) {  
                $('#f_permisos_modulos').removeAttr('disabled');
            }
            else {
                $('#f_permisos_modulos').attr('disabled', 'disabled');
            }
        });
        $('#slct_submodulos').change(function() {
            if ( $('#slct_modulos').val() === '') {
                $('#slct_submodulos').multiselect('deselectAll', false);
                $('#slct_submodulos').multiselect('refresh');
            }
        });
        $('#t_submoduloUsuario tbody').on( 'click', 'tr', function () {
            var clas =$(this).attr("class").split(' ');
            if ( clas[1]==='selected' ) {
                $(this).removeClass('selected');
            }
            else {
                table.$('tr.selected').removeClass('selected');
                $(this).addClass('selected');
            }
        });
        $("#show_hide_grupoquiebre").val('+');
        $("#show_hide_zonales").val('+');
        $("#t_restriccionquiebre").slideUp();
    });

    $('#usuarioModal').on('hide.bs.modal', function (event) {
        var modal = $(this);
        modal.find('.modal-body input').val('');
        $('#slct_empresas').multiselect('destroy');
        $('#slct_quiebregrupos').multiselect('destroy');
        $('#slct_modulos option:contains(.::Seleccione::.)').attr("selected","selected");
        $('#slct_modulos').multiselect('rebuild');
        $('#slct_perfil option:contains(.::Seleccione::.)').attr("selected","selected");
        $('#slct_perfil').multiselect('rebuild');
        zonales_selec=[];
        $('#slct_empresa').multiselect('destroy');
        $('#slct_area option:contains(.::Seleccione::.)').attr("selected","selected");
        $('#slct_area').multiselect('rebuild');
        $("#slct_zonal").multiselect('deselectAll', false);
        $("#slct_zonal").multiselect('rebuild');
        $("#slct_zonal").multiselect('refresh');
        $("#t_submoduloUsuario").html('');
        modulos_selec=[];
    });

});

activarTabla=function(){
    //$("#t_usuarios").dataTable(); // inicializo el datatable    
    $('#t_usuarios thead th').each( function () {
        var title = $('#t_usuarios thead th').eq( $(this).index() ).text();
        if (title=='Perfil' ) {
            $(this).html( '<input id="filter_perfil" type="text" placeholder="Search '+title+'" />' );
        } else if ( title=='Empresa'){
            $(this).html( '<input id="filter_empresa" type="text" placeholder="Search '+title+'" />' );
        }
    } );
    $("#t_usuarios").dataTable();
    $( '#filter_perfil').on( 'keyup change', function () {
        $('#t_usuarios').DataTable().column( 4 ).search(
            $( '#filter_perfil').val(),
            true,
            true
        ).draw();
    } );
    $(  '#filter_empresa' ).on( 'keyup change', function () {
        $('#t_usuarios').DataTable().column( 5 ).search(
            $( '#filter_empresa').val(),
            true,
            true
        ).draw();
    } );
};


CheckUncheckall =  function(chk, con) {
    var master = "#chkAll"+con;
    var checks = ".opcion"+con;
    var marcado = $(master).prop("checked");
    if(marcado){
        $(checks).prop("checked", true);
    } else {
        $(checks).prop("checked", false);
    }
};
CheckUncheckall2 =  function(chk, con) {
    var master = "#chkAll2"+con;
    var checks = ".opcion2"+con;
    var marcado = $(master).prop("checked");
    if(marcado){
        $(checks).prop("checked", true);
    } else {
        $(checks).prop("checked", false);
    }
};
CheckUncheckall3 =  function(chk, con) {
    var master = "#chkAll3"+con;
    var checks = ".opcion3"+con;
    var marcado = $(master).prop("checked");
    if(marcado){
        $(checks).prop("checked", true);
    } else {
        $(checks).prop("checked", false);
    }
};

Editar=function(){
    if(validaUsuarios()){
        Usuario.AgregarEditarUsuario(1);
    }
};

activar=function(id){
    Usuario.CambiarEstadoUsuarios(id,1);
};
desactivar=function(id){
    Usuario.CambiarEstadoUsuarios(id,0);
};

Agregar=function(){
    if(validaUsuarios()){
        Usuario.AgregarEditarUsuario(0);
    }
};
AgregarSubmodulo=function(){
    //añadir registro "opcion" por usuario
    var modulo_id=$('#slct_modulos option:selected').val();
    var modulo=$('#slct_modulos option:selected').text();
    var buscar_modulo = $('#modulo_'+modulo_id).text();
    if (modulo_id!=='') {
        if (buscar_modulo==="") {

            var html='';
            html+="<li class='list-group-item'><div class='row'>";
            html+="<div class='col-sm-4' id='modulo_"+modulo_id+"'><h5>"+modulo+"</h5></div>";

            html+="<div class='col-sm-6'>";
            html+="<select class='form-control' multiple='multiple' name='slct_submodulos"+modulo_id+"[]' id='slct_submodulos"+modulo_id+"'></select></div>";
            var envio = {modulo_id: modulo_id};

            html+='<div class="col-sm-1">';
            html+='<button type="button" id="'+modulo_id+'" Onclick="EliminarSubmodulo(this)" class="btn btn-danger btn-sm" >';
            html+='<i class="fa fa-minus fa-sm"></i> </button></div>';

html+='<div class="col-sm-1">';
html+='<button type="button" value="+" Onclick="CrearPrivilegios('+modulo_id+')" class="btn btn-warning btn-sm" title="Privilegios">';
html+='<i class="fa fa-key fa-sm"></i> </button>';
html+='</div>';

html+='<div class="row"><div class="col-sm-12">';
html+='<div class="box" style="border-top: 0px solid #c1c1c1!important;display: none;" id="t_privilegios'+modulo_id+'"><div class="box-body">';
html+='<table class="table table-bordered">';
html+='<thead><tr><th>Submodulo</th><th style="width: 15px">Agregar</th><th style="width: 15px">Editar</th><th style="width: 15px">Desactivar</th></tr></thead>';
html+='<tbody id="tb_privilegios'+modulo_id+'"></tbody>';
html+='</table>';
html+='</div></div>';
html+='</div></div>';

            html+="</div></li>";

            $("#t_submoduloUsuario").append(html);
            slctGlobal.listarSlct('submodulo','slct_submodulos'+modulo_id,'multiple',null,envio);
            modulos_selec.push(modulo_id);
        } else
            Psi.mensaje('danger', 'Ya se agrego este modulo', 6000);
    } else
        Psi.mensaje('danger', 'Seleccione Modulo', 6000);
};
ChangeGrupoQuiebre=function(grupo_quiebre_id,checked){
    var grupo_quiebre=$("#slct_quiebregrupos option[value='"+grupo_quiebre_id+"']" ).text();

    if (checked) {
        $("#show_hide_grupoquiebre").val('-');
        var html='';
        if (grupo_quiebre_id===undefined) {
            //selecionar toods
            $( "#slct_quiebregrupos option:selected" ).each(function() {
                var text =$( this ).text();
                var id = $( this ).val();

                html+="<li class='list-group-item'><div class='row'>";
                html+="<div class='col-sm-4' id='grupoquiebre_"+id+"'><h5>"+text+"</h5></div>";
                html+="<div class='col-sm-6'>";
                html+="<select class='form-control' multiple='multiple' name='slct_quiebres"+id+"[]' id='slct_quiebres"+id+"'></select></div>";
                var envio = {grupo_quiebre_id: id};
                
                html+="</div></li>";
                slctGlobal.listarSlct('quiebre','slct_quiebres'+id,'multiple',null,envio);
            });
            $("#t_restriccionquiebre").html(html);
            $("#t_restriccionquiebre").slideDown();
            $('.show_hide_filtros').attr("value", "-");
        } else {

            html+="<li class='list-group-item'><div class='row'>";
            html+="<div class='col-sm-4' id='grupoquiebre_"+grupo_quiebre_id+"'><h5>"+grupo_quiebre+"</h5></div>";

            html+="<div class='col-sm-6'>";
            html+="<select class='form-control' multiple='multiple' name='slct_quiebres"+grupo_quiebre_id+"[]' id='slct_quiebres"+grupo_quiebre_id+"'></select></div>";
            var envio = {grupo_quiebre_id: grupo_quiebre_id};
            
            html+="</div></li>";
            $("#t_restriccionquiebre").append(html);
            slctGlobal.listarSlct('quiebre','slct_quiebres'+grupo_quiebre_id,'multiple',null,envio);
        }
    } else {
        $("#show_hide_grupoquiebre").val('+');
        if (grupo_quiebre_id===undefined) {
            $("#t_restriccionquiebre").html('');
        } else {
            $("#grupoquiebre_"+grupo_quiebre_id ).parent().parent().remove();
            
        }
    }

};
EliminarSubmodulo=function(obj){
    //console.log(obj);
    var valor= obj.id;
    obj.parentNode.parentNode.parentNode.remove();
    var index = modulos_selec.indexOf(valor);
    modulos_selec.splice( index, 1 );
};

CargarPrivilegios=function(idModulo){
    //console.log(obj);
    var valor= idModulo;
    var idUsuario = UsuarioObj[usuario_id].id;
    var formulario=$("#slct_submodulos"+idModulo).serialize().split("slct_").join(""); 

    var btn_value = $("#t_privilegios"+valor).attr("value");
        if ( btn_value=='-' )
        {
            $("#t_privilegios"+valor).slideUp();
            $("#t_privilegios"+valor).attr("value", "+");
        } else {
            Usuario.CargarPrivilegio(idUsuario, idModulo,formulario); //Solo cargo los privilegios cuando estan sin desplegarse
            $("#t_privilegios"+valor).slideDown();
            $("#t_privilegios"+valor).attr("value", "-");
        }
};

CrearPrivilegios=function(idModulo){
    //console.log(obj);
    var valor= idModulo;
  //  var idUsuario = UsuarioObj[usuario_id].id;
    var formulario=$("#slct_submodulos"+idModulo).serialize().split("slct_").join(""); 

    var btn_value = $("#t_privilegios"+valor).attr("value");
        if ( btn_value=='-' )
        {
            $("#t_privilegios"+valor).slideUp();
            $("#t_privilegios"+valor).attr("value", "+");
        } else {
          //  Usuario.CrearPrivilegio(formulario); //Solo cargo los privilegios cuando estan sin desplegarse
/**/
            var html=""; var con = Math.round(Math.random()*10);;
            var agregar = "", editar = "", eliminar = "";
            var selText  = [];
            var data = formulario.split("&"); //corto el combobox serializado
            var variable = 0;
            var con2 = 0;

            html+='<tr>';
            html+='<td></td>';
            html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox" id="chkAll'+con+'" onclick="javascript:CheckUncheckall(this,'+con+' );">Todos</th></td>';
            html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox" id="chkAll2'+con+'" onclick="javascript:CheckUncheckall2(this,'+con+' );">Todos</td>';
            html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox" id="chkAll3'+con+'" onclick="javascript:CheckUncheckall3(this,'+con+' );">Todos</td>';
            html+='</tr>';

            $("#slct_submodulos"+idModulo+" option:selected").each(function () {
                selText.push($(this).text());
            });
            
            $.each(data, function(i,id){
                var comparar = id.split("submodulos"+idModulo+"%5B%5D=");
                if(comparar[1]) {
                    html+='<tr>';
                    html+='<td>'+selText[con2]+'</td>';
                    html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox opcion'+con+'" value="1" name="privilegio'+comparar[1]+'[]" " checked></td>';
                    html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox opcion2'+con+'" value="2" name="privilegio'+comparar[1]+'[]" " checked> </td>';
                    html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox opcion3'+con+'" value="3" name="privilegio'+comparar[1]+'[]" " checked></td>';
                    html+='</tr>';
                }
                con2++;
            });
            $("#tb_privilegios"+idModulo).html(html); 

/**/
            $("#t_privilegios"+valor).slideDown();
            $("#t_privilegios"+valor).attr("value", "-");
        }
};


AgregarZonal=function(){
    //añadir registro "opcion" por usuario
    //var empresaId=$('#slct_empresa option:selected').val();
    var zonalId=$('#slct_zonal option:selected').val();
    var zonal=$('#slct_zonal option:selected').text();
    var buscar_zonal = $('#zonal_'+zonalId).text();
    if (zonalId!=='' && zonalId!==undefined) {
        if (buscar_zonal==="") {
            //evaluar si selecciono empresa
            //if (empresaId!=='' && empresaId !==undefined) {
            var html='';
            html+="<li class='list-group-item'><div class='row'>";
            html+="<div class='col-sm-6' id='zonal_"+zonalId+"'><h5>"+zonal+"</h5></div>";
            html+="<div class='col-sm-4'>";
                html+="<div class='checkbox'>";
                    html+="<label><input type='radio' name='pertenece' id='pertenece' value='"+zonalId+"'>";
            html+="Pertenece</label></div></div>";
            html+='<div class="col-sm-2">';
            html+='<button type="button" id="'+zonalId+'" Onclick="EliminarZonal(this)" class="btn btn-danger btn-sm" >';
            html+='<i class="fa fa-minus fa-sm"></i> </button></div>';
            html+="</div></li>";
            $("#t_zonales").append(html);
            zonales_selec.push(Number(zonalId));
        } else 
            Psi.mensaje('danger', 'Ya se agrego este zonal', 6000);
    } else 
        Psi.mensaje('danger', 'seleccione zonal', 6000);

    $("#t_restriccionquiebre").slideUp();
    $(this).attr("value", "+");
};
EliminarZonal=function(obj){
    //console.log(obj);
    var valor= obj.id;
    obj.parentNode.parentNode.parentNode.remove();
    var index = zonales_selec.indexOf(Number(valor));
    zonales_selec.splice( index, 1 );
};
HTMLListarSlct=function(obj){
    var html="";
    $.each(obj.datos, function(i,zonal){
        var check = '', zonalId='';
        if (zonal.pertenece==1) {
            check = 'checked';
        }
        zonalId=zonal.id;
        html+="<li class='list-group-item'><div class='row'>";
        html+="<div class='col-sm-6' id='zonal_"+zonalId+"'><h5>"+zonal.nombre+"</h5></div>";
        html+="<div class='col-sm-4'>";
            html+="<div class='checkbox'>";
                html+="<label><input type='radio'"+check+" name='pertenece' id='pertenece' value='"+zonalId+"'>";
        html+="Pertenece</label></div></div>";
        html+='<div class="col-sm-2">';
        html+='<button type="button" id="'+zonalId+'" Onclick="EliminarZonal(this)" class="btn btn-danger btn-sm" >';
        html+='<i class="fa fa-minus fa-sm"></i> </button></div>';
        html+="</div></li>";
        zonales_selec.push(Number(zonalId));
    });
    $("#t_zonales").html(html); 
};
validaUsuarios=function(){
    $('#form_usuarios [data-toggle="tooltip"]').css("display","none");
    var a=[];
    a[0]=validar("txt","nombre","");
    var rpta=true;

    for(i=0;i<a.length;i++){
        if(a[i]===false){
            rpta=false;
            break;
        }
    }
    return rpta;
};

validar=function(inicial,id,v_default){
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

HTMLCargarUsuario=function(datos){
    var html="";
    $('#t_usuario').dataTable().fnDestroy();
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
            "<td>"+data.apellido+"</td>"+
            "<td>"+data.nombre+"</td>"+
            "<td>"+data.usuario+"</td>"+
            "<td>"+data.dni+"</td>"+
            "<td>"+data.perfil+"</td>"+
            "<td>"+data.empresa+"</td>"+
            "<td>"+estadohtml+"</td>";
             //PRIVILEGIO EDITAR
            if(editarG == 1) { 
                html+='<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#usuarioModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a>'+
                      '&nbsp;<a class="btn bg-navy btn-sm" data-toggle="modal" data-target="#usuarioModal" data-id="'+index+'" data-titulo="Nuevo - Clonar"><i class="fa fa-copy fa-lg"></i> </a></td>';
            } else {
                html+='<td class="editarG"></td>';
            }
            

        html+="</tr>";
    });
    $("#tb_usuarios").html(html);
    if(editarG == 0) $('.editarG').hide();  
    activarTabla();
};
successGrupoQuiebre=function(datos){
    //cargar los combos de quiebres
    var html='';
    $( "#slct_quiebregrupos option:selected" ).each(function() {
        var text =$( this ).text();
        var id = $( this ).val();

        html+="<li class='list-group-item'><div class='row'>";
        html+="<div class='col-sm-4' id='grupoquiebre_"+id+"'><h5>"+text+"</h5></div>";
        html+="<div class='col-sm-6'>";
        html+="<select class='form-control' multiple='multiple' name='slct_quiebres"+id+"[]' id='slct_quiebres"+id+"'></select></div>";
        var envio = {grupo_quiebre_id: id, user_id:UsuarioObj[usuario_id].id};
        
        html+="</div></li>";
        slctGlobal.listarSlct('quiebre','slct_quiebres'+id,'multiple',null,envio);
    });
    $("#t_restriccionquiebre").html(html);
};
</script>
