<script type="text/javascript">
    var actividadObj;
    var actividadTipoObj;
    var currentPage = 0;
    var search= '';
    var Actividad={
        CargarActividades:function(){
            $.ajax({
                url         : 'actividad/cargar',
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
                        HTMLCargarActividad(obj.datos);
                        $('#t_actividades').dataTable().fnPageChange(currentPage,true);
                        $('input[type=search]').val(search);
                        $('input[type=search]').trigger('keyup');
                        actividadObj=obj.datos;
                    }
                    $(".overlay,.loading-img").remove();
                },
                error: function(){
                    $(".overlay,.loading-img").remove();
                    Psi.mensaje('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.', 6000);
                }
            });
        },
        AgregarEditarActividad:function(AE){
            var datos=$("#form_actividades").serialize().split("txt_").join("").split("slct_").join("");
            var accion="actividad/crear";
            if(AE==1){
                accion="actividad/editar";
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
                        var table = $('#t_actividades').DataTable();
                        var info = table.page.info();
                        currentPage = info.page;
                        $('#t_actividades').dataTable().fnDestroy();
                        Actividad.CargarActividades();
                        $('#t_actividades').dataTable().fnPageChange(currentPage,true);
                        $('#actividadModal .modal-footer [data-dismiss="modal"]').click();
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
        CambiarEstadoActividad:function(id,AD){
            $("#form_actividades").trigger('reset');
            $("#form_actividades").append("<input type='hidden' value='"+id+"' name='id'>");
            $("#form_actividades").append("<input type='hidden' value='"+AD+"' name='estado'>");
            var datos=$("#form_actividades").serialize().split("txt_").join("").split("slct_").join("");
            $.ajax({
                url         : 'actividad/cambiarestado',
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
                        var table = $('#t_actividades').DataTable();
                        var info = table.page.info();
                        currentPage = info.page;
                        $('#t_actividades').dataTable().fnDestroy();
                        Actividad.CargarActividades();
                        $('#t_actividades').dataTable().fnPageChange(currentPage,true);
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
        },
        CargarActividadesTipos:function(){
            $.ajax({
                url         : 'actividad/cargartipos',
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
                        HTMLCargarActividadTipos(obj.datos);
                        $('#tb_actividadesTipo input[type=search]').val(search);
                        $('#tb_actividadesTipo input[type=search]').trigger('keyup');
                        $('#t_actividadesTipo').dataTable().fnPageChange(currentPage,true);
                        actividadTipoObj=obj.datos;
                    }
                    $(".overlay,.loading-img").remove();
                },
                error: function(){
                    $(".overlay,.loading-img").remove();
                    Psi.mensaje('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.', 6000);
                }
            });
        },
        AgregarEditarActividadTipo:function(AE){
            var datos=$("#form_actividadesTipo").serialize().split("txt_").join("").split("slct_").join("");
            var accion="actividad/creartipo";
            if(AE==1){
                accion="actividad/editartipo";
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
                        search=$('#tb_actividadesTipo input[type=search]').val();
                        var table = $('#t_actividadesTipo').DataTable();
                        var info = table.page.info();
                        currentPage = info.page;
                        $('#t_actividadesTipo').dataTable().fnDestroy();
                        Actividad.CargarActividadesTipos();
                        $('#t_actividadesTipo').dataTable().fnPageChange(currentPage,true);
                        $('#actividadTipoModel .modal-footer [data-dismiss="modal"]').click();
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
        CambiarEstadoActividadTipo:function(id,AD){
            $("#form_actividadesTipo").trigger('reset');
            $("#form_actividadesTipo").append("<input type='hidden' value='"+id+"' name='id'>");
            $("#form_actividadesTipo").append("<input type='hidden' value='"+AD+"' name='estado'>");
            var datos=$("#form_actividadesTipo").serialize().split("txt_").join("").split("slct_").join("");
            $.ajax({
                url         : 'actividad/cambiarestadotipo',
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
                        search=$('#tb_actividadesTipo input[type=search]').val();
                        var table = $('#t_actividadesTipo').DataTable();
                        var info = table.page.info();
                        currentPage = info.page;
                        $('#t_actividadesTipo').dataTable().fnDestroy();
                        Actividad.CargarActividadesTipos();
                        $('#t_actividadesTipo').dataTable().fnPageChange(currentPage,true);
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