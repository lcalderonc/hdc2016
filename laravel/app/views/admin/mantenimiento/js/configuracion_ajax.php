<script type="text/javascript">
    var search = '';
    var currentPage = 0;
    var configuracionObj;
    var Configuracion={
        AgregarEditarConfiguracion:function(AE){
            var datos=$("#form_configuracion").serialize().split("txt_").join("").split("slct_").join("");
            var accion="configuracion/crear";
            if(AE==1){
                accion="configuracion/editar";
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
                        search=$('input[type=search]').val();
                        var table = $('#t_configuracion').DataTable();
                        var info = table.page.info();
                        currentPage = info.page;
                        $('#t_configuracion').dataTable().fnDestroy();
                        Configuracion.CargarConfiguracion();
                        $('#t_configuracion').dataTable().fnPageChange(currentPage,true);
                        $('#configuracionModal .modal-footer [data-dismiss="modal"]').click();
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
        CargarConfiguracion:function(){
            $.ajax({
                url         : 'configuracion/cargar',
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
                        $('#t_configuracion').dataTable().fnPageChange(currentPage,true);
                        $('input[type=search]').val(search);
                        $('input[type=search]').trigger('keyup');
                        configuracionObj=obj.datos;
                    }
                    $(".overlay,.loading-img").remove();
                },
                error: function(){
                    $(".overlay,.loading-img").remove();
                    Psi.mensaje('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.', 6000);
                }
            });
        },
        CambiarEstadoConfiguracion:function(id,AD){
            $("#form_configuracion").append("<input type='hidden' value='"+id+"' name='id'>");
            $("#form_configuracion").append("<input type='hidden' value='"+AD+"' name='estado'>");
            var datos=$("#form_configuracion").serialize().split("txt_").join("").split("slct_").join("");
            $.ajax({
                url         : 'configuracion/cambiarestado',
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
                        var table = $('#t_configuracion').DataTable();
                        var info = table.page.info();
                        currentPage = info.page;
                        $('#t_configuracion').dataTable().fnDestroy();
                        Configuracion.CargarConfiguracion();
                        $('#t_configuracion').dataTable().fnPageChange(currentPage,true);
                        $('#configuracionModal .modal-footer [data-dismiss="modal"]').click();
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