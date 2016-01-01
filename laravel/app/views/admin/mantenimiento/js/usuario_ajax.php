<script type="text/javascript">
var zonales_selec=[],usuario_id, modulos_selec=[], UsuarioObj;
var Usuario={
    MisDatos:function(){
        var datos=$("#form_misdatos").serialize().split("txt_").join("").split("slct_").join("");
        var accion="usuario/misdatos";

        $.ajax({
            url         : accion,
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    Psi.mensaje('success', obj.msj, 6000);
                    $("#txt_password,#txt_newpassword,#txt_confirm_new_password").val('');
                }
                else{
                    $.each(obj.msj,function(index,datos){
                        $("#error_"+index).attr("data-original-title",datos);
                        $('#error_'+index).css('display',''); 
                        $("#txt_"+index+",#slct_"+index).focus();
                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
            }
        });
    },
    CargarZonales:function(usuario_id){
        $.ajax({
            url         : 'zonal/listar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : {'usuario_id':usuario_id},
            success : function(obj) {
                if(obj.rst==1){
                    HTMLListarSlct(obj);
                }
            }
        });
    },
    AgregarEditarUsuario:function(AE){
        $("#form_usuarios input[name='zonales_selec']").remove();
        $("#form_usuarios input[name='modulos_selec']").remove();
        $("#form_usuarios").append("<input type='hidden' value='"+zonales_selec+"' name='zonales_selec'>");
        $("#form_usuarios").append("<input type='hidden' value='"+modulos_selec+"' name='modulos_selec'>");

        var datos=$("#form_usuarios").serialize().split("txt_").join("").split("slct_").join("");

        var accion="usuario/crear";
        if(AE==1){
            accion="usuario/editar";
        }

        $.ajax({
            url         : accion,
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    $('#t_usuarios').dataTable().fnDestroy();

                    Usuario.CargarUsuarios(activarTabla);

                    $('#usuarioModal .modal-footer [data-dismiss="modal"]').click();
                    Psi.mensaje('success', obj.msj, 6000);
                }
                else{ 
                    $.each(obj.msj,function(index,datos){
                        $("#error_"+index).attr("data-original-title",datos);
                        $('#error_'+index).css('display','');
                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
            }
        });
    },
    CargarUsuarios:function(evento){
        $.ajax({
            url         : 'usuario/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                slctGlobal.listarSlct('modulo','slct_modulos','simple');//para que cargue antes el modulo
            },
            success : function(obj) {
                if(obj.rst==1){
                    HTMLCargarUsuario(obj.datos);
                    UsuarioObj=obj.datos;
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.', 6000);

            }
        });
    },
    CargarSubmodulo:function(usuario_id){
        $.ajax({
            url         : 'usuario/cargarsubmodulos',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : {usuario_id:usuario_id},
            async       : false,
            beforeSend : function() {
                
            },
            success : function(obj) {
                //CARGAR opciones
                if(obj.datos[0].DATA !== null){
                    var modulos = obj.datos[0].DATA.split("|"); 

                    var html="";
                    var con =0;
                    $.each(modulos, function(i,submodulo){
                        var data = submodulo.split("-");
                        con += 1;
                        html+="<li class='list-group-item'><div class='row'>";
                        html+="<div class='col-sm-4' id='modulo_"+data[0]+"'><h5>"+$("#slct_modulos option[value=" +data[0] +"]").text()+"</h5></div>";
                        var submodulos = data[1].split(",");
                        html+="<div class='col-sm-6'><select class='form-control' multiple='multiple' name='slct_submodulos"+data[0]+"[]' id='slct_submodulos"+data[0]+"' Onclick='CrearPrivilegios("+data[0]+")'></select></div>";
                        var envio = {modulo_id: data[0]};
                        slctGlobal.listarSlct('submodulo','slct_submodulos'+data[0],'multiple',submodulos,envio);

                        html+='<div class="col-sm-1">';
                        html+='<button type="button" id="'+data[0]+'" Onclick="EliminarSubmodulo(this)" class="btn btn-danger btn-sm" >';
                        html+='<i class="fa fa-minus fa-sm"></i> </button></div>';

                        html+='<div class="col-sm-1">';
                        html+='<button type="button" value="+" Onclick="CargarPrivilegios('+data[0]+')" class="btn btn-warning btn-sm" title="Privilegios">';
                        html+='<i class="fa fa-key fa-sm"></i> </button>';
                        html+='</div>';

                        html+='<div class="row"><div class="col-sm-12">';
                        html+='<div class="box" style="border-top: 0px solid #c1c1c1!important;display: none;" id="t_privilegios'+data[0]+'"><div class="box-body">';
                        html+='<table class="table table-bordered" id="tabla">';
                        html+='<thead><tr><th>Submodulo</th>'; 
                        html+='<th style="width: 30px">Agregar</th>';
                        html+='<th style="width: 30px">Editar</th>';
                        html+='<th style="width: 30px">Desactivar</th></tr></thead>';
                        html+='<tbody id="tb_privilegios'+data[0]+'"></tbody>';
                        html+='</table>';
                        html+='</div></div>';
                        html+='</div></div>';

                        html+='</div></li>';

                        modulos_selec.push(data[0]);
                    });
                    $("#t_submoduloUsuario").html(html); 
                }
            },
            error: function(){
            }
        });
    },
    CargarPrivilegio:function(usuario_id, modulo_id,formulario){
        $.ajax({
            url         : 'usuario/cargarprivilegios',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : {usuario_id:usuario_id, modulo_id:modulo_id},
            async       : false,
            beforeSend : function() {
                
            },
            success : function(obj) {
                //CARGAR privilegios                    
                    var html=""; var con = Math.round(Math.random()*10);;
                    var agregar = "", editar = "", eliminar = "";

                    var data = formulario.split("&"); //corto el combobox serializado
                    var variable = 0;
                    html+='<tr>';
                        html+='<td></td>';
                        html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox" id="chkAll'+con+'" onclick="javascript:CheckUncheckall(this,'+con+' );">Todos</th></td>';
                        html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox" id="chkAll2'+con+'" onclick="javascript:CheckUncheckall2(this,'+con+' );">Todos</td>';
                        html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox" id="chkAll3'+con+'" onclick="javascript:CheckUncheckall3(this,'+con+' );">Todos</td>';
                        html+='</tr>';

                  //  var arr_nombre = $("#slct_submodulos"+modulo_id+" option:selected");
                 //   alert(arr_nombre[0].text());
                    var selText  = [];
                    $("#slct_submodulos"+modulo_id+" option:selected").each(function () {
                        selText.push($(this).text());
                    });
                    //console.log(selText[0]);
                    var con2 = 0;
                    $.each(data, function(i,id){
                        var comparar = id.split("submodulos"+modulo_id+"%5B%5D=");
                        
                        $.each(obj.datos, function(rst,datos){
                            if(datos.agregar == 1){ agregar = 'checked'; } else { agregar = ''; }
                            if(datos.editar == 1) { editar = 'checked'; } else { editar = ''; }
                            if(datos.eliminar == 1) { eliminar = 'checked'; } else { eliminar = ''; }
            
                            //alert(comparar[1]);
                            if(comparar[1] == datos.submodulo_id) {
                                html+='<tr>';
                                html+='<td>'+datos.nombre+'</td>';
                                html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox opcion'+con+'" value="1" name="privilegio'+datos.submodulo_id+'[]" '+agregar+'></td>';
                                html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox opcion2'+con+'" value="2" name="privilegio'+datos.submodulo_id+'[]" '+editar+'></td>';
                                html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox opcion3'+con+'" value="3" name="privilegio'+datos.submodulo_id+'[]" '+eliminar+'></td>';
                                html+='</tr>';
                                variable = comparar[1];

                                return false;
                            } else {
                                variable = datos.submodulo_id;
                            }
                        });
    
    
                        if(comparar[1]) {
                            if(comparar[1] != variable) {
                                    html+='<tr bgcolor="#E0F8F1">';
                                    html+='<td>'+selText[con2]+'</td>';
                                    html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox opcion'+con+'" value="1" name="privilegio'+comparar[1]+'[]" " checked></td>';
                                    html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox opcion2'+con+'" value="2" name="privilegio'+comparar[1]+'[]" " checked> </td>';
                                    html+='<td style="width: 15px" align="center"><input type="checkbox" class="checkbox opcion3'+con+'" value="3" name="privilegio'+comparar[1]+'[]" " checked></td>';
                                    html+='</tr>';
                            }
                        }
                        con2++;
                    });
                    $("#tb_privilegios"+modulo_id).html(html); 
            },
            error: function(){
            }
        });
    },
    CambiarEstadoUsuarios:function(id,AD){
        $("#form_usuarios").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_usuarios").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_usuarios").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'usuario/cambiarestado',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    $('#t_usuarios').dataTable().fnDestroy();
                    Usuario.CargarUsuarios(activarTabla);

                    $('#usuarioModal .modal-footer [data-dismiss="modal"]').click();
                    Psi.mensaje('success', obj.msj, 6000);
                }
                else{ 
                    $.each(obj.msj,function(index,datos){
                        $("#error_"+index).attr("data-original-title",datos);
                        $('#error_'+index).css('display','');
                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.', 6000);

            }
        });

    }
};
</script>