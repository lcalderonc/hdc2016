<script type="text/javascript">
var CambiosObj;
var CambioDireccion={
    CargarCambios:function(evento){
        $.ajax({
            url         : 'datos/cargarcambiosdirecciones',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    HTMLCargarCambios(obj.datos);
                    CambiosObj=obj.datos;
                    evento();//filtro de tabla
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.', 6000);

            }
        });
    },
    AgregarEditar:function(AE){
        var datos=$("#form_cambiodir").serialize().split("txt_").join("").split("slct_").join("");
        var accion="datos/insertardirecciones";
        if(AE==1){
            accion="datos/actualizardirecciones";
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
                    $('#t_cambios_direcciones').dataTable().fnDestroy();
                    CambioDireccion.CargarCambios(activarTabla);
                    $('#cambioDireccionModal .modal-footer [data-dismiss="modal"]').click();
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
    CambiarEstado:function(id,AD){
        $("#form_cambiodir").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_cambiodir").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_cambiodir").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'datos/cambiarestado',
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
                    $('#t_cambios_direcciones').dataTable().fnDestroy();
                    CambioDireccion.CargarCambios(activarTabla);
                    $('#cambioDireccionModal .modal-footer [data-dismiss="modal"]').click();
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