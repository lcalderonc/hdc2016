<script type="text/javascript">
var estadomotivosubmotivoObj=[];
var EstadoMotivoSubmotivo={
    AgregarEditarEstadoMotivoSubmotivos:function(AE){
        var datos=$("#form_estadomotivosubmotivos").serialize().split("txt_").join("").split("slct_").join("");
        var accion="estadomotivosubmotivo/crear";
        if(AE==1){
            accion="estadomotivosubmotivo/editar";
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
                    $('#t_estadomotivosubmotivos').dataTable().fnDestroy();
                    EstadoMotivoSubmotivo.CargarEstadoMotivoSubmotivo(activarTabla4);
                    Psi.mensaje('success', obj.msj, 6000);
                    $('#estadomotivosubmotivoModal .modal-footer [data-dismiss="modal"]').click();
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
    },
    CargarEstadoMotivoSubmotivo:function(evento){
        $.ajax({
            url         : 'estadomotivosubmotivo/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    estadomotivosubmotivoObj=obj.datos;
                    evento();
                } else
                    $("#tb_estadomotivosubmotivos").html('');
            },
            error: function(){
                $("#tb_estadomotivosubmotivos").html('');
                $(".overlay,.loading-img").remove();
            }
        });
    },
    CambiarEstado:function(id,AD){
        $("#form_estadomotivosubmotivos").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_estadomotivosubmotivos").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_estadomotivosubmotivos").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'estadomotivosubmotivo/cambiarestado',
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
                    $('#t_estadomotivosubmotivos').dataTable().fnDestroy();
                    EstadoMotivoSubmotivo.CargarEstadoMotivoSubmotivo(activarTabla4);
                    Psi.mensaje('success', obj.msj, 6000);
                    $('#estadomotivosubmotivoModal .modal-footer [data-dismiss="modal"]').click();
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