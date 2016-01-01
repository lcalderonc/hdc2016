<script type="text/javascript">
var Empresas={
    AgregarEditarEmpresa:function(AE){
        var datos=$("#form_empresas").serialize().split("txt_").join("").split("slct_").join("");
        var accion="empresa/crear";
        if(AE==1){
            accion="empresa/editar";
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
                    $('#t_empresas').dataTable().fnDestroy();

                    Empresas.CargarEmpresas(activarTabla);
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#empresaModal .modal-footer [data-dismiss="modal"]').click();
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
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente. Si el problema persiste favor de comunicarse a ubicame@puedesencontrar.com</b>'+
                                '</div>');
            }
        });
    },
    CargarEmpresas:function(evento){
        $.ajax({
            url         : 'empresa/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                var html="";
                var estadohtml="";
                //PRIVILEGIO AGREGAR
                if(agregarG == 0) { 
                    $('#nuevo').remove();  
                } 
                if(obj.rst==1){

                    $.each(obj.datos,function(index,data){
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
                        var es_ec = (data.es_ec==1) ? 'Si':'No';
                        html+="<tr>"+
                            "<td id='nombre_"+data.id+"'>"+data.nombre+"</td>"+
                            "<td id='es_ec_"+data.id+"' data-es_ec='"+data.es_ec+"'>"+es_ec+"</td>"+
                            "<td id='estado_"+data.id+"' data-estado='"+data.estado+"'>"+estadohtml+"</td>";
                        //PRIVILEGIO EDITAR
                        if(editarG == 1) { 
                            html+='<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#empresaModal" data-id="'+data.id+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a></td>';
                        } else {
                            html+='<td class="editarG"></td>';
                        }
                        html+="</tr>";
                    });
                }
                $("#tb_empresas").html(html); 
                if(editarG == 0) $('.editarG').hide();  
                evento();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
            }
        });
    },
    CambiarEstadoEmpresas:function(id,AD){
        $("#form_empresas").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_empresas").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_empresas").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'empresa/cambiarestado',
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
                    $('#t_empresas').dataTable().fnDestroy();
                    Empresas.CargarEmpresas(activarTabla);
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#empresaModal .modal-footer [data-dismiss="modal"]').click();
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
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente. Si el problema persiste favor de comunicarse a ubicame@puedesencontrar.com</b>'+
                                '</div>');
            }
        });
    }
};
</script>