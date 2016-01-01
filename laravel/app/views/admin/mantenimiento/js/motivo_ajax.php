<script type="text/javascript">
var cuposObj=[];
var Motivos={
    AgregarEditarMotivos:function(AE){
        var datos=$("#form_motivos").serialize().split("txt_").join("").split("slct_").join("");
        var accion="motivo/crear";
        if(AE==1){
            accion="motivo/editar";
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
                    $('#t_motivos').dataTable().fnDestroy();

                    Motivos.CargarMotivos(activarTabla);
                    Psi.mensaje('success', obj.msj, 6000);
                    $('#motivoModal .modal-footer [data-dismiss="modal"]').click();
                } else { 
                    $.each(obj.msj,function(index,datos){
                        $("#error_"+index).attr("data-original-title",datos);
                        $('#error_'+index).css('display','');
                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
               /* $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente. Si el problema persiste favor de comunicarse a ubicame@puedesencontrar.com</b>'+
                                '</div>');*/
            }
        });
    },
    CargarMotivos:function(evento){
        $.ajax({
            url         : 'motivo/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    cuposObj=obj.datos;
                    evento();
                } else
                    $("#tb_motivos").html('');
            },
            error: function(){
                $("#tb_motivos").html('');
                $(".overlay,.loading-img").remove();
            }
        });
    },
    CambiarEstadoMotivos:function(id,AD){
        $("#form_motivos").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_motivos").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_motivos").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'motivo/cambiarestado',
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
                    $('#t_motivos').dataTable().fnDestroy();
                    $('#t_estadomotivosubmotivos').dataTable().fnDestroy();
                    Motivos.CargarMotivos(activarTabla);
                    EstadoMotivoSubmotivo.CargarEstadoMotivoSubmotivo(activarTabla4);
                    Psi.mensaje('success', obj.msj, 6000);
                    $('#motivoModal .modal-footer [data-dismiss="modal"]').click();
                } else { 
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
    }
};
</script>