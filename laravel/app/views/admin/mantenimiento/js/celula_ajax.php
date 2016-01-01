<script type="text/javascript">
var celulaObj;
var Celulas={
    AgregarEditarCelula:function(AE){
        var datos=$("#form_celulas").serialize().split("txt_").join("").split("slct_").join("");
        var accion="celula/crear";
        if(AE==1){
            accion="celula/editar";
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
                    $('#t_celulas').dataTable().fnDestroy();
                    Celulas.CargarCelulas();
                    $('#celulaModal .modal-footer [data-dismiss="modal"]').click();
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
    CargarCelulas:function(){
        $.ajax({
            url         : 'celula/cargar',
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
                    HTMLCargarCelula(obj.datos);
                    celulaObj=obj.datos;
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.', 6000);
            }
        });
    },
    CambiarEstadoCelulas:function(id,AD){
        $("#form_celulas").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_celulas").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_celulas").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'celula/cambiarestado',
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
                    $('#t_celulas').dataTable().fnDestroy();
                    Celulas.CargarCelulas();
                    $('#celulaModal .modal-footer [data-dismiss="modal"]').click();
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