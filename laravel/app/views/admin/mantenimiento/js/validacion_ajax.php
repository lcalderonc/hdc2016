<script type="text/javascript">
    var search = '';
    var currentPage = 0;
    var ValidacionObj;
    var Validacion={
        CargarValidaciones:function(){
            $.ajax({
                url         : 'configuracion/cargaractivos',
                type        : 'POST',
                cache       : false,
                dataType    : 'json',
                beforeSend : function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                success : function(obj) {
                    var html="";
                    var estadohtml="";
                        //PRIVILEGIO AGREGAR
                    if(agregarG == 0) { 
                        $('.nuevo').remove();  
                    }  
                    if(obj.rst==1){
                        HTMLCargarValidacion(obj.datos);
                        $('#t_validacion').dataTable().fnPageChange(currentPage,true);
                        $('input[type=search]').val(search);
                        $('input[type=search]').trigger('keyup');
                        ValidacionObj=obj.datos;
                    }
                    $(".overlay,.loading-img").remove();
                },
                error: function(){
                    $(".overlay,.loading-img").remove();
                    Psi.mensaje('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.', 6000);
                }
            });
        },
        CargaDetalleValidaciones:function(id,name){
            $.ajax({
                url         : 'configuracion/cargarrelaciones',
                type        : 'POST',
                cache       : false,
                dataType    : 'json',
                data        : {id:id},
                beforeSend : function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                success : function(obj) {
                    var html="";
                    var estadohtml="";
                    if(obj.rest==1){
                        HTMLCargarDetalleValidacion(obj.datos);
                        $("#nombreTabla").html(name);
                        $("#idTabla").val(id);
                        $("#btnRefrescar").attr('onclick',"verDetalle("+id+",'"+name+"')");
                        /*$('#t_validacion').dataTable().fnPageChange(currentPage,true);
                        $('input[type=search]').val(search);
                        $('input[type=search]').trigger('keyup');*/
                        //ValidacionObj=obj.datos;
                    }
                    $(".overlay,.loading-img").remove();
                },
                error: function(){
                    $(".overlay,.loading-img").remove();
                    Psi.mensaje('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.', 6000);
                }
            });
        },
        GetValue:function(id){
            console.log(id);
            $.ajax({
                url         : 'configuracion/cargarvalores',
                type        : 'POST',
                cache       : false,
                dataType    : 'json',
                data        : {id:id},
                beforeSend : function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                success : function(obj) {
                    var html="";
                    var estadohtml="";
                    if(obj.rest==1){

                    }
                    $(".overlay,.loading-img").remove();
                },
                error: function(){
                    $(".overlay,.loading-img").remove();
                    Psi.mensaje('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.', 6000);
                }
            });
        },
        SendValues:function(data,tabla){
            $.ajax({
                url: 'configuracion/guardarvalores',
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: data,
                success: function (obj) {
                    if (obj.rst == 1) {
                        Psi.mensaje('success', obj.msg, 6000);
                        verDetalle(tabla.id,tabla.nombre);
                        $("#resultados").html("");
                        $("#resultadosVacios").html("");
                    }else{
                        $.each(obj.msj,function(index,datos){
                            $("#error_"+index).attr("data-original-title",datos);
                            $('#error_'+index).css('display','');
                        });
                    }
                },
                error: function () {
                    $(".overlay,.loading-img").remove();
                    Psi.mensaje('danger', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
                }
            });
        }
    };
</script>