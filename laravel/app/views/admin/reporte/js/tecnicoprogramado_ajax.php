<script type="text/javascript">
var Tecnicos={
    tecnicoProgramado:function(evento){
        var datos=$("#form_tecnico_programado").serialize().split("txt_").join("").split("slct_").join("").split("chk_").join("");
        $.ajax({
            url         : 'reporte/tecnicoprogramado',
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
                    evento(obj.datos,obj.order,obj.fechacabecera,obj.horacabecera,obj.iconos);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                        '<i class="fa fa-ban"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b><?php echo trans("greetings.mensaje_error"); ?></b>'+
                                    '</div>');
            }
        });
    },
    rutaTecnico:function(carnet, dia, color, tecnico_id){

        if (objMarker.tecnico[tecnico_id].tpath != null)
        {
            //Tiene ruta en memoria, mostrar u ocultar
            var accion = objMarker.tecnico[tecnico_id].tpath.map;
            if (accion == null)
            {
                //Mostrar path
                objMarker.tecnico[tecnico_id].tpath.setMap(objRepMap);
                //Mostrar puntos del path
                $.each(objMarker.tecnico[tecnico_id].tpathMrk, function(){
                    this.setMap(objRepMap);
                });
            } else {
                objMarker.tecnico[tecnico_id].tpath.setMap(null);
                $.each(objMarker.tecnico[tecnico_id].tpathMrk, function(){
                    this.setMap(null);
                });
            }

        } else {

            //No tiene ruta en memoria, obtener datos de localizaciones
            $.ajax({
                url         : 'visorgps/codepath',
                type        : 'POST',
                cache       : false,
                dataType    : 'json',
                data        : 'codePath='+carnet+"&pdate="+dia,
                beforeSend : function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                success : function(data) {
                    $(".overlay,.loading-img").remove();

                    rutaDiaTecnico(data, carnet, color, objRepMap, tecnico_id);
                },
                error: function(){
                    $(".overlay,.loading-img").remove();
                    $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                            '<i class="fa fa-ban"></i>'+
                                            '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                            '<b><?php echo trans("greetings.mensaje_error"); ?></b>'+
                                        '</div>');
                }
            });

        }
    },
    listaQuiebre:function(){
        //Poblar listado de quiebres de acuerdo a grupos asignados
        $.ajax({
                url         : 'quiebre/listar',
                type        : 'POST',
                cache       : false,
                dataType    : 'json',
                data        : 'usuario_id=1',
                beforeSend : function() {

                },
                success : function(data) {
                    $.each(data.datos, function (id, val){
                        $("#slct_quiebre").append("<option value=\"" +
                                 val.id +
                                 "\">" +
                                 val.nombre +
                                 "</option>");
                    });
                },
                error: function(){

                }
            });
    }
};
</script>
