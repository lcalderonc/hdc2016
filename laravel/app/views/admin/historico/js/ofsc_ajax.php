<script type="text/javascript">
var OFSC={
    Completar:function(evento,datos){
        //var permiso=9;
        $.ajax({
            url         : 'bandeja/completarofsc',
            type        : 'POST',
            cache       : false,
            async       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                
                if(obj.rst==1){
                    evento(obj);
                    Psi.mensaje('success',obj.msj,3000);
                }
                else{
                    if( typeof obj.error_msg !='undefined' ){
                    alert(obj.error_msg);
                    }
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
    Iniciar:function(evento,datos){
        //var permiso=9;
        $.ajax({
            url         : 'bandeja/iniciarofsc',
            type        : 'POST',
            cache       : false,
            async       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                
                if(obj.rst==1){
                    evento(obj);
                    Psi.mensaje('success',obj.msj,3000);
                }
                else{
                    if( typeof obj.error_msg !='undefined' ){
                    alert(obj.error_msg);
                    }
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
    Cancelar:function(evento,datos){
        //var permiso=9;
        $.ajax({
            url         : 'bandeja/cancelarofsc',
            type        : 'POST',
            cache       : false,
            async       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                
                if(obj.rst==1){
                    evento(obj);
                    Psi.mensaje('success',obj.msj,3000);
                }
                else{
                    if( typeof obj.error_msg !='undefined' ){
                    alert(obj.error_msg);
                    }
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
    Capacity:function(evento,datos){
        //var permiso=9;
        $.ajax({
            url         : 'bandeja/capacity',
            type        : 'POST',
            cache       : false,
            async       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    //evento(obj.datos.get_capacity_response);
                    var duracion=parseInt(obj.duracion);
                    evento(obj.datos.data, duracion);
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
    EnvioOfsc:function(evento,datos){
        //var permiso=9;
        $.ajax({
            url         : 'bandeja/envioofsc',
            type        : 'POST',
            cache       : false,
            async       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    evento(obj);
                    Psi.mensaje('success',obj.msj,3000);
                }
                else{
                    alert('Error de envio Ofsc');
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
        
        return permiso;
    }
};
</script>
