<script type="text/javascript">
var EnviosOfsc={
    
    CargarEnviosOfsc:function(){
        $.ajax({
            url         : 'enviosofsc/reporteofsc',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data    : {
                fechaIni    : $("#fecha_recepcion_ini").val(),
                fechaFin    : $("#fecha_recepcion_fin").val(),
            },            
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                var html="";
                var estadohtml="";
                if(obj.rst==1){
                    
                    HTMLCargarEnviosOfsc(obj.datos);
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.', 6000);
            }
        });
    }
     
};
</script>