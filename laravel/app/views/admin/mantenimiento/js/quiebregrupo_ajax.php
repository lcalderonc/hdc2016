<script type="text/javascript">
var QuiebreGrupos={
    AgregarEditarQuiebreGrupo:function(AE){
        var datos=$("#form_quiebregrupos").serialize().split("txt_").join("").split("slct_").join("");
        var accion="quiebregrupo/crear";
        if(AE==1){
            accion="quiebregrupo/editar";
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
                    $('#t_quiebregrupo').dataTable().fnDestroy();

                    QuiebreGrupos.CargarQuiebreGrupos(activarTabla);
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#quiebregrupoModal .modal-footer [data-dismiss="modal"]').click();
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
    CargarQuiebreGrupos:function(evento){
        $.ajax({
            url         : 'quiebregrupo/cargar',
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
                        //PRIVILEGIO ELIMINAR
                        if(eliminarG == 0) {
                            estadohtml='<span class="">Inactivo</span>';
                            if(data.estado==1){
                                estadohtml='<span class="">Activo</span>';
                            }
                        } 
                        html+="<tr>"+
                            "<td id='nombre_"+data.id+"'>"+data.nombre+"</td>"+
                            "<td id='estado_"+data.id+"' data-estado='"+data.estado+"'>"+estadohtml+"</td>";
                        //PRIVILEGIO EDITAR
                        if(editarG == 1) { 
                            html+='<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#quiebregrupoModal" data-id="'+data.id+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a></td>';
                        } else {
                            html+='<td class="editarG"></td>';
                        }
                        html+="</tr>";
                    });
                }
                $("#tb_quiebregrupos").html(html); 
                if(editarG == 0) $('.editarG').hide();  
                evento();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
            }
        });
    },
    CambiarEstadoQuiebreGrupos:function(id,AD){
        $("#form_quiebregrupos").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_quiebregrupos").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_quiebregrupos").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'quiebregrupo/cambiarestado',
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
                    $('#t_quiebregrupo').dataTable().fnDestroy();
                    QuiebreGrupos.CargarQuiebreGrupos(activarTabla);
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#quiebregrupoModal .modal-footer [data-dismiss="modal"]').click();
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