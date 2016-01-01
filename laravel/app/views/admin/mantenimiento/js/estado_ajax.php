<script type="text/javascript">
var estadosObj=[];
var Estados={
    AgregarEditarEstados:function(AE){
        var datos=$("#form_estados").serialize().split("txt_").join("").split("slct_").join("");
        var accion="estado/crear";
        if(AE==1){
            accion="estado/editar";
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
                    $('#t_estados').dataTable().fnDestroy();

                    Estados.CargarEstados(activarTabla3);
                    Psi.mensaje('success', obj.msj, 6000);
                    $('#estadoModal .modal-footer [data-dismiss="modal"]').click();
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
    CargarEstados:function(evento){
        $.ajax({
            url         : 'estado/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    estadosObj=obj.datos;
                    evento();
                } else
                    $("#tb_estados").html('');
            },
            error: function(){
                $("#tb_estados").html('');
                $(".overlay,.loading-img").remove();
            }
        });
    },
    CambiarEstadoEstados:function(id,AD){
        $("#form_estados").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_estados").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_estados").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'estado/cambiarestado',
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
                    $('#t_estados').dataTable().fnDestroy();
                    Estados.CargarEstados(activarTabla3);
                    EstadoMotivoSubmotivo.CargarEstadoMotivoSubmotivo(activarTabla4);
                    Psi.mensaje('success', obj.msj, 6000);
                    $('#estadoModal .modal-footer [data-dismiss="modal"]').click();
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