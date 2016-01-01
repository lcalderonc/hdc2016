<script type="text/javascript">
var search='';
var currentPage = 0;
var Thorarios={
    AgregarEditarThorario:function(AE){
        var datos=$("#form_thorarios").serialize().split("txt_").join("").split("slct_").join("");
        var accion="thorario/crear";
        if(AE==1){
            accion="thorario/editar";
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
                    $('#t_thorarios').dataTable().fnDestroy();

                    Thorarios.CargarThorarios(activarTabla4);
                    $('#thorarioModal .modal-footer [data-dismiss="modal"]').click();
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
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente. Si el problema persiste favor de comunicarse a ubicame@puedesencontrar.com</b>'+
                                '</div>');
            }
        });
    },
    CargarThorarios:function(evento){
        $.ajax({
            url         : 'thorario/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                var html="";
                var estadohtml="";
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
                        html+="<tr>"+
                            "<td id='nombre_"+data.id+"'>"+data.nombre+"</td>"+
							"<td id='minutos_"+data.id+"'>"+data.minutos+"</td>"+
                            "<td id='estado_"+data.id+"' data-estado='"+data.estado+"'>"+estadohtml+"</td>";
                            //PRIVILEGIO EDITAR
                            if(editarG == 1) { 
                                html+='<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#thorarioModal" data-id="'+data.id+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a></td>';
                            } else {
                                html+='<td class="editarG"></td>';
                            }
                            

                        html+="</tr>";
                    });
                }
                $("#tb_thorarios").html(html); 
                if(editarG == 0) $('.editarG').hide(); 
                $(".overlay,.loading-img").remove();
                //activarTabla4();//filtro de tabla
                $('#t_thorarios').dataTable().fnPageChange(currentPage,true);
                $('input[type=search]').val(search);
                $('input[type=search]').trigger('keyup');
            },
            error: function(){
                $(".overlay,.loading-img").remove();
            }
        });
    },
    CambiarEstadoThorarios:function(id,AD){
        $("#form_thorarios").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_thorarios").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_thorarios").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'thorario/cambiarestado',
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
                    $('#t_thorarios').dataTable().fnDestroy();
                    Thorarios.CargarThorarios(activarTabla4);
                    $('#thorarioModal .modal-footer [data-dismiss="modal"]').click();
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