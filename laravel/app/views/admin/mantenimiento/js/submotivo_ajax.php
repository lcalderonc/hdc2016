<script type="text/javascript">
var submotivosObj=[];
var Submotivos={
    AgregarEditarSubmotivos:function(AE){
        var datos=$("#form_submotivos").serialize().split("txt_").join("").split("slct_").join("");
        var accion="submotivo/crear";
        if(AE==1){
            accion="submotivo/editar";
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
                    $('#t_submotivos').dataTable().fnDestroy();

                    Submotivos.CargarSubmotivos(activarTabla2);
                    Psi.mensaje('success', obj.msj, 6000);
                    $('#submotivoModal .modal-footer [data-dismiss="modal"]').click();
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
    CargarSubmotivos:function(evento){
        $.ajax({
            url         : 'submotivo/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    submotivosObj=obj.datos;
                    evento();
                } else
                    $("#tb_submotivos").html('');
            },
            error: function(){
                $("#tb_submotivos").html('');
                $(".overlay,.loading-img").remove();
            }
        });
    },
    CambiarEstadoSubmotivos:function(id,AD){
        $("#form_submotivos").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_submotivos").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_submotivos").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'submotivo/cambiarestado',
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
                    $('#t_submotivos').dataTable().fnDestroy();
                    Submotivos.CargarSubmotivos(activarTabla2);
                    EstadoMotivoSubmotivo.CargarEstadoMotivoSubmotivo(activarTabla4);
                    Psi.mensaje('success', obj.msj, 6000);
                    $('#submotivoModal .modal-footer [data-dismiss="modal"]').click();
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