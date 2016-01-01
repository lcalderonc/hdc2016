<script type="text/javascript">
var Officetrack={
    cargarOfficetrack:function(evento,variables){
        $.ajax({
            url         : 'officetrack/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : variables,
            beforeSend : function() {                
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){                    
                    evento(obj.datos);
                }  
                $(".overlay,.loading-img").remove();
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
    crearImagenes:function(variables){
        $.ajax({
            url         : 'imagen/imagen',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : variables,
            beforeSend : function() {
            },
            success : function() {
            },
            error: function(){
            }
        });
    },
    cargarDetalleOfficetrack:function(evento,variables,paso){
        $.ajax({
            url         : 'officetrack/cargardetalle',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : variables,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){                    
                    evento(obj.datos,paso);
                }  
                $(".overlay,.loading-img").remove();
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
}
</script>
